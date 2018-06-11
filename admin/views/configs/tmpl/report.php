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
                <?php echo $this->form->renderField('alert_lista_corsi'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('alert_days_before'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('alert_mail_object'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('alert_mail_text'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('campi_csv'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('log_utente'); ?>
            </div>

            <div class="row-fluid">
                <?php echo $this->form->renderField('colonne_somme_tempi'); ?>
            </div>


            <div class="row-fluid">
                <a id="empty_tables" class="btn active btn-success" onclick="prepare_db()">Svuota tutte le tabelle di Report</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var i,tabelle;

    function prepare_db() {

        Joomla.renderMessages({"success":["Attendi, caricamento dei dati in corso..."]});
        console.log('prepare');
        i = 0;
        tabelle=['scormvars','unit_map'];
        allinea_tabella(tabelle[i],'count');
    }

    function allinea_tabella(tabella,modalita) {
        Joomla.renderMessages({"success":["Attendi, caricamento dei dati in corso..."]});
        console.log(tabella+' '+modalita);
        jQuery.when(jQuery.get("index.php?option=com_gglms&tabella="+tabella+"&modalita="+modalita+"&task=report.allinea_tabella"))
            .done(function(data){
                result=JSON.parse(data);
                if(modalita=='delete'){
                    if(result==true){
                            i++;
                            if(i<tabelle.length){
                                allinea_tabella(tabelle[i],'count');
                            }else{
                                empty_tables();
                            }
                    }
               }else{
                    if(confirm('attenzione stai rimuovendo da '+tabella+' '+result+' record')){
                       allinea_tabella(tabella,'delete');
                    }
                }
            }).fail(function(data){
        });
    }

    function empty_tables() {

        console.log('empty')
        var ok=confirm('Attenzione: stai svuotando tutte le tabelle di report: dopo questa operazione sarà necessario ricaricarle');
        if(ok==true){

            location.href='index.php?option=com_gglms&task=report.empty_tables';
        }
    }

    jform_csvselect.onchange= function () {
        var e=document.getElementById('jform_csvselect');
        if(e.options[e.selectedIndex]==null){
            alert('attenzione inserire un campo nella scelta dei campi per csv');
        }
        
    }



</script>