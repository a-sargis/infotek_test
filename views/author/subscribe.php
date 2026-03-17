<?php

/** @var yii\web\View $this */
/** @var app\models\Author $author */
/** @var app\models\Subscription $subscription */

use yii\helpers\Html;

$this->title = 'Подписка на автора: ' . $author->name;
?>

<h1>Подписка на автора</h1>
<p>Вы подписываетесь на уведомления о новых книгах автора <strong><?= Html::encode($author->name) ?></strong>.</p>

<?php if ($subscription->hasErrors()): ?>
    <div class="alert alert-error">
        <?php foreach ($subscription->getFirstErrors() as $error): ?>
            <div><?= Html::encode($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="/author/subscribe?authorId=<?= $author->getAttribute('id') ?>">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

    <label>Номер телефона *</label>
    <input type="tel"
           name="Subscription[phone]"
           value="<?= Html::encode($subscription->phone) ?>"
           placeholder="+79001234567"
           required
           autofocus>
    <small>Формат: +79001234567</small>

    <br><br>
    <button type="submit" class="btn btn-success">Подписаться</button>
    <a href="/author/view?id=<?= $author->getAttribute('id') ?>" class="btn">Отмена</a>
</form>
