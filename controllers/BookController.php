<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use Yii;
use app\models\Book;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    public function actions()
    {
        $actions = parent::actions();
        // Disable default index action so we can customize it
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = Book::find();

        $author = Yii::$app->request->get('author');
        $genre  = Yii::$app->request->get('genre');
        $title  = Yii::$app->request->get('title');

        if ($author) {
            $query->andWhere(['author' => $author]);
        }

        if ($genre) {
            $query->andWhere(['genre' => $genre]);
        }

        if ($title) {
            $query->andWhere(['like', 'title', $title]);
        }

        return [
            'success' => true,
            'data' => $query->all()
        ];
    }
}
