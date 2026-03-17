<?php

/** @var yii\web\View $this */
/** @var array $authors */
/** @var int $year */
/** @var array $years */

use yii\helpers\Html;

$this->title = 'ТОП-10 авторов за ' . $year . ' год';
?>

<h1>ТОП-10 авторов по количеству книг</h1>

<form method="get" action="/author/top">
    <label>Выберите год:</label>
    <select name="year" onchange="this.form.submit()">
        <?php foreach ($years as $y): ?>
            <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit">Показать</button></noscript>
</form>

<br>

<?php if ($authors): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Автор</th>
            <th>Книг за <?= $year ?> год</th>
        </tr>
        <?php foreach ($authors as $i => $author): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <a href="/author/view?id=<?= $author['id'] ?>">
                        <?= Html::encode($author['name']) ?>
                    </a>
                </td>
                <td><?= $author['book_count'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Нет данных за <?= $year ?> год.</p>
<?php endif; ?>

<br>
<a href="/author/index" class="btn">← К списку авторов</a>
