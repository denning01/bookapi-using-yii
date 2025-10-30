<?php

namespace app\controllers;

//adds this line
use app\components\JwtHttpBearerAuth;


use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use Yii;
use app\models\Book;
use app\models\User;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']); // override index
        return $actions;
    }

    public function behaviors()
{
    $behaviors = parent::behaviors();

    // Force JSON response
    $behaviors['contentNegotiator'] = [
        'class' => \yii\filters\ContentNegotiator::class,
        'formats' => [
            'application/json' => \yii\web\Response::FORMAT_JSON,
        ],
    ];

    // Use our custom JWT authenticator
    $behaviors['authenticator'] = [
        'class' => JwtHttpBearerAuth::class,
    ];

    return $behaviors;
}

    // ✅ Books listing with filtering
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

        // Paginate & sort
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
            ],
        ]);

        return $dataProvider;
    }
}
