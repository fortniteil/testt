<?php

namespace app\controllers;

use app\forms\RegistrationForm;
use app\models\Sex;
use app\traits\AjaxValidationTrait;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class DefaultController extends \app\base\Controller
{
    use AjaxValidationTrait;

    /**
     * @return int|mixed|string|\yii\console\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->runAction('dashboard/index');
        }

        /** @var RegistrationForm $registrationForm */
        $registrationForm = Yii::createObject(RegistrationForm::class);

        $this->performAjaxValidation($registrationForm);

        return $this->render('index', [
            'sexModels' => Sex::find()->all(),
            'registrationForm' =>$registrationForm,
        ]);
    }
}
