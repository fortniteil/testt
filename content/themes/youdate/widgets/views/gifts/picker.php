<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $giftForm \app\forms\GiftForm */
/** @var $giftItems array */
/** @var $pjaxContainer string */
?>
<div class="modal modal-form modal-gift-form modal-md fade" id="send-gift"
     tabindex="-1" role="dialog" aria-labelledby="gift-form-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'gift-form',
                'action' => ['/gift/send'],
                'method' => 'post',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
                'options' => ['data-pjax-container' => $pjaxContainer],
            ]); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="gift-form-title">
                    <?= Yii::t('youdate', 'Send a gift') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form->errorSummary($giftForm) ?>
                <?= Html::activeHiddenInput($giftForm, 'toUserId', ['value' => $user->id]) ?>
                <div class="form-group">
                    <div class="gift-picker">
                        <?php foreach ($giftItems as $group): ?>
                            <div class="gift-category">
                                <?= Html::encode($group['category']['title']) ?>
                            </div>
                            <div class="gift-items">
                                <?php foreach ($group['items'] as $item): ?>
                                    <label class="gift-item text-center" rel="tooltip" title="<?= Html::encode($item['title']) ?>">
                                        <input type="radio" name="<?= Html::getInputName($giftForm, 'giftItemId') ?>" value="<?= $item['id'] ?>" class="gift-input">
                                        <span class="gift" style="background-image: url('<?= $item['url'] ?>')" ></span>
                                        <span class="gift-price badge badge-<?= $item['price'] == 0 ? 'success' : 'default' ?>">
                                            <?= Yii::t('youdate', '{n, plural, =0{Free} =1{# credit} other{# credits}}', ['n' => $item['price']]) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?= $form->field($giftForm, 'isPrivate')->checkbox() ?>
                <?= $form->field($giftForm, 'message')->textarea([
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => Yii::t('youdate', 'Enter your message...'),
                ])->hint(Yii::t('youdate', 'Not required'), ['class' => 'text-small text-muted']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= Yii::t('youdate', 'Cancel') ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?= Yii::t('youdate', 'Send gift') ?>
                </button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
