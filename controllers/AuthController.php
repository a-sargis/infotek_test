<?php
namespace app\controllers;

use Yii;
use app\models\User;
use app\models\LoginForm;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\TooManyRequestsHttpException;

class AuthController extends Controller
{
    private const int LAST_ATTEMPT_TIMEOUT = 900;

    private const int MAX_ATTEMPTS = 5;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'me'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Проверка rate limiting для защиты от брутфорса
     * @throws TooManyRequestsHttpException
     */
    private function checkRateLimit(): void
    {
        $session = Yii::$app->session;
        $ip = Yii::$app->request->userIP;
        $key = 'login_attempts_' . md5($ip);
        $attemptsKey = 'login_attempts_count_' . md5($ip);

        $lastAttempt = $session->get($key, 0);
        $attempts = $session->get($attemptsKey, 0);
        $now = time();

        // Если прошло больше 15 минут с последней попытки, сбрасываем счетчик
        if ($now - $lastAttempt > self::LAST_ATTEMPT_TIMEOUT) {
            $attempts = 0;
        }

        if ($attempts >= self::MAX_ATTEMPTS) {
            $waitTime = self::LAST_ATTEMPT_TIMEOUT - ($now - $lastAttempt);
            throw new TooManyRequestsHttpException("Слишком много попыток входа. Попробуйте через " . ceil($waitTime / 60) . " минут.");
        }

        $session->set($key, $now);
        $session->set($attemptsKey, $attempts + 1);
    }

    /**
     * @throws TooManyRequestsHttpException
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/book/index']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            // Проверка rate limiting перед попыткой входа
            $this->checkRateLimit();

            if ($model->login()) {
                // Успешный вход - сбрасываем счетчик попыток
                $session = Yii::$app->session;
                $ip = Yii::$app->request->userIP;
                $session->remove('login_attempts_' . md5($ip));
                $session->remove('login_attempts_count_' . md5($ip));

                return $this->redirect(['/book/index']);
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->redirect(['/auth/login']);
    }

    public function actionMe(): Response
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 401;
            return $this->asJson(['error' => 'Не авторизован']);
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        return $this->asJson([
            'id'       => $user->getAttribute('id'),
            'username' => $user->getAttribute('username'),
            'email'    => $user->getAttribute('email'),
        ]);
    }
}