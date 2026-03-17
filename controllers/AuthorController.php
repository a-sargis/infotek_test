<?php
namespace app\controllers;

use Yii;
use app\models\Author;
use app\models\Subscription;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

class AuthorController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        // Просмотр, подписка и отчёт — для всех включая гостей
                        'actions' => ['index', 'view', 'subscribe', 'top'],
                        'allow'   => true,
                        'roles'   => ['?', '@'],
                    ],
                    [
                        // CRUD — только залогиненный
                        'actions' => ['create', 'update', 'delete'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $authors = Author::find()->all();
        return $this->render('index', ['authors' => $authors]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', ['author' => $this->findAuthor($id)]);
    }

    /**
     * @throws Exception
     */
    public function actionCreate(): string|Response
    {
        $author = new Author();

        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            return $this->redirect(['view', 'id' => $author->getAttribute('id')]);
        }

        return $this->render('create', ['author' => $author]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): string|Response
    {
        $author = $this->findAuthor($id);

        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', ['author' => $author]);
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws NotFoundHttpException
     */
    public function actionDelete(): Response
    {
        $id = (int) Yii::$app->request->post('id');
        $this->findAuthor($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionSubscribe(int $authorId): Response|string
    {
        $author = $this->findAuthor($authorId);
        $subscription = new Subscription();
        $subscription->author_id = $authorId;

        if ($subscription->load(Yii::$app->request->post())) {
            if ($subscription->save()) {
                Yii::$app->session->setFlash('success', 'Вы подписались на автора. Мы уведомим вас о новых книгах.');
                return $this->redirect(['view', 'id' => $authorId]);
            }
        }

        return $this->render('subscribe', [
            'author' => $author,
            'subscription' => $subscription,
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionTop(?int $year = null): string
    {
        $currentYear = (int) date('Y');
        $year = $year ?? $currentYear;

        $authors = Author::find()
            ->select(['author.*', 'COUNT(book.id) as book_count'])
            ->innerJoin('{{%book_author}}', 'book_author.author_id = author.id')
            ->innerJoin('{{%book}}', 'book.id = book_author.book_id AND book.year = :year', [':year' => $year])
            ->groupBy('author.id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        $years = Yii::$app->db->createCommand(
            'SELECT DISTINCT year FROM {{%book}} ORDER BY year DESC'
        )->queryColumn();

        return $this->render('top', [
            'authors' => $authors,
            'year' => $year,
            'years' => $years,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findAuthor(int $id): Author
    {
        $author = Author::findOne($id);
        if (!$author) {
            throw new NotFoundHttpException('Автор не найден.');
        }
        return $author;
    }
}