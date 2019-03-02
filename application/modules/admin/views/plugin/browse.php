<?php

use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $pluginDataProvider \yii\data\ArrayDataProvider */
/** @var $searchQuery string */
/** @var $installedPlugins \app\plugins\Plugin[] */
/** @var $enabledPlugins \app\plugins\Plugin[] */
/** @var $zipExtensionLoaded bool */

$this->title = Yii::t('app', 'Browse plugins');
$this->params['breadcrumbs'][] = ['label' => 'Browse plugins', 'url' => ['browse']];
?>
<?php $this->beginBlock('plugins-actions') ?>

<div class="box-actions-search">
    <?= Html::beginForm(['browse'], 'get') ?>
    <?= Html::input('text', 'searchQuery', $searchQuery, [
        'class' => 'search-query form-control',
        'placeholder' => Yii::t('app', 'Search'),
    ]) ?>
    <?= Html::submitButton(\rmrevin\yii\fontawesome\FA::icon('search'), ['class' => 'btn btn-primary btn-sm']) ?>
    <?= Html::endForm() ?>
</div>

<?php $this->endBlock() ?>

<?php if ($zipExtensionLoaded == false): ?>
<div class="alert alert-warning">
    <?= Yii::t('app', 'Warning: {0} is not enabled/installed', Html::a('PHP Zip extension', 'http://php.net/manual/en/book.zip.php')) ?>
</div>
<?php endif; ?>

<div class="row">
    <?= \youdate\widgets\ListView::widget([
        'dataProvider' => $pluginDataProvider,
        'itemView' => '_plugin-item-browse',
        'viewParams' => ['installedPlugins' => $installedPlugins, 'enabledPlugins' => $enabledPlugins],
        'layout' => '{items} {pager}',
        'emptyView' => '_empty',
    ]) ?>
</div>
