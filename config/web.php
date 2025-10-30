<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    //adds this to allow fetching in the frontend
//it starts here
    'bootstrap' => ['log'],
    'as cors' => [
    'class' => \yii\filters\Cors::class,
    'cors' => [
        'Origin' => ['http://localhost:3000'], // React app URL
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'Access-Control-Allow-Credentials' => true,
        'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type'],
    ],
],



    ///this ends here
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'W4AwfBKAHZGHceS1bF-QvK6wlTmLX9B9',
            'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        //added this 

       'urlManager' => [
    'enablePrettyUrl' => true,
    'enableStrictParsing' => false,
    'showScriptName' => false,
    'rules' => [
        // ==========================
        // AUTHENTICATION ROUTES
        // ==========================
        'POST auth/signup' => 'auth/signup',
        'POST auth/login' => 'auth/login',
        'GET auth/verify' => 'auth/verify',

        // ==========================
        // BOOKS API ROUTES (REST)
        // ==========================
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['book'],
            'pluralize' => true,  // /books instead of /book
        ],

        // ==========================
        // ABOUT PAGE (WEB)
        // ==========================
        'GET about' => 'site/about',
    ],
],

// ],removes this one 
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
