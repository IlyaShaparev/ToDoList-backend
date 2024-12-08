<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use app\models\Task;

class ExportController extends Controller
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

    public function actionExport($format)
    {
        switch ($format) {
            case 'csv':
                return $this->exportCsv();
            case 'xml':
                return $this->exportXml();
            default:
                throw new \yii\web\BadRequestHttpException("Unsupported format: $format");
        }
    }

    private function exportCsv()
    {
        $user = Yii::$app->user->identity;
        $data = Task::find()->where(['user_id' => $user->getId()])->all();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Номер задачи', 'Название', 'Описание', 'Статус',
            'Время создания', 'Приоритет', 'Время завершения'
        ]);

        foreach ($data as $row) {
            fputcsv($output, [
                $row->id, $row->title, $row->description, $row->is_closed,
                $row->created_time, $row->priority, $row->closed_time
            ]);
        }
        fclose($output);
        exit;
    }

    private function exportXml()
    {
        $user = Yii::$app->user->identity;
        $data = Task::find()->where(['user_id' => $user->getId()])->all();

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="export.xml"');

        $xml = new \SimpleXMLElement('<root/>');

        foreach ($data as $row) {
            $item = $xml->addChild('item');
            $item->addChild('id', $row->id);
            $item->addChild('title', $row->title);
            $item->addChild('description', $row->description);
            $item->addChild('is_closed', $row->is_closed);
            $item->addChild('created_time', $row->created_time);
            $item->addChild('priority', $row->priority);
            $item->addChild('closed_time', $row->closed_time);
        }

        echo $xml->asXML();
        exit;
    }
}