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

            <div class="row-fluid">
                <?php echo $this->form->renderField('id_gruppi_visibili'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_community_builder_nome'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_community_builder_cognome'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_event_booking_nome'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('campo_event_booking_cognome'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('ulteriori_attestati'); ?>
            </div>
            <div class="alert alert-warning">
                <span class="icon-info-2" style="position: relative; float: left; font-size: 40px; margin-right: 35px; margin-top: 11px;">  </span>
                <h4 class="alert-heading">CUSTOM CSS</h4>
                <div class="alert-message">
                    E' possibile personalizzare il css nel frontend utilizzando il file <b>gglms_custom.css</b> posizionato nella
                    root dell'installazione di joomla (es. httpdocs/home/gglms_custom.css). Se non è presente crealo,
                    non verrà comunque sovrascritto da aggiornamenti di GGlms.
                </div>
            </div>

            <div class="alert alert-warning">
                <span class="icon-info-2" style="position: relative; float: left; font-size: 40px; margin-right: 35px; margin-top: 11px;">  </span>
                <h4 class="alert-heading">CONTENUTI SCORM</h4>
                <div class="alert-message">
                    Qualora tu debba aggiungere contenuti di tipo SCORM, devi prima aggiungere la cartella <b>scorm</b>.
                    Scarica quindi il file <a target="_blank" href="https://github.com/GGallery/GGLMS/raw/master/scorm.zip">SCORM</a>, decomprimilo senza creare sottocartelle e posizionala la cartella allo stesso livello della root di joomla (ad esempio "home")
                </div>
            </div>

            <div class="alert-info alert">
                <span class="icon-info-2" style="position: relative; float: left; font-size: 40px; margin-right: 35px; margin-top: 11px;">  </span>
                <h4 class="alert-heading">DOCUMENTAZIONE</h4>
                <div class="alert-message">
                    Scarica la documentazione del componente:


                    <a href="https://github.com/GGallery/GGLMS/raw/master/documentazioneGGLMS.docx" target="_blank">Download .docx</a>



                </div>
            </div>



        </div>
    </div>
</div>

