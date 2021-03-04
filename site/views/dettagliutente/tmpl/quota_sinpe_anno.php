<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">


    <table
            id="tbl_quote"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-search="true"
            data-side-pagination="server"
            data-pagination="true">
        <thead>
            <tr>
                <th data-field="id_pagamento" data-sortable="true">#</th>
                <?php if (is_null($this->user_id)) : ?>
                    <th data-field="username" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR10'); ?></th>
                    <th data-field="nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></th>
                    <th data-field="cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></th>
                    <th data-field="codice_fiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></th>
                <?php endif; ?>
                <th data-field="tipo_quota"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR2'); ?></th>
                <th data-field="icon_pagamento" data-align="center"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR3'); ?></th>
                <th data-field="check_pagamento" data-cell-style="cellStyle" data-align="center"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR9'); ?></th>
                <th data-field="anno" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR4'); ?></th>
                <th data-field="totale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR5'); ?></th>
                <th data-field="data_pagamento" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR6'); ?></th>
                <th data-field="dettagli_transazione"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR7'); ?></th>
            </tr>
        </thead>

    </table>

    <script type="text/javascript">

        var pTable = jQuery('#tbl_quote');

        function customAlertifyAlertSimple(pMsg) {
            alertify.alert()
                .setting({
                    'title': 'Attenzione!',
                    'label':'OK',
                    'message': pMsg
                }).show();
        }

        function confermaAcquistaEvento(idPagamento, userId, gruppoCorso) {

            alertify.confirm()
                .setting({
                    'title': 'Attenzione!',
                    'label': 'OK',
                    'message': "<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR32'); ?>"
                })
                .set('onok', function(closeEvent){ eseguiAcquistaEvento(idPagamento, userId, gruppoCorso) } )
                .show();

        }

        // utente che ha acquista un evento ed ha pagato con bonifico
        function eseguiAcquistaEvento(idPagamento, userId, gruppoCorso) {

            jQuery.get( "index.php?option=com_gglms&task=users.conferma_acquisto_evento",
                { id_pagamento: idPagamento, user_id: userId, gruppo_corso: gruppoCorso} )
                .done(function(results) {

                    // risposta non conforme
                    if (typeof results != "string"
                        || results == "") {
                        customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR14'); ?>");
                        return;
                    }
                    else {

                        // errore
                        var objRes = JSON.parse(results);
                        if (typeof objRes.error != "undefined") {
                            customAlertifyAlertSimple(objRes.error);
                            return;
                        }
                        // successo
                        else if (typeof objRes.success != "undefined") {
                            customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR34'); ?>");
                            //window.location.reload();
                            pTable.bootstrapTable('refresh');
                        }
                        // errore non gestito
                        else {
                            customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR16'); ?>");
                        }


                    }

                }).fail(function() {
                alert("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR13'); ?>");
            });

        }


        function cellStyle() {
            return {
                classes: 'alert-success'
            }
        }

        function ajaxRequest(params) {

            // data you may need
            console.log(params.data);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_quote_iscrizione",
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
