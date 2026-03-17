<?php


namespace app\controllers;

use Throwable;
use Yii;
use app\models\Book;
use app\models\Author;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

class BookController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        // Гость — только просмотр
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        // Залогиненный — всё остальное
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $books = Book::find()->with('authors')->all();
        return $this->render('index', ['books' => $books]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', ['book' => $this->findBook($id)]);
    }

    public function actionCreate(): string|Response
    {
        $book = new Book();
        $authors = Author::find()->all();

        if ($book->load(Yii::$app->request->post())) {
            $book->imageFile = UploadedFile::getInstance($book, 'imageFile');
            if ($book->validate() && $book->uploadImage() && $book->save(false)) {
                return $this->redirect(['view', 'id' => $book->getAttribute('id')]);
            }
        }

        return $this->render('create', ['book' => $book, 'authors' => $authors]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): string|Response
    {
        $book = $this->findBook($id);
        $authors = Author::find()->all();

        $book->authorIds = array_column($book->authors, 'id');

        if ($book->load(Yii::$app->request->post())) {
            $book->imageFile = UploadedFile::getInstance($book, 'imageFile');
            if ($book->validate() && $book->uploadImage() && $book->save(false)) {
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('update', ['book' => $book, 'authors' => $authors]);
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     * @throws NotFoundHttpException
     */
    public function actionDelete(): Response
    {
        $id = (int) Yii::$app->request->post('id');
        $this->findBook($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findBook(int $id): Book
    {
        $book = Book::findOne($id);
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена.');
        }
        return $book;
    }
}