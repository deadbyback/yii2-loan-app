<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\LoanApplication;
use common\services\LoanService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

final class LoanController extends AdminController
{
    public function __construct(
        $id,
        $module,
        private readonly LoanService $loanService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => LoanApplication::find()
                ->with('user')
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $loan = $this->loanService->getLoanById($id);

        return $this->render('view', [
            'model' => $loan,
        ]);
    }

    public function actionApprove(int $id): \yii\web\Response
    {
        $loan = $this->loanService->updateLoanStatus($id, LoanApplication::STATUS_APPROVED);
        Yii::$app->session->setFlash('success', "Loan application #{$loan->id} has been approved.");

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReject(int $id): \yii\web\Response
    {
        $loan = $this->loanService->updateLoanStatus($id, LoanApplication::STATUS_REJECTED);
        Yii::$app->session->setFlash('success', "Loan application #{$loan->id} has been rejected.");

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        try {
            $this->loanService->deleteLoan($id);
            Yii::$app->session->setFlash('success', 'Loan application has been deleted.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error deleting loan application.');
        }

        return $this->redirect(['index']);
    }
}