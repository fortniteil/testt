<?php

use yii\helpers\Html;
use app\helpers\Url;
use yii\helpers\ArrayHelper;

/** @var $this \app\base\View */

?>
<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="<?= Url::to(['/']) ?>">
                <?= Html::img($this->themeSetting('logoUrlLight', '@themeUrl/static/images/logo-light@2x.png'), [
                    'class' => 'header-brand-img', 'alt' => $this->frontendSetting('siteName', 'YouDate')
                ]) ?>
            </a>
            <div class="d-flex align-items-center order-lg-2 ml-auto">
                <span class="pr-4"><?= Yii::t('youdate', 'Have an account?') ?></span>
                <?= Html::a(Yii::t('youdate', 'Sign in'), ['/security/login'], ['class' => 'btn btn-pill btn-login']) ?>
            </div>
        </div>
    </div>
</div>
