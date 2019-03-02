<?php

namespace app\controllers;

use app\forms\GiftForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class GiftController extends \app\base\Controller
{
    /**
     * @var bool
     */
    public $prepareData = false;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send' => ['post'],
                ],
            ],
        ];
    }


    /**
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSend()
    {
        $currentUser = $this->getCurrentUser();
        $giftForm = new GiftForm();
        $giftForm->fromUserId = $currentUser->id;

        if ($giftForm->load(Yii::$app->request->post()) && $giftForm->validate() && $giftForm->validateUserBalance()) {
            $toUser = $this->userManager->getUserById($giftForm->toUserId);
            if (Yii::$app->giftManager->sendGift($currentUser, $toUser, $giftForm)) {
                return $this->sendJson([
                    'success' => true,
                    'message' => Yii::t('app', 'Gift has been sent'),
                    'balance' => $this->balanceManager->getUserBalance(Yii::$app->user->id),
                ]);
            }
        }

        return $this->sendJson([
            'success' => false,
            'errors' => $giftForm->errors,
        ]);
    }
}
