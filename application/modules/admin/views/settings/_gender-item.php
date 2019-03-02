<?php

use app\helpers\Html;

/** @var $i int */
/** @var $model \app\models\Sex */
/** @var $form \yii\widgets\ActiveForm; */
?>
<div class="item">
    <?php if (!$model->isNewRecord): ?>
        <?= Html::activeHiddenInput($model, "[{$i}]id") ?>
    <?php endif; ?>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, "[{$i}]alias")->textInput() ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, "[{$i}]title")->textInput() ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, "[{$i}]title_plural")->textInput() ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, "[{$i}]icon")->textInput() ?>
        </div>
        <div class="col-sm-2">
            <div class="form-group text-center">
                <div class="control-label">&nbsp;</div>
                <button type="button" class="add-item btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i></button>
                <button type="button" class="remove-item btn btn-danger btn-sm"><i class="glyphicon glyphicon-minus"></i></button>
            </div>
        </div>
    </div>
</div>
