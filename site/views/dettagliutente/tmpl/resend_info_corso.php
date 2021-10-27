<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 12/04/2021
 * Time: 16:18
 */

?>
<div class="container-fluid">

<table
            id="tbl_users"
            data-toggle="table"
            data-ajax="ajaxRequest"
            data-search="true"
            data-side-pagination="server"
            data-pagination="true"
            data-show-export="true"
            data-page-list="[10, 25, 50, 100, 200, All]"
    >
    <thead>
            <tr>
                <th data-field="username">Username</th>
                <th data-field="azioni" data-align="center">Azioni</th>
            </tr>
    </thead>
</table>

<script type="text/javascript">

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

    function resetPassword(userId, gruppoAzienda) {

        alertify.confirm()
            .setting({
                'title': 'Attenzione!',
                'label': 'OK',
                'message': "La password dell'utente verrà resettata ed impostata uguale a Username. Procedere?"
            })
            .set('onok', function(closeEvent){ eseguiResetPassword(userId, gruppoAzienda) } )
            .show();

    }

    function eseguiResetPassword(userId, gruppoAzienda) {

        jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.esegui_reset_password",
                // You are expected to receive the generated JSON (json_encode($data))
                data: {"id_utente" : userId, "gruppo_azienda" : gruppoAzienda},
                dataType: "json",
                success: function (data) {

                    // controllo errore
                    if (typeof data != "object") {
                        customAlertifyAlertSimple("Si è verificato un errore inaspettato");
                        return;
                    }
                    else if (typeof data.error != "undefined") {
                        customAlertifyAlertSimple(data.error);
                        return;
                    }
                    else {
                        customAlertifyAlertSimple("Reset password eseguito con successo!");
                    }
                },
                error: function (er) {
                    customAlertifyAlertSimple(er);
                }
            });

    }

    function ajaxRequest(params) {

        // data you may need
        params.data.boot_table = 1;
        console.log(params.data);

        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=users.get_utenti_per_societa",
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
