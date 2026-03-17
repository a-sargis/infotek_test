<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;
use Exception;
use app\models\User;


class UserController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionInitRbac(): int
    {
        $auth = Yii::$app->getAuthManager();

        // Создаём роли
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        $user = $auth->createRole('user');
        $user->description = 'Regular user';
        $auth->add($user);

        $this->stdout("RBAC инициализирован.\n", BaseConsole::FG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Создание тестового пользователя с ролью admin
     *
     * Использование:
     * php yii user/create-admin testuser test@example.com 123456
     *
     * @throws Exception
     */
    public function actionCreateAdmin($username, $email, $password): int
    {
        if (User::find()->where(['username' => $username])->orWhere(['email' => $email])->exists()) {
            $this->stderr("Пользователь с таким username или email уже существует.\n", BaseConsole::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();

        if (!$user->save()) {
            $this->stderr("Не удалось создать пользователя:\n", BaseConsole::FG_RED);
            foreach ($user->getFirstErrors() as $error) {
                $this->stderr("- $error\n", BaseConsole::FG_RED);
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // Назначаем роль admin через RBAC
        $auth = Yii::$app->getAuthManager();

        $adminRole = $auth->getRole('admin');
        if (!$adminRole) {
            $this->stderr("Роль 'admin' не найдена. Создайте её через RBAC.\n", BaseConsole::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $auth->assign($adminRole, $user->id);

        $this->stdout("Пользователь '$username' создан и получил роль 'admin'.\n", BaseConsole::FG_GREEN);

        return ExitCode::OK;
    }
}
