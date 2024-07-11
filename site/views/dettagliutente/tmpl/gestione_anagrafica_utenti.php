<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>

<style>

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 9999; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height:85%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        padding: 20px;
        border: 1px solid #888;
        width: 100%; /* Could be more or less, depending on screen size */
    }

    /* The Close Button */
    .close {
        color: #aaa;
        float: left;
        font-size: 1.2em;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #containerRow {
        background: #ccc;
        padding: 1%;
        border-radius: 1%;
    }

    #userModal {
        border-radius: 1%;
    }

    .required:after {
        content:" *";
        color: red;
    }

    .form-control-fixed {
        line-height:24px !important;
        font-size: 0.8em !important;
        font-family: sans-serif !important;
    }
</style>

<div class="container-fluid" style="position: relative;">

    <div id="toolbar" class="select">
		<div class="form-inline" role="form">
            <div class="form-group">
		        <button id="export" class="btn btn-sm btn-primary">Export</button>
			</div>
			<div class="form-group">
                <input id="customSearch" name="search" class="form-control" type="text" placeholder="Ricerca" style="height: inherit; line-height:24px; font-family: sans-serif; display:block; width: 250px;margin-bottom :8px;" />
            </div>
            <?php
            $modelUser = new gglmsModelUsers();
            $user = JFactory::getUser();
            

            if($modelUser->is_user_superadmin($user->id)){
            echo '<div class = "form-group">';
                echo '<label for="platforms">Piattaforma:</label>';
                 echo outputHelper::output_select('platforms', $this->platforms, 'value', 'text', null,'form-control'); 
            }
            echo '</div>';

            // if($modelUser->is_tutor_piattaforma($user->id)) {
            //     echo '<div class = "form-group">';
            //         echo '<label for="usergroups">Aziende:</label>';
            //          echo outputHelper::output_select('usergroups', $this->usergroups, 'id', 'title', null,'form-control'); 
            //     }
            //     echo '</div>';
                 ?>

            
		</div>
    </div>

    <table
            id="tbl_anagrafica"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-search-selector="#customSearch"
            data-ajax="ajaxRequest"
            data-search="true"
            data-side-pagination="server"
            data-pagination="true"
            data-show-export="true"
            data-page-list="[10, 25, 50, 100, 200, All]"
    >
        <thead>

            <tr>
                <th data-field="ref_dipendente" data-sortable="true">#</th>
                <th data-field="user_actions" data-sortable="false"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_AZIONI'); ?></th>
                <th data-field="cb_cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></th>
                <th data-field="cb_nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></th>
                <th data-field="cb_email" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?></th>
                <th data-field="cb_codicefiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></th>
                <th data-field="cb_datadinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?></th>
                <th data-field="cb_luogodinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_LUOGO_NASCITA'); ?></th>
                <th data-field="cb_provinciadinascita" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_CB_PROVINICIA_NASCITA'); ?></th>
                <th data-field="cb_indirizzodiresidenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?></th>
                <th data-field="cb_citta" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR26'); ?></th>
                <th data-field="cb_cap" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_CAP'); ?></th>
                <th data-field="cb_provdiresidenza" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?></th>
                <th data-field="cb_azienda" data-sortable="true"  data-align="center"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR25'); ?></th>
                <th data-field="cb_professione" data-sortable="true" data-align="center"><?php echo Jtext::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR21') ?></th>
                <th data-field="cb_ruolo" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR45'); ?></th>
            </tr>

            <tr><td>1</td></tr>

        </thead>
    </table>

    <!-- The Modal -->
    <div id="userModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span 
                id="closeUserModal" 
                class="close">X</span>

                <div class="container-fluid">
            
                    <div class="row" id="containerRow">

                        <div class="col-xs-12">
                            <form class="form-horizontal" id="userEditForm">
                                <div class="form-group">
                                    <label for="cb_nome" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-fixed" id="cb_nome" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_cognome" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-fixed" id="cb_cognome" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_email" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control form-control-fixed" id="cb_email" placeholder="<?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_password" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR3'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control form-control-fixed" id="cb_password" placeholder="<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR3'); ?>">
                                        <span id="helpBlockPassword" class="help-block"><small>Se aggiunto la password sarà creata automaticamente. In modifica, verrà aggiornata solo se inserita</small></span>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="cb_codicefiscale" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control required form-control-fixed" id="cb_codicefiscale" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_ruolo" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR45'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-fixed" id="cb_ruolo" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR45'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_azienda" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR25'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w-25" id="cb_azienda" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR25'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_datadinascita" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control w-25 form-control-fixed" id="cb_datadinascita" placeholder="<?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_luogodinascita" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_CB_LUOGO_NASCITA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-fixed" id="cb_luogodinascita" placeholder="<?php echo JText::_('COM_GGLMS_CB_LUOGO_NASCITA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_provinciadinascita" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_CB_PROVINICIA_NASCITA'); ?></label>
                                    <div class="col-sm-10" id="provNascitaContainer"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_indirizzodiresidenza" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-fixed" id="cb_indirizzodiresidenza" placeholder="<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_cap" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_CB_CAP'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control w-25 form-control-fixed" id="cb_cap" placeholder="<?php echo JText::_('COM_GGLMS_CB_CAP'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_citta" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR26'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-fixed" id="cb_citta" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR26'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_provdiresidenza" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?></label>
                                    <div class="col-sm-10" id="provResidenzaContainer"></div>
                                </div>



                                <div class="col-xs-12 text-center">
                                    <button 
                                        type="submit" 
                                        class="btn btn-success"
                                        style="text-transform: uppercase !important;"
                                        >Salva</button>
                                    <button 
                                        type="button" 
                                        class="btn btn-danger"
                                        style="text-transform: uppercase !important;"
                                        onclick="hideUseModal()"
                                        >Chiudi</button>
                                </div>

                                <input type="hidden" id="editingUserId" value="" />
                            </form>
                        </div>

                    </div>
                </div>

        </div>

    </div>

</div>

<script type="text/javascript">

usergroupRequest(208)

        const pTable = jQuery('#tbl_anagrafica');

        let userModal = document.getElementById('userModal');
        let editingUser = document.getElementById("editingUserId");
        
        let cb_nome = document.getElementById("cb_nome");
        let cb_cognome = document.getElementById("cb_cognome");
        let cb_email = document.getElementById("cb_email");
        let cb_codicefiscale = document.getElementById("cb_codicefiscale");
        let cb_datadinascita = document.getElementById("cb_datadinascita");
        let cb_luogodinascita = document.getElementById("cb_luogodinascita");
        let cb_provinciadinascita = document.getElementById("cb_provinciadinascita");
        let cb_indirizzodiresidenza = document.getElementById("cb_indirizzodiresidenza");
        let cb_cap = document.getElementById("cb_cap");
        let cb_citta = document.getElementById("cb_citta");
        let cb_azienda = document.getElementById("cb_azienda");
        let cb_professione = document.getElementById("cb_professione");
        let cb_ruolo =document.getElementById("cb_ruolo")


        
        let provNascitaContainer = document.getElementById("provNascitaContainer");
        let provResidenzaContainer = document.getElementById("provResidenzaContainer");

        function hideUseModal() {

            editingUser.value = '';
            cb_email.value = '';
            cb_nome.value = '';
            cb_cognome.value = '';
            cb_azienda.value = '';
            cb_codicefiscale.value = '';
            cb_datadinascita.value = '';
            cb_luogodinascita.value = '';
            cb_indirizzodiresidenza.value = '';
            cb_cap.value = '';
            cb_citta.value = '';
            cb_ruolo.value = '';

            provNascitaContainer.innerHTML = '';
            provResidenzaContainer.innerHTML = '';

            userModal.style.display = "none";

        }

        document.getElementById('userEditForm').addEventListener("submit", function (e) {

            e.preventDefault();
            console.log('target', e.target);

            let cb_provinciadinascita = document.getElementById("cb_provinciadinascita");
            let cb_provdiresidenza = document.getElementById("cb_provdiresidenza");

            if (cb_nome.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_MISSING_NOME'); ?>");
                return;
            }

            if (cb_cognome.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_MISSING_COGNOME'); ?>");
                return;
            }

            if (cb_email.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_MISSING_EMAIL'); ?>");
                return;
            }
            
            if (cb_codicefiscale.value == "") {
                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_MISSING_CF'); ?>");
                return;
            }
            
            let formData = new FormData();
            formData.append('editingUser', editingUser.value);
            formData.append('cb_ruolo', cb_ruolo.value);
            formData.append('cb_nome', cb_nome.value);
            formData.append('cb_cognome', cb_cognome.value);
            formData.append('cb_email', cb_email.value);
            
            if (cb_password.value != '') formData.append('cb_password', cb_password.value);
            
            formData.append('cb_azienda', cb_azienda.value);
            formData.append('cb_codicefiscale', cb_codicefiscale.value);
            formData.append('cb_datadinascita', cb_datadinascita.value);
            formData.append('cb_luogodinascita', cb_luogodinascita.value);
            formData.append('cb_provinciadinascita', cb_provinciadinascita.value);
            formData.append('cb_indirizzodiresidenza', cb_indirizzodiresidenza.value);
            formData.append('cb_cap', cb_cap.value);
            formData.append('cb_citta', cb_citta.value);
            formData.append('cb_provdiresidenza', cb_provdiresidenza.value);

            fetch('index.php?option=com_gglms&task=users.update_anagrafica_utente', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the server
                if (data.error) {
                    alert(data.error);
                    return;
                }
                else if (data.success) {
                    alert("<?php echo JText::_('COM_GGLMS_DATA_UPDATED'); ?>");
                    pTable.bootstrapTable('refresh');
                    hideUseModal();
                }
            })

        }); 
        
        document.getElementById('closeUserModal').addEventListener("click", function (e) {

            e.preventDefault();
            hideUseModal();

        });

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

        function customAlertifyAlertSimple(pMsg) {
    alertify.alert()
        .setting({
            'title': 'Attenzione!',
            'label':'OK',
            'message': pMsg
        }).show();
}

        function editUser(userId) {

            editingUser.value = '';
            cb_email.value = '';
            cb_nome.value = '';
            cb_cognome.value = '';
            cb_azienda.value = '';
            cb_codicefiscale.value = '';
            cb_datadinascita.value = '';
            cb_luogodinascita.value = '';
            cb_indirizzodiresidenza.value = '';
            cb_cap.value = '';
            cb_citta.value = '';

            jQuery.get( "index.php?option=com_gglms&task=users.get_anagrafica_utente", { user_id: userId } )
            .done(function(results) {

                if (typeof results != "string"
                    || results == "") {
                    customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR14'); ?>");
                    return;
                }
                else {

                    // errore
                    let objRes = JSON.parse(results);
                    if (typeof objRes.error != "undefined") {
                        customAlertifyAlertSimple(objRes.error);
                        return;
                    }
                    else {
                        //console.log(objRes.success);
                        const usersObj = objRes.success.editingUser;
                        const provNascitaObj = objRes.success.cb_provinciadinascita;
                        const provResidenzaObj = objRes.success.cb_provdiresidenza;

                        editingUser.value = usersObj.ref_dipendente;
                        cb_email.value = usersObj.cb_email;
                        cb_nome.value = usersObj.cb_nome;
                        cb_cognome.value = usersObj.cb_cognome;
                        cb_azienda.value = usersObj.cb_azienda;
                        cb_codicefiscale.value = usersObj.cb_codicefiscale;
                        cb_datadinascita.value = usersObj.cb_datadinascita;
                        cb_luogodinascita.value = usersObj.cb_luogodinascita;
                        cb_indirizzodiresidenza.value = usersObj.cb_indirizzodiresidenza;
                        cb_cap.value = usersObj.cb_cap;
                        cb_citta.value = usersObj.cb_citta;
                        cb_ruolo.value = usersObj.cb_ruolo;                        
                        provNascitaContainer.innerHTML = provNascitaObj;
                        provResidenzaContainer.innerHTML = provResidenzaObj;
                    }

                }

            })
            .fail(function() {
                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR16'); ?>");
            });

            userModal.style.display = "block";

        }

        $("#platforms").change(function () {
            jQuery("#usergroups").bootstrapTable('refresh');
            pTable.bootstrapTable('refresh');
        })

        $("#usergroups").change(function () {
            pTable.bootstrapTable('refresh');

        })

        function usergroupRequest(usergroupId){
            let params = {}
            params.usergroupId = usergroupId;

            jQuery.ajax({
                type:"GET",
                url: "index.php?option=com_gglms&task=users.getSocietaByPlatform",
                data: params,
                dataType:"json",
                success:function(data){
                    console.log(data);
                },
                // error: function (er) {
                //     params.error(er);
                // }
            })
        }

        function ajaxRequest(params) {

            let platforms = jQuery('#platforms').val();
            if (typeof platforms !== 'undefined'){
                params.data.platforms = platforms.trim();
            }
            let usergroups = jQuery('#usergroups').val();
            if (typeof usergroups !== 'undefined'){
                params.data.usergroups = usergroups.trim();
            }
            
             
            // data you may need
            console.log(params.data);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_anagrafica_utenti",
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