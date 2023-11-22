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
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    /* The Close Button */
    .close {
        color: #aaa;
        float: right;
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
</style>

<div class="container-fluid" style="position: relative;">

    <div id="toolbar" class="select">
        <button id="export" class="btn btn-sm btn-primary">Export</button>
        <button 
            id="addUser" 
            class="btn btn-sm btn-primary"
            onclick="addUser()"
            ><?php echo JText::_('COM_GGLMS_FARMACIE_NUOVO'); ?></button>
    </div>

    <table
            id="tbl_anagrafica"
            data-toggle="table"
            data-toolbar="#toolbar"
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
                <th data-field="user_actions" data-sortable="false"><?php echo JText::_('COM_GGLMS_FARMACIE_AZIONI'); ?></th>
                <th data-field="cb_block" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_FARMACIE_BLOCCATO'); ?></th>
                <th data-field="cb_farmacia" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_FARMACIA'); ?></th>
                <th data-field="cb_codiceestrenocdc3" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_3'); ?></th>
                <th data-field="cb_cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></th>
                <th data-field="cb_nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></th>
                <th data-field="cb_email" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?></th>
                <th data-field="role_title" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_RUOLO'); ?></th>
                <th data-field="cb_azienda" data-sortable="true"  data-align="center"><?php echo JText::_('COM_GGLMS_FARMACIE_AZIENDA'); ?></th>
                <th data-field="cb_filiale" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_FILIALE'); ?></th>
                <th data-field="cb_matricola" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_FARMACIE_MATRICOLA'); ?></th>
                <th data-field="cb_codicefiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></th>
                <th data-field="cb_datadinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?></th>
                <th data-field="cb_luogodinascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COMUNE_NASCITA'); ?></th>
                <th data-field="cb_provinciadinascita" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_CB_PROVINICIA_NASCITA'); ?></th>
                <th data-field="cb_indirizzodiresidenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?></th>
                <th data-field="cb_cap" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_CAP_RESIDENZA'); ?></th>
                <th data-field="cb_citta" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_CODICE_COMUNE_RESIDENZA'); ?></th>
                <th data-field="cb_provdiresidenza" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?></th>
                <th data-field="cb_dataassunzione" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_ASSUNZIONE'); ?></th>
                <th data-field="cb_datainiziorapporto" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_INIZIO_RAPPORTO'); ?></th>
                <th data-field="cb_datalicenziamento" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_LICENZIAMENTO'); ?></th>
                <!--
                <th data-field="cb_codiceestrenocdc2" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_2'); ?></th>
                <th data-field="cb_codiceestrenorep2" data-sortable="true" data-align="center"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_EXT_REP2'); ?></th>
                -->
            </tr>

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

                        <div class="col-xs-12 p-4">
                            <form class="form-horizontal" id="userEditForm">
                                <div class="form-group">
                                    <label for="cb_block" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_BLOCCATO'); ?></label>
                                    <div class="col-sm-10">
                                        <input id="cb_block" type="checkbox" value="1" />
                                        <span id="helpBlockBlocked" class="help-block"><small>Se bloccato l'utente non sarà in grado di loggare nel sistema</small></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="farmacia_id" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_FARMACIE_FARMACIA'); ?></label>
                                    <div class="col-sm-10" id="farmaciaContainer"></div>
                                </div>
                                <!--
                                <div class="form-group">
                                    <label for="cb_codiceestrenocdc3" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_3'); ?></label>
                                    <div class="col-sm-10" id="codiceCdcContainer"></div>
                                </div>
                                -->
                                <div class="form-group">
                                    <label for="cb_nome" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="cb_nome" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_cognome" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="cb_cognome" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_email" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="cb_email" placeholder="<?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_password" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR3'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="cb_password" placeholder="<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR3'); ?>">
                                        <span id="helpBlockPassword" class="help-block"><small>Se aggiunto la password sarà creata automaticamente. In modifica, verrà aggiornata solo se inserita</small></span>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="cb_codicefiscale" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control required" id="cb_codicefiscale" placeholder="<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="role_id" class="col-sm-2 control-label required"><?php echo JText::_('COM_GGLMS_FARMACIE_RUOLO'); ?></label>
                                    <div class="col-sm-10" id="roleTitleContainer"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_azienda" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_AZIENDA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w-25" id="cb_azienda" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_AZIENDA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_filiale" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_FILIALE'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w-25" id="cb_filiale" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_FILIALE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_matricola" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_MATRICOLA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w-25" id="cb_matricola" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_MATRICOLA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_datadinascita" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control w-25" id="cb_datadinascita" placeholder="<?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_luogodinascita" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_COMUNE_NASCITA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="cb_luogodinascita" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_COMUNE_NASCITA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_provinciadinascita" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_CB_PROVINICIA_NASCITA'); ?></label>
                                    <div class="col-sm-10" id="provNascitaContainer"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_indirizzodiresidenza" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="cb_indirizzodiresidenza" placeholder="<?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_cap" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_CAP_RESIDENZA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control w-25" id="cb_cap" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_CAP_RESIDENZA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_citta" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_CODICE_COMUNE_RESIDENZA'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="cb_citta" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_CODICE_COMUNE_RESIDENZA'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_provdiresidenza" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?></label>
                                    <div class="col-sm-10" id="provResidenzaContainer"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_dataassunzione" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_ASSUNZIONE'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control w-25" id="cb_dataassunzione" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_DATA_ASSUNZIONE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_datainiziorapporto" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_INIZIO_RAPPORTO'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control w-25" id="cb_datainiziorapporto" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_DATA_INIZIO_RAPPORTO'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_datalicenziamento" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_LICENZIAMENTO'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control w-25" id="cb_datalicenziamento" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_DATA_LICENZIAMENTO'); ?>">
                                    </div>
                                </div>
                                <!--
                                <div class="form-group">
                                    <label for="cb_codiceestrenocdc2" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_2'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w-25" id="cb_codiceestrenocdc2" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_2'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cb_codiceestrenorep2" class="col-sm-2 control-label"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_EXT_REP2'); ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w-25" id="cb_codiceestrenorep2" placeholder="<?php echo JText::_('COM_GGLMS_FARMACIE_COD_EXT_REP2'); ?>">
                                    </div>
                                </div>
                                -->


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
                                <input type="hidden" id="cb_codiceestrenocdc3" value="" />
                                <input type="hidden" id="role_id_old" value="" />
                                <input type="hidden" id="farmacia_id_old" value="" />
                            </form>
                        </div>

                    </div>
                </div>

        </div>

    </div>

</div>

<script type="text/javascript">

        const pTable = jQuery('#tbl_anagrafica');

        let userModal = document.getElementById('userModal');
        let editingUser = document.getElementById("editingUserId");
        
        let cb_block = document.getElementById("cb_block");
        let cb_email = document.getElementById("cb_email");
        let cb_nome = document.getElementById("cb_nome");
        let cb_cognome = document.getElementById("cb_cognome");
        let cb_azienda = document.getElementById("cb_azienda");
        let cb_filiale = document.getElementById("cb_filiale");
        let cb_matricola = document.getElementById("cb_matricola");
        let cb_codicefiscale = document.getElementById("cb_codicefiscale");
        let cb_datadinascita = document.getElementById("cb_datadinascita");
        let cb_luogodinascita = document.getElementById("cb_luogodinascita");
        
        let cb_indirizzodiresidenza = document.getElementById("cb_indirizzodiresidenza");
        let cb_cap = document.getElementById("cb_cap");
        let cb_citta = document.getElementById("cb_citta");
        let cb_dataassunzione = document.getElementById("cb_dataassunzione");
        let cb_datainiziorapporto = document.getElementById("cb_datainiziorapporto");
        let cb_datalicenziamento = document.getElementById("cb_datalicenziamento");
        let cb_codiceestrenocdc3 = document.getElementById("cb_codiceestrenocdc3");
        let role_id_old = document.getElementById("role_id_old");
        let farmacia_id_old = document.getElementById("farmacia_id_old");
        /*
        let cb_codiceestrenocdc2 = document.getElementById("cb_codiceestrenocdc2");
        let cb_codiceestrenorep2 = document.getElementById("cb_codiceestrenorep2");
        */

        
        let roleTitleContainer = document.getElementById("roleTitleContainer");
        let farmaciaContainer = document.getElementById("farmaciaContainer");
        let provNascitaContainer = document.getElementById("provNascitaContainer");
        let provResidenzaContainer = document.getElementById("provResidenzaContainer");
        //let codiceCdcContainer = document.getElementById("codiceCdcContainer");

        function hideUseModal() {

            editingUser.value = '';
            cb_block.checked = false;
            cb_email.value = '';
            cb_nome.value = '';
            cb_cognome.value = '';
            cb_azienda.value = '';
            cb_filiale.value = '';
            cb_matricola.value = '';
            cb_codicefiscale.value = '';
            cb_datadinascita.value = '';
            cb_luogodinascita.value = '';
            cb_indirizzodiresidenza.value = '';
            cb_cap.value = '';
            cb_citta.value = '';
            cb_dataassunzione.value = '';
            cb_datainiziorapporto.value = '';
            cb_datalicenziamento.value = '';
            /*
            cb_codiceestrenocdc2.value = '';
            cb_codiceestrenorep2.value = '';
            */
            cb_codiceestrenocdc3.value = '';
            role_id_old.value = '';
            farmacia_id_old.value = '';

            roleTitleContainer.innerHTML = '';
            farmaciaContainer.innerHTML = '';
            provNascitaContainer.innerHTML = '';
            provResidenzaContainer.innerHTML = '';

            userModal.style.display = "none";

        }

        document.getElementById('userEditForm').addEventListener("submit", function (e) {

            e.preventDefault();
            console.log('target', e.target);

            let farmacia_id = document.getElementById("farmacia_id");
            let role_id = document.getElementById("role_id");
            let cb_provinciadinascita = document.getElementById("cb_provinciadinascita");
            let cb_provdiresidenza = document.getElementById("cb_provdiresidenza");

            if (farmacia_id.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_FARMACIA'); ?>");
                return;
            }

            if (role_id.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_RUOLO'); ?>");
                return;
            }

            if (cb_nome.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_NOME'); ?>");
                return;
            }

            if (cb_cognome.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_COGNOME'); ?>");
                return;
            }

            if (cb_email.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_EMAIL'); ?>");
                return;
            }
            
            if (cb_codicefiscale.value == "") {
                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_CF'); ?>");
                return;
            }

            if (cb_codiceestrenocdc3.value == "") {
                alert("<?php echo JText::_('COM_GGLMS_FARMACIE_MISSING_COD_CDC_3'); ?>");
                return;
            }
            
            let formData = new FormData();
            formData.append('editingUser', editingUser.value);
            formData.append('farmacia_id', farmacia_id.value);
            formData.append('role_id', role_id.value);
            formData.append('cb_nome', cb_nome.value);
            formData.append('cb_cognome', cb_cognome.value);
            formData.append('cb_email', cb_email.value);
            
            if (cb_password.value != '') formData.append('cb_password', cb_password.value);
            
            formData.append('cb_azienda', cb_azienda.value);
            formData.append('cb_filiale', cb_filiale.value);
            formData.append('cb_matricola', cb_matricola.value);
            formData.append('cb_codicefiscale', cb_codicefiscale.value);
            formData.append('cb_datadinascita', cb_datadinascita.value);
            formData.append('cb_luogodinascita', cb_luogodinascita.value);
            formData.append('cb_provinciadinascita', cb_provinciadinascita.value);
            formData.append('cb_indirizzodiresidenza', cb_indirizzodiresidenza.value);
            formData.append('cb_cap', cb_cap.value);
            formData.append('cb_citta', cb_citta.value);
            formData.append('cb_provdiresidenza', cb_provdiresidenza.value);
            formData.append('cb_dataassunzione', cb_dataassunzione.value);
            formData.append('cb_datainiziorapporto', cb_datainiziorapporto.value);
            formData.append('cb_datalicenziamento', cb_datalicenziamento.value);
            formData.append('cb_codiceestrenocdc3', cb_codiceestrenocdc3.value);
            formData.append('role_id_old', role_id_old.value);
            formData.append('farmacia_id_old', farmacia_id_old.value);
            
            /*
            formData.append('cb_codiceestrenocdc2', cb_codiceestrenocdc2.value);
            formData.append('cb_codiceestrenorep2', cb_codiceestrenorep2.value);
            */
            formData.append('cb_block', cb_block.checked ?? 0);

            fetch('index.php?option=com_gglms&task=users.update_anagrafica_farmacista', {
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
                    alert("<?php echo JText::_('COM_GGLMS_FARMACIE_DATA_UPDATED'); ?>");
                    pTable.bootstrapTable('refresh');
                    hideUseModal();
                }
            })

        }); 
        
        document.getElementById('closeUserModal').addEventListener("click", function (e) {

            e.preventDefault();
            hideUseModal();

        });

        function changeFarmacia(selectedOption) {
            document.getElementById("cb_codiceestrenocdc3").value = selectedOption.getAttribute('data-hh');
        }

        /*
        document.getElementById('farmacia_id').addEventListener('change', function() {

            let selectedOption = this.options[this.selectedIndex];

            // Ottenere il valore dell'attributo 'data-color'
            let cdcCode = selectedOption.getAttribute('data-hh');

            document.getElementById("cb_codiceestrenocdc3").value = cdcCode;

        });
        */

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

        function editUser(userId) {

            editingUser.value = '';
            cb_block.checked = false;
            cb_email.value = '';
            cb_nome.value = '';
            cb_cognome.value = '';
            cb_azienda.value = '';
            cb_filiale.value = '';
            cb_matricola.value = '';
            cb_codicefiscale.value = '';
            cb_datadinascita.value = '';
            cb_luogodinascita.value = '';
            cb_indirizzodiresidenza.value = '';
            cb_cap.value = '';
            cb_citta.value = '';
            cb_dataassunzione.value = '';
            cb_datainiziorapporto.value = '';
            cb_datalicenziamento.value = '';
            /*
            cb_codiceestrenocdc2.value = '';
            cb_codiceestrenorep2.value = '';
            */
            cb_codiceestrenocdc3.value = '';

            jQuery.get( "index.php?option=com_gglms&task=users.get_anagrafica_farmacista", { user_id: userId } )
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
                        const qualificheObj = objRes.success.qualifiche;
                        const farmacieObj = objRes.success.farmacie;
                        const provNascitaObj = objRes.success.cb_provinciadinascita;
                        const provResidenzaObj = objRes.success.cb_provdiresidenza;
                        //const codiceCdcObj = objRes.success.cb_codiceestrenocdc3;

                        editingUser.value = usersObj.ref_dipendente;
                        cb_block.checked = usersObj.cb_block == "1" ? true : false;
                        cb_email.value = usersObj.cb_email;
                        cb_nome.value = usersObj.cb_nome;
                        cb_cognome.value = usersObj.cb_cognome;
                        cb_azienda.value = usersObj.cb_azienda;
                        cb_filiale.value = usersObj.cb_filiale;
                        cb_matricola.value = usersObj.cb_matricola;
                        cb_codicefiscale.value = usersObj.cb_codicefiscale;
                        cb_datadinascita.value = usersObj.cb_datadinascita;
                        cb_luogodinascita.value = usersObj.cb_luogodinascita;
                        cb_indirizzodiresidenza.value = usersObj.cb_indirizzodiresidenza;
                        cb_cap.value = usersObj.cb_cap;
                        cb_citta.value = usersObj.cb_citta;
                        cb_dataassunzione.value = usersObj.cb_dataassunzione;
                        cb_datainiziorapporto.value = usersObj.cb_datainiziorapporto;
                        cb_datalicenziamento.value = usersObj.cb_datalicenziamento;
                        cb_codiceestrenocdc3.value = usersObj.cb_codiceestrenocdc3;
                        role_id_old.value = usersObj.role_id;
                        farmacia_id_old.value = usersObj.farmacia_id;
                        /*
                        cb_codiceestrenocdc2.value = usersObj.cb_codiceestrenocdc2;
                        cb_codiceestrenorep2.value = usersObj.cb_codiceestrenorep2;
                        */
                        roleTitleContainer.innerHTML = qualificheObj;
                        farmaciaContainer.innerHTML = farmacieObj;
                        provNascitaContainer.innerHTML = provNascitaObj;
                        provResidenzaContainer.innerHTML = provResidenzaObj;
                        //codiceCdcContainer.innerHTML = codiceCdcObj;

                    }

                }

            })
            .fail(function() {
                customAlertifyAlertSimple("<?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR16'); ?>");
            });

            userModal.style.display = "block";

        }

        function addUser() {

            editingUser.value = '';
            cb_block.checked = false;
            cb_email.value = '';
            cb_nome.value = '';
            cb_cognome.value = '';
            cb_azienda.value = '';
            cb_filiale.value = '';
            cb_matricola.value = '';
            cb_codicefiscale.value = '';
            cb_datadinascita.value = '';
            cb_luogodinascita.value = '';
            cb_indirizzodiresidenza.value = '';
            cb_cap.value = '';
            cb_citta.value = '';
            cb_dataassunzione.value = '';
            cb_datainiziorapporto.value = '';
            cb_datalicenziamento.value = '';
            cb_codiceestrenocdc3.value = '';
            /*
            cb_codiceestrenocdc2.value = '';
            cb_codiceestrenorep2.value = '';
            */

            jQuery.get( "index.php?option=com_gglms&task=users.add_new_farmacista")
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

                        const qualificheObj = objRes.success.qualifiche;
                        const farmacieObj = objRes.success.farmacie;
                        const provNascitaObj = objRes.success.cb_provinciadinascita;
                        const provResidenzaObj = objRes.success.cb_provdiresidenza;

                        roleTitleContainer.innerHTML = qualificheObj;
                        farmaciaContainer.innerHTML = farmacieObj;
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

        function ajaxRequest(params) {

            // data you may need
            console.log(params.data);

            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=users.get_anagrafica_farmacisti",
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