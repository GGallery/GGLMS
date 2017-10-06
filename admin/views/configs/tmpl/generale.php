<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 10/05/2017
 * Time: 17:18
 */

?>
<div class="row-fluid">
    <div class="span6">

        <div class="row-fluid">

            <div class="row-fluid">
                <?php echo $this->form->renderField('data_sync'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('integrazione'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_event_booking_auto_abilitazione_coupon'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_community_builder_auto_abilitazione_coupon'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('verifica_cf'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_event_booking_controllo_cf'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_community_builder_controllo_cf'); ?>
            </div>

            <div class="alert alert-warning">
                <span class="icon-info-2" style="position: relative; float: left; font-size: 40px; margin-right: 35px; margin-top: 11px;">  </span>
                <h4 class="alert-heading">CUSTOM CSS</h4>
                <div class="alert-message">
                    E' possibile personalizzare il css nel frontend utilizzando il file <b>ggslm_custom.css</b> posizionato nella
                    root dell'installazione di joomla (es. httpdocs/home/gglms_custom.css). Se non è presente crealo,
                    non verrà comunque sovrascritto da aggiornamenti di GGlms.
                </div>
            </div>

            

        </div>
    </div>
</div>

