<?php

/** @var yii\web\View $this */
/** @var app\models\Book[] $books */

use yii\helpers\Html;

$this->title = 'Книги';
?>
<h1>Каталог книг</h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <a href="/book/create" class="btn btn-success">+ Добавить книгу</a>
<?php endif; ?>

<br><br>

<table>
    <tr>
        <th>Название</th>
        <th>Год</th>
        <th>ISBN</th>
        <th>Авторы</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><?= Html::encode($book->title) ?></td>
            <td><?= $book->year ?></td>
            <td><?= Html::encode($book->isbn) ?></td>
            <td>
                <?php
                $authorLinks = [];
                foreach ($book->authors as $author) {
                    $authorLinks[] = '<a href="/author/view?id=' . $author->getAttribute('id') . '">' . Html::encode($author->name) . '</a>';
                }
                echo implode(', ', $authorLinks);
                ?>
            </td>
            <td>
                <a href="/book/view?id=<?= $book->getAttribute('id') ?>" class="btn">👁 Просмотр</a>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <a href="/book/update?id=<?= $book->getAttribute('id') ?>" class="btn">✏️ Изменить</a>
                    <form method="post" action="/book/delete" style="display:inline">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <input type="hidden" name="id" value="<?= $book->getAttribute('id') ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить?')">🗑 Удалить</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>