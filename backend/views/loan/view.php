<?php

use common\models\LoanApplication;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var LoanApplication $model */

$this->title = "Loan Application #{$model->id}";
$this->params['breadcrumbs'][] = ['label' => 'Loan Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($model->status === LoanApplication::STATUS_PENDING): ?>
            <?= Html::a('Approve', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data-confirm' => 'Are you sure you want to approve this loan?',
            ]) ?>
            <?= Html::a('Reject', ['reject', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data-confirm' => 'Are you sure you want to reject this loan?',
            ]) ?>
        <?php endif; ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => 'Are you sure you want to delete this loan application?',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user_id',
                'value' => function(LoanApplication $model) {
                    return Html::a(
                        $model->user->email,
                        ['user/view', 'id' => $model->user_id],
                        ['data-pjax' => 0]
                    );
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'amount',
                'value' => Yii::$app->formatter->asCurrency($model->amount),
            ],
            'term_months',
            'purpose:ntext',
            [
                'attribute' => 'monthly_income',
                'value' => Yii::$app->formatter->asCurrency($model->monthly_income),
            ],
            [
                'attribute' => 'monthly_payment',
                'value' => Yii::$app->formatter->asCurrency($model->monthly_payment),
            ],
            [
                'attribute' => 'status',
                'value' => Html::tag('span', $model->status, [
                    'class' => 'badge badge-' . match($model->status) {
                            LoanApplication::STATUS_PENDING => 'warning',
                            LoanApplication::STATUS_APPROVED => 'success',
                            LoanApplication::STATUS_REJECTED => 'danger',
                            default => 'secondary',
                        }
                ]),
                'format' => 'raw',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <h2>Applicant Details</h2>
    <?= DetailView::widget([
        'model' => $model->user,
        'attributes' => [
            'email:email',
            'first_name',
            'last_name',
            'date_of_birth:date',
            'passport_number',
            'passport_expiry_date:date',
        ],
    ]) ?>

    <?php if ($documents = $model->user->documents): ?>
        <h2>Documents</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Original Name</th>
                    <th>Size</th>
                    <th>Uploaded</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($documents as $document): ?>
                    <tr>
                        <td><?= Html::encode($document->type) ?></td>
                        <td><?= Html::encode($document->original_name) ?></td>
                        <td><?= Yii::$app->formatter->asShortSize($document->size) ?></td>
                        <td><?= Yii::$app->formatter->asDatetime($document->created_at) ?></td>
                        <td>
                            <?= Html::a('Download', ['document/download', 'id' => $document->id], [
                                'class' => 'btn btn-primary btn-sm',
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>