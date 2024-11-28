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
            [['text', 'created_time', 'task_id', 'user_id'], 'required'],
            ['text', 'string', 'min' => 3, 'max' => 255],
            ['created_time', 'string', 'max' => 20],
            ['task_id' => 'integer'],
            ['user_id' => 'integer'],
        ];
    }
}