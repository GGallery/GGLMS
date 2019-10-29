<?php
defined('_JEXEC') or die;
?>

<div>
    <div id="barrafiltri" class="span2">

        <form id="theform" class="form-inline" action="index.php">


            <!--            <h3>Corso</h3>-->
            <div class="form-group">
                <label for="corso_id">Corso:</label><br>
                <?php //echo outputHelper::output_select('corso_id', $this->corsi, 'id_contenuto_completamento', 'titolo', null, 'refresh'); ?>
                <select id="corso_id" name="corso_id" class="refresh">
                    <?php
                    foreach ($this->corsi as $corso) {

                        echo '<option value="' . $corso->id . '|' . $corso->id_contenuto_completamento . '">' . $corso->titolo . "</option>";
                    }
                    ?>
                </select>

            </div>

            <!--            <h4>Tipo Report</h4>-->
            <div class="form-group">
                <label for="tipo_report">Tipo Report:</label><br>
                <select id="tipo_report" name="tipo_report" class="refresh">
                    <option selected value="0">per Corso</option>
                    <option value="1">per Unità</option>
                    <option value="2">per Contenuto</option>
                </select>

            </div>


            <hr>
            <h5 style="text-align: center">Filtri</h5>

            <div class="form-group">
                <label for="usergroups">Azienda:</label><br>
                <?php echo outputHelper::output_select('usergroups', $this->usergroups, 'id', 'title', 2, 'refresh'); ?>
            </div>

            <div class="form-group" id="filterstatodiv">
                <label for="filterstato">Stato corso</label>
                <select id="filterstato" name="filterstato" class="refresh">
                    <option value="0">Qualisiasi stato</option>
                    <option value="1">Solo completati</option>
                    <option value="2">Solo NON compleati</option>
                    <option value="3">In scadenza</option>
                </select>
            </div>

            <div class="form-group" id="calendar_startdate_div">
                <label for="startdate">Completato dal:</label><br>
                <?php echo JHTML::calendar('', 'startdate', 'startdate', '%Y-%m-%d'); ?>
            </div>

            <div class="form-group" id="calendar_finishdate_div">
                <label for="finishdate">Completato al:</label><br>
                <?php echo JHTML::_('calendar', '', 'finishdate', 'finishdate', '%Y-%m-%d'); ?>


            </div>


            <div class="form-group" id="searchPhrase_div">
                <label for="searchPhrase">Cerca:</label><br>
                <input type="text" id="searchPhrase">
            </div>


            <input type="hidden" id="option" name="option" value="com_gglms">
            <input type="hidden" id="task" name="task" value="api.get_csv">

            <div class="form-group">
                <button type="button" id="update" class="width100 btn" onclick="reload()">AGGIORNA DATI</button>
                <button type="button" id="get_csv" class="btn width100" onclick="loadCsv()">SCARICA REPORT CSV</button>
            </div>
            <!--
            <div class="form-group">
                <button type="button" id="get_csv" class="btn btn-warning btn-lg width100" onclick="sendAllMail()">INVIA MAIL IN SCADENZA</button>
            </div>

            <div>
                <button type="button" class="btn btn-info btn-lg width100" onclick="dataSyncUsers()">SINCRONIZZA TABELLA REPORT</button>
            </div>
    -->
            <!--        <div class="form-group">-->
            <!--        -->
            <!--        </div>-->
        </form>

        <hr>
        <?php
        echo "Report aggiornato al :" . $this->state->get('params')->get('data_sync');
        ?>

        <!--        <canvas id="myChart" width="100" height="100" style="visibility: hidden"></canvas>-->


    </div>
    <div id="contenitoreprincipale" class="span8">

        <div class="row">
            <div class="grid-container">

                <table id="grid-basic" class="table table-condensed table-hover table-striped ">

                </table>

                <div class="col-sm-6">
                    <ul class="pagination">
                        <li class="first" aria-disabled="true">
                            <a data-page="first" class="button">«</a></li>
                        <li class="prev" aria-disabled="true">
                            <a data-page="prev" class="button">&lt;</a></li>
                        <li class="page-1" aria-disabled="false" aria-selected="false">
                            <a data-page="1" class="button">1</a></li>
                        <li class="page-2" aria-disabled="false" aria-selected="false">
                            <a data-page="2" class="button">2</a></li>
                        <li class="page-3" aria-disabled="false" aria-selected="false">
                            <a data-page="3" class="button">3</a></li>
                        <li class="page-4" aria-disabled="false" aria-selected="false">
                            <a data-page="4" class="button">4</a></li>
                        <li class="page-5" aria-disabled="false" aria-selected="false">
                            <a data-page="5" class="button">5</a></li>
                        <li class="next" aria-disabled="false">
                            <a data-page="next" class="button">&gt;</a></li>
                        <li class="last" aria-disabled="false">
                            <a data-page="last" class="button">»</a></li>
                        <li class="last" aria-disabled="false">
                            <span id="totalcount"></span></li>
                    </ul>

                </div>

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
                    <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Valore</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <!--                <button type="button" class="tn btn-success btn-lg" onclick="loadLibretto()"-->
                <!--                        style="font-size:12px;padding:4px;position:ABSOLUTE;left:4%;">Libretto Formativo-->
                <!--                </button>-->

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
                    <thead>
                    <tr>
                        <th>Titolo unità</th>
                        <th>Titolo contenuto</th>
                        <th>stato</th>
                        <th>data</th>
                    </tr>
                    </thead>
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


<!-- Modal Dettagli Aggiornamento Report-->
<div id="aggiornamentoReport" class="modal fade " role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-body">
                caricamento dati...
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
                <textarea cols="50" rows="5" id="testomail" style="width: 560px;"></textarea>
            </div>
            <div class="modal-footer">
                <button id="sendmailbutton" type="button" class="btn btn-success btn-lg" onclick="sendMail()">Invia
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<script type="text/javascript">


    //CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI
    var testo_base_mail = '<?php echo $this->state->get('params')->get('alert_mail_text'); ?>';
    var loadreportlimit = 0;
    var loadreportoffset = 15;
    var actualminpage = 1;
    var columnfilter = ['id_anagrafica', 'scadenza', 'fields'];//CAMPI DA NON MOSTRARE IN TABELLA
    var columnmappingname = [{name: 'data_inizio', alias: 'data inizio'},
        {name: 'data_fine', alias: 'data fine'}];
    var buttonscolumn = ['fields', 'attestato'];//CAMPO CHE SI TRASFORMA IN PULSANTE
    var buttonscolumnname = ['DETTAGLI', 'ATTESTATI'];//CAMPO CHE DA IL NOME AL PULSANTE
    var buttonkeyidfield = ['id_anagrafica', 'attestati'];//CHIAVE DI ASSOCIAZIONE AL PULSANTE


    var user_detail_columns = ['id', 'cb_cognome',
        'cb_codicefiscale',
        'cb_datadinascita',
        'cb_luogodinascita',
        'cb_provinciadinascita',
        'cb_indirizzodiresidenza',
        'cb_provdiresidenza',
        'cb_cap',
        'cb_telefono',
        'cb_nome',
        'username',
        'email',
        'registerDate',
        'lastvisitDate'];

    var maxNofpages;
    var viewReportColumns;
    var fields = [];


    jQuery(document).ready(function ($) {

        window.console.log('document ready');
        // default is by corso
        jQuery('#filterstatodiv').show();
        jQuery('#calendar_startdate_div').hide();
        jQuery('#calendar_finishdate_div').hide();
        loadData(null);

//        TORTA
//         var ctx = document.getElementById("myChart").getContext('2d');
//         var notcompleted = 0;
//         var completed = 0;
        // var myChart = new Chart(ctx, {
        //     type: 'pie',
        //     data: {
        //         labels: ["Utenti che hanno completato", "Utenti che non hanno completato"],
        //         datasets: [{
        //             label: '% corsi completati',
        //             data: [completed, notcompleted],
        //             backgroundColor: [
        //                 'rgba(0, 123, 132, 0.2)',
        //                 'rgba(128, 162, 25, 0.2)'
        //             ],
        //             borderColor: [
        //                 'rgba(0,123,132,1)',
        //                 'rgba(128, 162, 25, 1)'
        //
        //             ],
        //             borderWidth: 5
        //         }]
        //     },
        //
        // });


        //  TABELLA
        $(".refresh").change(function () {

            notcompleted = 0;
            completed = 0;
            loadData(null);
            //$("#grid-basic").bootgrid("reload");
        });

        $("#tipo_report").change(function () {

            if ($("#tipo_report option:selected").val() == 0) {


                $("#filterstatodiv").show();
                $("#calendar_startdate_div").show();
                $("#calendar_finishdate_div").show();
            } else {
                $("#filterstatodiv").hide();
                $("#calendar_startdate_div").hide();
                $("#calendar_finishdate_div").hide();
            }

        });

        $("#filterstato").change(function () {


            if ($("#filterstato option:selected").val() == 1) {
                // solo completati
                $("#calendar_startdate_div").show();
                $("#calendar_finishdate_div").show();
            } else {
                $("#calendar_startdate_div").hide();
                $("#calendar_finishdate_div").hide();
            }
        });

        $("#startdate").bind('change', function () {

            notcompleted = 0;
            completed = 0;
            loadData(null);
            //$("#grid-basic").bootgrid("reload");
        });

        $("#finishdate").change(function () {

            notcompleted = 0;
            completed = 0;
            loadData(null);
            //$("#grid-basic").bootgrid("reload");
        });


    });

    jQuery('.button').click(function () {

        switch (jQuery(this).attr('data-page')) {

            case 'first':
                jQuery("a[data-page='1']").html('1');
                jQuery("a[data-page='2']").html('2');
                jQuery("a[data-page='3']").html('3');
                jQuery("a[data-page='4']").html('4');
                jQuery("a[data-page='5']").html('5');
                actualminpage = 1;
                break;

            case 'prev':
                if (actualminpage > 1) {
                    jQuery("a[data-page='1']").html(parseInt(jQuery("a[data-page='1']").html()) - 1);
                    jQuery("a[data-page='2']").html(parseInt(jQuery("a[data-page='2']").html()) - 1);
                    jQuery("a[data-page='3']").html(parseInt(jQuery("a[data-page='3']").html()) - 1);
                    jQuery("a[data-page='4']").html(parseInt(jQuery("a[data-page='4']").html()) - 1);
                    jQuery("a[data-page='5']").html(parseInt(jQuery("a[data-page='5']").html()) - 1);
                    actualminpage--;
                }
                break;

            case 'next':


                jQuery("a[data-page='1']").html(parseInt(jQuery("a[data-page='1']").html()) + 1);
                jQuery("a[data-page='2']").html(parseInt(jQuery("a[data-page='2']").html()) + 1);
                jQuery("a[data-page='3']").html(parseInt(jQuery("a[data-page='3']").html()) + 1);
                jQuery("a[data-page='4']").html(parseInt(jQuery("a[data-page='4']").html()) + 1);
                jQuery("a[data-page='5']").html(parseInt(jQuery("a[data-page='5']").html()) + 1);
                actualminpage++;
                break;

            case 'last':

                jQuery("a[data-page='1']").html(maxNofpages - 4);
                jQuery("a[data-page='2']").html(maxNofpages - 3);
                jQuery("a[data-page='3']").html(maxNofpages - 2);
                jQuery("a[data-page='4']").html(maxNofpages - 1);
                jQuery("a[data-page='5']").html(maxNofpages);
                actualminpage = maxNofpages - 4;
                break;

            default:
                loadreportlimit = (parseInt(jQuery(this).html()) * loadreportoffset) - loadreportoffset;
                loadData("pagination");
        }
    });


    function loadData(sender) {


        var url = "index.php?option=com_gglms&task=api.get_report&corso_id=" + jQuery("#corso_id").val();
        url = url + "&startdate=" + jQuery("#startdate").val();
        url = url + "&finishdate=" + jQuery("#finishdate").val();
        url = url + "&filterstato=" + jQuery("#filterstato").val();
        url = url + "&usergroups=" + jQuery("#usergroups").val();
        url = url + "&tipo_report=" + jQuery("#tipo_report").val();
        url = url + "&searchPhrase=" + jQuery("#searchPhrase").val();

        if (sender != 'pagination') {
            jQuery("a[data-page='1']").html('1');
            jQuery("a[data-page='2']").html('2');
            jQuery("a[data-page='3']").html('3');
            jQuery("a[data-page='4']").html('4');
            jQuery("a[data-page='5']").html('5');
            actualminpage = 1;
            url = url + "&limit=0";
        } else {
            url = url + "&limit=" + loadreportlimit;
        }

        url = url + "&offset=" + loadreportoffset;
        jQuery("#aggiornamentoReport").modal('show');
        jQuery.when(jQuery.get(url))
            .done(function (data) {

            })
            .fail(function (data) {

            })
            .then(function (data) {


                data = JSON.parse(data);

                console.log('data', data);

                jQuery('#grid-basic').empty();
                jQuery('#totalcount').empty();
                jQuery('#totalcount').html('record totali:' + data['rowCount']);
                viewReportColumns = [];
                fields = data;
                maxNofpages = parseInt((data['rowCount'] / loadreportoffset) + 1);
                jQuery("#aggiornamentoReport").modal('hide');
                data['columns'].forEach(addColumn);
                if (buttonscolumn.length > 0) {

                    jQuery.each(buttonscolumn, function (i, item) {

                        // console.log(item);
                        jQuery('#grid-basic').append('<th>' + buttonscolumnname[i] + '</th>');
                        viewReportColumns.push(buttonscolumnname[i]);
                    });

                }
                for (i = 0; i < data['rows'].length; i++) {

                    var row = data['rows'][i];
                    //fields[row['id_anagrafica']]=JSON.parse(row['fields']);

                    jQuery('#grid-basic').append('<tr class=\'' + defineRowBootClass(row) + '\'>');

                    for (ii = 0; ii < viewReportColumns.length; ii++) {

                        addCell(jQuery('#grid-basic tr:last'), row, row[data['columns'][data['columns'].indexOf(viewReportColumns[ii])]], i, ii, jQuery("#tipo_report").val(), viewReportColumns)
                    }

                }

            });

    }

    function defineRowBootClass(row) {

        if (row['scadenza'] == 1) {

            return 'warning';
        }
        if (row['stato'] == 1) {

            return 'success';
        }

    }

    function addCell(table, row, rowCellData, rowindex, columIndex, viewType, dataColumns) {

        stiletd = 'border-left: 1px solid #ddd;';
        stiletdcenter = " text-align:center;";
        //SET OF RULES

        if (rowCellData == '1') {

            rowCellData = "<span title='completato' class='glyphicon glyphicon-ok' style='color:green; font-size: 20px;'></span>";
            stiletd = stiletd + stiletdcenter;
        }

        if (rowCellData == '0') {

            rowCellData = "<span title='iniziato' class='glyphicon glyphicon-log-in' style='font-size: 20px;'></span>";
            stiletd = stiletd + stiletdcenter;
        }

        if (rowCellData == '0000-00-00') {

            rowCellData = ""
        }


        if (jQuery.inArray(dataColumns[columIndex], buttonscolumnname) > -1) {


            rowCellData = addButtonsCell(row, jQuery.inArray(dataColumns[columIndex], buttonscolumnname));
        }


        switch (viewType) {

            case '0':
                break;
            case '1':
            case '2':


                break;
        }

        table.append("<td  style='" + stiletd + "'>" + rowCellData + "</td>");
    }

    function addColumn(item, index) {


        if (columnfilter.indexOf(item) == -1) {

            switch (jQuery("#tipo_report").val()) {

                case '2':

                    //classtouse="class=rotated";
                    break;
                default:
                    classtouse = "";
                    break;
            }
            columnname = item.toString();
            for (var i = 0; i < columnmappingname.length; i++) {

                if (columnmappingname[i]['name'] == item) {
                    columnname = columnmappingname[i]['alias'];
                }
            }
            //if(columnmappingname.filter(c=>c.name==item).length>0){
            //     columnname= columnmappingname.filter(c=>c.name==item)[0]['alias'];

            //}else {

            //}


            //NASCONDO LE COLONNE CHE HANNO _HIDDEN NEL NOME
            if (!columnname.includes('_hidden')) {
                jQuery('#grid-basic').append('<th ' + classtouse + '>' + columnname.toUpperCase() + '</th>');
                viewReportColumns.push(item);
            }

        }
    }

    function addButtonsCell(row, _index) {

        rowCellData = '';


        var btnColumnName = buttonscolumnname[_index];
        if (btnColumnName == "DETTAGLI") {
            rowCellData = rowCellData + "<button id='columnbutton'";
            rowCellData = rowCellData + " type='button' class=\"btn btn-xs btn-default command-edit\" data-row=\"";
            //rowCellData = rowCellData+rowindex;
            rowCellData = rowCellData + "\" onclick=playbutton(" + row[buttonkeyidfield[_index]] + ",'" + buttonscolumn[_index] + "') ><span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\" title='" + buttonscolumn[_index].toString() + "'></span></button>";
        }

        // non mostro genera attestao se la visualizzazione non è per corso
        if (btnColumnName == "ATTESTATI" && jQuery("#tipo_report option:selected").val() == 0) {
            // console.log('row', row);
            var disabled = row.stato == 1 ? false : true;

            // console.log('fields',JSON.parse(row['fields']));

            var user_id = JSON.parse(row['fields']).user_id;

            // creo un bottone per ogni attestato
            var att_array = row.attestati_hidden.split('|');

            // se ci sono attestati
            if (att_array.length > 0 && att_array[0] != "") {

                jQuery.each(att_array, function (i, content_info) {


                    var content_id = content_info.split('#')[0];
                    var content_title = content_info.split('#')[1];
                    var url = "index.php?option=com_gglms&task=reportutente.generateAttestato&content_id=" + content_id + "&user_id=" + user_id;
                    if (!disabled) {
                        rowCellData = rowCellData + "<a target='_blank' type='button' title='"+ content_title + "' class='btn btn-xs btn-default command-edit' href='" + url + "' >";
                    } else {
                        rowCellData = rowCellData + "<a target='_blank' disabled type='button' style='display: none' href='" + url + "' >";
                    }
                    rowCellData = rowCellData + "<span class='glyphicon glyphicon-save-file'></span></a>"

                });
            }


        }


        //jQuery(rowCellData).append("<span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\"></span>");
        // }<
        return rowCellData;
    }


    function playbutton(searchkey, field) {
        var id;
        for (var i = 0; i < fields['rows'].length; i++) {


            if (fields['rows'][i][buttonkeyidfield[0]] == searchkey) {

                jQuery('#details_table tbody').empty();
                jQuery.each(JSON.parse(fields['rows'][i][field]), function (key, value) {


                    if (jQuery.inArray(key, user_detail_columns) > -1) {
                        var eachrow = "<tr>" + "<td>" + key + "</td>" + "<td>" + value + "</td>" + "</tr>";

                        if (key == "id") {
                            id = value;
                        }
                        jQuery('#details_table tbody').append(eachrow);
                    }

                });

                jQuery("#details").append('<input id=modal_id_utente type=hidden value=' + id + '>');
                jQuery("#details").modal('show');
            }
        }

    }


    function reload() {

        loadData(null);
        //jQuery("#grid-basic").bootgrid("reload");
    }

    function loadCsv() {
        var url = "index.php?option=com_gglms&task=api.get_csv&corso_id=" + jQuery("#corso_id").val();
        url = url + "&startdate=" + jQuery("#startdate").val();
        url = url + "&finishdate=" + jQuery("#finishdate").val();
        url = url + "&filterstato=" + jQuery("#filterstato").val();
        url = url + "&usergroups=" + jQuery("#usergroups").val();
        url = url + "&tipo_report=" + jQuery("#tipo_report").val();
        url = url + "&searchPhrase=" + jQuery("#searchPhrase").val();

        location.href = url;

    }


    function sendMail() {

        oggettomail = jQuery('#oggettomail').val();
        testomail = jQuery('#testomail').val();
        //to=jQuery('#to').html(); ATTENZIONE QUESTA RIGA IN PRODUZIONE ANDRA' SCOMMENTATA
        to = "a.petruzzella71@gmail.com";
        jQuery.when(jQuery.get("index.php?to=" + to + "&oggettomail=" + oggettomail + "&testomail=" + testomail + "&option=com_gglms&task=api.sendMail"))

            .done(function (data) {

                result = JSON.parse(data);

                if (result == true) {
                    jQuery('#sendmailbutton').hide();
                    jQuery('#div_send_mail_textarea').hide();
                    jQuery('#details_table_invio_mail tbody').append('<tr><td>email inviata con successo, puoi chiudere questa finestra</td><tr>');
                }
            }).fail(function (data) {

        });

    }

    function sendAllMail() {


        nome_corso = jQuery('#corso_id option:selected').text();
        oggettomail = jQuery('#oggettomail').val();
        testomail = testo_base_mail + nome_corso;
        var id_corso = jQuery('#corso_id')[0]['value'];
        var usergroups = jQuery('#usergroups')[0]['value'];
        jQuery.when(jQuery.get("index.php?corso_id=" + id_corso + "&usergroups=" + usergroups + "&oggettomail=" + oggettomail + "&testomail=" + testomail + "&option=com_gglms&task=api.sendAllMail"))

            .done(function (data) {

            }).fail(function (data) {

        });

    }

    function loadLibretto() {

        var user_id = jQuery('#modal_id_utente').val();


        location.href = window.location + '../../libretto.html?user_id=' + user_id


    }


</script>
