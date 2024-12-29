<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\forms\LoanApplicationForm;
use common\models\LoanApplication;
use common\services\LoanService;
use OpenApi\Annotations as OA;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * @OA\Tag(
 *     name="Loans",
 *     description="Loan applications management"
 * )
 *
 * @OA\Schema(
 *     schema="LoanApplication",
 *     required={"amount", "term_months", "purpose", "monthly_income"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="term_months", type="integer"),
 *     @OA\Property(property="purpose", type="string"),
 *     @OA\Property(property="monthly_income", type="number", format="float"),
 *     @OA\Property(property="monthly_payment", type="number", format="float"),
 *     @OA\Property(property="status", type="string", enum={"pending", "approved", "rejected"}),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
final class LoanController extends ApiController
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
     * @OA\Post(
     *     path="/loan/create",
     *     summary="Create new loan application",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"amount", "term_months", "purpose", "monthly_income"},
     *             @OA\Property(property="amount", type="number", format="float", example=10000),
     *             @OA\Property(property="term_months", type="integer", minimum=6, maximum=60, example=12),
     *             @OA\Property(property="purpose", type="string", example="Home renovation"),
     *             @OA\Property(property="monthly_income", type="number", format="float", example=5000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Loan application created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="term_months", type="integer"),
     *             @OA\Property(property="purpose", type="string"),
     *             @OA\Property(property="monthly_income", type="number", format="float"),
     *             @OA\Property(property="monthly_payment", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"pending"}),
     *             @OA\Property(property="created_at", type="string", format="datetime")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function actionCreate(): array
    {
        $this->checkPermission('createLoan');

        $form = new LoanApplicationForm();
        if (!$form->load(Yii::$app->request->post(), '') || !$form->validate()) {
            throw new BadRequestHttpException('Invalid loan application data');
        }

        return $this->loanService->createLoanApplication(
            $form->getAttributes(),
            Yii::$app->user->getId()
        )->getAttributes();
    }

    /**
     * @OA\Get(
     *     path="/loan/list",
     *     summary="Get user's loan applications",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of loan applications",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/LoanApplication")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function actionList(): array
    {
        $this->checkPermission('viewOwnLoans');

        return array_map(
            fn($loan) => $loan->getAttributes(),
            $this->loanService->getLoansByUserId(Yii::$app->user->getId())
        );
    }

    /**
     * @OA\Get(
     *     path="/loan/{id}",
     *     summary="Get loan application details",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Loan application details",
     *         @OA\JsonContent(ref="#/components/schemas/LoanApplication")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function actionView(int $id): array
    {
        $this->checkPermission('viewOwnLoans');

        $loan = $this->loanService->getLoanById($id);
        if ($loan->user_id !== Yii::$app->user->getId() && !Yii::$app->user->can('manageLoanApplications')) {
            throw new ForbiddenHttpException('You can only view your own loans');
        }

        return $loan->getAttributes();
    }

    /**
     * @OA\Post(
     *     path="/loan/{id}/status",
     *     summary="Update loan application status",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending", "approved", "rejected"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="status", type="string", enum={"pending", "approved", "rejected"}),
     *             @OA\Property(property="updated_at", type="string", format="datetime")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function actionUpdateStatus(int $id): array
    {
        $this->checkPermission('manageLoanApplications');

        $status = Yii::$app->request->post('status');
        if (!in_array($status, [
            LoanApplication::STATUS_PENDING,
            LoanApplication::STATUS_APPROVED,
            LoanApplication::STATUS_REJECTED
        ])) {
            throw new BadRequestHttpException('Invalid status');
        }

        $loan = $this->loanService->updateLoanStatus($id, $status);

        return [
            'id' => $loan->id,
            'status' => $loan->status,
            'updated_at' => $loan->updated_at,
        ];
    }
}