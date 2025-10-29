<?php

namespace app\models;

use yii\db\ActiveRecord;

class Book extends ActiveRecord
{
    public static function tableName()
    {
        return 'book';
    }

    public function rules()
    {
        return [
            [['title', 'author', 'published_year'], 'required'],
            [['published_year'], 'integer'],
            [['title', 'author', 'genre'], 'string', 'max' => 255],
        ];
    }
}
