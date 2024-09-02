<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 02/02/2023
 * Time: 10:26
 */

defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">
    
    <table
            id="tbl_accesso"
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
            <th data-field="user_id" data-sortable="true">#</th>
            <th data-field="username" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR3'); ?></th>
            <th data-field="nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR5'); ?></th>
            <th data-field="cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR6'); ?></th>
            <th data-field="email" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR4'); ?></th>
            <th data-field="tipo_azione" data-sortable="false"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR10'); ?></th>
        </tr>
        </thead>
    </table>

    <script type="text/javascript">

        var pTable = jQuery('#tbl_accesso');
        var btnOk = jQuery('#ok');


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


            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_accesso_utenti",
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




        function AbilitaAccessoUtente(userId, statoUser) {

            alertify.confirm()
                .setting({
                    'title': 'Attenzione!',
                    'label': 'OK',
                    'message': "<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR49'); ?>"
                })
                .set('onok', function(closeEvent){ eseguiDisabilitaAccesso(userId, statoUser)} )
                .show();


        }

        function DisabilitaAccessoUtente(userId, statoUser) {

            alertify.confirm()
                    .setting({
                        'title': 'Attenzione!',
                        'label': 'OK',
                        'message': "<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR48'); ?>"
                    })
                    .set('onok', function(closeEvent){ eseguiDisabilitaAccesso(userId, statoUser)} )
                    .show();


        }

        function eseguiDisabilitaAccesso(userId, statoUser) {



                    jQuery.get("index.php?option=com_gglms&task=users.disabilita_accesso_utente", {
                        user_id: userId,
                        stato_user: statoUser
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
                                    if(statoUser == 0) {
                                        customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR51'); ?>");
                                    }else{
                                        customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR50'); ?>");
                                    }
                                    //window.location.reload();
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






    </script>

</div>


