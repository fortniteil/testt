<?php

use app\helpers\Html;
use app\models\GiftItem;
use dosamigos\grid\columns\ToggleColumn;
use yii2mod\editable\EditableColumn;
use youdate\widgets\GridView;
use youdate\widgets\ActiveForm;

/** @var $giftCategory \app\models\GiftCategory */
/** @var $this \app\base\View */
/** @var $itemsProvider \yii\data\ActiveDataProvider */
/** @var $giftItemsUploadForm \app\modules\admin\forms\GiftItemsUploadForm */

$this->title = Yii::t('app', 'Update gift category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Gift categories'), 'url' => ['categories']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $this->beginBlock('gift-actions') ?>
<?= Html::a('<i class="fa fa-search"></i> ' . Yii::t('app', 'Scan Current'),
    ['scan-category', 'id' => $giftCategory->id], ['class' => 'btn btn-primary btn-sm']) ?>
<?php $this->endBlock() ?>

<div class="row">
    <div class="col-sm-4">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Category info') ?></h3>
            </div>
            <div class="box-body">
                <?php $categoryForm = ActiveForm::begin(); ?>

                <?= $categoryForm->field($giftCategory, 'is_visible')->checkbox() ?>
                <?= $categoryForm->field($giftCategory, 'title')->textInput(['maxlength' => 255]) ?>
                <?= $categoryForm->field($giftCategory, 'language_category')
                    ->textInput(['maxlength' => 64])
                    ->hint(Yii::t('app', 'Default is {0}', 'app')) ?>

                <div class="form-group">
                    <?= Html::submitButton($giftCategory->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
                        ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Upload more') ?></h3>
            </div>
            <div class="box-body">

                <div class="alert alert-info">
                    <?= Yii::t('app', 'Supported extensions: {0}', 'png, svg') ?>
                    <br>
                    <?= Yii::t('app', 'Preferred size: {0}', '128x128') ?>
                </div>

                <?php $uploadForm = ActiveForm::begin([
                    'action' => ['upload-items', 'id' => $giftCategory->id],
                    'options' => ['enctype' => 'multipart/form-data']
                ]) ?>

                <?= $uploadForm->field($giftItemsUploadForm, 'files[]')
                    ->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Upload'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Gift items') ?></h3>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $itemsProvider,
                    'layout' => "{summary}\n{items}\n{pager}",
                    'tableOptions' => ['class' => 'table table-vcenter gift-items'],
                    'columns' => [
                        [
                            'format' => 'raw',
                            'contentOptions' => ['width' => 70],
                            'value' => function (GiftItem $item) {
                                return Html::img($item->getUrl(), ['class' => 'gift-image']);
                            },
                        ],
                        [
                            'class' => EditableColumn::class,
                            'attribute' => 'title',
                            'url' => ['editable-item'],
                        ],
                        [
                            'class' => EditableColumn::class,
                            'attribute' => 'language_category',
                            'url' => ['editable-item'],
                        ],
                        [
                            'class' => EditableColumn::class,
                            'attribute' => 'price',
                            'url' => ['editable-item'],
                        ],
                        [
                            'class' => ToggleColumn::class,
                            'attribute' => 'is_visible',
                            'url' => ['toggle-item'],
                            'onLabel' => Yii::t('app', 'Visible'),
                            'offLabel' => Yii::t('app', 'Hidden'),
                            'onIcon' => 'fa fa-check-square-o',
                            'offIcon' => 'fa fa-square-o',
                            'contentOptions' => ['class' => 'text-center'],
                            'filter' => [
                                GiftItem::VISIBLE => Yii::t('app', 'Visible'),
                                GiftItem::HIDDEN => Yii::t('app', 'Hidden'),
                            ]
                        ],
                        [
                            'attribute' => 'file',
                            'format' => 'raw',
                            'value' => function (GiftItem $item) {
                                return Html::tag('span', $item->file, ['class' => 'label label-primary']);
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete-item}',
                            'contentOptions' => ['class' => 'text-right'],
                            'buttons' => [
                                'delete-item' => function ($url, $model, $key) {
                                    return Html::a('<span class="fa fa-trash"></span>', $url, [
                                        'title' => Yii::t('app', 'Delete'),
                                        'data-pjax' => 0,
                                        'data-method' => 'post',
                                        'data-confirm' => Yii::t('app', 'Are you sure want to delete this gift item?'),
                                        'class' => 'btn btn-sm btn-danger',
                                    ]);
                                },
                            ]
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
