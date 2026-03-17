<?php

// views/book/update.php
/** @var yii\web\View $this */
/** @var app\models\Book $book */
/** @var app\models\Author[] $authors */
$this->title = 'Редактировать: ' . $book->title;
?>
    <h1>Редактировать книгу</h1>
<?= $this->render('_form', ['book' => $book, 'authors' => $authors]) ?>