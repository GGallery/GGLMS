<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class outputHelper {

    public static function buildContentBreadcrumb($id){

        $breadcrumblist= array();
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('c.*, u.idunita');
            $query->from('#__gg_unit_map AS u');
            $query->join('inner', '#__gg_contenuti as c on u.idcontenuto = c.id');
            $query->where("u.idcontenuto=" . $id);
            $query->setLimit(1);

            $db->setQuery($query);
            $content = $db->loadObject();

            $breadcrumblist[] = $content;

            $unitbreadcrumb = outputHelper::buildUnitBreadcrumb($content->idunita);

            $breadcrumblist = (array_merge(($unitbreadcrumb), $breadcrumblist));

            return $breadcrumblist;

        }catch (Exception $e){
            DEBUGG::log($e, "ERROR", 1);
        }

    }



    public static function buildUnitBreadcrumb($id){

        $currentid= $id;
        $breadcrumblist= array();

        while ($currentid > 0 ){
            $element = outputHelper::queryUnitDb($currentid);
            $breadcrumblist[]=$element;
            $currentid      = $element->unitapadre;
        }

        $breadcrumblist = array_reverse($breadcrumblist);

        return $breadcrumblist;

    }

    public  static function queryUnitDb($id){

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('id, unitapadre, titolo, alias');
            $query->from('#__gg_unit AS u');
            $query->where("u.id=" . $id);

            $db->setQuery($query);
            $res = $db->loadObject();

            return $res;
        }
        catch (Exception $e){
            echo "";
            DEBUGG::log($e, "Problemi nel creare il brearcrumb - sono nel queryUnitDb", 1);
        }
    }








    public static function DISATTIVATOmenu($item = 2, $active = null) {

        $root = outputHelper::getUnitmenu($item);
        $out = '<nav>';
        $out.=outputHelper::buildmenu($root, 0, $active);
        $out.='</nav>';
        return $out;
    }

    public static function DISATTIVATObuildmenu($items, $level = 0, $active = null) {

        // FB::log($items, "items build menu") ;
        $classlevel = "level" . $level;
        $level++;
        $badge = "";
        $out = "";


        if (sizeof($items) > 0) {
            $out = "<ul class='$classlevel list-group'>";

            foreach ($items as $item) {
                if (isset($item->titolo)) {
                    // FB::log($active."-".$item->id, "active - item id");
                    $activeclass = ($active && $active == $item->id) ? " active " : "";

                    $out .="<li class='list-group-item" . $activeclass . "'>";

                    $subUnit = outputHelper::getUnitmenu($item->id);

                    // if (sizeof($subUnit) > 0)
                    //     $badge = ' <span class="badge">' . sizeof($subUnit) . '</span>';
                    $badge = ''; //Basta scommentare le righe sopra per riattivare il numero di sottounit nel menu.

                    $out.='<a class="link' . $activeclass . '" href="' . JURI::base() . "component/gglms/unita/" . $item->alias . '">' . $item->titolo . $badge . '</span></a>';
                    $out.=outputHelper::buildmenu($subUnit, $level, $active);

                    $out.="</li>";
                }
            }
            $out.="</ul>";
        }

        return $out;
    }

    public static function DISATTIVATOgetUnitmenu($item) {
        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('*');
            $query->from('#__gg_unit AS u');
            $query->where("u.categoriapadre=" . $item);
            $query->where("u.tipologia != 110");
            $query->order("ordinamento");





            $db->setQuery($query);
            // Check for a database error.
            if ($db->getErrorNum()) {
                JError::raiseWarning(500, $db->getErrorMsg());
            }

            $res = $db->loadObjectList();

            foreach ($res as $key => $item) {
//                $sub_content = gglmsHelper::getTOTContenuti($item->id);
//                $sub_unit = gglmsHelper::getSubUnit($item->id);

//                if (!$sub_content && !$sub_unit)
//                    unset($res[$key]);
            }

            DEBUGG::log($res, " getUnitMenu");

            return $res;
        } catch (Exception $e) {

        }
    }

    public static function DISATTIVATOgetContentIconStatus($prerequisiti, $stato) {

        if (!$prerequisiti) {
            echo '<img class="img-rounded" title="Contenuto non ancora visionabile" src="components/com_gglms/images/state_red.jpg"> ';
        } else {
            if ($stato == "completed") {
                echo '<img class="img-rounded" title="Contenuto già visionato" src="components/com_gglms/images/state_green.jpg">';
            } else {
                echo '<img class="img-rounded" title="Contenuto da visionare" src="components/com_gglms/images/state_grey.jpg"> ';
            }
        }
    }

    public static function DISATTIVATOconvertiDurata($durata) {
        $m = floor(($durata % 3600) / 60);
        $s = ($durata % 3600) % 60;
        $result = sprintf('%02d:%02d', $m, $s);

        return $result;
    }

    public static function DISATTIVATOgetContent_Footer($item){

        DEBUGG::log($item, 'itemFooter');


        echo '<a href="component/gglms/contenuto/'. $item['alias'] . '"  title="'.htmlentities(utf8_decode($item['abstract'])).'" >';
        ?>
        <div class="boxContentFooter img-rounded">
            <div class="boxtitle">
                <?php
                $maxlengh = 80;
                if(strlen($item['titolo'])>$maxlengh)
                    $item['titolo'] = substr($item['titolo'], 0, $maxlengh)."...";
                echo $item['titolo'];
                ?>
            </div>

            <div class="boximg">

                <?php
                if(file_exists('../mediagg/contenuti/'.$item["id"].'/'.$item["id"].'.jpg'))
                    echo '<img class="img-responsive" src="../mediagg/contenuti/'.$item["id"].'/'.$item["id"].'.jpg">';
                else
                    echo '<img class="img-responsive" src="components/com_gglms/images/sample.jpg">';
                ?>
            </div>

            <div class="boxinfo">
                <table width="100%">
                    <tr>
                        <td rowspan="2" width="33%"><?php echo  outputHelper::getContentIconStatus($item); ?> </td>
                        <!--  <td width="33%">Durata</td>
                <td width="33%"><?php //echo outputHelper::convertiDurata($item["durata"]);   ?></td> -->
                    </tr>
                    <tr>
                        <!--  <td>Visite</td>
                <td><?php //echo $item["views"]; ?></td> -->
                    </tr>
                </table>
            </div>
        </div>
        </a>
        <?php
    }


    public static function output_select ($name, $items, $value, $text, $default=null, $class=null)
    {
        
        
        $html = '<select id="'.$name.'" name="'.$name.'" class="'.$class.'">';

        foreach ($items as $item)
        {
                $selected = ($item->$value == $default) ? 'selected="selected"' : '';

                $html .= "<option value=".$item->$value." $selected>".$item->$text."</option>";
        }
        $html .= "</select>";
        return $html;
    }

    public static function getDettaglioVisione($durata = 0,
                                               $tempo_visualizzato,
                                               $con_orari = false,
                                               $tempo_assenza = null) {

        $_html = "";
        // con orari customizzati
        if ($con_orari
            && !is_null($tempo_assenza)) {

            $_html = self::buildRowDettagliTemporali($durata, $tempo_visualizzato, $tempo_assenza);
HTML;
        } // calcolo la % completamento su progress bar
        else if ($durata > 0
            && $tempo_visualizzato <= $durata) {
            $perc_completamento = ($tempo_visualizzato/$durata)*100;
            // rendo int la %
            $perc_completamento = round($perc_completamento);
            // bg della barra in base a %
            $style_barra = self::setProgressBarStyle($perc_completamento);
            $_cell_title1 = JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR4');
            $_cell_title2 = JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR5');
            //$durata_ore = gmdate("H:i:s", $durata);
            $durata_ore = utilityHelper::sec_to_hr($durata);
            $_html = <<<HTML
            <div class="row">
                <div class="col-xs-6"><strong>{$_cell_title1}:</strong> {$durata_ore}</div>
            </div>
            <div class="row">
                <div class="col-xs-6"><strong>{$_cell_title2}</strong></div>
            </div>
            <div class="row">
                <div class="col-xs-10">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped {$style_barra}" 
                            role="progressbar" 
                            style="width: {$perc_completamento}%; height: 100% !important; color: black; font-weight: bold;" aria-valuenow="{$perc_completamento}" aria-valuemin="0" aria-valuemax="100">{$perc_completamento}%</div>
                    </div>
                </div>
            </div>
HTML;

        }
        // converto in ore i secondi
        else {
            //$ore_visualizzazione = gmdate("H:i:s", $tempo_visualizzato);
            $ore_visualizzazione = UtilityHelper::sec_to_hr($tempo_visualizzato);
            $_cell_title = JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR2');
            $_html = <<<HTML
            <div class="row">
                <div class="col-xs-6">{$_cell_title}:</div>
                <div class="col-xs-3">{$ore_visualizzazione}</div>
            </div>
HTML;
        }

        return $_html;
    }

    private static function buildRowDettagliTemporali($durata, $visualizzazione, $assenza, $is_totale = false) {

        $_html = "";

        $ore_durata = utilityHelper::sec_to_hr($durata);
        $ore_visualizzazione = utilityHelper::sec_to_hr($visualizzazione);
        $ore_assenza = utilityHelper::sec_to_hr($assenza);
        $_cell_title1 = ($is_totale) ? JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR10') . ' ' : "";
        $_cell_title1 .= JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR6');
        $_cell_title2 = ($is_totale) ? JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR10') . ' ' : "";
        $_cell_title2 .= JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR7');
        $_cell_title3 = ($is_totale) ? JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR10') . ' ' : "";
        $_cell_title3 .= JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR8');
        $_row_style = "";

        if ($is_totale) {
            $_html .= <<<HTML
            <div class="row">
                &nbsp;
            </div>

HTML;
            $_row_style = 'style="background: #cce5ff; font-weight: bolder;"';
        }
        $_html .= <<<HTML
            <div class="row" {$_row_style}>
                <div class="col-xs-2">{$_cell_title1}:</div>
                <div class="col-xs-2">{$ore_durata}</div>
                <div class="col-xs-2">{$_cell_title2}:</div>
                <div class="col-xs-2">{$ore_visualizzazione}</div>
                <div class="col-xs-2">{$_cell_title3}:</div>
                <div class="col-xs-2">{$ore_assenza}</div>
            </div>
HTML;

        return $_html;

    }

    public static function getRowTotaleCorso($totale_durata,
                                             $totale_visualizzazione,
                                             $con_orari = false,
                                             $totale_assenza = 0) {

        $_html = "";
        if ($con_orari) {
            $_html = self::buildRowDettagliTemporali($totale_durata, $totale_visualizzazione, $totale_assenza, true);
        }
        else {
            $perc_completamento = 0;

            if ($totale_durata > 0
                && $totale_visualizzazione <= $totale_durata) {
                $perc_completamento = ($totale_visualizzazione / $totale_durata) * 100;
                // rendo int la %
                $perc_completamento = round($perc_completamento);
                // bg della barra in base a %
            }

            $style_barra = self::setProgressBarStyle($perc_completamento);

            $_cell_title = JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR3');
            $_html .= <<<HTML
            <div class="row">
                <div class="col-xs-6">
                    <h5><strong>{$_cell_title}</strong></h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-10">
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar {$style_barra}" 
                            role="progressbar" 
                            style="width: {$perc_completamento}%; height: 100%; color: black; font-weight: bold;" aria-valuenow="{$perc_completamento}" aria-valuemin="0" aria-valuemax="100">{$perc_completamento}%</div>
                    </div>
                </div>
            </div>
HTML;
        }

        return $_html;

    }

    public static function setProgressBarStyle($perc_bar) {

        switch ($perc_bar) {

            case ($perc_bar <= 10):
                return "bg-danger";

            case ($perc_bar > 10 && $perc_bar <= 50):
                return "bg-warning";

            case ($perc_bar > 50 && $perc_bar <= 75):
                return "bg-info";

            case ($perc_bar > 75 && $perc_bar <= 100):
                return "bg-success";

            default:
                return "";
        }

    }

    public static function buildRowsDettaglioCorsi($arr_corsi, $arr_dettaglio_corsi, $con_orari) {

        try {

            $cards = 0;
            $semaforo_totale = true;
            $totale_durata  = 0;
            $totale_visualizzazione = 0;
            $totale_assenza = 0;
            $corsi = 0;

            // se ci sono più corsi visualizzerò un riga in più con i totali delle durate dei singoli corsi e delle visualizzazioni
            if (count($arr_dettaglio_corsi) > 1) {
                $semaforo_totale = true;
            }

            $_html = <<<HTML
            <div id="accordion">
HTML;

            foreach ($arr_dettaglio_corsi as $id_padre => $sub_corso) {

                $titolo_padre = utilityHelper::getTitoloCorsoPadre($id_padre, $arr_corsi);

                $_html .= <<<HTML
                <div class="card">
                    <div class="card-header" id="heading-{$cards}">
                        <h5 class="mb-0">
                            <button class="btn btn-link" 
                                    data-toggle="collapse" 
                                    data-target="#collapse-{$cards}" 
                                    aria-expanded="true" 
                                    aria-controls="collapse-{$cards}" 
                                    style="background: #fff; color: red; line-height: inherit;">
                                <strong>{$titolo_padre}</strong>
                            </button>
                        </h5>
                    </div>
                    <div id="collapse-{$cards}" 
                         class="collapse show" 
                         aria-labelledby="heading-{$cards}" 
                         data-parent="#accordion">
                        <div class="card-body">
HTML;
                foreach ($sub_corso as $key => $corso) {

                    // se anche uno dei corsi ha durata 0 non visualizzo la barra dei totale
                    if ($corso['durata_evento'] == 0)
                        $semaforo_totale = false;

                    $tempo_assenza = (isset($corso['tempo_assenza']) && $con_orari) ? $corso['tempo_assenza'] : null;
                    $dettaglio_visione = self::getDettaglioVisione($corso['durata_evento'],
                                                                $corso['tempo_visualizzato'],
                                                                $con_orari,
                                                                $tempo_assenza);
                    $titolo_evento = (!$con_orari) ? $corso['titolo_evento'] : JText::_('COM_GGLMS_CRUSCOTTO_ORE_STR9') . ' ' . $corso['data_accesso'];
                    $_html .= <<<HTML
                       <div class="row">
                            <div class="col-xs-6">
                                <h6><strong>{$titolo_evento}</strong></h6>
                            </div>
                       </div>
                       {$dettaglio_visione}
HTML;
                    if (!$con_orari)
                        $totale_durata += $corso['durata_evento'];
                    else {
                        $totale_durata = $corso['totale_durata'];
                        $totale_assenza += $corso['tempo_assenza'];
                    }

                    $totale_visualizzazione += $corso['tempo_visualizzato'];
                    $corsi++;
                }

                if ($semaforo_totale
                    && $corsi > 1)
                    $_html .= self::getRowTotaleCorso($totale_durata,
                                                    $totale_visualizzazione,
                                                    $con_orari,
                                                    $totale_assenza);

                $_html .= <<<HTML
                        </div><!-- card-body -->
                    </div> <!-- collapse show -->
                </div> <!-- card -->
HTML;
                $cards++;
            }

            $_html .= <<<HTML
            </div> <!-- accordion -->
HTML;

            return $_html;

        } catch (Exception $e) {
            DEBUGG::error($e, __FUNCTION__);
        }

    }

    public static function buildListaAzienda($lista_azienda) {

        if (isset($lista_azienda)) {
            $_selected = "";
            $_default = "";
            if (count($lista_azienda) == 1)
                $_selected = "selected";
            else {
                $_option_label = JText::_('COM_GGLMS_GLOBAL_SCEGLI_AZIENDA');
                $_default = <<<HTML
                <option value="">{$_option_label}</option>
HTML;
            }
            $_company_label = JText::_('COM_GGLMS_GLOBAL_COMPANY');
            echo <<<HTML
                <div class="form-group row">
                    <label class="col-sm-2" for="id_azienda">{$_company_label}:</label>
                    <div class="col-sm-10">
                        <select required placeholder="Azienda" type="text" class="form-control cpn_opt"
                                id="id_azienda" name="id_azienda">
                            {$_default}
HTML;

                            foreach ($lista_azienda as $key => $az) {
                                echo <<<HTML
                                <option value="{$az['id_gruppo']}" {$_selected}>
                                    {$az['azienda']}
                                </option>
HTML;
                            }
            echo <<<HTML
                        </select>
                    </div>
                </div>
HTML;
        }

    }

    public static function buildFiltroAzienda($usergroups) {

        if (isset ($usergroups)) {
            if (count($usergroups) > 1) {
                $_select_output = outputHelper::output_select('usergroups', $usergroups, 'id', 'title', 2, 'refresh');
                $_company_label = JText::_('COM_GGLMS_GLOBAL_COMPANY');
                echo <<<HTML
                <div class="form-group">
                    <label for="usergroups">{$_company_label}</label><br>
                    {$_select_output}
                </div>
HTML;
            }
            else {
                echo <<<HTML
                <input type="hidden" name="usergroups" id="usergroups" value="{$usergroups[0]->id}"/>
HTML;
            }

        }

    }

    // panel_jumper output
    public static function buildPanelJumperBox($obj_jumper, $obj_contenuto) {

        $i = 0;
        $lista_jumper = "";
        foreach ($obj_jumper as $var) {
            $_titolo = $var['titolo'];
            $_tstart = $var['tstart'];

            //Genero il minutaggio del Jumper
            $h = floor($_tstart / 3600);
            $m = floor(($_tstart % 3600) / 60);
            $s = ($_tstart % 3600) % 60;
            $_durata = sprintf('%02d:%02d:%02d', $h, $m, $s);

            //DIV ID del jumper che serve poi impostare il colore di background
            $_jumper_div_id = $i;

            //Anteprima Jumper
            $_id_contenuto = JRequest::getInt('id', 0);

            $_img_contenuto = $obj_contenuto->_path . "images/normal/Slide" . ($i + 1) . ".jpg";
            $_background = "background-image: url('" . $_img_contenuto . "'); background-size: 60px 50px; background-position: center;  width: 60px; height: 50px;";
//            $class = ($this->elemento['track']['cmi.core.lesson_status'] == 'completed') ? 'enabled' : 'disabled';
            $class = ($obj_contenuto->getStato()->completato) ? 'enabled' : 'disabled';

            $jumper = '<div class="jumper ' . $class . '" id="' . $_jumper_div_id . '" rel="' . $_tstart . '">';
            // $jumper.='<div class="anteprima_jumper" style="' . $_background . '"></div>';
            $jumper .= $_durata . "<br>" . $_titolo;
            $jumper .= '</div>';
            //echo $jumper;
            $lista_jumper .= $jumper;
            $i++;
        }

        return $lista_jumper;
    }

    // costruisco la vista da presentare dopo aver effettuato l'operazione di pagamento
    public static function get_result_view($target, $call_result, $redirect=null) {

        $_html = "";
        $_result_class = "success";
        $_result_icon = "fa-check";
        $_result_msg = "L'operazione è andata a buon fine, puoi effettura il login alla tua area riservata";
        $_result_extra = "";
        //$_href = (!is_null($redirect) && $redirect != "") ? $redirect : "index.php";
        $_href = utilityHelper::set_index_redirect_url($redirect);

        if ($target == "sinpe"
            || $target == "servizi_extra"
            || $target == "acquistaevento") {

            $_result_extra = <<<HTML
            <p class="text-center">
                Sarai reindirizzato in 5 secondi, altrimenti clicca <a href="index.php">QUI</a>
            </p>
            <script>
                 setTimeout(function(){
                    window.location.href = "{$_href}";
                 }, 5000);
            </script>
HTML;


            if ($call_result != "tuttook") {
                $_result_class = "danger";
                $_result_icon = "fa-times";
                $_result_msg = " L'operazione non è andata a buon fine, di seguito il dettaglio dell'errore:";
                $_result_extra = <<<HTML
                        <p class="text-center">
                            <pre>{$call_result}</pre>
                        </p>
HTML;

            }

            $_html = <<<HTML

                     <div class="row">
                        <div class="col-12">
                            <div class="alert alert-{$_result_class}" role="alert">

                                <p class="text-center">
                                    <i class="fas {$_result_icon} fa-5x"></i>
                                </p>

                                <p class="text-center">
                                    {$_result_msg}
                                </p>
                                
                                {$_result_extra}

                            </div>
                        </div>
                    </div>
    
HTML;

        }

        return $_html;

    }

    // messaggio di errore in ingresso al form di pagamento
    public static function get_payment_form_error($msg, $redirect=null) {

        //$_href = (!is_null($redirect) && $redirect != "") ? $redirect : "index.php";
        $_href = utilityHelper::set_index_redirect_url($redirect);

        $_html = <<<HTML
            <script>
                alert('{$msg}');
                window.location.href = "{$_href}";
            </script>
HTML;

        return $_html;

    }

    public static function get_user_registration_form_sponsor_evento($_params, $id_evento) {

        try {

            $_title_advise = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR1');
            $_cb_nome  = UtilityHelper::get_cb_field_name($_params, 'campo_cb_nome', 'name');
            $_cb_cognome = UtilityHelper::get_cb_field_name($_params, 'campo_cb_cognome', 'name');
            $_cb_cf = UtilityHelper::get_cb_field_name($_params, 'campo_cb_cf', 'name');
            $_cb_data_nascita = UtilityHelper::get_cb_field_name($_params, 'campo_cb_data_nascita', 'name');
            $_cb_luogo_nascita = UtilityHelper::get_cb_field_name($_params, 'campo_cb_luogodinascita', 'name');
            $_cb_provincia_nascita = UtilityHelper::get_cb_field_name($_params, 'campo_cb_provinciadinascita', 'name');
            $_cb_indirizzo = UtilityHelper::get_cb_field_name($_params, 'campo_cb_indirizzo', 'name');
            $_cb_citta = UtilityHelper::get_cb_field_name($_params, 'campo_cb_citta', 'name');
            $_cb_cap = UtilityHelper::get_cb_field_name($_params, 'campo_cb_cap', 'name');
            $_cb_provincia = UtilityHelper::get_cb_field_name($_params, 'campo_cb_provincia', 'name');
            $_cb_telefono = UtilityHelper::get_cb_field_name($_params, 'campo_cb_telefono', 'name');
            $_cb_professione_disciplina = UtilityHelper::get_cb_field_name($_params, 'campo_cb_professione_disciplina', 'name');
            $_cb_ordine = UtilityHelper::get_cb_field_name($_params, 'campo_cb_ordine', 'name');
            $_cb_numeroiscrizione = UtilityHelper::get_cb_field_name($_params, 'campo_cb_numeroiscrizione', 'name');
            $_cb_ruolo = UtilityHelper::get_cb_field_name($_params, 'campo_cb_ruolo', 'name');
            $_cb_reclutamento = UtilityHelper::get_cb_field_name($_params, 'campo_cb_reclutamento', 'name');

            $_cb_provincia_nascita_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_provinciadinascita');
            $_cb_provincia_nascita_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_provinciadinascita');

            $_cb_provincia_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_provincia');
            $_cb_provincia_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_provincia');

            $_cb_professione_disciplina_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_professione_disciplina');
            $_cb_professione_disciplina_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_professione_disciplina');

            $_cb_ordine_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_provincia');
            $_cb_ordine_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_provincia');

            $_cb_ruolo_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_ruolo');
            $_cb_ruolo_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_ruolo');

            $_cb_reclutamento_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_reclutamento');
            $_cb_reclutamento_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_reclutamento');

            $_label_registrazione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR5');
            $_label_username = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR2');
            $_label_password = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR3');
            $_label_email = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR20');
            $_label_nome = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR4');
            $_label_cognome = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR5');
            $_label_cf = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR6');
            $_label_dt_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR7');
            $_label_citta_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR8');
            $_label_pv_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR9');
            $_label_indirizzo = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR10');
            $_label_citta = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR11');
            $_label_cap = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR12');
            $_label_pv = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR13');
            $_label_telefono = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR14');
            $_label_professione = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR15');
            $_label_ruolo = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR16');
            $_label_ordine = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR17');
            $_label_iscrizione = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR18');
            $_label_reclutamento = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR19');

            $token = UtilityHelper::build_token_url(0, $id_evento, 0, 0, 0, 0);
            $_ref_registrazione = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'user_registration_sponsor_request');


            $_html = <<<HTML
            <link href="components/com_gglms/libraries/css/custom-form.css" rel="stylesheet" />

            <div class="row">
                <div class="col-12">
                    <h5><span style="color: black; font-weight: bold">{$_title_advise}</span></h5>
                </div>
            </div>
            <hr />
            <div class="container-form">
                <form>
                
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="username">{$_label_username}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="username" style="width: 320px;" placeholder="{$_label_username}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="password_utente">{$_label_password}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="password" id="password_utente" style="width: 220px;" value="" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="email_utente">{$_label_email}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="email" id="email_utente" style="width: 220px;" placeholder="{$_label_email}" />
                      </div>
                    </div>
                
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="nome_utente">{$_label_nome}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="nome_utente" style="width: 320px;" data-campo="{$_cb_nome}" placeholder="{$_label_nome}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="cognome_utente">{$_label_cognome}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="cognome_utente" style="width: 320px;" data-campo="{$_cb_cognome}" placeholder="{$_label_cognome}" />
                      </div>
                    </div>
                    
                     <div class="rowcustom">
                      <div class="col-25">
                        <label for="cf_utente">{$_label_cf}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="cf_utente" style="width: 320px;" maxlength="16" data-campo="{$_cb_cf}" placeholder="{$_label_cf}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="data_nascita_utente">{$_label_dt_nascita}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control datepicker" type="text" id="data_nascita_utente" style="width: 220px;"  data-campo="{$_cb_data_nascita}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="citta_nascita_utente">{$_label_citta_nascita}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="citta_nascita_utente" style="width: 220px;" data-campo="{$_cb_luogo_nascita}" placeholder="{$_label_citta_nascita}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="pv_nascita_utente">{$_label_pv_nascita}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="pv_nascita_utente" data-campo="{$_cb_provincia_nascita}" data-id-ref="{$_cb_provincia_nascita_id}">
                                <option value="">-</option>
                                {$_cb_provincia_nascita_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="citta_utente">{$_label_indirizzo}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="indirizzo_utente" style="width: 420px;" data-campo="{$_cb_indirizzo}" placeholder="{$_label_indirizzo}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="citta_utente">{$_label_citta}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="citta_utente" style="width: 220px;" data-campo="{$_cb_citta}" placeholder="{$_label_citta}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="cap_utente">{$_label_cap}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="cap_utente" style="width: 120px;" data-campo="{$_cb_cap}" placeholder="{$_label_cap}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="pv_utente">{$_label_pv}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="pv_utente" data-campo="{$_cb_provincia}" data-id-ref="{$_cb_provincia_id}">
                                <option value="">-</option>
                                {$_cb_provincia_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="telefono_utente">{$_label_telefono}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="telefono_utente" style="width: 220px;" data-campo="{$_cb_telefono}"  placeholder="{$_label_telefono}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="professione_utente">{$_label_professione}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="professione_utente" data-campo="{$_cb_professione_disciplina}" data-id-ref="{$_cb_professione_disciplina_id}">
                                <option value="">-</option>
                                {$_cb_professione_disciplina_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="ruolo_utente">{$_label_ruolo}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="ruolo_utente" data-campo="{$_cb_ruolo}" data-id-ref="{$_cb_ruolo_id}">
                                <option value="">-</option>
                                {$_cb_ruolo_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="ordine_utente">{$_label_ordine}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="ordine_utente" data-campo="{$_cb_ordine}" data-id-ref="{$_cb_ordine_id}">
                                <option value="">-</option>
                                {$_cb_ordine_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="iscrizione_albo_utente">{$_label_iscrizione}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="iscrizione_albo_utente" style="width: 220px;" data-campo="{$_cb_numeroiscrizione}"  placeholder="{$_label_iscrizione}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="reclutamento_utente">{$_label_reclutamento}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="reclutamento_utente" data-campo="{$_cb_reclutamento}" data-id-ref="{$_cb_reclutamento_id}">
                                <option value="">-</option>
                                {$_cb_reclutamento_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-large btn-primary btn-registrazione" data-ref="{$_ref_registrazione}">{$_label_registrazione}</button>
                        </div>
                     </div>
                     <input type="hidden" id="token" value="{$token}" />
                     <input type="hidden" id="id_evento" value="{$id_evento}" />
                    
                </form>
            </div>

HTML;

            $_ret['success'] = $_html;
            return $_ret;

        }
        catch (Exception $e) {
            DEBUGG::log(json_encode($e->getMessage()), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }

    }

    public static function get_user_registration_form_acquisto_evento($unit_prezzo,
                                                                      $unit_id,
                                                                      $user_id,
                                                                      $sconto_data,
                                                                      $sconto_custom,
                                                                      $in_groups,
                                                                      $_params) {

        try {

            $_ret = array();

            $_title_advise = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR6');
            $_cb_nome  = UtilityHelper::get_cb_field_name($_params, 'campo_cb_nome', 'name');
            $_cb_cognome = UtilityHelper::get_cb_field_name($_params, 'campo_cb_cognome', 'name');
            $_cb_cf = UtilityHelper::get_cb_field_name($_params, 'campo_cb_cf', 'name');
            $_cb_data_nascita = UtilityHelper::get_cb_field_name($_params, 'campo_cb_data_nascita', 'name');
            $_cb_telefono = UtilityHelper::get_cb_field_name($_params, 'campo_cb_telefono', 'name');
            $_cb_anno_laurea = UtilityHelper::get_cb_field_name($_params, 'campo_cb_annolaurea', 'name');
            $_cb_professione_disciplina = UtilityHelper::get_cb_field_name($_params, 'campo_cb_professione_disciplina', 'name');
            $_cb_laureain = UtilityHelper::get_cb_field_name($_params, 'campo_cb_laureain', 'name');
            $_cb_citta = UtilityHelper::get_cb_field_name($_params, 'campo_cb_citta', 'name');
            $_cb_provincia = UtilityHelper::get_cb_field_name($_params, 'campo_cb_provincia', 'name');
            $_cb_indirizzo = UtilityHelper::get_cb_field_name($_params, 'campo_cb_indirizzo', 'name');
            $_cb_cap = UtilityHelper::get_cb_field_name($_params, 'campo_cb_cap', 'name');
            // lista options da community builder
            $_cb_professione_disciplina_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_professione_disciplina');
            $_cb_professione_disciplina_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_professione_disciplina');
            $_cb_laureain_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_laureain');
            $_cb_laureain_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_laureain');
            $_cb_provincia_options = UtilityHelper::get_cb_field_select($_params,'campo_cb_provincia');
            $_cb_provincia_id = UtilityHelper::get_params_from_object($_params, 'campo_cb_provincia');

            $_label_registrazione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR5');
            $_label_nome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR8');
            $_label_cognome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR9');
            $_label_password = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR10');
            $_label_r_password = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR11');
            $_label_cf = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR12');
            $_label_email = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR13');
            $_label_indirizzo = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR14');
            $_label_citta = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR15');
            $_label_pv = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR16');
            $_label_cap = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR17');
            $_label_dt_nascita = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR18');
            $_label_tel = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR19');
            $_label_professione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR20');
            $_label_laurea_in = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR21');
            $_label_anno_laurea = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR22');

            $token = UtilityHelper::build_token_url($unit_prezzo, $unit_id, $user_id, $sconto_data, $sconto_custom, $in_groups);
            $_ref_registrazione = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'user_registration_request');

            $_html = <<<HTML
            
            <link href="components/com_gglms/libraries/css/custom-form.css" rel="stylesheet" />
            
            <div class="row">
                <div class="col-12">
                    <h5><span style="color: black; font-weight: bold">{$_title_advise}</span></h5>
                </div>
            </div>
            <hr />
            <div class="container-form">
                <form>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="nome_utente">{$_label_nome}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="nome_utente" style="width: 320px;" data-campo="{$_cb_nome}" placeholder="{$_label_nome}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="cognome_utente">{$_label_cognome}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="cognome_utente" style="width: 320px;" data-campo="{$_cb_cognome}" placeholder="{$_label_cognome}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="password_utente">{$_label_password}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="password" id="password_utente" style="width: 220px;" value="" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="ripeti_password_utente">{$_label_r_password}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="password" id="ripeti_password_utente" style="width: 220px;" value="" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="cf_utente">{$_label_cf}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="cf_utente" style="width: 320px;" maxlength="16" data-campo="{$_cb_cf}" placeholder="{$_label_cf}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="email_utente">{$_label_email}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="email" id="email_utente" style="width: 220px;" placeholder="{$_label_email}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="citta_utente">{$_label_indirizzo}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="indirizzo_utente" style="width: 420px;" data-campo="{$_cb_indirizzo}" placeholder="{$_label_indirizzo}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="citta_utente">{$_label_citta}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="citta_utente" style="width: 220px;" data-campo="{$_cb_citta}" placeholder="{$_label_citta}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="pv_utente">{$_label_pv}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="pv_utente" data-campo="{$_cb_provincia}" data-id-ref="{$_cb_provincia_id}">
                                <option value="">-</option>
                                {$_cb_provincia_options}
                            </select>
                      </div>
                    </div>
                    
                     <div class="rowcustom">
                      <div class="col-25">
                        <label for="cap_utente">{$_label_cap}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="cap_utente" style="width: 120px;" data-campo="{$_cb_cap}" placeholder="{$_label_cap}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="data_nascita_utente">{$_label_dt_nascita}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control datepicker" type="text" id="data_nascita_utente" style="width: 220px;"  data-campo="{$_cb_data_nascita}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="telefono_utente">{$_label_tel}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="telefono_utente" style="width: 220px;" data-campo="{$_cb_telefono}"  placeholder="{$_label_tel}" />
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="professione_utente">{$_label_professione}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="professione_utente" data-campo="{$_cb_professione_disciplina}" data-id-ref="{$_cb_professione_disciplina_id}">
                                <option value="">-</option>
                                {$_cb_professione_disciplina_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="laureain_utente">{$_label_laurea_in}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-25">
                        <select class="form-control" id="laureain_utente" data-campo="{$_cb_laureain}" data-id-ref="{$_cb_laureain_id}">
                                <option value="">-</option>
                                {$_cb_laureain_options}
                            </select>
                      </div>
                    </div>
                    
                    <div class="rowcustom">
                      <div class="col-25">
                        <label for="anno_laurea_utente">{$_label_anno_laurea}<span style="color: red">*</span></label>
                      </div>
                      <div class="col-75">
                        <input class="form-control" type="text" id="anno_laurea_utente" style="width: 220px;" data-campo="{$_cb_anno_laurea}"  placeholder="Anno di laurea" />
                      </div>
                    </div>
               
                     <div class="rowcustom">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-large btn-primary btn-registrazione" data-ref="{$_ref_registrazione}">{$_label_registrazione}</button>
                        </div>
                     </div>
                     <input type="hidden" id="token" value="{$token}" />
                </form>
            </div>
HTML;
            $_ret['success'] = $_html;
            return $_ret;

        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }
    }

    // avverte l'utente della buona riuscita della registrazione e lo reindirizza in home page
    public static function get_user_registration_confirm_acquisto_evento($unit_prezzo,
                                                                         $unit_id,
                                                                         $user_id,
                                                                         $sconto_data,
                                                                         $in_groups,
                                                                         $_params) {

        try {

            $_ret = array();

            $_href = utilityHelper::set_index_redirect_url();
            $_ref_accedi = "index.php?option=com_comprofiler&view=login";

            $_html = <<<HTML
                <div class="jumbotron">
                    <h4>Grazie!</h4>
                    <p>La tua registrazione è andata a buon fine
                        <br />
                        Riceverai una E-Mail all'indirizzo indicato durante la registrazione con i tuoi dati
                        <br />
                        Accedi alla pagina di <a href="{$_ref_accedi}">LOGIN</a> 
                        Oppure tra 10 secondi sarai reindirizzato alla pagina <a href="{$_href}">HOME</a> 
                        </p>
                </div>

                <script>
                    setTimeout(function () {
                        window.location.href = "{$_href}";     
                    }, 10000);
                   
                </script>
HTML;
            $_ret['success'] = $_html;
            return $_ret;

        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }

    }

    // reindirizza l'utente al login oppure ad una registrazione molto rapida per consetire di usufruire soltanto dell'evento
    public static function get_user_action_request_form_acquisto_evento($unit_prezzo,
                                                                          $unit_id,
                                                                          $user_id,
                                                                          $sconto_data,
                                                                          $sconto_custom,
                                                                          $in_groups,
                                                                          $_params) {
        try {

            $_ret = array();

            $_title_advise = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR3');
            $_label_accedi = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR4');
            $_label_registrazione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR5');
            $_ref_accedi = "index.php?option=com_comprofiler&view=login";

            $token = UtilityHelper::build_token_url($unit_prezzo, $unit_id, $user_id, $sconto_data, $sconto_custom, $in_groups);
            $_ref_registrazione = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'new_user_request');

            $_html = <<<HTML
            <div class="row">
                <div class="col-12">
                    <h5><span style="color: black; font-weight: bold">{$_title_advise}</span></h5>
                </div>
            </div>
            <hr />
            <br />
            <br />
            <div class="row">
                <div class="col-xs-6 text-center">
                    <button class="btn btn-large btn-primary btn-request" data-ref="{$_ref_accedi}">{$_label_accedi}</button>
                </div>
                <div class="col-xs-6 text-center">
                    <button class="btn btn-large btn-primary btn-request" data-ref="{$_ref_registrazione}">{$_label_registrazione}</button>
                </div>
            </div>
HTML;

            $_ret['success'] = $_html;
            return $_ret;

        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }

    }

    // semplice messaggio che informa l'utente che la sua richiesta di pagamento sarà elaborata post ricezione bonifico
    public static function get_payment_form_acquisto_evento_bonifico($user_id, $_event_title, $totale, $_params) {

        try {

            $_ret = array();

            $_email_to  = UtilityHelper::get_params_from_object($_params, 'email_default');
            $_href = utilityHelper::set_index_redirect_url();
            $_html = <<<HTML
                <div class="jumbotron">
                    <h4>Grazie!</h4>
                    <p>Per confermare l'iscrizione all'evento  <b>{$_event_title}</b> invia una copia della ricevuta a <b>{$_email_to}</b> 
                        con le seguenti indicazioni: <br />
                        <b>Nome</b> e <b>Cognome</b> - <b>Titolo del corso acquistato</b> - <b>Codice fiscale</b> - <b>Recapito telefonico</b>
                        <br />
                        Tra 20 secondi sarai reindirizzato alla pagina <a href="{$_href}">HOME</a> 
                        </p>
                </div>

                <script>
                    setTimeout(function () {
                        window.location.href = "{$_href}";     
                    }, 20000);
                   
                </script>
HTML;
            $_ret['success'] = $_html;
            return $_ret;
        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }
    }

    // form di pagamento per acquisto di un evento a calendario
    public static function get_payment_form_acquisto_evento($unit_prezzo,
                                                            $unit_id,
                                                            $user_id,
                                                            $sconto_data,
                                                            $sconto_custom,
                                                            $in_groups,
                                                            $_params) {

        try {

            $_ret = array();
            $_html = "";

            // controllo se il prezzo è valido
            if ($unit_prezzo == ""
                || $unit_prezzo == 0)
                throw new Exception("Il prezzo indicato per la transazione non è valido", 1);

            // controllo se l'unita indicata è valida
            if ($unit_id == ""
                || $unit_id == 0)
                throw new Exception("L'unità indicata per la transazione non è valida", 1);

            // mi servono informazioni sull'unita
            $unit_model = new gglmsModelUnita();
            $_unit = $unit_model->getUnita($unit_id);

            $dt = new DateTime($_unit->data_inizio);
            $_tipo_sconto = UtilityHelper::get_tipo_sconto_evento($sconto_data, $sconto_custom, $in_groups, $_unit);

            $_descr_checkbox_evento = "Acquisto " . $_unit->titolo;
            $_descr_checkbox_evento .= $_tipo_sconto['descrizione_sconto'] != "" ? ' ' . $_tipo_sconto['descrizione_sconto'] : '';

            $_style_totale = $_tipo_sconto['label_sconto'] != "" ? 'style="text-decoration: line-through;"' : '';

            $_descr_attr_evento = $_unit->alias;
            $_descr_attr_evento .= ($sconto_data == 1) ? '-sc_data' : '';
            $_descr_attr_evento .= ($in_groups == 1) ? '-sc_gruppo' : '';

            $_descrizione_hidden = $_descr_attr_evento;

            $_testo_pagamento_paypal = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR1');
            $_testo_pagamento_bonifico_btn = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR2');
            $_testo_pagamento_bonifico = UtilityHelper::get_params_from_object($_params, 'testo_pagamento_bonifico');
            $_row_pagamento_bonfico = "";

            $token = UtilityHelper::build_token_url($unit_prezzo, $unit_id, $user_id, $sconto_data, $sconto_custom, $in_groups);
            $endpoint = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'bb_buy_request');

            if ($_testo_pagamento_bonifico != "")

                $_row_pagamento_bonfico = <<<HTML
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            {$_testo_pagamento_bonifico}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <button class="btn btn-primary" id="btn-bonifico" data-ref="{$endpoint}">{$_testo_pagamento_bonifico_btn}</button>
                        </td>
                    </tr>
HTML;


            $_html = <<<HTML
                    <table style="width: 100%;">
HTML;

            $_html .= <<<HTML
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <h4><span style="color: black; font-weight: bold">{$_testo_pagamento_paypal}</span></h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" value="{$unit_prezzo}" id="evento_da_pagare" data-descr="{$_descr_attr_evento}" checked />
                        </td>
                        <td>&nbsp;</td>
                        <td id="cella_evento">
                            {$_descr_checkbox_evento}
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <h5>€ <b>{$unit_prezzo}</b></h5>
                        </td>
                    </tr>
                     <tr>
                        <td colspan="2">&nbsp;</td>
                        <td><h5><b>TOTALE</b></h5></td>
                        <td>&nbsp;</td>
                        <td><h5>€ <b><span id="amount_span" {$_style_totale}>{$_unit->prezzo}</span> {$_tipo_sconto['label_sconto']}</b></h5></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <span id="paypal-button-container"></span>
                        </td>
                    </tr>
                    {$_row_pagamento_bonfico}
                </table>
                
                <input style="display: none;" type="number" id="amount" name="amount" value="{$unit_prezzo}" />
                <input type="hidden" id="token" value="{$token}" />
                <textarea style="display: none;" id="description" name="description">{$_descrizione_hidden}</textarea>
HTML;

            $_ret['success'] = $_html;
            return $_ret;

        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }

    }

    // form di pagamento da prensentare al login dell'utente
    public static function get_payment_form_from_year($user_id,
                                                      $_ultimo_anno_pagato,
                                                      $_anno_corrente,
                                                      $_user_details,
                                                      $_gruppi_online="",
                                                      $_gruppi_moroso="",
                                                      $_gruppi_decaduto="") {

        try {

            $_ret = array();
            $_html = "";

            // controllo se l'ultimo anno di pagamento è uguale a quello corrente
            // se così non produco nessun form di pagamento
            // qui si potrebbero inserire pagamenti alternativi (ESPEN?)
            if ($_ultimo_anno_pagato == $_anno_corrente) {

                $_html = <<<HTML
                <div class="jumbotron">
                    <h4>Tutto ok!</h4>
                    <p>Nessuna azione richiesta, sei in regola con i pagamenti delle quote per l'anno {$_anno_corrente}</p>
                </div>
HTML;
                return $_html;
            }

            $_msg_extra = " Si prega di compilare i campi mancanti nella pagina del proprio profilo";

            // controllo campi necessari per il calcolo delle tariffe
            // tipo_laurea
            if (!isset($_user_details['tipo_laurea'])
                || $_user_details['tipo_laurea'] == "")
                throw new Exception("Impossibile calcolare il tariffario, tipo di laurea non specificato." . $_msg_extra, 1);

            // anno di laurea
            if (!isset($_user_details['anno_laurea'])
                || $_user_details['anno_laurea'] == ""
                || (int) $_user_details['anno_laurea'] == 0
                || (int) $_user_details['anno_laurea'] < 1900)
                throw new Exception("Impossibile calcolare il tariffario, anno di laurea non correttamente specificato." . $_msg_extra,1);

            // anno di nascita
            if (!isset($_user_details['data_nascita'])
                || $_user_details['data_nascita'] == "")
                throw new Exception("Impossibile calcolare il tariffario, data di nascita non specificata." . $_msg_extra, 1);

            $_tipo_laurea = $_user_details['tipo_laurea'];
            $_anno_laurea = (int) $_user_details['anno_laurea'];
            $_anzianita = $_anno_corrente-$_anno_laurea;
            $_data_nascita = $_user_details['data_nascita'];

            //$_nome_utente = $_user_details['nome_utente'];
            //$_cognome_utente = $_user_details['cognome_utente'];
            //$_codice_fiscale = $_user_details['codice_fiscale'];
            //$_descrizione_hidden = "USERNAME: " . $_username . "\n";
            //$_descrizione_hidden = (!is_null($_nome_utente) && $_nome_utente != "") ? "NOME: " . $_nome_utente . "\n" : "";
            //$_descrizione_hidden .= (!is_null($_cognome_utente) && $_cognome_utente != "") ? "COGNOME: " . $_cognome_utente . "\n" : "";
            //$_descrizione_hidden .= (!is_null($_codice_fiscale) && $_codice_fiscale != "") ? "CF/PIVA: " . $_codice_fiscale . "\n" : "";

            $_tariffa = UtilityHelper::calcola_quota_socio($_tipo_laurea, $_anzianita, $_data_nascita);
            $_tariffa_espen = 0;
            $_diff_anni = $_anno_corrente-$_ultimo_anno_pagato;

            if ($_tariffa == 0)
                throw new Exception("Impossibile calcolare il tariffario, non è stato possibile determinare i prezzi da applicare. Si prega di contattare l'assistenza", 1);

            $_anni_da_pagare = "";
            $_html = <<<HTML
                    <table style="width: 100%;">
HTML;

            // sinpe
            $index = 1;
            for ($i=$_ultimo_anno_pagato; $i<=$_anno_corrente; $i++) {
                if ($i > $_ultimo_anno_pagato)
                    $_anni_da_pagare .= $i . ",";
            }

            $_tariffa *= $_diff_anni;
            $_anni_da_pagare = trim(rtrim($_anni_da_pagare, ","));
            $_descr_attr_sinpe = "rinnovo_quote_sinpe_" . str_replace(",", "_", $_anni_da_pagare);
            $_descr_checkbox_sinpe = "Rinnovo quote SINPE per annualit&agrave; " . $_anni_da_pagare;

            //$_descrizione_hidden .= $_descr_checkbox_sinpe . "\n";
            $_descrizione_hidden = $_descr_attr_sinpe . "\n";

            $_html .= <<<HTML
                    <tr>
                        <td>
                            <input type="checkbox" value="{$_tariffa}" id="anni_da_pagare" data-descr="{$_descr_attr_sinpe}" checked />
                        </td>
                        <td>&nbsp;</td>
                        <td id="cella_sinpe">
                            {$_descr_checkbox_sinpe}
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <h5>€ <b>{$_tariffa}</b></h5>
                        </td>
                    </tr>
HTML;

            // se l'utente è in regola con la quota dell'anno scorso gli presento anche il rinnovo ESPEN
            // sempre proposto a prescindere
            //if ($_diff_anni == 1) {
                $_tariffa_espen = UtilityHelper::calcola_quota_socio($_tipo_laurea, $_anzianita, $_data_nascita, 'espen');
                $_descr_attr_espen = "rinnovo_quote_espen_" . $_anno_corrente;
                $_descr_checkbox_espen = "Rinnovo quota ESPEN per annualit&agrave; " . $_anno_corrente ." (facoltativo)";
                $_html .= <<<HTML
                    <tr>
                        <td>
                            <input class="form-check-input" type="checkbox" value="{$_tariffa}" id="anni_da_pagare_espen" data-descr="{$_descr_attr_espen}" />
                        </td>
                        <td>&nbsp;</td>
                        <td id="cella_espen">
                            {$_descr_checkbox_espen}
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <h5>€ <b>{$_tariffa_espen}</b></h5>
                        </td>
                    </tr>
                    <input style="display: none;" type="text" id="anni_espen" value="{$_anno_corrente}" />
HTML;
            //}

            $_html .= <<<HTML
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td><h5><b>TOTALE</b></h5></td>
                        <td>&nbsp;</td>
                        <td><h5>€ <b><span id="amount_span">{$_tariffa}</span></b></h5></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <span id="paypal-button-container"></span>
                        </td>
                    </tr>
                </table>
                
                <input style="display: none;" type="number" id="amount" name="amount" value="{$_tariffa}" />
                <input style="display: none;" type="number" id="amount_espen" name="amount_espen" value="0" />
                <input style="display: none;" type="number" id="tariffa_espen" name="tariffa_espen" value="{$_tariffa_espen}" />
                <input style="display: none;" type="text" id="anni_sinpe" value="{$_anni_da_pagare}" />
                <input type="hidden" id="user_id" value="{$user_id}" />
                <input type="hidden" id="anno_corrente" value="{$_anno_corrente}" />
                <textarea style="display: none;" id="description" name="description">{$_descrizione_hidden}</textarea>
HTML;

            if ($_gruppi_online != "")
                $_html .= <<<HTML
                <input type="hidden" id="gruppi_online" value="{$_gruppi_online}" />
HTML;
            if ($_gruppi_moroso != "")
                $_html .= <<<HTML
                <input type="hidden" id="gruppi_moroso" value="{$_gruppi_moroso}" />
HTML;
            if ($_gruppi_decaduto != "")
                $_html .= <<<HTML
                <input type="hidden" id="gruppi_decaduto" value="{$_gruppi_decaduto}" />
HTML;

            $_ret['success'] = $_html;
            return $_ret;

        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }
    }

    public static function get_payment_extra($_ret) {

        // elaboro l'html o il testo del metodo di pagamento alternativo
        $_html = "";
        $_testo_pagamento_bonifico = utilityHelper::get_params_from_object($_ret, 'testo_pagamento_bonifico');

        if ($_testo_pagamento_bonifico != "")
            $_html = <<<HTML
                <div class="row">
                    <div class="col-12">
                        {$_testo_pagamento_bonifico}
                    </div>
                </div>
HTML;

        return $_html;

    }

    // vista dei pagamenti extra per utente
    public static function get_pagamenti_servizi_extra($user_id) {

        $_html = "";
        $_corpo_tabella = "";
        $dt = new DateTime();
        $_anno_corrente = $dt->format('Y');

        // dettagli utente
        $_user = new gglmsModelUsers();
        $_user_details = $_user->get_user_details_cb($user_id);
        $_user_quote = $_user->get_user_quote($user_id, $_anno_corrente, 'espen');

        $_msg_extra = " Si prega di compilare i campi mancanti nella pagina del proprio profilo";

        // controllo campi necessari per il calcolo delle tariffe
        // tipo_laurea
        if (!isset($_user_details['tipo_laurea'])
            || $_user_details['tipo_laurea'] == "")
            throw new Exception("Impossibile calcolare il tariffario, tipo di laurea non specificato." . $_msg_extra, 1);

        // anno di laurea
        if (!isset($_user_details['anno_laurea'])
            || $_user_details['anno_laurea'] == ""
            || (int) $_user_details['anno_laurea'] == 0
            || (int) $_user_details['anno_laurea'] < 1900)
            throw new Exception("Impossibile calcolare il tariffario, anno di laurea non correttamente specificato." . $_msg_extra,1);

        // anno di nascita
        if (!isset($_user_details['data_nascita'])
            || $_user_details['data_nascita'] == "")
            throw new Exception("Impossibile calcolare il tariffario, data di nascita non specificata." . $_msg_extra, 1);

        $_html = <<<HTML
            <table style="width: 100%;">
HTML;

        $_tipo_laurea = $_user_details['tipo_laurea'];
        $_anno_laurea = (int) $_user_details['anno_laurea'];
        $_anzianita = $_anno_corrente-$_anno_laurea;
        $_data_nascita = $_user_details['data_nascita'];

        // inizializzazione totali
        $_tariffa = 0;
        $_tariffa_espen = 0;
        $_descrizione_hidden = "";

        // controllo se ho già pagato ESPEN
        if (is_null($_user_quote)
            || !is_array($_user_quote)
            || count($_user_quote) == 0) {
            // sezione ESPEN
            $_tariffa_espen = UtilityHelper::calcola_quota_socio($_tipo_laurea, $_anzianita, $_data_nascita, 'espen');
            //$_tariffa += $_tariffa_espen;

            $_descr_attr_espen = "rinnovo_quote_espen_" . $_anno_corrente;
            //$_descrizione_hidden .= $_descr_attr_espen;
            $_descr_checkbox_espen = "Rinnovo quota ESPEN per annualit&agrave; " . $_anno_corrente . " (facoltativo)";

            $_corpo_tabella .= <<<HTML
                        <tr>
                            <td>
                                <input class="form-check-input" type="checkbox" value="{$_tariffa_espen}" id="anni_da_pagare_espen" data-descr="{$_descr_attr_espen}" />
                            </td>
                            <td>&nbsp;</td>
                            <td id="cella_espen">
                                {$_descr_checkbox_espen}
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <h5>€ <b>{$_tariffa_espen}</b></h5>
                            </td>
                        </tr>
                        <input style="display: none;" type="text" id="anni_espen" value="{$_anno_corrente}" />
HTML;
        }

        if ($_corpo_tabella != "") {
            $_html .= $_corpo_tabella;
            $_html .= <<<HTML
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td><h5><b>TOTALE</b></h5></td>
                        <td>&nbsp;</td>
                        <td><h5>€ <b><span id="amount_span">{$_tariffa}</span></b></h5></td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <span id="paypal-button-container"></span>
                        </td>
                    </tr>
                </table>
                
                <input style="display: none;" type="number" id="amount" name="amount" value="{$_tariffa}" />
                <input style="display: none;" type="number" id="amount_espen" name="amount_espen" value="0" />
                <input style="display: none;" type="number" id="tariffa_espen" name="tariffa_espen" value="{$_tariffa_espen}" />
                <input type="hidden" id="user_id" value="{$user_id}" />
                <input type="hidden" id="anno_corrente" value="{$_anno_corrente}" />
                <textarea style="display: none;" id="description" name="description">{$_descrizione_hidden}</textarea>
HTML;
        }
        else
            return "";

        return $_html;

    }

    public static function get_zoom_users_options($_response) {

        $_html = <<<HTML
        <option value="">Seleziona utente Zoom</option>
HTML;

        if (!isset($_response['success'])
            || $_response['success'] == "")
            return "";

        $_obj_decoded = json_decode($_response['success']);

        if (!isset($_obj_decoded->users)
            || count($_obj_decoded->users) == 0)
            return "";

        foreach ($_obj_decoded->users as $user_key) {

            $user_arr = (array) $user_key;

            $_str_name = isset($user_arr['first_name']) ? $user_arr['first_name'] : "";
            $_str_name .= isset($user_arr['last_name']) ? (($_str_name != "") ? " " : "") . $user_arr['last_name'] : "";

            $_html .= <<<HTML
            <option value="{$user_arr['id']}">{$user_arr['email']} ({$_str_name})</option>
HTML;
        }

        return $_html;
    }

    public static function check_subtitles_solovideo($check_path, $content_path) {

        /*
         * <track kind="subtitles" label="English" src="<?php echo PATH_CONTENUTI . '/' . $this->contenuto->id . '/'; ?>mediaelement.srt" srclang="en">
         * */
        $_html = "";
        $_arr_files = array();
        $subs_list = UtilityHelper::files_list_from_folder($check_path);
        if (!is_array($subs_list)
            || count($subs_list) == 0)
            return "";

        foreach ($subs_list as $file_key => $file) {

            if (strpos($file, 'subtitle_') !== false)
                $_arr_files[] = $file;

        }

        if (count($_arr_files) == 0)
            return "";

        foreach ($_arr_files as $arr_key => $track) {

            $_tmp_arr = explode("_", basename($track, ".srt"));
            $_label = ucwords($_tmp_arr[1]);
            $_lang = strtolower(($_tmp_arr[2]));
            $_src = $content_path . '/' . $track;

            $_html .= <<<HTML
                <track kind="subtitles" label="{$_label}" src="{$_src}" srclang="{$_lang}" />
HTML;
        }

        return $_html;

    }

    public static function get_month_select_body($arr_range) {

        $_html = <<<HTML
        <option value="">-</option>
HTML;

        foreach ($arr_range as $sel_value => $sel_text) {
            $sel_value = trim($sel_value);
            $sel_text = trim($sel_text);
            $_html .= <<<HTML
                <option value="{$sel_value}">{$sel_text}</option>
HTML;
        }

        return $_html;

    }

}
