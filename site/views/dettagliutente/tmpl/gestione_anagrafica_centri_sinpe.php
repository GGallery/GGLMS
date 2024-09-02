<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 20/10/2022
 * Time: 15:28
 */
defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">

    <table
        id="tbl_centri"
        data-toggle="table"
        data-toolbar="#toolbar"
        data-ajax="ajaxRequest"
        data-side-pagination="server"
        data-pagination="true"
        data-show-export="true"
        data-page-list="[10, 25, 50, 100, 200, All]"
    >
        <thead>
        <tr>
            <th data-field="id" data-sortable="true">#</th>
            <th data-field="centro" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR40'); ?></th>
            <th data-field="indirizzo" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR39'); ?></th>
            <th data-field="telefono_responsabile" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR41'); ?></th>
            <th data-field="telefono_servizio" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR42'); ?></th>
            <th data-field="fax" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR43'); ?></th>
            <th data-field="email" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR4'); ?></th>
            <th data-field="responsabile" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR44'); ?></th>
            <th data-field="ruolo" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR45'); ?></th>
        </tr>
        </thead>
    </table>

    <script type="text/javascript">

        var pTable = jQuery('#tbl_centri');
        // var btnOk = jQuery('#ok');


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
            var pTipo = jQuery('#tipo_centri').val();
            // params.data.tipo_socio = parseInt(pTipo);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_anagrafica_centri",
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

