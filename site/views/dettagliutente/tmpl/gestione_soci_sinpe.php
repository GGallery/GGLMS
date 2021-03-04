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
                <span>Tipo </span>
                <select id="tipo_socio" class="form-control">
                    <option value="<?php echo $this->online; ?>"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR17');?></option>
                    <option value="<?php echo $this->moroso; ?>"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR18');?></option>
                    <option value="<?php echo $this->decaduto; ?>"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR19');?></option>
                    <option value="<?php echo $this->preiscritto; ?>"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR25');?></option>
                </select>
            </div>
            <button id="ok" type="submit" class="btn btn-primary">OK</button>
        </div>
    </div>
    <table
            id="tbl_soci"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-search="true"
            data-side-pagination="server"
            data-pagination="true">
        <thead>
        <tr>
            <th data-field="user_id" data-sortable="true">#</th>
            <th data-field="username" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR3'); ?></th>
            <th data-field="email" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR4'); ?></th>
            <th data-field="nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR5'); ?></th>
            <th data-field="cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR6'); ?></th>
            <th data-field="codice_fiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR7'); ?></th>
            <th data-field="ultimo_anno" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR8'); ?></th>
            <th data-field="tipo_socio" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR9'); ?></th>
            <th data-field="tipo_azione" data-sortable="false"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR10'); ?></th>
        </tr>
        </thead>
    </table>

    <script type="text/javascript">

        var pTable = jQuery('#tbl_soci');
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
            // aggiunto tipologia socio
            var pTipo = jQuery('#tipo_socio').val();
            params.data.tipo_socio = parseInt(pTipo);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_soci_iscritti",
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


        // pagamento extra con bonifico (es. espen)
        function impostaPagamentoExtra(userId) {

            alertify.prompt("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR29'); ?>",
                '',
                function (evt, pTotale) {
                    setTimeout(function () {
                        impostaPagamentoExtraTipo(userId, pTotale);
                    }, 500);
                })
                .setting({
                    'title' : 'Attenzione!'
                })
                .show();

        }

        function impostaPagamentoExtraTipo(userId, pTotale) {

            alertify.prompt("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR35'); ?>",
                '',
                function (evt, pTipoQuota) {
                    setTimeout(function () {
                        eseguiPagamentoExtra(userId, pTotale, pTipoQuota);
                    }, 500);
                })
                .setting({
                    'title' : 'Attenzione!'
                })
                .show();

        }

        function eseguiPagamentoExtra(userId, pTotale, pTipoQuota) {

            if (pTotale != null
                && pTotale != ""
                && parseInt(pTotale) > 0) {

                // controllo se è un numero
                var pTest = pTotale % 1;
                if (isNaN(pTest)) {
                    customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR22'); ?>")
                    return;
                } else if (pTipoQuota != 'espen') {
                        customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR37'); ?>")
                        return;
                }
                else {

                    jQuery.get("index.php?option=com_gglms&task=users.inserisci_pagamento_extra", {
                        user_id: userId,
                        totale: pTotale,
                        tipo_quota: pTipoQuota
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


            } else {
                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR24'); ?>");
            }

        }

        // da decaduto a moroso
        function impostaMoroso(userId) {

            alertify.confirm()
                .setting({
                    'title': 'Attenzione!',
                    'label': 'OK',
                    'message': "<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR27'); ?>"
                })
                .set('onok', function(closeEvent){ eseguiImpostaMoroso(userId) } )
                .show();

        }

        // utente riabilitato al pagamento (moroso di un anno)
        function eseguiImpostaMoroso(userId) {

                jQuery.get( "index.php?option=com_gglms&task=users.riabilita_decaduto", { user_id: userId} )
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
                                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR28'); ?>");
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

        // da moroso ad online
        function impostaPagato(userId) {

            alertify.confirm()
                .setting({
                    'title': 'Attenzione!',
                    'label': 'OK',
                    'message': "<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR21'); ?>"
                })
                .set('onok', function(closeEvent){ eseguiImpostaPagato(userId) } )
                .show();

        }

        function eseguiImpostaPagato(userId) {

            alertify.prompt("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR29'); ?>",
                            '',
                            function (evt, pTotale) {
                                setTimeout(function () {
                                    eseguiPagamento(userId, pTotale);
                                }, 500);

                            })
                            .setting({
                                'title' : 'Attenzione!'
                            })
                            .show();

        }

        // inserimento del pagamento della quota a mezzo bonifico
        function eseguiPagamento(userId, pTotale) {

                if (pTotale != null
                    && pTotale != ""
                    && parseInt(pTotale) > 0) {

                    // controllo se è un numero
                    var pTest = pTotale % 1;
                    if (isNaN(pTest)) {
                        customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR22'); ?>")
                        return;
                    } else {

                        jQuery.get("index.php?option=com_gglms&task=users.inserisci_pagamento_moroso", {
                            user_id: userId,
                            totale: pTotale
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

                } else {
                    customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR24'); ?>");
                }

        }

        function riabilitaDecaduto(userId) {

            alertify.confirm()
                .setting({
                    'title': 'Attenzione!',
                    'label': 'OK',
                    'message': "<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR12'); ?>"
                })
                .set('onok', function(closeEvent){ eseguiRiabilitaDecaduto(userId) } )
                .show();

        }

        // utente riabilitato al pagamento (moroso di un anno)
        function eseguiRiabilitaDecaduto(userId) {

                jQuery.get( "index.php?option=com_gglms&task=users.riabilita_decaduto", { user_id: userId} )
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
                                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR15'); ?>");
                                //window.location.reload();
                                pTable.bootstrapTable('refresh');
                            }
                            // errore non gestito
                            else {
                                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR16'); ?>");
                            }


                        }

                    }).fail(function() {
                        customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR13'); ?>");
                    });

        }

    </script>

</div>


