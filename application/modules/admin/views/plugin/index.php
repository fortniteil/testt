<?php

use app\plugins\Plugin;

/* @var $this \yii\web\View */
/* @var $plugins Plugin[] */

$this->title = Yii::t('app', 'Installed plugins');
$this->params['breadcrumbs'][] = ['label' => 'Plugins', 'url' => ['installed']];
?>
<?php if (count($plugins)): ?>
    <div class="row">
        <div class="list-view">
            <?php foreach ($plugins as $pluginId => $plugin): ?>
                <?= $this->render('_plugin-item-installed', [
                    'pluginId' => $pluginId,
                    'plugin' => $plugin,
                ]) ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="box box-solid">
        <div class="box-body">
            <?= Yii::t('app', 'No plugins found.') ?>
        </div>
    </div>
<?php endif; ?>
