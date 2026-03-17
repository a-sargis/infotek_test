<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<h1><?= Html::encode($name) ?></h1>
<p><?= Html::encode($message) ?></p>
<a href="/book/index" class="btn">← На главную</a>