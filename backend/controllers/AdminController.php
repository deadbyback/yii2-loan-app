<?php

declare(strict_types=1);

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

abstract class AdminController extends Controller
{
    public function init(): void
    {
        parent::init();
        if (!Yii::$app->user->can('manageLoanApplications')) {
            //throw new ForbiddenHttpException('Access denied');
        }
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }
}