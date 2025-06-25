<?php
defined('_JEXEC') or die;
?>

<style>
    table.table-bordered thead th {
        background-color: white !important;
        text-align: center;
    }

    .table th, .table td {
        text-align: center !important;
        line-height: 14px !important;
    }

    .detail-icon {
        color: #337ab7 !important;
    }

    .dropdown-toggle {
        background-color: #337ab7 !important;
        border: 1px solid #337ab7 !important;
    }

    #toolbar {
        background-color: #f8f9fa;
        border: 1px solid #007bff;
    }

    #toolbar h4 {
        color: #325482;
    }

    #toolbar .btn {
        background-color: #325482;
        color: white;
        border: none;
    }

</style>

<div class="container-fluid">
    <div id="toolbar" class="container-fluid p-4 border rounded shadow-sm bg-light">
        <h4 class="text-primary mb-3"><?php echo JText::_('COM_GGLMS_GLOBAL_FILTRI') ?></h4>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="corso_id"><?php echo JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label>
                <select id="corso_id" name="corso_id" class="form-control">
                    <?php foreach ($this->corsi as $corso): ?>
                        <option value="<?= $corso->id . '|' . $corso->id_contenuto_completamento ?>">
                            <?= $corso->titolo ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="filterstatodiv" class="form-group col-md-6">
                <label for="filterstato"><?php echo JText::_('COM_GGLMS_GLOBAL_STATO') ?></label>
                <select id="filterstato" name="filterstato" class="form-control">
                    <option value="0"><?php echo JText::_('COM_GGLMS_GLOBAL_STATO_ANY') ?></option>
                    <option value="1"><?php echo JText::_('COM_GGLMS_REPORT_COMPLETATI') ?></option>
                    <option value="2"><?php echo JText::_('COM_GGLMS_REPORT_NON_COMPLETATI') ?></option>
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-2">
                <button type="button" id="export_csv" class="btn btn-primary w-100">
                    <?php echo JText::_('COM_GGLMS_GLOBAL_EXPORT_CSV') ?>
                </button>
            </div>

            <div class="form-group col-md-2">
                <button type="button" id="btn_cerca" class="btn btn-primary w-100">
                    <?php echo JText::_('COM_GGLMS_REPORT_AGGIORNA') ?>
                </button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col text-right" id="upd_at">
                <?php echo JText::_('COM_GGLMS_REPORT_UPDATED_AT') ?>
            </div>
        </div>
    </div>


    <table
            id="tbl_report"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-side-pagination="server"
            data-pagination="true"
            data-page-size="5"
            data-show-export="true"
            data-detail-view="true"
            data-force-hide="true"
            data-detail-formatter="detailFormatter"
            data-page-list="[5, 10, 20, 50, 100, All]"
    >
        <thead>
        <tr>
            <th data-field="cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_REPORT_COGNOME'); ?></th>
            <th data-field="nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_REPORT_NOME'); ?></th>
            <th data-class="showColumn" data-field="stato" ><?php echo JText::_('COM_GGLMS_REPORT_STATO'); ?></th>
            <th data-class="showColumn" data-field="data_inizio" ><?php echo JText::_('COM_GGLMS_REPORT_DATA_INIZIO'); ?></th>
            <th data-class="showColumn" data-field="data_fine" ><?php echo JText::_('COM_GGLMS_REPORT_DATA_FINE'); ?></th>
            <th data-class="showColumn" data-field="Introduzione" ><?php echo JText::_('COM_GGLSM_REPORT_INTRODUZIONE'); ?></th>
            <th data-class="showColumn" data-field="Soggetti aziendali: diritti e doveri" ><?php echo JText::_('COM_GGLMS_REPORT_SOGGETTI_AZIENDALI'); ?></th>
            <th data-class="showColumn" data-field="Test intermedio n. 1" ><?php echo JText::_('COM_GGLMS_REPORT_TEST_INTERMEDIO'); ?></th>
            <th data-class="showColumn" data-field="Valutazione dei rischi e sorveglianza sanitaria" >Valutazione Dei Rischi E Sorveglianza Sanitaria</th>
            <th data-class="showColumn" data-field="I concetti di rischio,pericolo e danno" >I Concetti Di Rischio,Pericolo E Danno</th>
            <th data-class="showColumn" data-field="Test intermedio n. 2" >Test Intermedio N. 2"</th>
            <th data-class="showColumn" data-field="DVR, misure di prevenzione e protezione" >DVR, Misure Di Prevenzione E Protezione</th>
            <th data-class="showColumn" data-field="Obblighi dei lavoratori e DPI" >Obblighi Dei Lavoratori E DPI</th>
            <th data-class="showColumn" data-field="Formazione, informazione e addestramento" >Formazione, Informazione E Addestramento</th>
            <th data-class="showColumn" data-field="La gestione della prevenzione nei luoghi di lavoro" >La Gestione Della Prevenzione Nei Luoghi Di Lavoro</th>
            <th data-class="showColumn" data-field="Test intermedio n. 3" >Test Intermedio N. 3</th>
            <th data-class="showColumn" data-field="Una visita ispettiva" >Una Visita Ispettiva"</th>
            <th data-class="showColumn" data-field="Organi di vigilanza" >Organi Di Vigilanza</th>
            <th data-class="showColumn" data-field="Il sopralluogo e la gestione delle emergenze" >Il Sopralluogo E La Gestione Delle Emergenze</th>
            <th data-class="showColumn" data-field="Il regime sanzionatorio" >Il Regime Sanzionatorio</th>
            <th data-class="showColumn" data-field="Conclusioni" >Conclusioni</th>
            <th data-class="showColumn" data-field="Test intermedio n. 4" >Test Intermedio N. 4"</th>
            <th data-class="showColumn" data-field="Approfondimento: i fondamenti giuridici" >Approfondimento: I Fondamenti Giuridici</th>
            <th data-class="showColumn" data-field="Test intermedio n. 5" >Test Intermedio N. 5</th>
            <th data-class="showColumn" data-field="Slide scaricabili" >Slide Scaricabili</th>
            <th data-class="showColumn" data-field="TEST FINALE - Formazione generale" >TEST FINALE - Formazione Generale</th>
            <th data-class="showColumn" data-field="Questionario di valutazione della qualità" >Questionario Di Valutazione Della Qualità</th>
            <th data-field="cb_codicefiscale" ><?php echo JText::_('COM_GGLMS_REPORT_CODICE_FISCALE'); ?></th>
            <th data-field="cb_azienda" ><?php echo JText::_('COM_GGLSM_REPORT_AZIENDA'); ?></th>
            <th data-field="attestati_hidden" >Attestato 1</th>
            <th data-field="Attestato" >Attestato 2</th>
        </tr>
        </thead>
    </table>

<!--    tabella dettaglio utante-->
   <table id="detailTable">
    </table>



<script type="text/javascript">


    jQuery('#calendar_startdate_div').hide();
    jQuery('#calendar_finishdate_div').hide();
    var pTable = jQuery('#tbl_report');


    function ajaxRequest(params) {

        // preparo i params

        var pCorso = jQuery('#corso_id').val();
        params.data.corso_id = parseInt(pCorso);

        params.data.startdate = jQuery('#startdate').val();

        params.data.finishdate =jQuery('#finishdate').val();

        var pStato = jQuery('#filterstato').val();
        params.data.filterstato = parseInt(pStato);

        //params.data.usergroups = jQuery('#usergroups').val().trim();


        //var pTipo = jQuery('#tipo_report').val().trim();
        //params.data.tipo_report = parseInt(pTipo);


            pTable.bootstrapTable('showColumn', 'stato');
            pTable.bootstrapTable('showColumn', 'data_inizio');
            pTable.bootstrapTable('showColumn', 'data_fine');
            pTable.bootstrapTable('hideColumn', 'Introduzione');
            pTable.bootstrapTable('hideColumn', 'Soggetti aziendali: diritti e doveri');
            pTable.bootstrapTable('hideColumn', 'Test intermedio n. 1');
            pTable.bootstrapTable('hideColumn', 'Valutazione dei rischi e sorveglianza sanitaria');
            pTable.bootstrapTable('hideColumn', 'I concetti di rischio,pericolo e danno');
            pTable.bootstrapTable('hideColumn', 'Test intermedio n. 2');
            pTable.bootstrapTable('hideColumn', 'DVR, misure di prevenzione e protezione');
            pTable.bootstrapTable('hideColumn', 'Obblighi dei lavoratori e DPI');
            pTable.bootstrapTable('hideColumn', 'Formazione, informazione e addestramento');
            pTable.bootstrapTable('hideColumn', 'La gestione della prevenzione nei luoghi di lavoro');
            pTable.bootstrapTable('hideColumn', 'Test intermedio n. 3');
            pTable.bootstrapTable('hideColumn', 'Una visita ispettiva');
            pTable.bootstrapTable('hideColumn', 'Organi di vigilanza');
            pTable.bootstrapTable('hideColumn', 'Il sopralluogo e la gestione delle emergenze');
            pTable.bootstrapTable('hideColumn', 'Il regime sanzionatorio');
            pTable.bootstrapTable('hideColumn', 'Conclusioni');
            pTable.bootstrapTable('hideColumn', 'Test intermedio n. 4');
            pTable.bootstrapTable('hideColumn', 'Approfondimento: i fondamenti giuridici');
            pTable.bootstrapTable('hideColumn', 'Test intermedio n. 5');
            pTable.bootstrapTable('hideColumn', 'Slide scaricabili');
            pTable.bootstrapTable('hideColumn', 'TEST FINALE - Formazione generale');
            pTable.bootstrapTable('hideColumn', 'Questionario di valutazione della qualità');



        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_report_pagamenti",
            // You are expected to receive the generated JSON (json_encode($data))
            data: params.data,
            dataType: "json",
            success: function (data) {

                // controllo errore
                if (typeof data != "object") {
                    params.error(data);
                    return;
                }
                else if (typeof data.error != "undefined") {
                    params.error(data.error);
                    return;
                }
                else {

                    params.success({

                        // By default, Bootstrap table wants a "rows" property with the data
                        "rows": data.rows,
                        // You must provide the total item ; here let's say it is for array length
                        "total": data.rowCount
                    })



                }
            },
            error: function (er) {
                params.error(er);
            }


        });

    }

    function detailFormatter(index, row, element) {

        var arr = [];
        arr.push(row.fields);

       //le colone di tabella dettaglio utente
        $('#detailTable').bootstrapTable({
            data: arr,
            columns: [{
                field: 'id',
                title: 'ID'
            }, {
                field: 'cb_cognome',
                title: '<?php echo JText::_('COM_GGLMS_REPORT_COGNOME'); ?>'
            }, {
                field: 'cb_codicefiscale',
                title: '<?php echo JText::_('COM_GGLSM_REPORT_CODICE_FISCALE'); ?>'
            }, {
                field: 'cb_datadinascita',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR7'); ?>'
            }, {
                field: 'cb_luogodinascita',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR8'); ?>'
            }, {
                field: 'cb_provinciadinascita',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR9'); ?>'
            }, {
                field: 'cb_indirizzodiresidenza',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?>'
            }, {
                field: 'cb_provdiresidenza',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?>'
            }, {
                field: 'cb_cap',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR12'); ?>'
            }, {
                field: 'cb_telefono',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR14'); ?>'
            }, {
                field: 'cb_nome',
                title: '<?php echo JText::_('COM_GGLMS_REPORT_NOME'); ?>'
            }, {
                field: 'username',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR2'); ?>'
            }, {
                field: 'email',
                title: '<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR20'); ?>'
            }, {
                field: 'registerDate',
                title: '<?php echo JText::_('COM_GGLMS_GLOBAL_CREATION_DATE'); ?>'
            }, {
                field: 'lastvisitDate',
                title: '<?php echo JText::_('COM_GGLMS_REPORT_LAST_VISIT'); ?>'
            }]

        });

        $(element).html($('#detailTable').clone(true).attr('id', "tbl_" + row.id_anagrafica).show());

        $("#detailTable").hide();
        $("#detailTable").bootstrapTable('destroy');
    }

    //button aggiorna dati
    jQuery('#btn_cerca').on('click',function () {
        pTable.bootstrapTable('refresh');
    })

    //esporto i dati in csv
    jQuery('#export_csv').on('click', function() {


        pTable.tableExport({
            type: 'csv',
            escape: false,
            ignoreColumn:[0],
            fileName: 'Report_generico',
            exportDataType: 'all',
            refreshOptions: {
                exportDataType: 'all'
            }
        });
    })

    //select Stato
    $("#filterstato").change(function () {


             if ($("#filterstato option:selected").val() == 1) {
                 // solo completati
                 $("#calendar_startdate_div").show();
                 $("#calendar_finishdate_div").show();
             } else {
                 $("#calendar_startdate_div").hide();
                 $("#calendar_finishdate_div").hide();
             }
        pTable.bootstrapTable('refresh');
    })

      //select start date
     $("#startdate").bind('change', function () {

         notcompleted = 0;
         completed = 0;
         pTable.bootstrapTable('refresh');
     })

    //select finish date
     $("#finishdate").change(function () {

         notcompleted = 0;
         completed = 0;
         pTable.bootstrapTable('refresh');
     })

    //select Corso
    $("#corso_id").change(function () {

        pTable.bootstrapTable('refresh');
    })



   </script>
</div>
