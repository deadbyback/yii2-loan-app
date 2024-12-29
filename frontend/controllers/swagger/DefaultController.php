<?php

namespace frontend\controllers\swagger;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('/swagger/default/index');
    }
}