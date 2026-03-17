<?php

/** @var yii\web\View $this */
/** @var app\models\Author[] $authors */

use yii\helpers\Html;

$this->title = 'Авторы';
?>
<h1>Авторы</h1>

<a href="/author/top" class="btn">ТОП-10 авторов</a>
<?php if (!Yii::$app->user->isGuest): ?>
    <a href="/author/create" class="btn btn-success">+ Добавить автора</a>
<?php endif; ?>
<br><br>

<table>
    <tr>
        <th>ФИО</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($authors as $author): ?>
        <tr>
            <td><?= Html::encode($author->name) ?></td>
            <td>
                <a href="/author/view?id=<?= $author->getAttribute('id') ?>" class="btn">👁 Просмотр</a>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <a href="/author/update?id=<?= $author->getAttribute('id') ?>" class="btn">✏️ Изменить</a>
                    <form method="post" action="/author/delete" style="display:inline">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <input type="hidden" name="id" value="<?= $author->getAttribute('id') ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить?')">🗑 Удалить</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>