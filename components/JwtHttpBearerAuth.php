<?php
namespace app\components;

use yii\filters\auth\AuthMethod;
use Yii;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHttpBearerAuth extends AuthMethod
{
    public $header = 'Authorization';
    public $tokenParam = 'token';
    public $jwtSecret = 'your-secret-key'; // replace with secure key

    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get($this->header);
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
                return $user::findIdentity($decoded->id);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
