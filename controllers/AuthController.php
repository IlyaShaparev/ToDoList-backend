<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\base\Exception;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends \yii\rest\Controller
{
    public string $modelClass = 'app\models\User';

    /**
     * @throws UnauthorizedHttpException
     * @throws Exception
     * @return array
     */
    public function actionAuth(): array
    {
        $request = Yii::$app->request;
        $login = $request->post('login');
        $password = $request->post('password');

        $user = User::findOne(['login' => $login]);

        // TODO: Заменить на проверку хэша
        if ($user && $password === $user->password_hash) {

            $token = Yii::$app->security->generateRandomString();
            $user->token = $token;
            $user->token_updated_time = date('Y-m-d H:i:s');


            if ($user->save()) {
                return ['token' => $token];
            } else {
                // TODO: Кастомный Exception
                throw new Exception('Bad save');
            }
        } else {
            throw new UnauthorizedHttpException('Bad auth data');
        }
    }

    /**
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $login = $request->post('login');
        $password = $request->post('password');
        $name = $request->post('name');

        $user = User::findOne(['login' => $login]);

        if($user) {
            throw new ForbiddenHttpException('A user with this username already exists');
        }

        $user = new User();
        $user->name = $name;
        $user->login = $login;
        $user->password_hash = $password;
        if($user->save()) {
            return $user;
        } else {
            throw new Exception('Bad save');
        }
    }
}