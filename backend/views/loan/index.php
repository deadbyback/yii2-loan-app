<?php

use common\models\LoanApplication;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Loan Applications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
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
                'value' => function(LoanApplication $model) {
                    return Yii::$app->formatter->asCurrency($model->amount);
                },
            ],
            'term_months',
            [
                'attribute' => 'monthly_payment',
                'value' => function(LoanApplication $model) {
                    return Yii::$app->formatter->asCurrency($model->monthly_payment);
                },
            ],
            [
                'attribute' => 'status',
                'value' => function(LoanApplication $model) {
                    return Html::tag('span', $model->status, [
                        'class' => 'badge badge-' . match($model->status) {
                                LoanApplication::STATUS_PENDING => 'warning',
                                LoanApplication::STATUS_APPROVED => 'success',
                                LoanApplication::STATUS_REJECTED => 'danger',
                                default => 'secondary',
                            }
                    ]);
                },
                'format' => 'raw',
            ],
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {approve} {reject} {delete}',
                'buttons' => [
                    'approve' => function($url, LoanApplication $model) {
                        if ($model->status === LoanApplication::STATUS_PENDING) {
                            return Html::a(
                                '<i class="fas fa-check"></i>',
                                ['approve', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-success btn-sm',
                                    'title' => 'Approve',
                                    'data-confirm' => 'Are you sure you want to approve this loan?',
                                ]
                            );
                        }
                        return '';
                    },
                    'reject' => function($url, LoanApplication $model) {
                        if ($model->status === LoanApplication::STATUS_PENDING) {
                            return Html::a(
                                '<i class="fas fa-times"></i>',
                                ['reject', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Reject',
                                    'data-confirm' => 'Are you sure you want to reject this loan?',
                                ]
                            );
                        }
                        return '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>