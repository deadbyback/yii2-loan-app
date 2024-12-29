<?php

declare(strict_types=1);

namespace backend\controllers;

use common\services\UserService;
use yii\data\ActiveDataProvider;
use common\models\User;
use Yii;
use yii\web\Response;

final class UserController extends AdminController
{
    public function __construct(
        $id,
        $module,
        private readonly UserService $userService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentUser' => Yii::$app->user->identity,
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->userService->getUserById($id),
            'loans' => $this->userService->getUserLoans($id),
            'documents' => $this->userService->getUserDocuments($id),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->userService->deleteUser($id);
            Yii::$app->session->setFlash('success', 'User has been deleted successfully.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error deleting user: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}