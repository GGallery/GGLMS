<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 12/04/2021
 * Time: 16:18
 */

?>
<div class="container-fluid">

<small>
    <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_null($this->_html)) :
                    echo <<<HTML
                    <tr>
                        <td colspan="2">Nessun utente trovato</td>
                    </tr>
    HTML;
                    else :
                        echo $this->_html;
                    endif;
                ?>
            </tbody>
    </table>
</small>

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

</script>

</div>
