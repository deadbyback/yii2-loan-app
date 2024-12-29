<?php

namespace console\controllers;

use common\services\LoanService;
use yii\console\Controller;
use yii\console\ExitCode;

class LoanController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly LoanService $loanService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Clear debt for random user with approved loan
     */
    public function actionClearRandomDebt(): int
    {
        try {
            $this->loanService->clearRandomUserDebt();
            $this->stdout("Random user debt has been cleared successfully.\n");
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error while clearing debt: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}