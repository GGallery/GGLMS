<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>

<div class="container-fluid">

    <div id="toolbar" class="select">
        <button id="export" class="btn btn-sm btn-primary">Export</button>
    </div>

    <table
            id="tbl_quote"
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
                <th data-field="cb_cognome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR11'); ?></th>
                <th data-field="cb_nome" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR12'); ?></th>
                <th data-field="cb_email" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_EMAIL'); ?></th>
                <th data-field="cb_azienda" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_AZIENDA'); ?></th>
                <th data-field="cb_filiale" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_FILIALE'); ?></th>
                <th data-field="cb_matricola" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_FILIALE'); ?></th>
                <th data-field="cb_codicefiscale" data-sortable="true"><?php echo JText::_('COM_GGLMS_DETTAGLI_UTENTE_QUOTE_STR13'); ?></th>
                <th data-field="cb_data_nascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_DATA_NASCITA'); ?></th>
                <th data-field="cb_codice_comune_nascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COMUNE_NASCITA'); ?></th>
                <th data-field="cb_comune_nascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COMUNE_NASCITA'); ?></th>
                <th data-field="cb_pv_nascita" data-sortable="true"><?php echo JText::_('COM_GGLMS_CB_PROVINICIA_NASCITA'); ?></th>
                <th data-field="cb_indirizzo_residenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10'); ?></th>
                <th data-field="cb_cap_residenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_CAP_RESIDENZA'); ?></th>
                <th data-field="cb_comune_residenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_CODICE_COMUNE_RESIDENZA'); ?></th>
                <th data-field="cb_pv_residenza" data-sortable="true"><?php echo JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13'); ?></th>
                <th data-field="cb_data_assunzione" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_ASSUNZIONE'); ?></th>
                <th data-field="cb_data_licenziamento" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_LICENZIAMENTO'); ?></th>
                <th data-field="cb_descrizione_qualifica" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_LICENZIAMENTO'); ?></th>
                <th data-field="cb_codice_esterno_cdc_2" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_2'); ?></th>
                <th data-field="cb_codice_esterno_cdc_3" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_CDC_3'); ?></th>
                <th data-field="cb_esterno_rep_2" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_COD_EXT_REP2'); ?></th>
                <th data-field="cb_data_inizio_rapporto" data-sortable="true"><?php echo JText::_('COM_GGLMS_FARMACIE_DATA_INIZIO_RAPPORTO'); ?></th>
            </tr>

        </thead>
    </table>

</div>