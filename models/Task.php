<?php

namespace app\models;

class Task extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%task}}';
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'priority', 'user_id'], 'required'],
            ['title', 'string', 'min' => 3, 'max' => 50],
            ['description', 'string'],
            ['created_time', 'string', 'max' => 20],
            ['updated_time', 'string', 'max' => 20],
            ['closed_time', 'string', 'max' => 20],
            ['priority', 'integer'],
            ['user_id', 'integer'],
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}