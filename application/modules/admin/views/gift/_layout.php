<?php

use app\helpers\Html;
use yii\bootstrap\Nav;

/** @var $content string */
/** @var $this \app\base\View */

$this->beginContent('@app/modules/admin/views/layouts/main.php');
?>

<div class="box box-solid box-nav">
    <div class="box-body no-padding clearfix">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav-pills pull-left',
                    ],
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Categories'),
                            'url' => ['gift/categories']
                        ],
                    ]
                ]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="box-actions">
                    <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create-category'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::a('<i class="fa fa-search"></i> ' . Yii::t('app', 'Scan'), ['scan'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= $this->blocks['gift-actions'] ?? '' ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $content ?>

<?php $this->endContent() ?>
