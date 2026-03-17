<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public int|string $id {
        get => (int) ($this->getAttribute('id') ?? 0);
    }

    public string $auth_key {
        get => (string) ($this->getAttribute('auth_key'));
    }

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'password_hash'], 'required'],
            ['email', 'email'],
            [['username', 'email'], 'string', 'max' => 255],
        ];
    }

    public static function findIdentity($id): IdentityInterface|static|null
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): IdentityInterface|static|null
    {
        return static::findOne(['auth_key' => $token]);
    }

    public static function findByUsername(string $username): null|static
    {
        return static::findOne(['username' => $username]);
    }

    public function getId(): int
    {
        return (int) $this->getAttribute('id');
    }

    public function getAuthKey(): ?string
    {
        return $this->getAttribute('auth_key');
    }

    public function validateAuthKey($authKey): ?bool
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->getAttribute('password_hash'));
    }

    /**
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->setAttribute('password_hash', Yii::$app->security->generatePasswordHash($password));
    }

    public function generateAuthKey(): void
    {
        $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());
    }
}
