<?php
defined('_JEXEC') or die;
?>

<div>
    <div id="barrafiltri" class="span2">

        <form id="theform" class="form-inline" action="index.php">

            <!--            <h3>Corso</h3>-->
            <div class="form-group">
                <label for="corso_id"><?php echo JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label><br>
                <select id="corso_id" name="corso_id" class="refresh">
                    <?php
                    foreach ($this->lista_corsi as $key => $corso) {
                        echo '<option value="' . $corso->value . '">' . $corso->text . "</option>";
                    }
                    ?>
                </select>

            </div>

            <hr>
            <h5 style="text-align: center"><?php echo JText::_('COM_GGLMS_GLOBAL_FILTRI') ?></h5>

            <?php
                // mostro il select azienda soltanto se ho multiple istanze
                echo outputHelper::buildFiltroAzienda($this->usergroups);
                ?>

            <div class="form-group" id="calendar_startdate_div">
                <label for="startdate"><?php echo JText::_('COM_GGLMS_REPORT_COMPLETATI_FROM') ?>:</label><br>
                <?php echo JHTML::calendar('', 'startdate', 'startdate', '%Y-%m-%d'); ?>
            </div>

            <div class="form-group" id="calendar_finishdate_div">
                <label for="finishdate"><?php echo JText::_('COM_GGLMS_REPORT_COMPLETATI_TO') ?>:</label><br>
                <?php echo JHTML::_('calendar', '', 'finishdate', 'finishdate', '%Y-%m-%d'); ?>


            </div>

            <div class="form-group" id="searchPhrase_div">
                <label for="searchPhrase"><?php echo JText::_('COM_GGLMS_GLOBAL_SEARCH') ?>:</label><br>
                <input type="text" id="searchPhrase" placeholder="<?php echo JText::_('COM_GGLMS_QUIZ_ORE_CERCA_PER') ?>">
            </div>


            <input type="hidden" id="option" name="option" value="com_gglms">
            <input type="hidden" id="task" name="task" value="api.get_csv">

            <div class="form-group">
                <button type="button" id="update" class="width100 btn"
                        onclick="reload()"><?php echo JText::_('COM_GGLMS_REPORT_AGGIORNA') ?></button>
            </div>

        </form>

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

<div id="cover-spin"></div>

<script type="text/javascript">


    //CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI CONFIGURAZIONI
    var loadreportoffset = 0;
    var loadreportlimit = 15;

    var actualminpage = 1;
    var columnfilter = ['quiz_ref', 'quiz_id', 'student_id'];//CAMPI DA NON MOSTRARE IN TABELLA
    var columnmappingname = [{name: 'data_inizio', alias: 'data inizio'},
        {name: 'data_fine', alias: 'data fine'}];
    var buttonscolumn = ['quiz_id'];//CAMPO CHE SI TRASFORMA IN PULSANTE
    var buttonscolumnname = ['ANNULLA'];//CAMPO CHE DA IL NOME AL PULSANTE
    var buttonkeyidfield = ['quiz_id'];//CHIAVE DI ASSOCIAZIONE AL PULSANTE

    var maxNofpages;
    var viewReportColumns;
    var fields = [];

    jQuery(document).ready(function ($) {

        window.console.log('document ready');
        // default is by corso
        //jQuery('#filterstatodiv').show();
        //jQuery('#calendar_startdate_div').hide();
        //jQuery('#calendar_finishdate_div').hide();
        loadData(null);

        //  TABELLA
        $(".refresh").change(function () {

            notcompleted = 0;
            completed = 0;
            loadData(null);
        });

        /*
        $("#tipo_report").change(function () {

            if ($("#tipo_report option:selected").val() == 0) {


                $("#filterstatodiv").show();

                if ($("#filterstatodiv option:selected").val() == 1) {
                    $("#calendar_startdate_div").show();
                    $("#calendar_finishdate_div").show();
                }

            } else {
                $("#filterstatodiv").hide();
                $("#calendar_startdate_div").hide();
                $("#calendar_finishdate_div").hide();
            }

        });
        */

        /*
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
        */

        $("#startdate").bind('change', function () {

            notcompleted = 0;
            completed = 0;
            loadData(null);
        });

        $("#finishdate").change(function () {

            notcompleted = 0;
            completed = 0;
            loadData(null);
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
                loadreportoffset = (parseInt(jQuery(this).html()) * loadreportlimit) - loadreportlimit;
                loadData("pagination");
        }
    });


    function loadData(sender) {


        var url = "index.php?option=com_gglms&task=api.get_user_quiz_per_azienda_corso&corso_id=" + jQuery("#corso_id").val();
        url = url + "&startdate=" + jQuery("#startdate").val();
        url = url + "&finishdate=" + jQuery("#finishdate").val();
        url = url + "&usergroups=" + jQuery("#usergroups").val();
        url = url + "&searchPhrase=" + jQuery("#searchPhrase").val();

        if (sender != 'pagination') {
            jQuery("a[data-page='1']").html('1');
            jQuery("a[data-page='2']").html('2');
            jQuery("a[data-page='3']").html('3');
            jQuery("a[data-page='4']").html('4');
            jQuery("a[data-page='5']").html('5');
            actualminpage = 1;
            url = url + "&limit=" + loadreportlimit;
        } else {
            url = url + "&limit=" + loadreportlimit;
        }

        url = url + "&offset=" + loadreportoffset;
        jQuery('#cover-spin').show(0);


        jQuery.when(jQuery.get(url))
            .done(function (data) {

            })
            .fail(function (data) {

            })
            .then(function (data) {
                jQuery('#cover-spin').hide(0);

                data = JSON.parse(data);

                console.log('data', data);

                jQuery('#grid-basic').empty();
                jQuery('#totalcount').empty();
                var totCount = Joomla.JText._('COM_GGLMS_GLOBAL_RECORD');
                jQuery('#totalcount').html(totCount + ':' + data['rowCount']);
                viewReportColumns = [];
                fields = data;
                maxNofpages = parseInt((data['rowCount'] / loadreportlimit) + 1);
                data['columns'].forEach(addColumn);

                if (buttonscolumn.length > 0) {

                    jQuery.each(buttonscolumn, function (i, item) {

                        var text = Joomla.JText._("COM_GGLMS_REPORT_" + buttonscolumnname[i].toString().toUpperCase()) || buttonscolumnname[i];
                        // console.log('rincomincia da quiiiii', text);

                        jQuery('#grid-basic').append('<th>' + text.toUpperCase() + '</th>');
                        viewReportColumns.push(buttonscolumnname[i]);
                    });

                }

                for (i = 0; i < data['rows'].length; i++) {

                    var row = data['rows'][i];
                    //fields[row['id_anagrafica']]=JSON.parse(row['fields']);

                    jQuery('#grid-basic').append('<tr class=\'' + defineRowBootClass(row) + '\'>');

                    for (ii = 0; ii < viewReportColumns.length; ii++) {

                        addCell(jQuery('#grid-basic tr:last'),
                            row,
                            row[data['columns'][data['columns'].indexOf(viewReportColumns[ii])]],
                            i,
                            ii,
                            jQuery("#tipo_report").val(),
                            viewReportColumns);
                    }

                }

            });

    }

    function removeUserQuiz(delCid, callback) {

        var url = "index.php?option=com_gglms&task=api.del_user_quiz&cid=" + delCid;
        jQuery.when(jQuery.get(url))
            .done(function (data) {

            })
            .fail(function (data) {

            })
            .then(function (data) {

                var j = JSON.parse(data);
                var errMsg = "";
                var infoMsg = "";

                if (j.errors != undefined) {
                    errMsg = j.errors;
                }
                if (j.messages != undefined) {
                    jQuery.each(j.messages, function(i, item) {
                        infoMsg += j.messages[i] + '\n';
                    });
                }

                callback(errMsg, infoMsg);
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

        var stiletd = 'border-left: 1px solid #ddd;';
        var stiletdcenter = " text-align:center;";
        //SET OF RULES

        if (rowCellData == '1'
            && columIndex == 5) {

            rowCellData = "<span title='Completato' class='glyphicon glyphicon-ok' style='color:green; font-size: 20px;'></span>";
            stiletd = stiletd + stiletdcenter;
        }

        else if (rowCellData == '0'
                    && columIndex == 5) {

            rowCellData = "<span title='Non completato' class='glyphicon glyphicon-log-in' style='font-size: 20px;'></span>";
            stiletd = stiletd + stiletdcenter;
        }

        else if (rowCellData == '0000-00-00') {

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
            var columnname = item.toString();
            // console.log(columnname, 'aaaaaaaaaaaaa');
            if (!columnname.includes('_hidden')) {

                columnname = Joomla.JText._("COM_GGLMS_QUIZ_" + item.toString().toUpperCase()) || columnname;
            }

            //NASCONDO LE COLONNE CHE HANNO _HIDDEN NEL NOME
            if (!columnname.includes('_hidden') && !columnname.includes('no_column')) {
                jQuery('#grid-basic').append('<th ' + classtouse + '>' + columnname.toUpperCase() + '</th>');
                viewReportColumns.push(item);
            }

        }
    }

    function addButtonsCell(row, _index) {

        rowCellData = '';


        var btnColumnName = buttonscolumnname[_index];
        if (btnColumnName == "ANNULLA") {
            rowCellData = rowCellData + "<button id='columnbutton'";
            rowCellData = rowCellData + " type='button' class=\"btn btn-xs btn-default command-edit\" data-row=\"";
            rowCellData = rowCellData + "\" onclick=playbutton(" + row[buttonkeyidfield[_index]] + ",'" + buttonscolumn[_index] + "') ><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\" title='" + buttonscolumn[_index].toString() + "'></span></button>";
        }

        return rowCellData;

    }

    function reload() {
        loadData(null);
    }

    function playbutton(delCid, field) {

        var confText = Joomla.JText._('COM_GGLMS_QUIZ_RIMOZIONE_UTENTE');
        var r = confirm(confText);
        if (r) {
            jQuery('#cover-spin').show(0);
            removeUserQuiz(delCid, function(errMsg, infoMsg) {
                jQuery('#cover-spin').hide(0);

                if (errMsg != "")
                    alert(errMsg);
                else {
                    if (infoMsg != "")
                        alert(infoMsg);

                    loadData();
                }
            });
        }
    }


</script>
