<?php

declare(strict_types=1);

namespace frontend\controllers;

use OpenApi\Annotations as OA;
use Yii;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Loan Application System API"
 * )
 */
abstract class ApiController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options', 'login', 'signup'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'except' => ['options', 'login', 'signup'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => Response::FORMAT_JSON,
        ];

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    protected function checkPermission(string $permissionName): void
    {
        if (!Yii::$app->user->can($permissionName)) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }
    }

    public function actions(): array
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }
}