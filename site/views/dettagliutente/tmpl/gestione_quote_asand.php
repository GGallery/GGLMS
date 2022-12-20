<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 18:26
 */

defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">

    <div id="toolbar">
        <div class="form-inline" role="form">
            <div class="form-group">
                <span>Tipo quota</span>
                <select id="tipo_quota" class="form-control">
                    <option value="">-</option>
                    <option value="<?php echo $this->quota_standard; ?>"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR3');?></option>
                    <option value="<?php echo $this->quota_studente; ?>"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR4');?></option>
                </select>
            </div>
            <div class="form-group ml-2">
                <span>Stato pagamento</span>
                <select id="stato_pagamento" class="form-control">
                    <option value="">-</option>
                    <option value="1"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR16');?></option>
                    <option value="0"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR17');?></option>
                </select>
            </div>
            <div class="form-group ml-2">
                <button id="ok" type="submit" class="btn btn-primary">OK</button>
                <button id="export" class="btn btn-sm btn-primary ml-1">Export</button>
            </div>
        </div>
    </div>
    <table
            id="tbl_soci"
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
            <th data-field="cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR6'); ?></th>
            <th data-field="nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR5'); ?></th>
            <th data-field="email" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR4'); ?></th>
            <th data-field="codice_fiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR7'); ?></th>
            <th data-field="telefono" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR23'); ?></th>
            <th data-field="tipo_pagamento" data-sortable="true"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR12'); ?></th>
            <th data-field="stato_pagamento2" data-sortable="true"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR14'); ?></th>
            <th data-field="data_pagamento" data-sortable="true"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR9'); ?></th>
            <th data-field="anno_pagamento_quota" data-sortable="true"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR10'); ?></th>
            <th data-field="totale_quota" data-sortable="true"><?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR11'); ?></th>
            <th data-field="tipo_azione" data-sortable="false"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR10'); ?></th>
        </tr>
        </thead>
    </table>

    <script type="text/javascript">

        var pTable = jQuery('#tbl_soci');
        var btnOk = jQuery('#ok');

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

        jQuery(function() {
            btnOk.click(function () {
                pTable.bootstrapTable('refresh');
            })
        });

        function customConfirm(pTitle, pMsg, pUserId, pCallbackOk) {

            alertify.confirm()
                .setting({
                    'title': pTitle,
                    'label':'OK',
                    'message': pMsg ,
                    'onok' : pCallbackOk(pUserId)
                }).show();

        }

        function customAlertifyAlertSimple(pMsg) {
            alertify.alert()
                .setting({
                    'title': 'Attenzione!',
                    'label':'OK',
                    'message': pMsg
                }).show();
        }

        function ajaxRequest(params) {

            // data you may need
            console.log(params.data);
            // aggiunto tipologia socio
            var pTipo = jQuery('#tipo_quota').val();
            if (pTipo != "")
                params.data.tipo_quota = parseInt(pTipo);

            var pStatoPagamento = jQuery('#stato_pagamento').val();
            if (pStatoPagamento != "")
                params.data.stato_pagamento = parseInt(pStatoPagamento);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_quote_asand",
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

        // da moroso ad online
        function impostaPagato(quotaId) {

            alertify.confirm()
                .setting({
                    'title': 'Attenzione!',
                    'label': 'OK',
                    'message': "<?php echo JText::_('COM_REGISTRAZIONE_ASAND_STR15'); ?>"
                })
                .set('onok', function(closeEvent){ eseguiPagamento(quotaId) } )
                .show();

        }

        // inserimento del pagamento della quota a mezzo bonifico
        function eseguiPagamento(quotaId) {

            // controllo se è un numero
            var pTest = quotaId % 1;
            if (isNaN(pTest)) {
                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR22'); ?>")
                return;
            } else {

                jQuery.get("index.php?option=com_gglms&task=users.conferma_pagamento_bonifico_asand", {
                    id_quota: quotaId
                })
                    .done(function (results) {

                        // risposta non conforme
                        if (typeof results != "string"
                            || results == "") {
                            customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR14'); ?>");
                            return;
                        } else {

                            // errore
                            var objRes = JSON.parse(results);
                            if (typeof objRes.error != "undefined") {
                                customAlertifyAlertSimple(objRes.error);
                                return;
                            }
                            // successo
                            else if (typeof objRes.success != "undefined") {
                                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR23'); ?>");
                                pTable.bootstrapTable('refresh');
                            }
                            // errore non gestito
                            else {
                                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR16'); ?>");
                            }


                        }

                    }).fail(function () {
                    customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR13'); ?>");
                });

            }

        }

    </script>

</div>


