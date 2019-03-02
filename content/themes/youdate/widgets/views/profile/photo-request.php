<?php

use app\models\PhotoAccess;
use app\helpers\Url;

/** @var $profile \app\models\Profile */
/** @var $requestStatus int */
?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-request-access']) ?>

<div class="text-center pl-4 pr-4 pt-4">
    <?php if ($requestStatus === null): ?>
        <button class="btn btn-secondary btn-block btn-ajax"
                data-action="<?= Url::to(['/profile/request-access', 'username' => $profile->user->username]) ?>"
                data-pjax-container="#pjax-request-access"
                data-type="post">
            <i class="fe fe-eye"></i>
            <?= Yii::t('youdate', 'Request access') ?>
        </button>
    <?php elseif ($requestStatus === PhotoAccess::STATUS_REQUESTED): ?>
        <button class="btn btn-outline-secondary btn-block btn-disabled"
                rel="tooltip"
                title="<?= Yii::t('youdate', 'Private photos access has been requested') ?>"
                disabled="disabled">
            <i class="fe fe-eye"></i>
            <?= Yii::t('youdate', 'Access has been requested') ?>
        </button>
    <?php elseif ($requestStatus === PhotoAccess::STATUS_REJECTED): ?>
        <button class="btn btn-outline-danger btn-block btn-disabled"
                rel="tooltip"
                title="<?= Yii::t('youdate', 'User rejected a request to view private photos') ?>"
                disabled="disabled">
            <i class="fe fe-eye-off"></i>
            <?= Yii::t('youdate', 'Access has been rejected') ?>
        </button>
    <?php endif; ?>
</div>
<?php \yii\widgets\Pjax::end() ?>
