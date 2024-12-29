<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\LoanApplication;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

final class LoanApplicationRepository
{
    public function findById(int $id): ?LoanApplication
    {
        return LoanApplication::findOne($id);
    }

    public function save(LoanApplication $loan): bool
    {
        return $loan->save();
    }

    public function getById(int $id): LoanApplication
    {
        $loan = $this->findById($id);

        if ($loan === null) {
            throw new NotFoundHttpException('Loan application not found');
        }

        return $loan;
    }

    public function findRandomApprovedLoan(): ?LoanApplication
    {
        return LoanApplication::find()
            ->where(['status' => LoanApplication::STATUS_APPROVED])
            ->orderBy(new Expression('RANDOM()'))
            ->one();
    }

    public function findByUserId(int $userId): array
    {
        return LoanApplication::find()
            ->where(['user_id' => $userId])
            ->all();
    }

    public function deleteById(int $id): bool
    {
        $loan = $this->findById($id);
        if ($loan === null) {
            throw new NotFoundHttpException('Loan application not found');
        }

        $deletedCount = $loan->delete();

        return is_int($deletedCount) && $deletedCount > 0;
    }
}