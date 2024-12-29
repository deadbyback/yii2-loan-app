<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var User $model */
/** @var array $loans */
/** @var array $documents */

$this->title = "User: {$model->email}";
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => 'Are you sure you want to delete this user?',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'email:email',
            'first_name',
            'last_name',
            'date_of_birth:date',
            'passport_number',
            'passport_expiry_date:date',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <h2>Loan Applications</h2>
    <?php if ($loans): ?>
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels' => $loans,
                'pagination' => false,
            ]),
            'columns' => [
                'id',
                [
                    'attribute' => 'amount',
                    'value' => function($model) {
                        return Yii::$app->formatter->asCurrency($model->amount);
                    },
                ],
                'term_months',
                [
                    'attribute' => 'monthly_payment',
                    'value' => function($model) {
                        return Yii::$app->formatter->asCurrency($model->monthly_payment);
                    },
                ],
                'status',
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function($url, $model) {
                            return Html::a(
                                '<i class="fas fa-eye"></i>',
                                ['loan/view', 'id' => $model->id],
                                ['class' => 'btn btn-primary btn-sm']
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    <?php else: ?>
        <p class="text-muted">No loan applications found.</p>
    <?php endif; ?>

    <h2>Documents</h2>
    <?php if ($documents): ?>
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels' => $documents,
                'pagination' => false,
            ]),
            'columns' => [
                'id',
                'type',
                'original_name',
                'mime_type',
                [
                    'attribute' => 'size',
                    'value' => function($model) {
                        return Yii::$app->formatter->asShortSize($model->size);
                    },
                ],
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{download}',
                    'buttons' => [
                        'download' => function($url, $model) {
                            return Html::a(
                                '<i class="fas fa-download"></i>',
                                ['document/download', 'id' => $model->id],
                                ['class' => 'btn btn-primary btn-sm']
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    <?php else: ?>
        <p class="text-muted">No documents found.</p>
    <?php endif; ?>
</div>