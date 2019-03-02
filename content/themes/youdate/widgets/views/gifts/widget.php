<?php

use app\helpers\Html;
use youdate\widgets\EmptyState;

/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $gifts \app\models\Gift[] */
/** @var $isCurrentUser bool */
/** @var $currentUserId int */
?>
<?php \yii\widgets\Pjax::begin(['id' => 'pjax-user-gifts']) ?>

<div class="card card-gifts">
    <div class="card-body">
        <h5 class=""><?= Yii::t('youdate', 'Gifts') ?> &mdash;
            <span class="text-muted">
                <?= Html::a(Yii::t('youdate', 'see all'), '#', [
                    'data-toggle' => 'modal',
                    'data-target' => '#user-gifts',
                ]) ?>
            </span>
        </h5>
        <div class="gifts row mt-4">
            <?php foreach (array_slice($gifts, 0, 6) as $gift): ?>
                <div class="gift col-4">
                    <img src="<?= $gift->giftItem->getUrl() ?>" alt="<?= Html::encode($gift->giftItem->getTitle()) ?>"
                    rel="tooltip">
                    <div class="sender">
                        <?php if ($isCurrentUser == true || $gift->is_private == 0 || $gift->from_user_id == $currentUserId): ?>
                            <?= Yii::t('youdate', 'From {0}', Html::encode($gift->fromUser->profile->getDisplayName())) ?>
                        <?php else: ?>
                            <?= Yii::t('youdate', 'Private') ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($gifts) == 0): ?>
            <div class="text-muted text-center py-2">
                <?= Yii::t('youdate', 'No gifts yet') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal modal-user-gifts fade" id="user-gifts"
     tabindex="-1" role="dialog" aria-labelledby="users-gifts-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="user-gifts-title">
                    <?= Yii::t('youdate', 'User gifts') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="gifts row">
                    <?php foreach ($gifts as $gift): ?>
                        <div class="gift col-12 d-flex flex-row">
                            <img src="<?= $gift->giftItem->getUrl() ?>" alt="<?= Html::encode($gift->giftItem->getTitle()) ?>"
                                 rel="tooltip">
                            <div class="gift-info d-flex flex-column justify-content-center">
                                <?php if ($isCurrentUser == true || $gift->is_private == 0 || $gift->from_user_id == $currentUserId): ?>
                                    <div class="sender">
                                        <?= Yii::t('youdate', 'Gift from {0}', Html::a(Html::encode($gift->fromUser->profile->getDisplayName()), [
                                            '/profile/view', 'username' => $gift->fromUser->username,
                                        ], ['data-pjax' => '0'])) ?>
                                    </div>
                                    <?php if ($gift->message): ?>
                                        <div class="message">
                                            <?= Html::encode($gift->getMessage()) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="sender sender-private">
                                        <?= Yii::t('youdate', 'Private') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($gifts) == 0): ?>
                    <?= EmptyState::widget([
                        'icon' => 'fa fa-gift',
                        'title' => Yii::t('youdate', 'No gifts'),
                        'subTitle' => Yii::t('youdate', '{0} doesn\'t have any gifts yet', $profile->getDisplayName()),
                    ]) ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= Yii::t('youdate', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php \yii\widgets\Pjax::end() ?>
