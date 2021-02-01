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
