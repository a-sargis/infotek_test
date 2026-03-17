<?php

/** @var yii\web\View $this */
/** @var app\models\Author $author */

use yii\helpers\Html;

$isNew  = $author->isNewRecord;
$action = $isNew ? '/author/create' : '/author/update?id=' . $author->getAttribute('id');
?>

<?php if ($author->hasErrors()): ?>
    <div class="alert alert-error">
        <?php foreach ($author->getFirstErrors() as $error): ?>
            <div><?= Html::encode($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= $action ?>">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

    <label>ФИО *</label>
    <input type="text" name="Author[name]" value="<?= Html::encode($author->name) ?>" required autofocus>

    <br>
    <button type="submit" class="btn btn-success">💾 Сохранить</button>
    <a href="/author/index" class="btn">Отмена</a>
</form>