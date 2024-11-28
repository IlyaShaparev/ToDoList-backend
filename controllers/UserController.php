<?php

namespace app\controllers;

use app\models\User;
use DateTime;
use Yii;
use yii\base\Exception;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

//TODO: Реализовать IdentyInterface
class UserController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\User';

    /**
     * Объявление поведений контроллера
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }
}