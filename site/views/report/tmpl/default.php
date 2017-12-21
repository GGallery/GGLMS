<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

JHtml::_('bootstrap.modal');


?>
<div id="barrafiltri" class="span2">

    <form id="theform" class="form-inline" action="index.php">


        <h2>Corso</h2>
        <div class="form-group">

            <?php //echo outputHelper::output_select('corso_id', $this->corsi, 'id_contenuto_completamento', 'titolo', null, 'refresh'); ?>
            <select id="corso_id" name="corso_id" class="refresh">
                <?php
                foreach ($this->corsi as $corso){

                    echo '<option value="'.$corso->id.'|'.$corso->id_contenuto_completamento.'">'.$corso->titolo."</option>";
                }
                ?>
            </select>

        </div>

        <h2>Filtri</h2>

        <div class="form-group">
            <label for="usergroups">Gruppo utenti</label>
            <?php echo outputHelper::output_select('usergroups', $this->usergroups, 'id', 'title', 2 , 'refresh'); ?>
        </div>

        <div class="form-group">
            <label for="filterstato">Stato corso</label>
            <select id="filterstato" name="filterstato" class="refresh">
                <option value="2">Qualisiasi stato</option>
                <option value="1">Solo completati</option>
                <option value="0">Solo NON compleati</option>
                <option value="3">In scadenza</option>
            </select>
        </div>

        <div class="form-group" id="calendar_startdate_div">
            <label for="startdate">Completato dal:</label><br>
            <?php echo JHTML::calendar('','startdate','startdate','%Y-%m-%d'); ?>
        </div>

        <div class="form-group" id="calendar_finishdate_div">
            <label for="finishdate">Completato al:</label><br>
            <?php echo JHTML::_( 'calendar','','finishdate','finishdate','%Y-%m-%d'); ?>


        </div>


        <div class="form-group">
            <button type="button" id="update" class="btn btn-success btn-lg" onclick="reload()">AGGIORNA DATI</button>
        </div>
        <HR>
        <input type="hidden" id="option" name="option" value="com_gglms">
        <input type="hidden" id="task" name="task" value="api.get_csv">
        <div class="form-group">
            <button type="button" id="get_csv" class="btn btn-success btn-lg" onclick="sendAllMail()">INVIA MAIL IN SCADENZA</button>
        </div>
        <div class="form-group">
            <button type="button" id="get_csv" class="btn btn-success btn-lg" onclick="loadCsv()">SCARICA REPORT CSV</button>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-lg" onclick="checkSeconds()">SINCRONIZZA TABELLA REPORT</button>
        </div>

    </form>

    <hr>


    <canvas id="myChart" width="100" height="100"></canvas>



</div>
<div id="contenitoreprincipale" class="span8">

    <div class="row">
        <div class="span12">

            <table id="grid-basic" class="table table-condensed table-hover table-striped ">
                <thead>
                <tr>
                    <th data-column-id="cognome" data-sortable="false">Cognome</th>
                    <th data-column-id="nome"  data-sortable="false">Nome</th>
                    <th data-column-id="stato" data-formatter="stato"  data-sortable="false">Stato</th>
                    <th data-column-id="hainiziato" data-sortable="false">Iniziato il:</th>
                    <th data-column-id="hacompletato" data-sortable="false">Completato il:</th>
                    <th data-column-id="alert" data-formatter="alert" data-sortable="false">In scadenza</th>
                    <th data-column-id="fields" data-visible="false" data-sortable="false">Campi</th>
                    <th data-column-id="id_utente" data-visible="false" data-sortable="false">Id</th>
                    <th data-column-id="commands" data-formatter="commands" data-sortable="false">Dettagli</th>
<!--                    <th data-column-id="dettaglicorso" data-formatter="dettaglicorso" data-sortable="false">Dettagli Corso</th>-->
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>
</div>
</div>

<!-- Modal -->
<div id="details" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Dettagli utente</h4>
            </div>
            <div class="modal-body">
                <table id="details_table" class="table table-condensed table-hover table-striped ">
                    <thead> <tr> <th>Campo</th> <th>Valore</th> </tr> </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="tn btn-success btn-lg" onclick="loadLibretto()" style="font-size:12px;padding:4px;position:ABSOLUTE;left:4%;">Libretto Formativo</button>

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal Dettagli Corso-->
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

<!-- Modal Dettagli Caricamento CSV-->
<div id="detailsCaricamentoCSV" class="modal fade " role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Dettagli del caricamento CSV</h4>
            </div>
            <div class="modal-body">
                <table id="details_table_caricamento_csv" class="table table-condensed table-hover table-striped ">
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

<!-- Modal Dettagli Caricamento Tabella Report-->
<div id="detailsCaricamentoReport" class="modal fade " role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Dettagli del caricamento Tabella Report</h4>
            </div>
            <div class="modal-body">
                <table id="details_table_caricamento_report" class="table table-condensed table-hover table-striped ">
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

<!-- Modal Dettagli invio mail -->
<div id="detailsInvioMail" class="modal fade " role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Dettagli di invio della mail di avviso</h4>
            </div>
            <div class="modal-body">
                <table id="details_table_invio_mail" class="table table-condensed table-hover table-striped ">
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="div_send_mail_textarea" class="modal-body">Confermi di inviare questa email?<br>
                oggetto:<input id="oggettomail" type="text" value="promemoria scadenza corso">
                <textarea   cols="50" rows="5" id="testomail" style="width: 560px;"></textarea>
            </div>
            <div class="modal-footer">
                <button id="sendmailbutton" type="button" class="btn btn-success btn-lg"  onclick="sendMail()">Invia</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<?php
echo "Report aggiornato al :" .$this->state->get('params')->get('data_sync');
?>

<script type="text/javascript">


//MODIFICARE QUI QUANDO CI SARA' IL PARAMETRO
var testo_base_mail='<?php echo $this->state->get('params')->get('alert_mail_text'); ?>';
var loadreportlimit;
var loadreportoffset;

    jQuery( document ).ready(function($) {

//        TORTA
        var ctx = document.getElementById("myChart").getContext('2d');
        var notcompleted = 0;
        var completed = 0;
        var fields = new Array();

        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ["Utenti che hanno completato", "Utenti che non hanno completato"],
                datasets: [{
                    label: '% corsi completati',
                    data: [completed , notcompleted ],
                    backgroundColor: [
                        'rgba(0, 123, 132, 0.2)',
                        'rgba(128, 162, 25, 0.2)'
                    ],
                    borderColor: [
                        'rgba(0,123,132,1)',
                        'rgba(128, 162, 25, 1)'

                    ],
                    borderWidth: 5
                }]
            },

        });




        //  TABELLA
        $(".refresh").change(function(){

            notcompleted = 0;
            completed = 0;
            $("#grid-basic").bootgrid("reload");
        });

        $("#filterstato").change(function(){


            if($("#filterstato option:selected").val()==1 || $("#filterstato option:selected").val()==2){
                $("#calendar_startdate_div").show();
                $("#calendar_finishdate_div").show();
            }else{
                $("#calendar_startdate_div").hide();
                $("#calendar_finishdate_div").hide();
            };

        });

        $("#startdate").bind('change',function(){

            notcompleted = 0;
            completed = 0;
            $("#grid-basic").bootgrid("reload");
        });

        $("#finishdate").change(function(){

            notcompleted = 0;
            completed = 0;
            $("#grid-basic").bootgrid("reload");
        });

        var grid = $("#grid-basic").bootgrid({
            ajax: true,
            multiSort: true,
            requestHandler: function (request) {
                //Add your id property or anything else
                request.corso_id = $("#corso_id").val();
                request.startdate = $("#startdate").val();
                request.finishdate = $("#finishdate").val();
                request.filterstato = $("#filterstato").val();
                request.usergroups = $("#usergroups").val();
                return request;
            },
            url: "index.php?option=com_gglms&task=api.get_report",
            formatters: {
                "stato": function(column, row)
                {
                    if(row.stato == 1) {
                        completed++;
                        return "Completato";
                    }
                    else {
                        notcompleted++;
                        return "Non Completato";
                    }
                },
                "alert": function (column, row)
                {
                    if(row.alert == 1) {
                        fields[row.id_utente]=row.fields;
                        return '<span class="glyphicon glyphicon-alert" style="color:gold; font-size: 23px;" aria-hidden="true"></span>' +

                            '<button type="button" style="color:gold; font-size: 23px;    margin-left: 10px; margin-top: -10px;" title="email" class="btn btn-xs btn-default command-edit-sendMail" data-row-id=\"' + row.id_utente + '\"><span class="glyphicon glyphicon-envelope" aria-hidden="true" style="color:red; font-size:16px;"></span></button>';
                    }
                    else {

                        return null;//'<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>';
                    }
                },
                "commands": function(column, row)
                {
                    fields[row.id_utente]=row.fields;
                    return '<button type="button" title="anagrafica" class="btn btn-xs btn-default command-edit" data-row-id=\"' + row.id_utente + '\"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></button>'+
                        '<button type="button" title="dettagli corso" class="btn btn-xs btn-default command-edit-dettagli" data-row-id=\"' + row.id_utente + '\"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></button>'+
                        '<a href='+window.location+'../../libretto.html?user_id='+row.id_utente+' title="libretto formativo" class="btn btn-xs btn-default" \><span class="glyphicon glyphicon-book" aria-hidden="true"></span></a>';
                },
                "dettaglicorso": function(column, row)
                {
                    //fields[row.id_utente]=row.fields;
                    //return '<button type="button" class="btn btn-xs btn-default command-edit-dettagli" data-row-id=\"' + row.id_utente + '\"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></button>';
                }
            }

        }).on("loaded.rs.jquery.bootgrid", function(e)
        {
            myChart.data.datasets[0].data[0] = completed;
            myChart.data.datasets[0].data[1] = notcompleted;
            myChart.update();
            grid.find(".command-edit").on("click", function(e)
            {
                scelta = $(this).data("row-id");
                data= JSON.parse(fields[scelta]);
                $('#details_table tbody').empty();
                $.each(data, function (key, value) {
                    var eachrow = "<tr>" + "<td>" +  key + "</td>" + "<td>" +  value + "</td>" + "</tr>";
                    $('#details_table tbody').append(eachrow);

                });
                $("#details").append('<input id=modal_id_utente type=hidden value='+scelta+'>');
                $("#details").modal('show');

            }).end();

            grid.find(".command-edit-sendMail").on("click", function(e)
            {
                $('#testomail').empty();
                jQuery('#sendmailbutton').show();
                jQuery('#div_send_mail_textarea').show();
                scelta = $(this).data("row-id");
                data= JSON.parse(fields[scelta]);
                $('#details_table_invio_mail tbody').empty();

                    var eachrow = "<tr>" + "<td>Nome</td>" + "<td>" +  data['nome'] + "</td>" + "</tr>";
                    eachrow += "<tr>" + "<td>Cognome</td>" + "<td>" +  data['cognome'] + "</td>" + "</tr>";
                    eachrow += "<tr>" + "<td>Email</td>" + "<td id='to'>" +  data['email'] + "</td>" + "</tr>";

                    $('#details_table_invio_mail tbody').append(eachrow);
                    nome_corso=$('#corso_id option:selected').text();
                    $('#testomail').append(testo_base_mail+" "+nome_corso);


                $("#detailsInvioMail").modal('show');

            }).end();

            grid.find(".command-edit-dettagli").on("click", function(e)
            {
                scelta = $(this).data("row-id");
                var id_utente=scelta;
                var id_corso=$('#corso_id')[0]['value'].split('|')[0];

                jQuery.when(jQuery.get("index.php?option=com_gglms&task=api.buildDettaglioCorso&id_corso="+id_corso+"&id_utente="+id_utente))
                    .done(function(data){
                        data=JSON.parse(data);
                        $('#details_table_corso tbody').empty();
                        var eachrow;
                        $.each(data, function (key,value) {

                            eachrow=eachrow+"<tr><td>"+value['titolo unità']+"</td>"+
                                "<td>"+value['titolo contenuto']+"</td>"+
                                "<td>"+value['stato']+"</td>"+
                                "<td>"+value['data']+"</td></tr>";
                        });

                        $('#details_table_corso tbody').append(eachrow);
                        $("#detailsCorso").modal('show');

                    }).fail(function(data){

                });



            }).end();
        });
    });

function checkSeconds() {
    jQuery("#detailsCaricamentoReport").modal('show');
    jQuery('#details_table_caricamento_report').empty();
    jQuery('#details_table_caricamento_report').append('<tr><td>inizio caricamento</td></tr><tr><td>stiamo caricando i tuoi dati ti invitiamo ad attendere...</td></tr>');


    jQuery.when(jQuery.get("index.php?option=com_gglms&task=report.checkSeconds"))
        .done(function (data) {
            data=JSON.parse(data);
            if(data=='true'){
                dataSyncUsers(loadreportlimit,loadreportoffset);
            }else{
                jQuery('#details_table_caricamento_report').append('<tr><td>caricamento stoppato!</td></tr><tr><td>non è stato superato il limite di tempo necessario per un nuovo caricamento</td></tr>');

            }

        })
        .fail(function (data) {

        })
        .then(function (data) {


        });

}
    function dataSyncUsers(loadreportlimit,loadreportoffset) {//E' LA FUNZIONE CHE INIZIA LA PROCEDURA DI CARICAMENTO TABELLA REPORT


        loadreportlimit=0
        loadreportoffset=100;
        console.log('dataSyncUsers');
        jQuery.when(jQuery.get("index.php?option=com_gglms&task=report.sync_report_users"))
            .done(function (data) {

            })
            .fail(function (data) {

            })
            .then(function (data) {
                dataSyncReportCount(loadreportlimit,loadreportoffset);

            });

    }

    function dataSyncReportCount(loadreportlimit,loadreportoffset) {

        console.log('dataSyncReportCount');
        jQuery.when(jQuery.get("index.php?option=com_gglms&task=report.sync_report_count"))
            .done(function (data) {
                data=JSON.parse(data);
                jQuery('#details_table_caricamento_report').append('<tr><td>Caricamento totale di  '+data+' records</td></tr>');

            })
            .fail(function (data) {

            })
            .then(function (data) {
                dataSyncReport(loadreportlimit,loadreportoffset);
            });

    }
    function dataSyncReport(loadreportlimit,loadreportoffset) {
        console.log('dataSyncReport');

        jQuery.when(jQuery.get("index.php?limit="+loadreportlimit+"&offset="+loadreportoffset+"&option=com_gglms&task=report.sync_report"))
            .done(function(data){
                data=JSON.parse(data);
                loadreportlimit+=loadreportoffset;

                console.log('data:'+data+' loadreportlimit a:'+loadreportlimit);
                if(data=='true') {
                    jQuery('#details_table_caricamento_report').append('<tr><td>caricamento fino a record n° '+loadreportlimit+'</td></tr>');
                    dataSyncReport(loadreportlimit, loadreportoffset);
                }else{
                    dataUpdateConfig();
                }
            }).fail(function(data){
        })
            .then(function (data) {

            });
    }

    function dataUpdateConfig() {
        console.log('dataUpdateConfig');
        jQuery.when(jQuery.get("index.php?option=com_gglms&task=report.updateconfig"))
            .done(function (data) {
                jQuery('#details_table_caricamento_report').append('<tr><td>caricamento completato</td></tr>');

            })
            .fail(function (data) {

            });
    }

    function reload() {
        jQuery("#grid-basic").bootgrid("reload");
    }

    function loadCsv() {
        var total;
        var id_chiamata=Math.floor(Math.random()*100000);
        var id_corso= jQuery('#corso_id')[0]['value'];
        var usergroups= jQuery('#usergroups')[0]['value'];
        var filterstato= jQuery('#filterstato')[0]['value'];
        var startdate= jQuery("#startdate")[0]['value'];
        var finishdate= jQuery("#finishdate")[0]['value'];
        jQuery('#details_table_caricamento_csv').empty();
        jQuery('#details_table_caricamento_csv').append('<tr><td>inizio caricamento</td></tr><tr><td>stiamo caricando i tuoi dati ti inviatiamo ad attendere...</td></tr>');
        jQuery("#detailsCaricamentoCSV").modal('show');
        jQuery.when(jQuery.get("index.php?corso_id="+id_corso+"&usergroups="+usergroups+"&filterstato="+filterstato+
                                "&startdate="+startdate+"&finishdate="+finishdate+"&csvlimit=0$csvoffset=0&id_chiamata="+id_chiamata+"&option=com_gglms&task=api.get_csv"))
            .done(function(data){

            }).then(function (data) {

            data=JSON.parse(data);
            total=data['total'];
            jQuery('#details_table_caricamento_csv').append('<tr><td>caricamento di '+total+' records, attendere il completamento della procedura...</td></tr>');
            var csvoffset=100;
            var csvlimit=100;
            $datafromquery=LoadCSVDataFromJquery(id_corso,usergroups,filterstato,startdate,finishdate,csvoffset,csvlimit,total,id_chiamata);
        }).fail(function(data) {

        });

    }

    function LoadCSVDataFromJquery(id_corso,usergroups,filterstato,startdate,finishdate,csvoffset,csvlimit,total,id_chiamata) {

        var jqxhr=jQuery.get("index.php?corso_id=" + id_corso + "&usergroups=" + usergroups +
            "&filterstato=" + filterstato +"&startdate=" + startdate + "&finishdate=" + finishdate + "&csvlimit="
            + csvlimit +"&csvoffset="+csvoffset+"&id_chiamata="+id_chiamata+"&option=com_gglms&task=api.get_csv", function (data) {
            data=JSON.parse(data);
        })
            .done(function (data) {
                jQuery('#details_table_caricamento_csv').append('<tr><td>caricamento fino a record n° '+csvlimit+'</td></tr>');
                if(csvlimit<parseInt(total)){
                    csvlimit=csvlimit+csvoffset;
                    LoadCSVDataFromJquery(id_corso,usergroups,filterstato,startdate,finishdate,csvoffset,csvlimit,total,id_chiamata)
                }else {
                    jQuery('#details_table_caricamento_csv').append('<tr><td>caricamento completato</td></tr>');
                    location.href='index.php?option=com_gglms&id_chiamata='+id_chiamata+'&corso_id="'+id_corso.substr(0,id_corso.indexOf('|'))+'"&task=api.createCSV';
                }
            }).fail(function (data) {
                jQuery('#details_table_caricamento_csv').append('<tr><td>ERROR\! nel caricamento fino a record n° '+csvlimit+'</td></tr>');
            });
        jqxhr=null;
    }

    function sendMail() {

        oggettomail=jQuery('#oggettomail').val();
        testomail=jQuery('#testomail').val();
       // to=jQuery('#to').html(); ATTENZIONE QUESTA RIGA IN PRODUZIONE ANDRA' SCOMMENTATA
        to="a.petruzzella71@gmail.com";
       jQuery.when(jQuery.get("index.php?to="+to+"&oggettomail="+oggettomail+"&testomail="+testomail+"&option=com_gglms&task=api.sendMail"))

            .done(function(data){

                result=JSON.parse(data);

                if(result==true){
                    jQuery('#sendmailbutton').hide();
                    jQuery('#div_send_mail_textarea').hide();
                    jQuery('#details_table_invio_mail tbody').append('<tr><td>email inviata con successo, puoi chiudere questa finestra</td><tr>');
                }
            }).fail(function(data){

        });

    }

    function sendAllMail() {


        nome_corso=jQuery('#corso_id option:selected').text();
        oggettomail=jQuery('#oggettomail').val();
        testomail=testo_base_mail+nome_corso;
        var id_corso= jQuery('#corso_id')[0]['value'];
        var usergroups= jQuery('#usergroups')[0]['value'];
        jQuery.when(jQuery.get("index.php?corso_id="+id_corso+"&usergroups="+usergroups+"&oggettomail="+oggettomail+"&testomail="+testomail+"&option=com_gglms&task=api.sendAllMail"))

            .done(function(data){

            }).fail(function(data){

        });

    }

    function loadLibretto() {

        var user_id=jQuery('#modal_id_utente').val();


        location.href=window.location+'../../libretto.html?user_id='+user_id


    }


</script>