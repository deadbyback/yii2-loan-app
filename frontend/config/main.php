<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'app-loan-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'swagger' => [
            'class' => '\yii\swagger\Module',
            'scanDir' => [
                '@frontend/controllers',
                '@frontend/web/swagger-docs',
            ],
            'cache' => 'cache',
            'generatePath' => '@frontend/web/swagger-docs',
            'openApiPath' => '@frontend/web/swagger-docs/openapi.json',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'swagger' => 'swagger/default/index',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['loan', 'document'],
                    'extraPatterns' => [
                        'POST {id}/status' => 'update-status',
                    ],
                ],
            ],
        ],
    ],
    'as authenticator' => [
        'class' => 'sizeg\jwt\JwtHttpBearerAuth',
        'except' => [
            'auth/login',
            'auth/signup',
            'site/index',
            'swagger/*'
        ],
    ],
    'params' => $params,
];
