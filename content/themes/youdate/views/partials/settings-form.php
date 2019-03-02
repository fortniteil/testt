<?php

use app\settings\SettingsModel;
use app\helpers\Html;
use yii\helpers\ArrayHelper;
use youdate\widgets\ActiveForm;
use trntv\aceeditor\AceEditor;

/** @var $elements array */
/** @var $title string */
/** @var $subTitle string */
/** @var $model SettingsModel */

$message = \Yii::$app->session->getFlash('settings');
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($title) ?></h3>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin() ?>

        <?= $this->render('/_alert') ?>

        <?php if (isset($subTitle)): ?>
            <h4 class="mb-5"><?= Html::encode($subTitle) ?></h4>
        <?php endif; ?>

        <?php foreach ($elements as $alias => $element) {
            if (is_callable($element['type'])) {
                echo $element['type']($model, $alias);
            } else {
                switch ($element['type']) {
                    case 'dropdown':
                        echo $form->field($model, $alias)
                            ->dropDownList(is_callable($element['options']) ? $element['options']() : $element['options'], [
                                'prompt' => Yii::t('youdate', '-- Select --'),
                            ])
                            ->hint($element['help']);
                        break;
                    case 'checkboxList':
                        echo $form->field($model, $alias)->checkboxList($element['options'])->hint($element['help']);
                        break;
                    case 'checkbox':
                        echo $form->field($model, $alias)->checkbox(isset($element['options']) ?: [])->hint($element['help']);
                        break;
                    case 'code':
                        echo $form->field($model, $alias)->widget(AceEditor::class, ArrayHelper::merge(isset($element['options']) ? $element['options'] : [], [
                            'mode' => 'php',
                            'containerOptions' => [
                                'style' => 'width: 100%; min-height: 200px'
                            ]
                        ]))->hint($element['help']);;
                        break;
                    default:
                    case 'text':
                        echo $form->field($model, $alias)->hint($element['help']);;
                }
            }
        } ?>
        <?= Html::submitButton(Yii::t('youdate', 'Save'), ['class' => 'btn btn-primary float-right']) ?>
        <?php $form->end() ?>
    </div>
</div>
