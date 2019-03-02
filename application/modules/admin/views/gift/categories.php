<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\models\GiftCategory;
use dosamigos\grid\columns\ToggleColumn;
use yii2mod\editable\EditableColumn;

/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Gift categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nav-tabs-custom">
    <div class="tab-content no-padding table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table table-vcenter'],
            'columns' => [
                [
                    'class' => EditableColumn::class,
                    'attribute' => 'title',
                    'url' => ['editable-category'],
                ],
                [
                    'class' => EditableColumn::class,
                    'attribute' => 'language_category',
                    'url' => ['editable-category'],
                ],
                [
                    'class' => ToggleColumn::class,
                    'attribute' => 'is_visible',
                    'url' => ['toggle-category'],
                    'onLabel' => Yii::t('app', 'Visible'),
                    'offLabel' => Yii::t('app', 'Hidden'),
                    'onIcon' => 'fa fa-check-square-o',
                    'offIcon' => 'fa fa-square-o',
                    'contentOptions' => ['class' => 'text-center'],
                    'filter' => [
                        GiftCategory::VISIBLE => Yii::t('app', 'Visible'),
                        GiftCategory::HIDDEN => Yii::t('app', 'Hidden'),
                    ]
                ],
                [
                    'attribute' => 'directory',
                    'format' => 'raw',
                    'value' => function (GiftCategory $category) {
                        return Html::tag('span', $category->directory, ['class' => 'label label-primary']);
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-category} {delete-category}',
                    'contentOptions' => ['class' => 'text-right'],
                    'buttons' => [
                        'update-category' => function ($url, $model, $key) {
                            return Html::a('<span class="fa fa-pencil"></span>', $url, [
                                'title' => Yii::t('app', 'Update'),
                                'data-pjax' => 0,
                                'class' => 'btn btn-sm btn-primary',
                            ]);
                        },
                        'delete-category' => function ($url, $model, $key) {
                            return Html::a('<span class="fa fa-trash"></span>', $url, [
                                'title' => Yii::t('app', 'Delete'),
                                'data-pjax' => 0,
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('app', 'Are you sure want to delete this category?'),
                                'class' => 'btn btn-sm btn-danger',
                            ]);
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
