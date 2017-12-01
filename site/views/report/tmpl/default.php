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

        <div class="form-group">
            <label for="startdate">Dal</label><br>
            <?php echo JHTML::calendar('','startdate','startdate','%Y-%m-%d'); ?>
        </div>

        <div class="form-group">
            <label for="finishdate">Al</label><br>
            <?php echo JHTML::_( 'calendar','','finishdate','finishdate','%Y-%m-%d'); ?>


        </div>


        <div class="form-group">
            <button type="button" id="update" class="btn btn-success btn-lg" onclick="reload()">AGGIORNA DATI</button>
        </div>
        <HR>
        <input type="hidden" id="option" name="option" value="com_gglms">
        <input type="hidden" id="task" name="task" value="api.get_csv">
        <div class="form-group">
            <button type="submit" id="get_csv" class="btn btn-success btn-lg">SCARICA CSV</button>


            <button type="button" class="btn btn-success btn-lg" onclick="dataSync()">SINCRONIZZA</button>
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
                    <th data-column-id="cognome" data-order="asc" >Cognome</th>
                    <th data-column-id="nome"  >Nome</th>
                    <th data-column-id="stato" data-formatter="stato"  >Stato</th>
                    <th data-column-id="hainiziato">Iniziato il:</th>
                    <th data-column-id="hacompletato">Completato il:</th>
                    <th data-column-id="alert" data-formatter="alert">In scadenza</th>
                    <th data-column-id="fields" data-visible="false">Campi</th>
                    <th data-column-id="id_utente" data-visible="false" >Id</th>
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




<?php
echo "Report aggiornato al :" .$this->state->get('params')->get('data_sync');
?>

<script type="text/javascript">



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

        $("#startdate").bind('change',function(){

            notcompleted = 0;
            completed = 0;
            $("#grid-basic").bootgrid("reload");
        });

        $("#finishdate").change(function(){
            console.log('finishdate');
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

                        return '<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>';
                    }
                    else {

                        return null;//'<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>';
                    }
                },
                "commands": function(column, row)
                {
                    fields[row.id_utente]=row.fields;
                    return '<button type="button" title="anagrafica" class="btn btn-xs btn-default command-edit" data-row-id=\"' + row.id_utente + '\"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></button>'+'<button type="button" title="dettagli corso" class="btn btn-xs btn-default command-edit-dettagli" data-row-id=\"' + row.id_utente + '\"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></button>';
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

            /* Executes after data is loaded and rendered */
            grid.find(".command-edit").on("click", function(e)
            {
                scelta = $(this).data("row-id");
                data= JSON.parse(fields[scelta]);
                $('#details_table tbody').empty();
                $.each(data, function (key, value) {
                    var eachrow = "<tr>" + "<td>" +  key + "</td>" + "<td>" +  value + "</td>" + "</tr>";
                    $('#details_table tbody').append(eachrow);

                });
                $("#details").modal('show');

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


    function dataSync() {
        jQuery.when(jQuery.get("index.php?option=com_gglms&task=report.sync"))
            .done(function(data){

            }).fail(function(data){

        });
    }

    function reload() {
        jQuery("#grid-basic").bootgrid("reload");
    }

</script>