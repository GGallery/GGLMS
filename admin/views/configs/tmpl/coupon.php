<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 10/05/2017
 * Time: 17:18
 */

?>

<div class="row-fluid">

    <div class="row-fluid">
        <?php echo $this->form->renderField('url_redirect_on_access_deny'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('titolo_pagina_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('descrizione_pagina_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_inserimento_success'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_inserimento_wrong'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_inserimento_pending'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_inserimento_duplicate'); ?>
    </div>

</div>

