<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
?>
<div id="barrafiltri" class="span2">

    <form class="form-inline" action="index.php">


        <h2>Corso</h2>
        <div class="form-group">
            <?php echo outputHelper::output_select('corso_id', $this->corsi, 'id_contenuto_completamento', 'titolo', null, 'refresh'); ?>
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
            </select>
        </div>

        <div class="form-group">
            <label for="startdate">Dal</label>
            <?php echo JHTML::_( 'calendar','','startdate','startdate','%Y-%m-%d'); ?>
        </div>

        <div class="form-group">
            <label for="finishdate">Al</label>
            <?php echo JHTML::_( 'calendar','','finishdate','finishdate','%Y-%m-%d'); ?>
        </div>

        <input type="hidden" id="option" name="option" value="com_gglms">
        <input type="hidden" id="task" name="task" value="api.get_csv">

        <div class="form-group">
            <button type="submit" id="get_csv" class="btn btn-success btn-lg">SCARICA CSV</button>
        </div>



    </form>

    <hr>

    <!--    <div class="span6">-->
    <canvas id="myChart" width="100" height="100"></canvas>
    <!--    </div>-->


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
                    <th data-column-id="data"  >Data</th>
                    <th data-column-id="fields" data-visible="false">Campi</th>

                    <th data-column-id="id_utente" data-visible="false" >Id</th>
                    <th data-column-id="commands" data-formatter="commands" data-sortable="false">Commands</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>
    <div class="row">
        <div id="" class="span2">
            <h2>Dettagli</h2>
            <table id="details" class="table table-condensed table-hover table-striped ">
                <thead> <tr> <th>Campo</th> <th>Valore</th> </tr> </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php
//echo "report aggiornato al :" .$this->state->get('params')->get('data_sync');
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

        $("#startdate").change(function(){
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
                "commands": function(column, row)
                {
                    fields[row.id_utente]=row.fields;
                    return '<button type="button" class="btn btn-xs btn-default command-edit" data-row-id=\"' + row.id_utente + '\">DETTAGLI<span class="fa fa-pencil"></span></button>';
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
                $('#details').html('');
                scelta = $(this).data("row-id");
                data= JSON.parse(fields[scelta]);
                $.each(data, function (key, value) {
                    var eachrow = "<tr>" + "<td>" +  key + "</td>" + "<td>" +  value + "</td>" + "</tr>";
                    $('#details').append(eachrow);
                });
            }).end();
        });
    });
</script>