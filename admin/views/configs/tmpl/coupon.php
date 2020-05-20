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



    <hr>

    <h2 style="text-align: center">GENERA COUPON</h2>
    <div class="row-fluid">
        <?php echo $this->form->renderField('mail_coupon_acitve'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('coupon_active_default'); ?>
    </div>

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


</div>

