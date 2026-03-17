<?php

// views/book/create.php
/** @var yii\web\View $this */
/** @var app\models\Book $book */
/** @var app\models\Author[] $authors */
$this->title = 'Добавить книгу';
?>
    <h1>Добавить книгу</h1>
<?= $this->render('_form', ['book' => $book, 'authors' => $authors]) ?>