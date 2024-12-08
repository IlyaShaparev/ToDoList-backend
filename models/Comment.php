<?php

namespace app\models;

use \yii\db\ActiveRecord;

class Comment extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%comment}}';
    }

    public function rules(): array
    {
        return [
            [['commentText', 'created_time', 'task_id', 'user_id'], 'required'],
            ['commentText', 'string'],
            ['created_time', 'string'],
            ['task_id', 'integer'],
            ['user_id', 'integer']
        ];
    }
}