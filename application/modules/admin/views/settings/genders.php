<?php

use app\helpers\Url;
use app\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\widgets\DynamicFormWidget;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */
/** @var $genders \app\models\Sex[] */

$title = Yii::t('app', 'Gender settings');
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => Url::current()];

$this->beginContent('@app/modules/admin/views/settings/_layout.php');
?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form', 'action' => ['genders']]); ?>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('app', 'Gender settings') ?></h3>
    </div>
    <div class="box-body">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'wrapper-genders',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'limit' => 30,
            'min' => 1,
            'insertButton' => '.add-item',
            'deleteButton' => '.remove-item',
            'model' => count($genders) ? $genders[0] : new \app\models\Sex(),
            'template' => $this->render('_gender-item', [
                'i' => 0,
                'form' => $form,
                'model' => count($genders) ? $genders[0] : new \app\models\Sex(),
            ]),
            'formId' => 'dynamic-form',
            'formFields' => [
                'alias',
                'title',
                'title_plural',
                'icon',
            ],
        ]); ?>
        <div class="wrapper-genders">
            <div class="container-items">
                <div class="alert alert-info">
                    <?= Yii::t('app', 'Icons: Font Awesome are used by default - {0}',
                        Html::a('https://fontawesome.com/v4.7.0/icons/',  'https://fontawesome.com/v4.7.0/icons/')) ?>
                </div>
                <?php foreach ($genders as $i => $model): ?>
                    <?= $this->render('_gender-item', [
                        'i' => $i,
                        'model' => $model,
                        'form' => $form,
                    ]) ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>
    <div class="box-footer">
        <?= Html::submitButton( Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->endContent() ?>
