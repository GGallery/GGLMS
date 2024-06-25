<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 09/11/2020
 * Time: 10:37
 */
?>

<!-- Genera coupon -->
<div class="row-fluid">

    <h4>Contenuto</h4>

    <div class="row-fluid">
        <?php echo $this->form->renderField('attiva_blocco_video_focus'); ?>
    </div>

    <h4>Associa coupon</h4>

    <div class="row-fluid">
        <?php echo $this->form->renderField('url_redirect_on_access_deny'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('accesso_corsi_tutoraz'); ?>
    </div>

    <h4>Genera coupon</h4>

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
        <?php echo $this->form->renderField('nome_azienda_intestazione_email_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('durata_standard_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_forum'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_label_partita_iva'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_label_partita_iva_missing'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_label_ragione_sociale'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_label_email_tutor_aziendale'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_visualizza_venditore'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_visualizza_ateco'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_visualizza_stampa_tracciato'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_visualizza_tipo_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_coupon_tipi_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('genera_disabilita_coupon'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $this->form->renderField('durata_massima'); ?>
    </div>

    <h4>Extra</h4>

    <div class="row-fluid">
        <?php echo $this->form->renderField('extra_index_redirect'); ?>
    </div>

</div>

<hr />

<!-- Monitora coupon -->
<div class="row-fluid">

    <h4>Monitora coupon</h4>

    <div class="row-fluid">
        <?php echo $this->form->renderField('monitora_coupon_disattiva_coupon'); ?>
    </div>

</div>

<hr />

<!-- Summary report -->
<div class="row-fluid">

    <h4>Summary report</h4>

    <div class="row-fluid">
        <?php echo $this->form->renderField('summary_report_nascondi_colonne'); ?>
    </div>

    <h5>Dizionario colonne</h5>

    <div class="row-fluid">

        <table class="table table-striped">

            <tr>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_coupon'); ?>
                </td>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_nome'); ?>
                </td>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_cognome'); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_cf'); ?>
                </td>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_corso'); ?>
                </td>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_azienda'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_stato'); ?>
                </td>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_attestato'); ?>
                </td>
                <td>
                    <?php echo $this->form->renderField('summary_report_label_venditore'); ?>
                </td>
            </tr>

        </table>

    </div>

    <div class="row-fluid">

    </div>


</div>
