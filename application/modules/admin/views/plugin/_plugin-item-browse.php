<?php

use app\helpers\Html;
use rmrevin\yii\fontawesome\FA;

/** @var $model array */
/** @var $installedPlugins \app\plugins\Plugin[] */
/** @var $enabledPlugins \app\plugins\Plugin[] */

$pluginInfo = new \app\plugins\PluginInfo(['attributes' => $model]);
$gotUpdate = false;
if (isset($enabledPlugins[$pluginInfo->alias])) {
    if (version_compare($enabledPlugins[$pluginInfo->alias]->getVersion(), $pluginInfo->version) == -1) {
        $gotUpdate = true;
    }
}
?>
<div class="col-md-4">
    <div class="box box-default box-plugin">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($pluginInfo->title) ?></h3>
        </div>
        <div class="box-body no-padding">
            <div class="plugin-content">
                <?= Html::img($pluginInfo->imageFileUrl, ['class' => 'plugin-image']) ?>
                <div class="plugin-description">
                    <?= Html::encode($pluginInfo->description) ?>
                </div>
            </div>
            <div class="plugin-author">
                <strong><?= Html::encode($pluginInfo->author) ?></strong>
                <div class="plugin-url">
                    <?= Html::a(Html::encode($pluginInfo->website), $pluginInfo->website) ?>
                </div>
                <div class="plugin-version">
                    <span class="label label-<?= $gotUpdate ? 'success' : 'default' ?>">
                        <?= Html::encode($pluginInfo->version) ?>
                    </span>
                </div>
            </div>
            <div class="plugin-actions">
                <?php if (isset($installedPlugins[$pluginInfo->alias])): ?>

                    <?php if (isset($enabledPlugins[$pluginInfo->alias])): ?>
                        <?= Html::a(FA::icon('ban') . 'Disable', ['disable', 'pluginId' => $pluginInfo->alias], [
                            'class' => 'btn btn-warning btn-slow-action',
                            'data-method' => 'post'
                        ]) ?>
                        <?php if ($enabledPlugins[$pluginInfo->alias] instanceof \app\settings\HasSettings): ?>
                            <?= Html::a(FA::icon('cog') . 'Settings', ['settings', 'pluginId' => $pluginInfo->alias], [
                                'class' => 'btn btn-primary',
                            ]) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?= Html::a(FA::icon('check') . 'Enable', ['enable', 'pluginId' => $pluginInfo->alias], [
                            'class' => 'btn btn-primary btn-slow-action',
                            'data-method' => 'post'
                        ]) ?>
                    <?php endif; ?>

                    <!-- Update button -->
                    <?php if ($gotUpdate): ?>
                        <?= Html::a(FA::icon('refresh') . 'Update', ['update', 'pluginId' => $pluginInfo->alias], [
                            'class' => 'btn btn-success btn-slow-action',
                            'data-method' => 'post'
                        ]) ?>
                    <?php endif; ?>

                    <!-- Uninstall button -->
                    <?= Html::a(FA::icon('trash') . 'Uninstall', ['uninstall', 'pluginId' => $pluginInfo->alias], [
                        'class' => 'btn btn-danger btn-slow-action pull-right',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'Are you sure you want to uninstall this plugin?'),
                    ]) ?>

                <?php else: ?>

                    <!-- Install button -->
                    <?= Html::a(FA::icon('download') . 'Install', ['install', 'pluginId' => $pluginInfo->alias], [
                        'class' => 'btn btn-default btn-slow-action',
                        'data-method' => 'post'
                    ]) ?>

                <?php endif ?>
            </div>
        </div>
    </div>
</div>
