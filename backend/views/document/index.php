<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Documents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    return Html::a(
                        $model->user->email,
                        ['user/view', 'id' => $model->user_id],
                        ['data-pjax' => 0]
                    );
                },
                'format' => 'raw',
            ],
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
                'template' => '{download} {delete}',
                'buttons' => [
                    'download' => function($url, $model) {
                        return Html::a(
                            '<i class="fas fa-download"></i>',
                            ['download', 'id' => $model->id],
                            [
                                'class' => 'btn btn-primary btn-sm',
                                'title' => 'Download',
                            ]
                        );
                    },
                    'delete' => function($url, $model) {
                        return Html::a(
                            '<i class="fas fa-trash"></i>',
                            ['delete', 'id' => $model->id],
                            [
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'Delete',
                                'data-confirm' => 'Are you sure you want to delete this document?',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>