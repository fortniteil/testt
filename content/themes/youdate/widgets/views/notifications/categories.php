<?php

use app\helpers\Url;

/** @var $categories \app\notifications\BaseNotificationCategory[] */
/** @var $filters array */
?>
<div class="notification-categories custom-controls-stacked" data-url="<?= Url::to(['/notifications/index']) ?>">
    <?php foreach ($categories as $category): ?>
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="<?= $category->id ?>"
                   <?= in_array($category->id, $filters) || !count($filters) ? 'checked' : '' ?>
                   value="<?= in_array($category->id, $filters) || !count($filters) ? 1 : 0 ?>">
            <span class="custom-control-label">
                <?= Yii::t('youdate', $category->getTitle()) ?>
            </span>
        </label>
    <?php endforeach; ?>
</div>
