<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var $giftCategory \app\models\GiftCategory */
/** @var $this \app\base\View */
/** @var $directories array */

$this->title = Yii::t('app', 'New gift category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Gift categories'), 'url' => ['categories']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-solid">
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($giftCategory, 'title')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($giftCategory, 'language_category')
            ->textInput(['maxlength' => 64])
            ->hint(Yii::t('app', 'Default is {0}', 'app')) ?>
        <?= $form->field($giftCategory, 'directory')->textInput(['maxlength' => 255]) ?>

        <label for="scan">
            <?= Html::checkbox('scan', false, ['id' => 'scan']) ?>
            <?= Yii::t('app', 'Scan directory') ?>
        </label>

        <div class="form-group">
            <?= Html::submitButton($giftCategory->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
                ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
