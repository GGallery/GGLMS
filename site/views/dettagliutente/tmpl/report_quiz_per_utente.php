<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 12/04/2021
 * Time: 16:18
 */

$_select_corsi = outputHelper::get_corsi_options($this->corsi);
$_form_class = ($_select_corsi == "") ? 'disabled' : '';
?>
<div class="container-fluid">

    <form id="get_quiz_report">

        <fieldset <?php echo $_form_class?>>

            <div class="form-group" id="div_corsi">
                <label for="corso">Corso</label>
                <select id="corso" class="form-control">
                    <?php echo $_select_corsi; ?>
                </select>
            </div>

            <div class="form-group to_show" id="div_quiz" style="display: none;">
                <label for="quiz">Quiz</label>
                <select id="quiz" class="form-control">

                </select>
            </div>

            <div class="form-group to_show" id="div_option" style="display: none;">
                <label for="selezione_utenti">Selezione utenti</label>
                <select id="selezione_utenti" class="form-control">
                    <option value="">-</option>
                    <option value="1">Tutti utenti</option>
                    <option value="2">Singolo utente</option>
                </select>
            </div>

            <div class="form-group to_show" id="div_utenti" style="display: none;">
                <label for="utenti">Utenti corso</label>
                <select id="utenti" class="form-control">

                </select>
            </div>

            <div class="form-group text-center to_show" id="btn_genera" style="display:none;">
                <button class="btn btn-success" id="btn-report">
                    SCARICA REPORT
                </button>
            </div>

        </fieldset>

    </form>

    <div class="row loading text-center" style="display: none;">
        <div class="col-xs-12">
            <i class="fa fa-circle-o-notch fa-spin"></i> Caricamento...
        </div>
    </div>

    <script type="text/javascript">

        function showLoading(w) {

            if (w == 's')
                jQuery('.loading').show();
            else
                jQuery('.loading').hide();

        }

        function clearShowing() {
            jQuery('.to_show').hide();
            jQuery('#utenti').html('');
        }

        function customAlertifyAlertSimple(pMsg) {
            alertify.alert()
                .setting({
                    'title': 'Attenzione!',
                    'label':'OK',
                    'message': pMsg
                }).show();
        }

        jQuery('.to_show').hide();

        jQuery(function() {

            // selezione del corso
            jQuery('#corso').on('change', function (e) {

                showLoading('s');
                var pCorso = jQuery(this).val();
                if (pCorso == ""
                    || parseInt(pCorso) == 0) {
                    showLoading('h');
                    clearShowing();
                    return;
                }

                jQuery.ajax({
                    type: "GET",
                    url: "index.php?option=com_gglms&task=contenuto.get_quiz_per_corso",
                    data: {"id_corso" : pCorso, "json" : 1},
                    // You are expected to receive the generated JSON (json_encode($data))
                    dataType: "json",
                    success: function (data) {

                        // controllo errore
                        if (typeof data != "object") {
                            showLoading('h');
                            customAlertifyAlertSimple(data);
                            return;
                        }
                        else if (typeof data.error != "undefined") {
                            showLoading('h');
                            customAlertifyAlertSimple(data.error);
                            return;
                        }
                        else {
                            showLoading('h');
                            if (typeof data.success != "object") {
                                customAlertifyAlertSimple("Oggetto dati dal server non conforme");
                                return;
                            }
                            else {

                                var target = data.success;

                                if (target.length == 0) {
                                    customAlertifyAlertSimple('Nessun quiz trovato');
                                    return;
                                }
                                else {

                                    var pSelectList = '<option value="">-</option>';
                                    for (var i = 0; i < target.length; i++) {

                                        var pIdQuiz = target[i].id_quiz;
                                        var pTitolo = target[i].titolo_quiz;

                                        pSelectList += '<option value="' + pIdQuiz + '">' + pTitolo + '</option>';
                                    }

                                    jQuery('#quiz').html(pSelectList);
                                    jQuery('#utenti').val('');
                                    jQuery('#selezione_utenti').val('');
                                    jQuery('#div_quiz').show();

                                }

                            }
                        }
                    },
                    error: function (err) {
                        showLoading('h');
                        customAlertifyAlertSimple(err);
                    }
                });

            });

            // selezione del quiz e produzione del report
            jQuery('#quiz').on('change', function (e) {

                var pCorso = jQuery('#corso').val();

                showLoading('s');
                if (pCorso == ""
                    || pCorso == 0) {
                    customAlertifyAlertSimple('Nessun corso selezionato');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                jQuery.ajax({
                    type: "GET",
                    url: "index.php?option=com_gglms&task=users.get_utenti_per_corso",
                    data: {"id_corso" : pCorso},
                    // You are expected to receive the generated JSON (json_encode($data))
                    dataType: "json",
                    success: function (data) {

                        // controllo errore
                        if (typeof data != "object") {
                            showLoading('h');
                            customAlertifyAlertSimple(data);
                            return;
                        }
                        else if (typeof data.error != "undefined") {
                            showLoading('h');
                            customAlertifyAlertSimple(data.error);
                            return;
                        }
                        else {
                            showLoading('h');
                            if (typeof data.success != "object") {
                                customAlertifyAlertSimple("Oggetto dati dal server non conforme");
                                return;
                            }
                            else {

                                var target = data.success;

                                if (target.length == 0) {
                                    customAlertifyAlertSimple('Nessun utente trovato');
                                    return;
                                }
                                else {

                                    var pSelectList = '<option value="">-</option>';
                                    for (var i = 0; i < target.length; i++) {

                                        var pUtenteId = target[i].id_utente;
                                        var pDenominazione = target[i].denominazione_utente;

                                        pSelectList += '<option value="' + pUtenteId + '">' + pDenominazione + '</option>';
                                    }
                                    jQuery('#selezione_utenti').val('');
                                    jQuery('#utenti').html(pSelectList);
                                    jQuery('#div_option').show();

                                }

                            }
                        }
                    },
                    error: function (err) {
                        showLoading('h');
                        customAlertifyAlertSimple(err);
                    }
                });

            });

            //selezione utenti
            jQuery('#selezione_utenti').on('change', function (e) {

                var pQuiz = jQuery('#quiz').val();

                showLoading('s');
                if (pQuiz == ""
                    || pQuiz == 0) {
                    customAlertifyAlertSimple('Nessun quiz selezionato');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                if (jQuery("#selezione_utenti option:selected").val() == 1) {

                    jQuery('#div_utenti').hide();
                    jQuery('#btn_genera').show();
                    showLoading('h');

                } else {

                    jQuery('#div_utenti').show();

                }
            });


            // selezione dell'utente
            jQuery('#utenti').on('change', function (e) {

                var pQuiz = jQuery(this).val();
                var pCorso = jQuery('#corso').val();
                var pUtente = jQuery('#utenti').val();
                var pOpzione = jQuery('#selezione_utenti').val();

                showLoading('s');
                if (pQuiz == ""
                    || pQuiz == 0) {
                    customAlertifyAlertSimple('Nessun quiz selezionato');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                if (pCorso == ""
                    || pCorso == 0) {
                    customAlertifyAlertSimple('Nessun corso selezionato');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                if (pOpzione == ""
                    || pOpzione == 0) {
                    customAlertifyAlertSimple('Nessun opzione per utenti selezionata');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                if (pUtente == ""
                    || pUtente == 0) {
                    customAlertifyAlertSimple('Nessun utente selezionato');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                jQuery('#btn_genera').show();
                showLoading('h');

            });

            // clicca bottone report
             jQuery('#btn-report').on('click', function (e) {

              var pQuiz = jQuery('#quiz').val();
              var pCorso = jQuery('#corso').val();
              var pUtente = jQuery('#utenti').val();
              var pOption = jQuery("#selezione_utenti option:selected").val() ;

                 if(pOption == 1) {

                     window.open("index.php?option=com_gglms&task=api.get_dettagli_quiz&quiz_id=" + pQuiz + "&all_users=" + pOption + "&corso_id=" + pCorso, "_blank");

                  } else {

                      window.open("index.php?option=com_gglms&task=api.get_dettagli_quiz&quiz_id=" + pQuiz + "&user_id=" + pUtente + "&all_users=" + pOption, "_blank");
                  }


           });

         });

    </script>

</div>
