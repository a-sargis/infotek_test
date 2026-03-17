<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\UploadedFile;

class Book extends ActiveRecord
{
    public array $authorIds = [];
    public ?UploadedFile $imageFile = null;

    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function rules(): array
    {
        return [
            [['title', 'year'], 'required'],
            [['year'], 'integer'],
            [['description'], 'string'],
            [['title', 'isbn', 'image'], 'string', 'max' => 255],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['imageFile'], 'file', 'extensions' => 'png, jpg, jpeg, gif, webp', 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function uploadImage(): bool
    {
        if ($this->imageFile === null) {
            return true;
        }

        $uploadPath = Yii::getAlias('@webroot/uploads/covers');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = uniqid('cover_') . '.' . $this->imageFile->extension;
        $filePath = $uploadPath . '/' . $fileName;

        if ($this->imageFile->saveAs($filePath)) {
            // Удаляем старую обложку если есть
            if ($this->image && str_starts_with($this->image, '/uploads/covers/')) {
                $oldFile = Yii::getAlias('@webroot') . $this->image;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $this->image = '/uploads/covers/' . $fileName;
            return true;
        }

        return false;
    }

    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'title'       => 'Название',
            'year'        => 'Год выпуска',
            'description' => 'Описание',
            'isbn'        => 'ISBN',
            'image'       => 'Фото обложки',
            'authorIds'   => 'Авторы',
        ];
    }

    public function getBookAuthors(): ActiveQuery
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->via('bookAuthors');
    }

    /**
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        BookAuthor::deleteAll(['book_id' => $this->getAttribute('id')]);

        foreach ($this->authorIds as $authorId) {
            $ba = new BookAuthor();
            $ba->book_id   = $this->getAttribute('id');
            $ba->author_id = (int) $authorId;
            $ba->save();
        }

        // Уведомляем подписчиков о новой книге
        if ($insert && !empty($this->authorIds)) {
            $this->notifySubscribers();
        }
    }

    private function notifySubscribers(): void
    {
        // Получаем уникальные номера телефонов подписчиков авторов этой книги
        $phones = Subscription::find()
            ->select('phone')
            ->where(['author_id' => $this->authorIds])
            ->distinct()
            ->column();

        if (empty($phones)) {
            return;
        }

        // Получаем имена авторов
        $authorNames = Author::find()
            ->select('name')
            ->where(['id' => $this->authorIds])
            ->column();

        $authorsText = implode(', ', $authorNames);
        $message = "Новая книга \"{$this->title}\" от автора: {$authorsText}. Год: {$this->year}";

        // Отправляем SMS
        foreach ($phones as $phone) {
            Yii::$app->sms->send($phone, $message);
        }
    }
}