<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 11:48
 */



?>
<div id="detailsCorso" class="modal fade " role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Dettagli del corso</h4>
            </div>
            <div class="modal-body">
                <table id="details_table_corso" class="table table-condensed table-hover table-striped ">
                    <thead> <tr> <th>Titolo unità</th> <th>Titolo contenuto</th>  <th>stato</th><th>data</th></tr></thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>




<div class="row-fluid">
    <div class="span6">

        <div class="row-fluid">

            <div class="row-fluid">
                <?php echo $this->form->renderField('data_sync'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('alert_days_before'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('alert_mail_text'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('data_sync_seconds_limit'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('sync_automatico'); ?>
            </div>
            <div class="row-fluid">
                <a id="empty_tables" class="btn active btn-success" onclick="empty_tables()">Svuota tutte le tabelle di Report</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    function empty_tables() {

        var ok=confirm('Attenzione: stai svuotando tutte le tabelle di report: dopo questa operazione sarà necessario ricaricarle');
        if(ok==true){

            location.href='index.php?option=com_gglms&task=report.empty_tables';


        }

    }



</script>