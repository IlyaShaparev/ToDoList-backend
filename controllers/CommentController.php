<?php

namespace app\controllers;

use app\models\Comment;
use Yii;
use yii\db\Exception;
use yii\filters\auth\HttpBearerAuth;
use \yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CommentController extends Controller
{
    /**
     * <b>Настраиваем аутентификацию по токену</b> <br>
     * <i>(Проверяется автоматически перед action)</i>
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

    /**
     * <b>Получаем запись по id</b> <br>
     * <i>(endpoint GET /comments/{task_id})</i>
     * @param int|null $task_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionView(?int $task_id): array
    {
        $comments = Comment::findAll(['task_id' => $task_id]);
        if (is_null($comments)) {
            throw new NotFoundHttpException("Comments not found");
        }
        return [$comments];
    }

    /**
     * <b>Оставляем комментарий</b> <br>
     * <i>(endpoint POST /comments)</i>
     * @return array
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionCreate(): array
    {
        $request = Yii::$app->request;
        $commentTemplate = new Comment();

        $commentTemplate->text = $request->post('text');
        $commentTemplate->task_id = $request->post('task_id');
        $commentTemplate->created_time = date("Y-m-d H:i:s");
        $commentTemplate->user_id = Yii::$app->user->identity->getId();

        if ($commentTemplate->save()) {
            return [$commentTemplate];
        }

        throw new BadRequestHttpException("Something went wrong");
    }
}