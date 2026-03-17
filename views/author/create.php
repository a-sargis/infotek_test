<?php

/** @var yii\web\View $this */
/** @var app\models\Author $author */

$this->title = 'Добавить автора';
?>
<h1>Добавить автора</h1>
<?= $this->render('_form', ['author' => $author]) ?>