<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

$_select_users = outputHelper::get_zoom_users_options($this->zoom_users);
$_form_class = ($_select_users == "") ? 'disabled' : '';
?>

<div class="container-fluid">

    <form id="get_zoom_report">

        <fieldset <?php echo $_form_class?>>

            <div class="form-group">
                <label for="zoom_origine">Sorgente dati</label>
                <select id="zoom_origine" class="form-control">
                    <option value="">-</option>
                    <option value="offline">Report memorizzati</option>
                    <option value="online">Report online</option>
                </select>
            </div>

            <div class="form-group to_show_offline" id="zoom_div_offline">
                <label for="zoom_local_event">Seleziona report</label>
                <select id="zoom_local_event" class="form-control">
                </select>
            </div>

            <div class="form-group to_show_online" id="zoom_div_online" style="display: none;">
                <label for="zoom_user">Utente Zoom</label>
                <select id="zoom_user" class="form-control">
                    <?php echo $_select_users; ?>
                </select>
            </div>

            <div class="form-group to_show_online" id="seleziona_tipo" style="display:none;">
                <label for="zoom_tipo">Seleziona tipo report</label>
                <select id="zoom_tipo" class="form-control">
                    <option value="">-</option>
                    <option value="meetings">Meetings</option>
                    <option value="webinars">Webinars</option>
                </select>
            </div>

            <div class="form-group to_show_online" id="seleziona_evento" style="display:none;">
                <label for="zoom_evento">Seleziona evento</label>
                <select id="zoom_evento" class="form-control"></select>
            </div>

            <div class="form-group text-center to_show_online" id="btn_genera" style="display:none;">
                <button class="btn btn-success">
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

</div>


<script type="text/javascript">

    jQuery('.to_show_online').hide();
    jQuery('.to_show_offline').hide();

    function showLoading(w) {

        if (w == 's')
            jQuery('.loading').show();
        else
            jQuery('.loading').hide();

    }

    function showLocalEvents(pSource) {

        showLoading('s');
        jQuery('.to_show_online').hide();
        jQuery('#zoom_local_event').html('');

        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_local_events",
            // You are expected to receive the generated JSON (json_encode($data))
            dataType: "json",
            success: function (data) {

                // controllo errore
                if (typeof data != "object") {
                    showLoading('h');
                    alert(data);
                    return;
                }
                else if (typeof data.error != "undefined") {
                    showLoading('h');
                    alert(data.error);
                    return;
                }
                else {
                    showLoading('h');
                    if (typeof data.success != "object") {
                        alert("Oggetto dati dal server non conforme");
                        return;
                    }
                    else {

                        var target = data.success;

                        if (target.length == 0) {
                            alert('Nessun evento trovato');
                            return;
                        }
                        else {

                            var pSelectList = '<option value="">-</option>';
                            for (var i = 0; i < target.length; i++) {

                                var pEventId = target[i].id_local;
                                var pTopic = target[i].label_evento;

                                pSelectList += '<option value="' + pEventId + '">' + pTopic + '</option>';
                            }

                            jQuery('#zoom_local_event').html(pSelectList);
                            jQuery('#zoom_div_offline').show();

                        }

                    }
                }
            },
            error: function (err) {
                showLoading('h');
                alert(err);
            }
        });

    }

    function showEvents(pSource) {

        if (pSource == 'online') {
            jQuery('#zoom_div_' + pSource).show();
            jQuery('.to_show_offline').hide();
        }
        else
            showLocalEvents();

    }

    // seleziona sorgente dati
    jQuery('#zoom_origine').on('change', function (e) {

        var pSource = jQuery(this).val();
        showEvents(pSource);

    });

    // seleziona report locale
    jQuery('#zoom_local_event').on('change', function (e) {
        jQuery('#btn_genera').show();
    });

    // selezione utente
    jQuery('#zoom_user').on('change', function (e) {

        var pCurr = jQuery(this).val();
        if (pCurr != '') {
            jQuery('.to_show_online').hide();
            jQuery('#zoom_div_online').show();
            jQuery('#zoom_tipo').val('');
            jQuery('#zoom_evento').html('');
            jQuery('#seleziona_tipo').show();
        }
        else
            jQuery('.to_show_online').hide();

    });

    // selezione evento
    jQuery('#zoom_evento').on('change', function (e) {
        jQuery('#btn_genera').show();
    });

    // clicca bottone report
    jQuery('#btn_genera').on('click', function (e) {

        var pSource = jQuery('#zoom_origine').val();

        if (pSource == 'offline') {
            var pEvento = jQuery('#zoom_local_event').val();

            if (pEvento == '')
                return;

            window.open("index.php?option=com_gglms&task=api.get_local_events&zoom_event_id=" + pEvento, "_blank");
        }
        else
        {
            var pTipo = jQuery('#zoom_tipo').val();
            var pEvento = jQuery('#zoom_evento').val();
            var pEventoLabel = jQuery("#zoom_evento").find("option[value='" + pEvento + "']").text();

            if (pEvento == '')
                return;

            window.open("index.php?option=com_gglms&task=api.get_event_participants&zoom_event_id=" + pEvento + "&zoom_tipo=" + pTipo + "&zoom_label=" + pEventoLabel, "_blank");
        }

    });

    // selezione tipo report
    jQuery('#zoom_tipo').on('change', function (e) {

        var pUser = jQuery('#zoom_user').val();
        var pTipo = jQuery(this).val();

        if (pTipo == '')
            return;

        showLoading('s');

        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_event_list",
            // You are expected to receive the generated JSON (json_encode($data))
            data: {"zoom_user" : pUser, "zoom_tipo" : pTipo},
            dataType: "json",
            success: function (data) {

                // controllo errore
                if (typeof data != "object") {
                    showLoading('h');
                    alert(data);
                    return;
                }
                else if (typeof data.error != "undefined") {
                    showLoading('h');
                    alert(data.error);
                    return;
                }
                else {
                    showLoading('h');
                    if (typeof data.success != "object") {
                        alert("Oggetto dati dal server non conforme");
                        return;
                    }
                    else {

                        var target = data.success;

                        if (target.length == 0) {
                            alert('Nessun evento trovato');
                            return;
                        }
                        else {

                            var pSelectList = '<option value="">-</option>';
                            for (var i = 0; i < target.length; i++) {

                                var pEventId = target[i].id;
                                var pTopic = target[i].topic;
                                var pCreated = target[i].created_at;

                                pSelectList += '<option value="' + pEventId + '">' + pCreated + ' - ' + pTopic + '</option>';
                            }

                            jQuery('#zoom_evento').html('');
                            jQuery('#zoom_evento').html(pSelectList);
                            jQuery('#seleziona_evento').show();

                        }

                    }
                }
            },
            error: function (err) {
                showLoading('h');
                alert(err);
            }
        });

    });

</script>

