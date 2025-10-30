<?php
namespace app\controllers;

use yii\rest\ActiveController;
use Yii;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Force JSON responses
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;
        return $behaviors;
    }

    // GET /user/profile
    public function actionProfile()
    {
        // Get Authorization header from request
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if (!$authHeader) {
            return ['status' => 'error', 'message' => 'Missing Authorization header'];
        }

        // Remove "Bearer " prefix
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Decode JWT token
            $decoded = JWT::decode($token, new Key('your-secret-key', 'HS256'));

            // Find user from token data
            $user = User::findOne($decoded->id);

            if (!$user) {
                return ['status' => 'error', 'message' => 'User not found'];
            }

            // Return user info (you can customize this)
            return [
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
            ];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Invalid or expired token'];
        }
    }
}
