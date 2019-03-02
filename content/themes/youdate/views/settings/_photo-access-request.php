<?php

use app\helpers\Html;
use app\helpers\Url;
use app\models\PhotoAccess;

/** @var $model \app\models\PhotoAccess */

?>
<div class="row d-flex align-items-center py-2">
    <div class="col d-flex align-items-center">
        <div class="photo">
            <div class="avatar avatar-md" style="background-image: url('<?= $model->fromUser->profile->getAvatarUrl() ?>')"></div>
        </div>
        <div class="info px-2">
            <div class="text-bolder">
                <?= Html::a($model->fromUser->profile->getDisplayName(), ['/profile/view', 'username' => $model->fromUser->username], [
                    'class' => 'text-dark',
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col d-flex justify-content-end">
        <?= Html::button('<i class="fe fe-eye"></i> ' . Yii::t('youdate', 'Approve'), [
            'class' => 'btn btn-ajax mx-1 ' . ($model->status == PhotoAccess::STATUS_APPROVED ? 'btn-success' : 'btn-outline-success'),
            'data-action' => Url::to(['/settings/photo-access-action', 'fromUserId' => $model->fromUser->id, 'action' => PhotoAccess::STATUS_APPROVED]),
            'data-type' => 'post',
            'data-pjax-container' => '#pjax-settings-private-photos',
        ]) ?>
        <?= Html::button('<i class="fe fe-eye-off"></i> ' . Yii::t('youdate', 'Reject'), [
            'class' => 'btn btn-ajax mx-1 ' . ($model->status == PhotoAccess::STATUS_REJECTED ? 'btn-danger' : 'btn-outline-danger'),
            'data-action' => Url::to(['/settings/photo-access-action', 'fromUserId' => $model->fromUser->id, 'action' => PhotoAccess::STATUS_REJECTED]),
            'data-type' => 'post',
            'data-pjax-container' => '#pjax-settings-private-photos',
        ]) ?>
    </div>
</div>
