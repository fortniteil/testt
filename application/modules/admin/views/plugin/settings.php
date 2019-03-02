<?php

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $plugin \app\plugins\Plugin */

use app\settings\SettingsForm;

$this->title = $plugin->getTitle();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plugins'), 'url' => ['index']];

echo SettingsForm::widget([
    'manager' => $settingsManager,
    'model' => $settingsModel,
    'formView' => '@app/modules/admin/views/partials/settings_form',
    'title' => $title,
]);
