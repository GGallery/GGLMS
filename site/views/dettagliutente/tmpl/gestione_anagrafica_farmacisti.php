<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">

    <div id="toolbar" class="select">
        <button id="export" class="btn btn-sm btn-primary">Export</button>
    </div>

    <table
            id="tbl_anagrafica"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-search="true"
            data-side-pagination="server"
            data-pagination="true"
            data-show-export="true"
            data-page-list="[10, 25, 50, 100, 200, All]"
    >
        <thead>

            <tr>
                <th data-field="ref_dipendente" data-sortable="true">#</th>
                <th data-field="cb_cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></th>
                <th data-field="cb_nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></th>
                <th data-field="cb_email" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?></th>
                <th data-field="role_title" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_RUOLO'); ?></th>
                <th data-field="cb_azienda" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_AZIENDA'); ?></th>
                <th data-field="cb_filiale" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_FILIALE'); ?></th>
                <th data-field="cb_matricola" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_MATRICOLA'); ?></th>
                <th data-field="cb_codicefiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></th>
                <th data-field="cb_datadinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?></th>
                <th data-field="cb_luogodinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COMUNE_NASCITA'); ?></th>
                <th data-field="cb_provinciadinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_PROVINICIA_NASCITA'); ?></th>
                <th data-field="cb_indirizzodiresidenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?></th>
                <th data-field="cb_cap" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_CAP_RESIDENZA'); ?></th>
                <th data-field="cb_citta" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_CODICE_COMUNE_RESIDENZA'); ?></th>
                <th data-field="cb_provdiresidenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?></th>
                <th data-field="cb_dataassunzione" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_ASSUNZIONE'); ?></th>
                <th data-field="cb_datainiziorapporto" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_INIZIO_RAPPORTO'); ?></th>
                <th data-field="cb_datalicenziamento" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_LICENZIAMENTO'); ?></th>
                <th data-field="cb_codiceestrenocdc2" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_2'); ?></th>
                <th data-field="cb_codiceestrenocdc3" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_3'); ?></th>
                <th data-field="cb_codiceestrenorep2" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_EXT_REP2'); ?></th>
            </tr>

        </thead>
    </table>

    <script type="text/javascript">

        var pTable = jQuery('#tbl_anagrafica');
        var pTable = jQuery('#tbl_quote');

        jQuery('#export').on('click', function() {
            pTable.tableExport({
                type: 'excel',
                escape: false,
                exportDataType: 'all',
                refreshOptions: {
                    exportDataType: 'all'
                }
            });
        });

        function ajaxRequest(params) {

            // data you may need
            console.log(params.data);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_anagrafica_farmacisti",
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
                            "total": data.total_rows
                        })
                    }
                },
                error: function (er) {
                    params.error(er);
                }
            });
        }


    </script>

</div>