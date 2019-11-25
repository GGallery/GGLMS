<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 10/05/2017
 * Time: 17:18
 */

?>

<div class="row-fluid">

    <h2 style="text-align: center">ASSOCIA COUPON</h2>

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
    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_inserimento_tutor'); ?>
    </div>

    <hr>

    <h2 style="text-align: center">GENERA COUPON</h2>
    <div class="row-fluid">
        <?php echo $this->form->renderField('mail_coupon_acitve'); ?>
    </div

    <div class="row-fluid">
        <?php echo $this->form->renderField('specifica_durata_coupon'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('durata_standard_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_forum'); ?>
    </div>

    <hr>


    <h2 style="text-align: center">RINNOVA COUPON</h2>

    <div class="row-fluid">
        <?php echo $this->form->renderField('titolo_pagina_rinnova_coupon'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('descrizione_pagina_rinnova_coupon'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_rinnovo_nouser'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_rinnovo_notutor'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_rinnovo_wrong_società'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_rinnovo_not_expired'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->form->renderField('messaggio_rinnovo_success'); ?>
    </div>


</div>

