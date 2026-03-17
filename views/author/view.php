<?php


/** @var yii\web\View $this */
/** @var app\models\Author $author */

use yii\helpers\Html;

$this->title = $author->name;
?>
    <h1><?= Html::encode($author->name) ?></h1>

    <h3>Книги автора</h3>
<?php if ($author->books): ?>
    <ul>
        <?php foreach ($author->books as $book): ?>
            <li>
                <a href="/book/view?id=<?= $book->getAttribute('id') ?>">
                    <?= Html::encode($book->title) ?>
                </a>
                (<?= $book->year ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Книг пока нет.</p>
<?php endif; ?>

    <br>
    <a href="/author/subscribe?authorId=<?= $author->getAttribute('id') ?>" class="btn btn-success">Подписаться на автора</a>
    <a href="/author/index" class="btn">← Назад</a>
<?php if (!Yii::$app->user->isGuest): ?>
    <a href="/author/update?id=<?= $author->getAttribute('id') ?>" class="btn">✏️ Изменить</a>
<?php endif; ?>