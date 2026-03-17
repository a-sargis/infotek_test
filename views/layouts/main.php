<?php
/** @var yii\web\View $this */
/** @var string $content */
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= Html::encode($this->title) ?> — Каталог книг</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 960px; margin: 0 auto; padding: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: #fff; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .btn { display: inline-block; padding: 6px 12px; background: #333; color: #fff; text-decoration: none; margin: 2px; }
        .btn-danger { background: #c00; }
        .btn-success { background: #060; }
        input[type=text], input[type=number], input[type=email], input[type=password], textarea, select {
            width: 100%; padding: 6px; margin: 4px 0 12px; box-sizing: border-box; border: 1px solid #ccc;
        }
        label { font-weight: bold; }
        .alert { padding: 10px; margin-bottom: 15px; border: 1px solid; }
        .alert-success { background: #dfd; border-color: #090; }
        .alert-error   { background: #fdd; border-color: #c00; }
        .alert-info    { background: #ddf; border-color: #009; }
        .errors { color: #c00; font-size: 0.9em; }
    </style>
</head>
<body>

<nav>
    <a href="/book/index">📚 Книги</a>
    <a href="/author/index">✍️ Авторы</a>
    <?php if (Yii::$app->user->isGuest): ?>
        <a href="/auth/login">Войти</a>
    <?php else: ?>
        <span style="color:#aaa">
            <?= Html::encode(Yii::$app->user->identity->getAttribute('username')) ?>
        </span>
        <a href="/auth/logout" style="margin-left:15px">Выйти</a>
    <?php endif; ?>
</nav>

<?php foreach (['success','error','info'] as $type): ?>
    <?php if (Yii::$app->session->hasFlash($type)): ?>
        <div class="alert alert-<?= $type ?>">
            <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?= $content ?>

</body>
</html>