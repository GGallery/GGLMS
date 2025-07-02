<?php
defined('_JEXEC') or die;
?>

<style>

    .bs-bars.pull-left {
        float: none !important;
    }

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
            data-force-hide="true"
            data-page-list="[5, 10, 20, 50, 100, All]"
    >
        <thead>
        <tr>
            <th data-field="cognome" ><?php echo JText::_('COM_GGLMS_REPORT_COGNOME'); ?></th>
            <th data-field="nome" ><?php echo JText::_('COM_GGLMS_REPORT_NOME'); ?></th>
            <th data-class="showColumn" data-field="stato" ><?php echo JText::_('COM_GGLMS_REPORT_STATO'); ?></th>
            <th data-field="data_inizio" ><?php echo JText::_('COM_GGLMS_REPORT_DATA_INIZIO'); ?></th>
            <th data-field="data_fine" ><?php echo JText::_('COM_GGLMS_REPORT_DATA_FINE'); ?></th>
            <th data-field="cb_codicefiscale" ><?php echo JText::_('COM_GGLMS_REPORT_CODICE_FISCALE'); ?></th>
            <th data-field="cb_professionedisciplina" ><?php echo JText::_('COM_GGLSM_REPORT_PROFESSIONE'); ?></th>
            <th data-field="totale" ><?php echo JText::_('COM_GGLSM_REPORT_TOTALE_PAGATO'); ?></th>
        </tr>
        </thead>
    </table>




<script type="text/javascript">


    jQuery('#calendar_startdate_div').hide();
    jQuery('#calendar_finishdate_div').hide();
    var pTable = jQuery('#tbl_report');


    function ajaxRequest(params) {

        // preparo i params

        var pCorso = jQuery('#corso_id').val();
        params.data.corso_id = parseInt(pCorso);

        var pStato = jQuery('#filterstato').val();
        params.data.filterstato = parseInt(pStato);

        //params.data.usergroups = jQuery('#usergroups').val().trim();


        //var pTipo = jQuery('#tipo_report').val().trim();
        //params.data.tipo_report = parseInt(pTipo);



        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_report_pagamenti",
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
    });



   </script>
</div>
