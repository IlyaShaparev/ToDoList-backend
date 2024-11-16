<?php

namespace app\controllers;

use yii\filters\auth\HttpBasicAuth;

//TODO: Реализовать IdentyInterface
class UserController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\User';
}