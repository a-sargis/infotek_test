<?php

/** @var yii\web\View $this */
/** @var app\models\Book $book */

use yii\helpers\Html;

$this->title = $book->title;
?>
    <h1><?= Html::encode($book->title) ?></h1>

    <table style="width:auto">
        <tr><th>Год</th><td><?= $book->year ?></td></tr>
        <tr><th>ISBN</th><td><?= Html::encode($book->isbn) ?></td></tr>
        <tr><th>Описание</th><td><?= nl2br(Html::encode($book->description)) ?></td></tr>
        <tr>
            <th>Авторы</th>
            <td>
                <?php foreach ($book->authors as $author): ?>
                    <a href="/author/view?id=<?= $author->getAttribute('id') ?>">
                        <?= Html::encode($author->name) ?>
                    </a><br>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php if ($book->image): ?>
            <tr>
                <th>Обложка</th>
                <td><img src="<?= Html::encode($book->image) ?>" style="max-width:200px"></td>
            </tr>
        <?php endif; ?>
    </table>

    <br>
    <a href="/book/index" class="btn">← Назад</a>
<?php if (!Yii::$app->user->isGuest): ?>
    <a href="/book/update?id=<?= $book->getAttribute('id') ?>" class="btn">✏️ Изменить</a>
<?php endif; ?>