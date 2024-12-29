<?php

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'email:email',
            'first_name',
            'last_name',
            'passport_number',
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
                'buttons' => [
                    'delete' => function($url, User $model) {
                        return Html::a(
                            '<i class="fas fa-trash"></i>',
                            ['delete', 'id' => $model->id],
                            [
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'Delete',
                                'data-confirm' => 'Are you sure you want to delete this user?'
                            ]
                        );
                    },
                    'view' => function($url, User $model) {
                        return Html::a(
                            '<i class="fas fa-eye"></i>',
                            ['view', 'id' => $model->id],
                            [
                                'class' => 'btn btn-primary btn-sm',
                                'title' => 'View'
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>