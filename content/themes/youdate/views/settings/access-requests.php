<?php

use app\helpers\Url;
use app\helpers\Html;
use yii\widgets\ListView;

/** @var $model \app\models\Profile */
/** @var $form \yii\widgets\ActiveForm */
/** @var $this \app\base\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $photoModerationEnabled bool */

$this->title = Yii::t('youdate', 'Private photos access requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-settings-private-photos', 'linkSelector' => false]) ?>
            <?php if ($dataProvider->getTotalCount() > 0): ?>
                <?= \youdate\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items} {pager}',
                    'itemView' => '_photo-access-request',
                ]) ?>
            <?php else: ?>
                <?= \youdate\widgets\EmptyState::widget([
                    'icon' => 'fe fe-image',
                    'subTitle' => Yii::t('youdate', 'You have no requests yet'),
                ]) ?>
            <?php endif; ?>
        <?php \yii\widgets\Pjax::end() ?>
    </div>
</div>
