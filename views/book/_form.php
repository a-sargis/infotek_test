<?php

// views/book/_form.php
/** @var yii\web\View $this */
/** @var app\models\Book $book */
/** @var app\models\Author[] $authors */
use yii\helpers\Html;

$isNew  = $book->isNewRecord;
$action = $isNew ? '/book/create' : '/book/update?id=' . $book->getAttribute('id');
?>

<?php if ($book->hasErrors()): ?>
    <div class="alert alert-error">
        <?php foreach ($book->getFirstErrors() as $error): ?>
            <div><?= Html::encode($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

    <label>Название *</label>
    <input type="text" name="Book[title]" value="<?= Html::encode($book->title) ?>" required>

    <label>Год выпуска *</label>
    <input type="number" name="Book[year]" value="<?= $book->year ?>" required>

    <label>Описание</label>
    <textarea name="Book[description]" rows="4"><?= Html::encode($book->description) ?></textarea>

    <label>ISBN</label>
    <input type="text" name="Book[isbn]" value="<?= Html::encode($book->isbn) ?>">

    <label>Обложка</label>
    <?php if ($book->image): ?>
        <div style="margin-bottom: 10px;">
            <img src="<?= Html::encode($book->image) ?>" style="max-width: 150px; max-height: 200px;">
            <br><small>Текущая обложка</small>
        </div>
    <?php endif; ?>
    <input type="file" name="Book[imageFile]" accept="image/png,image/jpeg,image/gif,image/webp">
    <small>PNG, JPG, GIF, WebP. Макс. 5 МБ</small>

    <label>Авторы</label>
    <select name="Book[authorIds][]" multiple size="5">
        <?php foreach ($authors as $author): ?>
            <option value="<?= $author->getAttribute('id') ?>"
                <?= in_array($author->getAttribute('id'), $book->authorIds ?? []) ? 'selected' : '' ?>>
                <?= Html::encode($author->name) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br>
    <button type="submit" class="btn btn-success">💾 Сохранить</button>
    <a href="/book/index" class="btn">Отмена</a>
</form>