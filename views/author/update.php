<?php

/** @var yii\web\View $this */
/** @var app\models\Author $author */

$this->title = 'Редактировать: ' . $author->name;
?>
<h1>Редактировать автора</h1>
<?= $this->render('_form', ['author' => $author]) ?>