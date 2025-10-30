<?php

namespace app\controllers;

use yii\rest\Controller;
use Yii;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    // Secret key for signing JWTs (move this to a secure location in production)
    private $jwtSecret = 'your-secret-key';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Return all responses as JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;

        // Disable CSRF for API requests
        $this->enableCsrfValidation = false;

        return $behaviors;
    }

    // ==========================
    // USER SIGNUP (Registration)
    // ==========================
    // Endpoint: POST /auth/signup
    public function actionSignup()
    {
        $request = Yii::$app->request;

        $model = new User();
        $model->username = $request->post('username');
        $model->password_hash = $request->post('password'); // Will be hashed in beforeSave()

        if ($model->save()) {
            // Create JWT payload
            $payload = [
                'id' => $model->id,
                'username' => $model->username,
                'iat' => time(),
                'exp' => time() + 3600, // 1 hour
            ];

            // Encode token
            $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

            return [
                'status' => 'success',
                'message' => 'User registered successfully!',
                'token' => $token,
                'user_id' => $model->id,
            ];
        }

        return [
            'status' => 'error',
            'errors' => $model->errors,
        ];
    }

    // ==========================
    // USER LOGIN
    // ==========================
    // Endpoint: POST /auth/login
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        $user = User::findByUsername($username);

        if ($user && $user->validatePassword($password)) {
            // Create JWT payload
            $payload = [
                'id' => $user->id,
                'username' => $user->username,
                'iat' => time(),
                'exp' => time() + 3600, // 1 hour
            ];

            $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

            return [
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Invalid username or password',
        ];
    }

    // ==========================
    // VERIFY TOKEN
    // ==========================
    // Endpoint: GET /auth/verify
    public function actionVerify()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if (!$authHeader) {
            return ['status' => 'error', 'message' => 'Missing Authorization header'];
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            return [
                'status' => 'success',
                'data' => $decoded,
            ];
        } catch (\Firebase\JWT\ExpiredException $e) {
            return [
                'status' => 'error',
                'message' => 'Token has expired',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Invalid token',
                'details' => $e->getMessage(),
            ];
        }
    }
}
