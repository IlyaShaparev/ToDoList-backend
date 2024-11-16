<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            [['login', 'name', 'password_hash'], 'required'],
            ['name', 'string', 'max' => 255],
            ['login', 'string', 'max' => 255],
            ['password_hash', 'string', 'max' => 255],
            ['token', 'string', 'max' => 255],
            ['token_updated_time', 'datetime']
        ];
    }
}
