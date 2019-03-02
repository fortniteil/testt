<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\search\MessageSearch;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class MessageController extends \app\modules\admin\components\Controller
{
    /**
     * Messages index page
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();

        return $this->render('index', [
            'dataProvider' => $searchModel->search(Yii::$app->request->get()),
            'searchModel' => $searchModel,
        ]);
    }
}
