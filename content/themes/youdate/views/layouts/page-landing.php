<?php

/* @var $content string */

?>
<?php $this->beginContent('@theme/views/layouts/base.php'); ?>
<div class="page">
    <div class="page-main">
        <?= $this->render('//partials/header-landing') ?>
        <?php echo $content ?>
    </div>
    <?= $this->render('//partials/footer') ?>
</div>
<?php $this->endContent(); ?>
