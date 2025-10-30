<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * Find user by ID
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Find user by access token (used for Bearer authentication)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Get user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validate auth key
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Find user by username
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Validate user password
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Hash password and generate auth_key before saving
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Hash password only when creating a new user or updating password
            if ($this->isAttributeChanged('password_hash')) {
                $this->password_hash = password_hash($this->password_hash, PASSWORD_DEFAULT);
            }

            // Generate auth_key for new users
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString(32);
            }

            return true;
        }
        return false;
    }
}
