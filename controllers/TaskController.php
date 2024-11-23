<?php

namespace app\controllers;

use app\models\Task;
use app\models\UserToTask;
use Exception;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use \yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class TaskController extends Controller
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
     * <b>Получаем все записи</b> <br>
     * <i>(endpoint GET /tasks)</i>
     * @return array
     */
    public function actionIndex(): array
    {
        return Task::find()->all();
    }

    /**
     * <b>Создаем задачу</b> <br>
     * <i>(endpoint POST /tasks)</i>
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCreate(): array
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user->identity;
        $title = $request->post("title");
        $description = $request->post("description");
        $color = $request->post("color");

        $taskTemplate = new Task();

        $taskTemplate->title = $title;
        $taskTemplate->description = $description;
        $taskTemplate->color = $color;
        $taskTemplate->created_time = date('Y-m-d H:i:s');

        if (!$taskTemplate->save()) {
           throw new BadRequestHttpException("Bad task data");
        }

        $taskToUserTemplate = new UserToTask();

        $taskToUserTemplate->task_id = $taskTemplate->getId();
        $taskToUserTemplate->user_id = $user->getId();

        if (!$taskToUserTemplate->save()) {
            throw new BadRequestHttpException("Bad task or user data");
        }

        return ['task' => $taskTemplate];
    }

    /**
     * <b>Получаем запись по id</b> <br>
     * <i>(endpoint GET /tasks/{id})</i>
     * @param int|null $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionView(?int $id): array
    {
        $task = Task::findOne($id);
        if (is_null($task)) {
            throw new NotFoundHttpException("Task not found");
        }
        return [$task];
    }

    /**
     * <b>Изменяем запись по id</b> <br>
     * <i>(endpoint PUT|PATCH /tasks/{id})</i>
     * @param int|null $id
     * @return array
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionUpdate(?int $id): array
    {
        $task = Task::findOne($id);
        if (is_null($task)) {
            throw new NotFoundHttpException("Task not found");
        }

        $params = json_decode(Yii::$app->request->getRawBody());

        foreach($params as $key => $value) {
            $task->$key = $value;
        }
        $task->updated_time = date("Y-m-d H:i:s");

        if ($task->save()) {
            return [$task];
        }

        throw new BadRequestHttpException("Something went wrong");
    }

    /**
     * <b>Удаляем задачу по id</b> <br>
     * <i>(endpoint DELETE /tasks/{id})</i>
     * @param int|null $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(?int $id): array
    {
        $task = Task::findOne($id);
        if (is_null($task)) {
            throw new NotFoundHttpException("Task not found");
        }

        try {
            $task->delete();
        } catch (Exception $e) {
            throw new BadRequestHttpException("Something went wrong");
        }
        return ["message" => "Resource deleted successfully."];
    }

    /**
     * <b>Изменяем статус задачи по id</b> <br>
     * <i>(endpoint POST /tasks/status/{id})</i>
     * @param int|null $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionStatus(?int $id): array
    {
        $task = Task::findOne($id);
        if (is_null($task)) {
            throw new NotFoundHttpException("Task not found");
        }

        $request = Yii::$app->request;
        $isClosed = $request->post('is_closed');

        if (!is_null($isClosed) && $isClosed) {
            $task->is_closed = 1;
            $task->closed_time = date("Y-m-d H:i:s");
        } elseif (!is_null($isClosed) && !$isClosed) {
            $task->is_closed = 0;
            $task->closed_time = null;
            $task->updated_time = date('Y-m-d H:i:s');
        } else {
            throw new BadRequestHttpException("Bad data");
        }

        if($task->save()) {
            return [$task];
        }

        throw new BadRequestHttpException("Something went wrong");
    }
}