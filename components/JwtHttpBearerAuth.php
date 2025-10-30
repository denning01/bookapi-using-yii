<?php
namespace app\components;

use yii\filters\auth\HttpBearerAuth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\models\User;
use Yii;

class JwtHttpBearerAuth extends HttpBearerAuth
{
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\\s+(.*?)$/', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                $decoded = JWT::decode($token, new Key('your-secret-key', 'HS256'));
                return User::findOne($decoded->id);
            } catch (\Exception $e) {
                Yii::warning('Invalid token: ' . $e->getMessage(), __METHOD__);
                return null;
            }
        }

        return null;
    }
}

