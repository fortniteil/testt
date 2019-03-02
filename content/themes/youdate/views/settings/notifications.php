<?php

use app\settings\SettingsForm;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */

$title = Yii::t('youdate', 'Notifications settings');
$this->title = $title;
?>
<?= SettingsForm::widget([
    'model' => $settingsModel,
    'formView' => '/partials/settings-form',
    'title' => Yii::t('youdate', 'E-mail notifications'),
]) ?>
