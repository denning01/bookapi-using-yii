<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use Yii;
use app\models\Book;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']); // override index
        return $actions;
    }
    //added this action 
    //start
    public function behaviors()
{
    $behaviors = parent::behaviors();

    // Add content negotiation to always return JSON
    $behaviors['contentNegotiator'] = [
        'class' => ContentNegotiator::class,
        'formats' => [
            'application/json' => Response::FORMAT_JSON,
        ],
    ];

    return $behaviors;
}
    //end

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

        // ActiveDataProvider for pagination
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5, // number of books per page
            ],
            'sort' => [
            'defaultOrder' => ['id' => SORT_ASC],
            ],
        ]);

        return $dataProvider;
    }
}
