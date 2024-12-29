<?php

declare(strict_types=1);

namespace common\services;

use common\models\LoanApplication;
use common\repositories\LoanApplicationRepository;
use common\repositories\UserRepository;
use RuntimeException;
use yii\web\NotFoundHttpException;

final class LoanService
{
    public function __construct(
        private readonly LoanApplicationRepository $loanRepository,
        private readonly UserRepository $userRepository
    ) {}

    public function getLoanById(int $id): LoanApplication
    {
        $loan = $this->loanRepository->findById($id);
        if (!$loan) {
            throw new NotFoundHttpException('Loan application not found');
        }

        return $loan;
    }

    public function createLoanApplication(array $data, int $userId): LoanApplication
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new RuntimeException('User not found');
        }

        $loan = new LoanApplication();
        $loan->setAttributes($data);
        $loan->user_id = $userId;
        $loan->status = LoanApplication::STATUS_PENDING;
        $loan->monthly_payment = $this->calculateMonthlyPayment($data['amount'], $data['term_months']);

        if (!$this->loanRepository->save($loan)) {
            throw new RuntimeException('Failed to create loan application');
        }

        return $loan;
    }

    public function updateLoanStatus(int $loanId, string $status): LoanApplication
    {
        $loan = $this->loanRepository->findById($loanId);
        if (!$loan) {
            throw new RuntimeException('Loan application not found');
        }

        $loan->status = $status;

        if (!$this->loanRepository->save($loan)) {
            throw new RuntimeException('Failed to update loan status');
        }

        return $loan;
    }

    public function clearRandomUserDebt(): void
    {
        $randomLoan = $this->loanRepository->findRandomApprovedLoan();
        if (!$randomLoan) {
            return;
        }

        $randomLoan->status = LoanApplication::STATUS_FINISHED;
        $randomLoan->amount = 0;
        $randomLoan->monthly_payment = 0;

        if (!$this->loanRepository->save($randomLoan)) {
            throw new RuntimeException('Failed to clear user debt');
        }
    }

    private function calculateMonthlyPayment(float $amount, int $termMonths): float
    {
        // Simplified calculation, in real project should include interest rate
        return $amount / $termMonths;
    }

    public function getLoansByUserId(int|string|null $userId): array
    {
        return $this->loanRepository->findByUserId($userId);
    }

    public function deleteLoan(int $id): bool
    {
        return $this->loanRepository->deleteById($id);
    }
}