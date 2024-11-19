<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function rules(): array
    {
        return [
            [['login', 'name', 'password_hash'], 'required'],
            ['name', 'string', 'max' => 255],
            ['login', 'string', 'max' => 255],
            ['password_hash', 'string', 'max' => 255],
            ['token', 'string', 'max' => 255],
            ['token_updated_time', 'string']
        ];
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($token)
    {
        return (bool) self::findIdentityByAccessToken($token) ?? false;
    }
}
