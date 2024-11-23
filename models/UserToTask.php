<?php

namespace app\models;

use \yii\db\ActiveRecord;

class UserToTask extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%user_to_task}}';
    }

    public function rules(): array
    {
        return [
            [['task_id', 'user_id'], 'required'],
            ['task_id', 'integer'],
            ['user_id', 'integer'],
        ];
    }
}