<?php

use app\helpers\Html;
use app\base\View;
use youdate\widgets\ActiveForm;
use youdate\helpers\HtmlHelper;
use youdate\widgets\Connect;

/** @var $this \app\base\View */
/** @var $sexModels \app\models\Sex[] */
/** @var $registrationForm \app\forms\RegistrationForm */

$this->title = Html::encode($this->themeSetting('homepageTitle'));
$this->context->layout = 'page-landing';
$this->params['body.cssClass'] = 'body-landing-page';
$flash = Yii::$app->session->getFlash('info', false);
if ($flash) {
    $this->registerJs(sprintf('Messenger().post("%s")', Yii::$app->session->getFlash('info')), View::POS_READY);
}
$this->registerJsFile('@themeUrl/static/js/landing.js', ['depends' => \youdate\assets\Asset::class]);
?>
<div class="content content-landing pt-5 pb-5" style="flex: 1">
    <div class="container">
        <div class="card w-100">
            <div class="row row-landing-page ">
                <div class="col-lg-6">
                    <div class="landing-page-bg"></div>
                </div>
                <div class="col-lg-6 d-flex flex-column">
                    <div class="landing-page-signup d-flex flex-column justify-content-center flex-grow-1">
                        <div class="landing-page-signup-head">
                            <h1><?= Html::encode($this->themeSetting('homepageTitle')) ?></h1>
                            <div class="subtitle">
                                <?= Html::encode($this->themeSetting('homepageSubTitle')) ?>
                            </div>
                        </div>
                        <div class="landing-page-signup-form flex-fill">
                            <?php $form = ActiveForm::begin([
                                'id' => 'registration-form',
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnBlur' => false,
                                'validateOnType' => false,
                                'validateOnChange' => false,
                                'action' => ['/registration/register'],
                            ]); ?>
                            <div class="steps">
                                <div class="step step-1">
                                    <div class="select-group select-group-sex">
                                        <div class="select-group-title"><?= Yii::t('youdate', 'Your are...') ?></div>
                                        <?= Html::activeHiddenInput($registrationForm, 'sex', ['class' => 'registration-sex']) ?>
                                        <?php foreach ($sexModels as $model): ?>
                                            <?= Html::button(HtmlHelper::sexToIcon($model) . $model->getTitle(), [
                                                'class' => 'btn btn-pill btn-outline-secondary btn-sex btn-' . $model->alias,
                                                'data-sex' => $model->sex,
                                            ]) ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="select-group">
                                        <div class="select-group-title"><?= Yii::t('youdate', 'My birthdate is...') ?></div>
                                        <div class="row">
                                            <div class="col-4">
                                                <?= Html::activeDropDownList($registrationForm, 'dobDay', $registrationForm->getDobDayOptions(), [
                                                    'class' => 'form-control form-control-lg',
                                                    'prompt' => Yii::t('youdate', 'Day'),
                                                ]) ?>
                                            </div>
                                            <div class="col-4">
                                                <?= Html::activeDropDownList($registrationForm, 'dobMonth', $registrationForm->getDobMonthOptions(), [
                                                    'class' => 'form-control form-control-lg',
                                                    'prompt' => Yii::t('youdate', 'Month'),
                                                ]) ?>
                                            </div>
                                            <div class="col-4">
                                                <?= Html::activeDropDownList($registrationForm, 'dobYear', $registrationForm->getDobYearOptions(), [
                                                    'class' => 'form-control form-control-lg',
                                                    'prompt' => Yii::t('youdate', 'Year'),
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?= Html::button(Yii::t('youdate', 'Continue'), [
                                        'class' => 'btn btn-secondary btn-block btn-continue btn-disabled',
                                        'disabled' => 'disabled',
                                    ]) ?>
                                </div>
                                <div class="step step-2 hide">
                                    <div class="row">
                                        <div class="col-6">
                                            <?= $form->field($registrationForm, 'name') ?>
                                        </div>
                                        <div class="col-6">
                                            <?= $form->field($registrationForm, 'username') ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <?= $form
                                                ->field($registrationForm, 'country', ['inputOptions' => ['autocomplete' => 'off']])
                                                ->widget(\youdate\widgets\CountrySelector::class) ?>
                                        </div>
                                        <div class="col-6">
                                            <?= $form
                                                ->field($registrationForm, 'city', ['inputOptions' => ['autocomplete' => 'off']])
                                                ->widget(\youdate\widgets\CitySelector::class) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <?= $form->field($registrationForm, 'email') ?>
                                        </div>
                                        <div class="col-6">
                                            <?= $form->field($registrationForm, 'password')->passwordInput() ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <?= Html::button(Yii::t('youdate', 'Back'), ['class' => 'btn btn-secondary btn-back btn-block']) ?>
                                        </div>
                                        <div class="col-6">
                                            <?= Html::submitButton(Yii::t('youdate', 'Sign up'), ['class' => 'btn btn-primary btn-block']) ?>
                                        </div>
                                    </div>
                                    <div class="terms text-muted pt-3 px-3 text-center text-small">
                                        <?= Yii::t('youdate', 'By continuing, you\'re confirming that you\'ve read and agree to our {terms}, {privacy} and {cookie}', [
                                            'terms' => Html::a(Yii::t('youdate', 'Terms and Conditions'), ['/page/terms-and-conditions']),
                                            'privacy' => Html::a(Yii::t('youdate', 'Privacy Policy'), ['/page/privacy-policy']),
                                            'cookie' => Html::a(Yii::t('youdate', 'Cookie Policy'), ['/page/cookie-policy']),
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <div class="landing-page-social-auth">
                        <?= Connect::widget([
                            'prepend' => Html::tag('div', Yii::t('youdate', 'or'), ['class' => 'landing-or']),
                            'append' => Html::tag('div', Yii::t('youdate', 'We never post on your behalf.'), [
                                'class' => 'text-muted',
                            ]),
                            'baseAuthUrl' => ['/security/auth'],
                            'options' => ['class' => 'social-auth social-auth-lp'],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
