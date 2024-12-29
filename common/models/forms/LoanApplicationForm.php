<?php

declare(strict_types=1);

namespace common\models\forms;

use common\models\LoanApplication;
use yii\base\Model;

class LoanApplicationForm extends Model
{
    public ?float $amount = null;
    public ?int $term_months = null;
    public ?string $purpose = null;
    public ?float $monthly_income = null;

    public function rules(): array
    {
        return [
            [['amount', 'term_months', 'purpose', 'monthly_income'], 'required'],
            [['amount', 'monthly_income'], 'number', 'min' => 0],
            ['term_months', 'integer', 'min' => 6, 'max' => 60],
            ['purpose', 'string'],
        ];
    }

    public function createLoanApplication(int $userId): ?LoanApplication
    {
        if (!$this->validate()) {
            return null;
        }

        $loan = new LoanApplication();
        $loan->user_id = $userId;
        $loan->amount = $this->amount;
        $loan->term_months = $this->term_months;
        $loan->purpose = $this->purpose;
        $loan->monthly_income = $this->monthly_income;
        $loan->status = LoanApplication::STATUS_PENDING;
        $loan->monthly_payment = $this->calculateMonthlyPayment();

        return $loan->save() ? $loan : null;
    }

    private function calculateMonthlyPayment(): float
    {
        return $this->amount / $this->term_months;
    }
}