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
    public static function get_result_view($target, $call_result, $redirect=null, $last_quota=null, $retArray=false) {

        $_html = "";
        $_result_class = "success";
        $_result_icon = "fa-check";
        $_result_msg = "L'operazione è andata a buon fine, puoi effettuare il login alla tua area riservata";
        $_result_extra = "";
        $_result_receipt = "";
        //$_href = (!is_null($redirect) && $redirect != "") ? $redirect : "index.php";
        $_href = utilityHelper::set_index_redirect_url($redirect);

        if ($target == "sinpe"
            || $target == "servizi_extra"
            || $target == "acquistaevento"
            || $target == "registrazioneasand"
            || $target == "voucher_buy_request") {

            if ($target != "registrazioneasand" && $target != "voucher_buy_request") {
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
            }

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

            // per asand visualizzo il link della ricevuta
            if ($call_result == "tuttook"
                && ($target == "registrazioneasand" || $target == "voucher_buy_request")
                && !is_null($last_quota)) {
              $check = utilityHelper::getJoomlaMainUrl(['asand', 'home']);
              $siteRefUrl = utilityHelper::getHostname(true) . (!is_null($check) ? '/' . $check : "") . "/index.php";
              $encodedReceiptId = utilityHelper::build_randon_token($last_quota);
              $_result_receipt = <<<HTML
              <p class="mb-5 mt-5 text-center">
                  <button style="border-radius: 10px;min-height: 50px; background-color: rgba(98, 161, 156, 0.82);color: #FFFFFF;" class="btn btn-lg" onclick="openRecepitPopup()">Visualizza ricevuta</button>
              </p>
              <script>

              function openRecepitPopup() {

                const siteRefUrl = '{$siteRefUrl}';
                let id = (new Date()).getTime();
                let myWindow = window.open(siteRefUrl + '&printerFriendly=true', id, "toolbar=1,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=1,width=800,height=600,left = 240,top = 212");
                const postData = {
                  recepit_id : '{$encodedReceiptId}'
                }
                $.post("index.php?option=com_gglms&task=api.printReceiptAsnd", postData).done(function(htmlContent) {
                  myWindow.document.write(htmlContent);
                  myWindow.focus();
                });
              }

              </script>
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
                                {$_result_receipt}

                            </div>
                        </div>
                    </div>

HTML;

        }

        if ($retArray) {
          $_ret = [];
          $_ret['success'] = $_html;
          return $_ret;
        }

        return $_html;

    }

    // messaggio di errore in ingresso al form di pagamento
    public static function get_payment_form_error($msg, $redirect=null, $forceIndexRedirect = false) {

        //$_href = (!is_null($redirect) && $redirect != "") ? $redirect : "index.php";
        $_href = utilityHelper::set_index_redirect_url($redirect, $forceIndexRedirect);

        $_html = <<<HTML
            <script>
                alert("{$msg}");
                window.location.href = "{$_href}";
            </script>
HTML;

        return $_html;

    }

    public static function get_user_insert_confirm_group_sponsor_evento($_titolo_evento) {

        $_ret = array();
        $_href = utilityHelper::set_index_redirect_url();

        $_html = <<<HTML
                <div class="jumbotron">
                    <h4>Grazie!</h4>
                    <p>La tua registrazione all'evento {$_titolo_evento} è andata a buon fine
                        <br />Tra 10 secondi sarai reindirizzato alla pagina <a href="{$_href}">HOME</a>
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

    // login utente per accedere ad evento sponsor
    public static function get_user_login_form_sponsor_evento($id_evento) {

        try {

            $token = UtilityHelper::build_token_url(0, $id_evento, 0, 0, 0, 0);
            $_ref_registrazione = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'user_insert_group_sponsor_evento');
            $_label_username = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR2');
            $_label_password = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR3');
            $_label_accedi = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR23');
            $_html = <<<HTML
            <link href="components/com_gglms/libraries/css/custom-form.css" rel="stylesheet" />
            <form>
                <div class="rowcustom">
                    <div class="col-75">
                        <label for="username">{$_label_username}<span style="color: red">*</span></label>
                        <br />
                        <input class="form-control" type="text" id="username" style="width: 320px;" placeholder="{$_label_username}" />
                    </div>
                </div>

                <div class="rowcustom">
                    <div class="col-75">
                        <label for="password_utente">{$_label_password}<span style="color: red">*</span></label>
                        <br />
                        <input class="form-control" type="password" id="password_utente" style="width: 320px;" value="" />
                    </div>
                </div>

                <div class="rowcustom">
                    <button class="btn btn-large btn-primary btn-accedi-sponsor" data-ref="{$_ref_registrazione}">{$_label_accedi}</button>
                </div>
                <input type="hidden" id="token" value="{$token}" />
            </form>
HTML;

            $_ret['success'] = $_html;
            return $_ret;
        }
        catch (Exception $e) {
            DEBUGG::log(json_encode($e->getMessage()), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }

    }

    public static function get_user_registration_form_sponsor_evento($_params, $id_evento) {

        try {

            // controllo se l'evento esiste ed è pubblicato
            $_unit = new gglmsModelUnita();
            $_check_evento = $_unit->find_corso_pubblicato($id_evento);
            if (!is_array($_check_evento))
                throw new Exception($_check_evento, 1);

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
                            <button class="btn btn-large btn-primary btn-registrazione-sponsor" data-ref="{$_ref_registrazione}">{$_label_registrazione}</button>
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
            $_cb_ragionesociale = UtilityHelper::get_cb_field_name($_params, 'campo_cb_ragionesociale', 'name');
            $_cb_partitaiva = UtilityHelper::get_cb_field_name($_params, 'campo_cb_partitaiva', 'name');
            $_cb_codicedestinatario = UtilityHelper::get_cb_field_name($_params, 'campo_cb_codicedestinatario', 'name');
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
            $_label_richiesta_fattura = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR23');
            $_label_ragione_sociale = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR24');
            $_label_piva = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR25');
            $_label_cod_dest =JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR26');

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
                      <div class="col-25">
                        <label for="richiesta_fattura">{$_label_richiesta_fattura}</label>
                        <input class="form-control" type="checkbox" id="check_richiesta_fattura" />
                      </div>
                    </div>

                    <div id="campi_fattura" style="display: none;">

                        <div class="rowcustom">
                          <div class="col-25">
                            <label for="ragione_sociale">{$_label_ragione_sociale}</label>
                          </div>
                          <div class="col-75">
                            <input class="form-control campi_fattura" type="text" id="ragione_sociale" style="width: 220px;" data-campo="{$_cb_ragionesociale}"  placeholder="{$_label_ragione_sociale}" />
                          </div>
                        </div>

                        <div class="rowcustom">
                          <div class="col-25">
                            <label for="partita_iva">{$_label_piva}</label>
                          </div>
                          <div class="col-75">
                            <input class="form-control campi_fattura" type="text" id="partita_iva" style="width: 220px;" data-campo="{$_cb_partitaiva}"  placeholder="{$_label_piva}" />
                          </div>
                        </div>

                        <div class="rowcustom">
                          <div class="col-25">
                            <label for="codice_destinatario">{$_label_cod_dest}</label>
                          </div>
                          <div class="col-75">
                            <input class="form-control campi_fattura" type="text" id="codice_destinatario" style="width: 220px;" data-campo="{$_cb_codicedestinatario}"  placeholder="{$_label_cod_dest}" />
                          </div>
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
                                                                         $_params,
                                                                         $_titolo_evento = "") {

        try {

            $_ret = array();

            $_href = utilityHelper::set_index_redirect_url();
            $_ref_accedi = "index.php?option=com_comprofiler&view=login";

            $_msg = "La tua registrazione è andata a buon fine";
            if ($_titolo_evento != "")
                $_msg = "La tua registrazione all'evento " . $_titolo_evento . " è andata a buon fine";

            $_html = <<<HTML
                <div class="jumbotron">
                    <h4>Grazie!</h4>
                    <p>{$_msg}
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

    // reindirizza l'utente al login oppure ad una registrazione esclusiva per lo sponsor
    public static function get_user_action_registration_form_sponsor_evento($unit_prezzo,
                                                                            $unit_id,
                                                                            $user_id,
                                                                            $unit_model) {
        try {

            $_ret = array();

            $_title_advise = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR22');
            $_label_accedi = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR23');
            $_label_registrazione = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR24');

            $token = UtilityHelper::build_token_url($unit_prezzo, $unit_id, $user_id, 0, 0, 0);
            $_ref_accedi = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'user_login_form_sponsor_evento');
            $_ref_registrazione = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'user_registration_form_sponsor_evento');

            if ((int) $user_id == 0)
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
            else {
                // se già online registro direttamente l'utente all'evento
                $_unit = $unit_model->getUnita($unit_id);
                $unit_gruppo = $unit_model->get_id_gruppo_unit($unit_id);

                // controllo se l'utente è già nel gruppo corso
                $_already_request = utilityHelper::check_user_into_ug($user_id, (array) $unit_gruppo);
                if ($_already_request)
                    throw new Exception("Sei già registrato all'evento selezionato", 1);

                $_insert_ug = UtilityHelper::set_usergroup_generic($user_id, $unit_gruppo);

                if (!is_array($_insert_ug))
                    throw new Exception($_insert_ug, 1);

                $_view = self::get_user_insert_confirm_group_sponsor_evento($_unit->titolo);
                $_html = $_view['success'];
            }

            $_ret['success'] = $_html;
            return $_ret;
        }
        catch (Exception $e) {
            $_log_error = "USER: " . $user_id . " - " . $e->getMessage();
            DEBUGG::log(json_encode($_log_error), __FUNCTION__ . '_error', 0, 1, 0 );
            return $e->getMessage();
        }

    }

    // reindirizza l'utente al login oppure ad una registrazione molto rapida per consentire di usufruire soltanto dell'evento
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

    // messaggio che informa l'utente che la sua richiesta di pagamento con voucher è andata a buon fine
    public static function get_payment_form_acquisto_evento_voucher($user_id, $_event_title) {

      try {

        $_href = utilityHelper::set_index_redirect_url();

        $_html = <<<HTML
                <div class="jumbotron">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Grazie!</h4>
                            <p>L'iscrizione all'evento <b>{$_event_title}</b> è andata a buon fine!
                                <br />
                                Clicca <a href="{$_href}">QUI</a> per ritornare in HOME page
                            </p>
                        </div>
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
    public static function get_payment_form_acquisto_evento_bonifico($user_id, $_event_title, $totale, $_params, $quotaAsand = false) {

        try {

            $_ret = array();

            $_email_to  = UtilityHelper::get_params_from_object($_params, 'email_default');
            $_href = utilityHelper::set_index_redirect_url();

            // testo pagamento bonifico con sostituzione della stringa manuale..pessima soluzione ma senza alternative
            $_testo_pagamento_bonifico = UtilityHelper::get_params_from_object($_params, 'testo_pagamento_bonifico');
            $_testo_pagamento_bonifico = str_replace('Oppure bonifico bancario alle seguenti coordinate', 'COORDINATE PER BONIFICO BANCARIO', $_testo_pagamento_bonifico);
            $_event_reference = !$quotaAsand
              ? " all'evento  <b>" . $_event_title . "</b> "
              : " ";
            $_event_title_reference = !$quotaAsand
              ? "- <b>Titolo del corso acquistato</b>"
              : " per un totale di &euro; " . number_format($totale, 2, ',', '') . " ";

            $_html = <<<HTML
                <div class="jumbotron">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Grazie!</h4>
                            <p>Per confermare l'iscrizione{$_event_reference}invia una copia del bonifico effettuato a <b>{$_email_to}</b>
                                con le seguenti indicazioni: <br />
                                <b>Nome</b> e <b>Cognome</b>{$_event_title_reference}- <b>Codice fiscale</b> - <b>Recapito telefonico</b>
                                <br />
                                Clicca <a href="{$_href}">QUI</a> per ritornare in HOME page
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tr>
                                    <td style="text-align: center;">
                                      {$_testo_pagamento_bonifico}
                                    </td>
                                </tr>
                            </table>
                        </div>
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

    // form di pagamento per acquisto quota annuale asand
    public static function get_payment_form_quota_asand($user_id, $prezzo_quota, $incremento_pp, $descrizione_quota, $_params, $token) {

      try {

        $_ret = array();
        $_html = "";

        // controllo se il prezzo è valido
        if ($prezzo_quota == "" || $prezzo_quota == 0)
          throw new Exception("Il prezzo indicato per la transazione non è valido", E_USER_ERROR);

        $dt = new DateTime();
        $parsedDescrizioneQuota = ucfirst(strtolower(str_replace("_", " ", $descrizione_quota)));
        $_descr_checkbox_evento = "Acquisto " . $parsedDescrizioneQuota . " per anno " . $dt->format('Y');
        $_descr_checkbox_evento_commissioni = "Commissioni spese PayPal";

        $_descr_attr_evento = "pagamento-" . $descrizione_quota . "-anno-" . $dt->format('Y');
        $_descr_attr_evento_commissioni = "commissioni-" . $descrizione_quota . "-anno-" . $dt->format('Y');
        $_descrizione_hidden = $_descr_attr_evento . "|" . $_descr_attr_evento_commissioni;

        $_testo_pagamento_paypal = JText::_('COM_REGISTRAZIONE_ASAND_STR7');
        $_testo_pagamento_bonifico_btn = JText::_('COM_REGISTRAZIONE_ASAND_STR8');
        $_testo_pagamento_bonifico = utilityHelper::get_params_from_object($_params, 'testo_pagamento_bonifico');
        $_row_pagamento_bonfico = "";

        $endpoint = utilityHelper::build_encoded_link($token, 'registrazioneasand', 'bb_buy_request');
        $endpointVoucher = utilityHelper::build_encoded_link($token, 'registrazioneasand', 'voucher_buy_request');
        $prezzoQuotaParsed = number_format($prezzo_quota, 2, '.', '');
        $totalePayPal = $prezzo_quota+$incremento_pp;
        if ($_testo_pagamento_bonifico != "")

                $_row_pagamento_bonfico = <<<HTML
                    <div class="row mt-5">
                      <div class="col-md-12 text-center">
                        {$_testo_pagamento_bonifico}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 text-center">
                        <button class="btn btn-lg btn-bonifico" id="btn-bonifico" data-ref="{$endpoint}">{$_testo_pagamento_bonifico_btn}</button>
                      </div>
                    </div>
HTML;

        $_row_pagamento_voucher = <<<HTML
        <div class="row mt-5">
          <div class="col-md-12 text-center">
            <p class="font-weight-bold h4 text-dark">Sei in possesso di un voucher?</p>
          </div>
        </div>
        <div class="row">
          <div class="offset-md-4 col-md-4">
            <input type="text" class="form-control h6" id="v_code" name="v_code" placeholder="Inserisci qui il codice" />
          </div>
        </div>
        <div class="row hidden" id="row_vcheck">
          <div class="col-md-4 offset-md-4 text-center" id="msg_vcheck"></div>
        </div>
        <div class="row">
          <div class="col-md-4 mt-5 offset-md-4 text-center">
            <button class="btn btn-lg btn-bonifico" id="btn-voucher" data-ref="">Verifica</button>
            <button class="btn btn-lg btn-bonifico hidden" id="btn-voucher-apply" data-ref="">Applica</button>
          </div>
        </div>
HTML;

        $_html .= <<<HTML

        {$_row_pagamento_voucher}

        <div class="row mt-5">
          <div class="col text-center">
            <h4><span style="color: black; font-weight: bold">{$_testo_pagamento_paypal}</span></h4>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-3 offset-md-3 " id="cella_evento">
            {$_descr_checkbox_evento}
          </div>
          <div class="col-md-3 text-right">
            <h5>€ <b>{$prezzoQuotaParsed}</b></h5>
          </div>
          <div class="col-md-3 hidden">
            <input type="checkbox" value="{$prezzo_quota}" id="evento_da_pagare" data-descr="{$_descr_attr_evento}" checked />
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 offset-md-3" id="cella_evento_commissioni">
            {$_descr_checkbox_evento_commissioni}
          </div>
          <div class="col-md-3 text-right">
            <h5>€ <b>{$incremento_pp}</b></h5>
          </div>
          <div class="col-md-3 hidden">
            <input type="checkbox" value="{$incremento_pp}" id="evento_da_pagare_commissione" data-descr="{$$_descr_checkbox_evento_commissioni}" checked />
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 offset-md-3">
            <h5><b>TOTALE</b></h5>
          </div>
          <div class="col-md-3 text-right">
            <h5>€ <b><span id="amount_span">{$totalePayPal}</span></b></h5>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12 text-center">
            <span id="paypal-button-container"></span>
          </div>
        </div>

        {$_row_pagamento_bonfico}

        <input style="display: none;" type="number" id="amount" name="amount" value="{$totalePayPal}" />
        <input type="hidden" id="token" value="{$token}" />
        <input type="hidden" id="v_url" value="{$endpointVoucher}" />
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

    // form di pagamento per acquisto di un evento a calendario
    public static function get_payment_form_acquisto_evento($unit_prezzo,
                                                            $unit_id,
                                                            $user_id,
                                                            $sconto_data,
                                                            $sconto_custom,
                                                            $in_groups,
                                                            $sconto_particolare = 0,
                                                            $acquisto_webinar = 0,
                                                            $perc_webinar = 0,
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
            $unit_prezzo_db = $_unit->prezzo;

            // se compro l'evento in modalità webinar il prezzo deve essere adeguato
            if ($acquisto_webinar > 0) {
                $unit_prezzo_db = $unit_prezzo;
                $unit_prezzo = $acquisto_webinar;
            }

            $dt = new DateTime($_unit->data_inizio);
            $_tipo_sconto = UtilityHelper::get_tipo_sconto_evento($sconto_data,
                $sconto_custom,
                $in_groups,
                $_unit,
                $sconto_particolare,
                $acquisto_webinar,
                $perc_webinar,
                $unit_prezzo);

            $_descr_checkbox_evento = "Acquisto " . $_unit->titolo;
            $_descr_checkbox_evento .= $_tipo_sconto['descrizione_sconto'] != "" ? ' ' . $_tipo_sconto['descrizione_sconto'] : '';

            $_style_totale = $_tipo_sconto['label_sconto'] != "" ? 'style="text-decoration: line-through;"' : '';

            $_descr_attr_evento = $_unit->alias;
            $_descr_attr_evento .= ($sconto_data == 1) ? '-sc_data' : '';
            $_descr_attr_evento .= ($in_groups == 1) ? '-sc_gruppo' : '';
            $_descr_attr_evento .= ($sconto_particolare > 0) ? '-sc_ps' : '';
            $_descr_attr_evento .= ($acquisto_webinar > 0) ? '-webinar' : '';

            $_descrizione_hidden = $_descr_attr_evento;

            $_testo_pagamento_paypal = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR1');
            $_testo_pagamento_bonifico_btn = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR2');
            $_testo_pagamento_bonifico = UtilityHelper::get_params_from_object($_params, 'testo_pagamento_bonifico');
            $_row_pagamento_bonfico = "";
            $_row_pagamento_voucher = "";

            $token = UtilityHelper::build_token_url($unit_prezzo, $unit_id, $user_id, $sconto_data, $sconto_custom, $in_groups);

            if ($_testo_pagamento_bonifico != "") {

              $endpointBB = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'bb_buy_request');

                $_row_pagamento_bonfico = <<<HTML
                    <tr>
                        <td colspan="5" style="text-align: center;">
                          {$_testo_pagamento_bonifico}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                          <button class="btn btn-primary" id="btn-bonifico" data-ref="{$endpointBB}">{$_testo_pagamento_bonifico_btn}</button>
                        </td>
                    </tr>
HTML;
            }

            if ((int) $_unit->buy_voucher == 1) {

              $_testo_pagamento_voucher = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR73');
              $_placeholder_voucher = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR74');

              $endpointVoucher = UtilityHelper::build_encoded_link($token, 'acquistaevento', 'voucher_buy_request');

              $_row_pagamento_voucher = <<<HTML
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                  <td colspan="5" style="text-align: center;">
                    <h4>
                      <span style="color: black; font-weight: bold;">{$_testo_pagamento_voucher}</span>
                    </h4>
                  </td>
              </tr>
              <tr>
                  <td colspan="5" style="text-align: center;">
                    <input 
                      class="w-25" 
                      style="padding: 10px 15px; font-size: 16px; border: 2px solid #ccc; border-radius: 5px; outline: none; transition: all 0.3s ease-in-out;"
                      type="text" 
                      id="buy_voucher_code" 
                      placeholder="{$_placeholder_voucher}" 
                    />
                  </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                  <td colspan="5" style="text-align: center;">
                    <button class="btn btn-primary" id="btn-voucher" data-ref="{$endpointVoucher}">{$_testo_pagamento_voucher}</button>
                  </td>
              </tr>
HTML;

            }


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
                        <!--
                        <td>&nbsp;</td>
                        <td>
                            <h5>€ <b>{$unit_prezzo}</b></h5>
                        </td>
                        -->
                        <td colspan="2">&nbsp;</td>
                    </tr>
                     <tr>
                        <td colspan="2">&nbsp;</td>
                        <td><h5><b>TOTALE</b></h5></td>
                        <td>&nbsp;</td>
                        <td>
                          <h5>€ <b><span id="amount_span" {$_style_totale}>{$unit_prezzo_db}</span> {$_tipo_sconto['label_sconto']}</b></h5>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <span id="paypal-button-container"></span>
                        </td>
                    </tr>
                    {$_row_pagamento_bonfico}
                    {$_row_pagamento_voucher}
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
                            <!-- <input type="checkbox" value="{$_tariffa}" id="anni_da_pagare" data-descr="{$_descr_attr_sinpe}" checked /> -->
                            <input class="form-control" type="checkbox" value="{$_tariffa}" id="anni_da_pagare" data-descr="{$_descr_attr_sinpe}" checked />
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
                            <!-- <input class="form-check-input" type="checkbox" value="{$_tariffa}" id="anni_da_pagare_espen" data-descr="{$_descr_attr_espen}" /> -->
                            <input class="form-control" type="checkbox" value="{$_tariffa}" id="anni_da_pagare_espen" data-descr="{$_descr_attr_espen}" />
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
                        <td colspan="5" style="text-align: center; padding-left:20%; padding-right:20%">
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

    public static function get_payment_extra($_ret, $view = 'rinnovoquote', $btnToken = '') {

        // elaboro l'html o il testo del metodo di pagamento alternativo
        $_html = "";
        $_testo_pagamento_bonifico = utilityHelper::get_params_from_object($_ret, 'testo_pagamento_bonifico');
        $_testo_pagamento_bonifico_bt = JText::_('COM_REGISTRAZIONE_ASAND_STR8');

        $token = utilityHelper::build_randon_token('');
        if ($btnToken != '') $token = $btnToken;

        $endpoint = UtilityHelper::build_encoded_link($token, $view, 'bb_buy_request');

        if ($_testo_pagamento_bonifico != "")
            $_html = <<<HTML
                    <!-- popup Disabilitato-->
                    <div class="form-popup" id="myForm" style="display: none">
                       <div class="row mt-5">
                          <div class="col-md-12 text-center">
                             {$_testo_pagamento_bonifico}
                           </div>
                       </div>
                    </div>

                    <div class="row mt-5">
                      <div class="col-md-12 text-center">
                        <button  class="btn btn-lg btn-bonifico" id="btn-bonifico" onclick="openForm()" data-ref="{$endpoint}" 
                          style="background-color: #17a2b8;font-weight: 900;height: 40px !important; width: 500px;font-size: 15px !important;">{$_testo_pagamento_bonifico_bt}</button>
                      </div>
                    </div>

                    <script>
                        function openForm() {
                          document.getElementById("myForm").style.display = "block";
                        }

                    </script>

HTML;

//        <div class="row">
//                    <div class="col-12 text-center">
//                        {$_testo_pagamento_bonifico}
//                    </div>
//                </div>

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
                        <td colspan="5" class="text-center">
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

    public static function get_corsi_options($_corsi, $sub_text = null, $sub_value = null, $select_default = 'Seleziona corso') {

        if (count($_corsi) == 0
            || empty($_corsi)
            || $_corsi == null)
            return "";

        $_html = <<<HTML
        <option value="">{$select_default}</option>
HTML;

        if (is_null($sub_value)
            && is_null($sub_text)) {

           foreach ($_corsi as $key_corso => $value_corso) {
                $_html .= <<<HTML
            <option value="{$value_corso->id}">{$value_corso->titolo}</option>
HTML;

            }
        }
        else {

            foreach ($_corsi as $key_sub => $sub_array) {
                $sub_array = !is_array($sub_array) ? (array) $sub_array : $sub_array;
                $_html .= <<<HTML
                <option value="{$sub_array[$sub_value]}">{$sub_array[$sub_text]}</option>
HTML;

            }

        }

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

    public static function visualizza_link_semplice_contenuto($link) {

        $_html = "";
        $link_semplice_contenuto1 = JText::_('COM_GGLMS_VISUALIZZA_LINK1');
        $link_semplice_contenuto2 = JText::_('COM_GGLMS_VISUALIZZA_LINK2');

        if (!is_null($link)
            && $link != "")
            $_html =  <<<HTML
            <p class="text-center">
                <u>{$link_semplice_contenuto1} <a href="{$link}" target="_blank">{$link_semplice_contenuto2}</a></u>
            </p>
HTML;

        return $_html;

    }

    public static function get_show_event_video($randomTokenValue, $getVideoParam) {

      try {

        $_title_advise = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR71');
        $_title_watch = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR72');
        $_label_nome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR8');
        $_label_cognome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR9');
        $_label_email = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR13');
        $_label_ordine_di = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR50');
        $_label_numeroiscrizione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR38');
        $_label_registrazione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR5');
        $_label_professionedisciplina = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR49');
        $_cb_ordine_options = utilityHelper::get_cb_field_select_by_name('cb_ordine');
        $_cb_professionedisciplina_options = utilityHelper::get_cb_field_select_by_name('cb_professionedisciplina', 
          [
            'studente', 
            'altro', 
            'specializzando', 
            'specializzando 2',
            'biologo/anatomia patologica',
            'biologo/biochimica clinica',
            'biologo/igiene degli alimenti e della nutrizione',
            'biologo/igiene, epidemiologia e sanità pubblica',
            'biologo/laboratorio di genetica medica',
            'biologo/medicina del lavoro',
            'biologo/medicina nucleare',
            'biologo/medicina trasfusionale',
            'biologo/microbiologia e virologia',
            'biologo/patologia clinica (laboratorio di analisi chimico, cliniche e microbiologia)',
            'logopedista',
            'psicologo/psicologia',
            'psicologo/psicoterapia',
          ]);


        
        $_html = <<<HTML
        <div class="row mt-4">
          <div class="col-12">
            <h5><span style="color: black;">{$_title_advise}</span></h5>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <h5><span style="color: black; font-weight: bold">La nutrizione nel percorso di cura anti-tumorale: corso pratico di Nutrizione Clinica per oncologi in formazione</span></h5>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <h5><span style="color: black;">{$_title_watch}</span></h5>
          </div>
        </div>

        <div class="container-form">

          <form method="POST" id="subscriptionForm" enctype="multipart/form-data">

            <div class="form-group row mt-4">
              <label for="cb_nome" class="col-sm-2 col-form-label">{$_label_nome}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="text" 
                  id="cb_nome" 
                  placeholder="{$_label_nome}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_cognome" class="col-sm-2 col-form-label">{$_label_cognome}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="text" 
                  id="cb_cognome" 
                  placeholder="{$_label_cognome}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="email_utente" class="col-sm-2 col-form-label">{$_label_email}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="email" 
                  id="email_utente" 
                  placeholder="{$_label_email}"
                  required  
                />
              </div>
            </div>
            
            <div class="form-group row">
              <label for="cb_professionedisciplina" class="col-sm-2 col-form-label">{$_label_professionedisciplina}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_professionedisciplina" 
                  required
                  >
                    <option value="">-</option>
                    {$_cb_professionedisciplina_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_ordine" class="col-sm-2 col-form-label">{$_label_ordine_di}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_ordine" 
                  required
                  >
                    <option value="">-</option>
                    {$_cb_ordine_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_numeroiscrizione" class="col-sm-2 col-form-label">{$_label_numeroiscrizione}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_numeroiscrizione" 
                  placeholder="{$_label_numeroiscrizione}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <div class="col-sm-6 offset-sm-3 text-center">
                <button class="btn btn-large btn-registrazione">{$_label_registrazione}</button>
              </div>
            </div>

            <input type="hidden" name="tts" id="tts" value="{$randomTokenValue}" />
            <input type="hidden" name="cc" id="cc" value="{$getVideoParam}" />
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

    public static function get_user_registration_form_sinpe() {

      try {

        $_title_advise = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR67');
        $_cb_nome  = 'cb_nome';
        $_cb_cognome = 'cb_cognome';
        $_cb_altraemail = 'cb_altraemail';
        $_cb_titolo = 'cb_titolo';
        $_cb_codicefiscale = 'cb_codicefiscale';
        $_cb_datadinascita = 'cb_datadinascita';
        $_cb_luogodinascita = 'cb_luogodinascita';
        $_cb_provinciadinascita = 'cb_provinciadinascita';
        $_cb_nazionalita = 'cb_nazionalita';
        $_cb_telefono = 'cb_telefono';
        $_cb_indirizzodiresidenza = 'cb_indirizzodiresidenza';
        $_cb_provdiresidenza = 'cb_provdiresidenza';
        $_cb_citta = 'cb_citta';
        $_cb_cap = 'cb_cap';
        $_cb_regione = 'cb_regione';
        $_cb_laureain = 'cb_laureain';
        $_cb_laureanno = 'cb_laureanno';
        $_cb_professionedisciplina = 'cb_professionedisciplina';
        $_cb_ordine = 'cb_ordine';
        $_cb_numeroiscrizione = 'cb_numeroiscrizione';
        $_cb_qualifica = 'cb_qualifica';
        $_cb_azienda = 'cb_azienda';
        $_cb_dipartimento = 'cb_dipartimento';
        $_cb_reparto = 'cb_reparto';
        $_cb_privacy = 'cb_privacy';
        $_cb_dtai_immagini = 'cb_dtai_immagini';
        $_cb_statuto = 'cb_statuto';
        $_cb_newsletter = 'cb_newsletter';
        $_cb_accessonutritiononline = 'cb_accessonutritiononline';
        $_cb_cv = 'cb_cv';
        // lista options da community builder
        $_cb_titolo_options = utilityHelper::get_cb_field_select_by_name('cb_titolo');
        $_cb_titolo_id = utilityHelper::get_cb_fieldId_by_name('cb_titolo');
        $_cb_provinciadinascita_options = utilityHelper::get_cb_field_select_by_name('cb_provinciadinascita');
        $_cb_provinciadinascita_id = utilityHelper::get_cb_fieldId_by_name('cb_provinciadinascita');
        $_cb_provdiresidenza_options = utilityHelper::get_cb_field_select_by_name('cb_provdiresidenza');
        $_cb_provdiresidenza_id = utilityHelper::get_cb_fieldId_by_name('cb_provdiresidenza');
        $_cb_regione_options = utilityHelper::get_cb_field_select_by_name('cb_regione');
        $_cb_regione_id = utilityHelper::get_cb_fieldId_by_name('cb_regione');
        $_cb_laureain_options = utilityHelper::get_cb_field_select_by_name('cb_laureain');
        $_cb_laureain_id = utilityHelper::get_cb_fieldId_by_name('cb_laureain');
        $_cb_professionedisciplina_options = utilityHelper::get_cb_field_select_by_name('cb_professionedisciplina');
        $_cb_professionedisciplina_id = utilityHelper::get_cb_fieldId_by_name('cb_professionedisciplina');
        $_cb_ordine_options = utilityHelper::get_cb_field_select_by_name('cb_ordine');
        $_cb_ordine_id = utilityHelper::get_cb_fieldId_by_name('cb_ordine');
        $_cb_qualifica_options = utilityHelper::get_cb_field_select_by_name('cb_qualifica');
        $_cb_qualifica_id = utilityHelper::get_cb_fieldId_by_name('cb_qualifica');

        $_label_registrazione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR5');
        $_label_nome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR8');
        $_label_cognome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR9');
        $_label_email = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR13');
        $_label_username = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR2');
        $_label_password = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR10');
        $_label_altra_email = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR45');
        $_label_titolo = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR46');
        $_label_cf = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR12');
        $_label_dt_nascita = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR18');
        $_label_citta_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR8');
        $_label_pv_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR9');
        $_label_nazionalita = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR47');
        $_label_tel = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR19');
        $_label_indirizzo = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR14');
        $_label_pv = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR16');
        $_label_citta = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR15');
        $_label_cap = JText::_('COM_REGISTRAZIONE_ASAND_STR6');
        $_label_regione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR48');
        $_label_laurea_in = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR21');
        $_label_laurea_anno = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR22');
        $_label_professionedisciplina = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR49');
        $_label_ordine_di = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR50');
        $_label_numeroiscrizione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR38');
        $_label_qualifica = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR52');
        $_label_azienda = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR53');
        $_label_dipartimento = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR54');
        $_label_reparto = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR56');
        $_label_cv = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR55');
        $_label_choose_file = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR57');
        $_label_file_upload = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR64');
        $_label_privacy_1 = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR58');
        $_label_privacy_2 = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR65');
        $_label_privacy_3 = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR59');
        $_label_cb_dtai_immagini = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR60');
        $_label_cb_statuto_1 = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR61');
        $_label_cb_statuto_2 = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR66');
        $_label_cb_newsletter = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR62');
        $_label_cb_accessonutritiononline = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR63');
        $_label_voucher = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR68');
        $_label_voucher_info = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR69');

        $_html =<<<HTML
        <div class="row mt-4">
          <div class="col-12">
            <h5><span style="color: black; font-weight: bold">{$_title_advise}</span></h5>
          </div>
        </div>
        <hr />
        <div class="container-form">

          <form method="POST" id="subscriptionForm" enctype="multipart/form-data">

            <div class="form-group row">
              <label for="cb_codicefiscale" class="col-sm-2 col-form-label">{$_label_cf}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_codicefiscale" 
                  maxlength="16" 
                  data-campo="{$_cb_codicefiscale}" 
                  placeholder="{$_label_cf}" 
                  required
                  />
              </div>
            </div>

            <div id="codiceFiscaleFeedback" class="form-group row sr-only">
              <div class="col-sm-12">
                <div id="codiceFiscaleFeedbackMsg" class="alert alert-danger" role="alert"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_nome" class="col-sm-2 col-form-label">{$_label_nome}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="text" 
                  id="cb_nome" 
                  data-campo="{$_cb_nome}" 
                  placeholder="{$_label_nome}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_cognome" class="col-sm-2 col-form-label">{$_label_cognome}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="text" 
                  id="cb_cognome" 
                  data-campo="{$_cb_cognome}" 
                  placeholder="{$_label_cognome}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="email_utente" class="col-sm-2 col-form-label">{$_label_email}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="email" 
                  id="email_utente" 
                  placeholder="{$_label_email}"
                  required  
                />
              </div>
            </div>

            <div id="emailFeedback" class="form-group row sr-only">
              <div class="col-sm-12">
                <div id="emailFeedbackMsg" class="alert alert-danger" role="alert"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="username" class="col-sm-2 col-form-label">{$_label_username}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="text" 
                  id="username" 
                  data-campo="{$_cb_nome}" 
                  placeholder="{$_label_username}" 
                  required
                  />
              </div>
            </div>

            <div id="usernameFeedback" class="form-group row sr-only">
              <div class="col-sm-12">
                <div id="usernameFeedbackMsg" class="alert alert-danger" role="alert"></div>
              </div>
            </div>

            <div class="form-group row">
                <label for="password_utente" class="col-sm-2 col-form-label">{$_label_password}<span style="color: red">*</span></label>
                <div class="col-sm-10">
                  <div class="input-group w-25">
                    <input class="form-control" type="password" id="password_utente" value="" required />
                    <div class="input-group-addon w-auto" id="show_hide_password">
                      <i class="fa fa-eye-slash" aria-hidden="true" id="show_hide_password_icon"></i>
                    </div>
                  </div>
                </div>
            </div>

            <div class="form-group row">
              <label for="cb_altraemail" class="col-sm-2 col-form-label">{$_label_altra_email}</label>
              <div class="col-sm-10">
                <input class="form-control w-25" type="email" id="cb_altraemail" data-campo="{$_cb_altraemail}" placeholder="{$_label_altra_email}" />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_titolo" class="col-sm-2 col-form-label">{$_label_titolo}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_titolo" 
                  data-campo="{$_cb_titolo}" 
                  data-id-ref="{$_cb_titolo_id}"
                  required
                  >
                    <option value="">-</option>
                    {$_cb_titolo_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_datadinascita" class="col-sm-2 col-form-label">{$_label_dt_nascita}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 datepicker" 
                  type="text" 
                  id="cb_datadinascita" 
                  data-campo="{$_cb_datadinascita}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_luogodinascita" class="col-sm-2 col-form-label">{$_label_citta_nascita}<span style="color: red">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_luogodinascita" 
                  data-campo="{$_cb_luogodinascita}" 
                  placeholder="{$_label_citta_nascita}" 
                  required
                  />
              </div>
            </div>
            
            <div class="form-group row">
              <label for="cb_provinciadinascita" class="col-sm-2 col-form-label">{$_label_pv_nascita}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_provinciadinascita" 
                  data-campo="{$_cb_provinciadinascita}" 
                  data-id-ref="{$_cb_provinciadinascita_id}"
                  required
                  >
                    <option value="">-</option>
                    {$_cb_provinciadinascita_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_nazionalita" class="col-sm-2 col-form-label">{$_label_nazionalita}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_nazionalita" 
                  data-campo="{$_cb_nazionalita}" 
                  placeholder="{$_label_nazionalita}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_telefono" class="col-sm-2 col-form-label">{$_label_tel}</label>
              <div class="col-sm-10">
                <input class="form-control w-25 text-uppercase" type="text" id="cb_telefono" data-campo="{$_cb_telefono}" placeholder="{$_label_tel}" />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_indirizzodiresidenza" class="col-sm-2 col-form-label">{$_label_indirizzo}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" id="cb_indirizzodiresidenza" 
                  data-campo="{$_cb_indirizzodiresidenza}" 
                  placeholder="{$_label_indirizzo}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_provdiresidenza" class="col-sm-2 col-form-label">{$_label_pv}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_provdiresidenza" 
                  data-campo="{$_cb_provdiresidenza}" 
                  data-id-ref="{$_cb_provdiresidenza_id}"
                  required
                  >
                    <option value="">-</option>
                    {$_cb_provdiresidenza_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_citta" class="col-sm-2 col-form-label">{$_label_citta}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_citta" 
                  data-campo="{$_cb_citta}" 
                  placeholder="{$_label_citta}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_cap" class="col-sm-2 col-form-label">{$_label_cap}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_cap" 
                  data-campo="{$_cb_cap}" 
                  placeholder="{$_label_cap}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_regione" class="col-sm-2 col-form-label">{$_label_regione}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_regione" 
                  data-campo="{$_cb_regione}" 
                  data-id-ref="{$_cb_regione_id}"
                  required
                  >
                    <option value="">-</option>
                    {$_cb_regione_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_laureain" class="col-sm-2 col-form-label">{$_label_laurea_in}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select class="form-control w-25" id="cb_laureain" data-campo="{$_cb_laureain}" data-id-ref="{$_cb_laureain_id}">
                    <option value="">-</option>
                    {$_cb_laureain_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_laureanno" class="col-sm-2 col-form-label">{$_label_laurea_anno}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_laureanno" 
                  data-campo="{$_cb_laureanno}" 
                  placeholder="{$_label_laurea_anno}" 
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_professionedisciplina" class="col-sm-2 col-form-label">{$_label_professionedisciplina}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_professionedisciplina" 
                  data-campo="{$_cb_professionedisciplina}" 
                  data-id-ref="{$_cb_professionedisciplina_id}"
                  required
                  >
                    <option value="">-</option>
                    {$_cb_professionedisciplina_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_ordine" class="col-sm-2 col-form-label">{$_label_ordine_di}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select 
                  class="form-control w-25" 
                  id="cb_ordine" 
                  data-campo="{$_cb_ordine}" 
                  data-id-ref="{$_cb_ordine_id}"
                  required
                  >
                    <option value="">-</option>
                    {$_cb_ordine_options}
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="cb_numeroiscrizione" class="col-sm-2 col-form-label">{$_label_numeroiscrizione}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_numeroiscrizione" 
                  data-campo="{$_cb_numeroiscrizione}" 
                  placeholder="{$_label_numeroiscrizione}" 
                  required
                  />
              </div>
            </div>

            <!--
            <div class="form-group row">
              <label for="cb_qualifica" class="col-sm-2 col-form-label">{$_label_qualifica}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select class="form-control w-25" id="cb_qualifica" data-campo="{$_cb_qualifica}" data-id-ref="{$_cb_qualifica_id}">
                    <option value="">-</option>
                    {$_cb_qualifica_options}
                </select>
              </div>
            </div>
          -->

            <div class="form-group row">
              <label for="cb_azienda" class="col-sm-2 col-form-label">{$_label_azienda}<span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_azienda" 
                  data-campo="{$_cb_azienda}" 
                  placeholder="{$_label_azienda}" 
                  required
                />
              </div>
            </div>
            
            <div class="form-group row">
              <label for="cb_dipartimento" class="col-sm-2 col-form-label">{$_label_dipartimento}</label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_dipartimento" 
                  data-campo="{$_cb_dipartimento}" 
                  placeholder="{$_label_dipartimento}" 
                  required
                  />
              </div>
            </div>
            
            <div class="form-group row">
              <label for="cb_reparto" class="col-sm-2 col-form-label">{$_label_reparto}</label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25 text-uppercase" 
                  type="text" 
                  id="cb_reparto" 
                  data-campo="{$_cb_reparto}" 
                  placeholder="{$_label_reparto}"
                  required
                  />
              </div>
            </div>

            <div class="form-group row">
              <label for="voucher_code" class="col-sm-2 col-form-label">{$_label_voucher}*</label>
              <div class="col-sm-10">
                <input 
                  class="form-control w-25" 
                  type="text" 
                  id="voucher_code" 
                  placeholder="{$_label_voucher}" 
                  required
                  />
                  <p class="small">*{$_label_voucher_info}</p>
              </div>
              
            </div>

            <div class="form-group row">
              <label for="cb_cv" class="col-sm-2 col-form-label">{$_label_cv}</label>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="cb_cv">{$_label_file_upload}</label>
                  <input 
                    id="cb_cv" 
                    data-campo="{$_cb_cv}" 
                    aria-describedby="cb_cv"
                    accept=".doc,.docx,.xls,.xlsx,.zip,.pdf"
                    type="file" 
                    class="form-control-file" 
                    style="height: 25% !important;"
                    />
                </div>
              </div>
            </div>

            <div class="form-group row no-gutters sectiontableentry1 cbft_checkbox cb_form_line">
              <label for="cb_privacy" class="col-form-label col-sm-3 pr-sm-2">{$_label_privacy_1}<a href="/informativa-sulla-privacy" target="_blank">{$_label_privacy_2}</a> {$_label_privacy_3}<span class="text-danger">*</span></label>
              <div class="cb_field col-sm-9">
                <div class="form-control-plaintext">
                  <div class="cbSingleCntrl m-0 form-check form-check-inline">
                    <input type="checkbox" id="cb_privacy" name="cb_privacy" value="1" data-campo="{$_cb_privacy}" class="required form-check-input m-0">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row no-gutters sectiontableentry1 cbft_checkbox cb_form_line">
              <label for="cb_dtai_immagini" class="col-form-label col-sm-3 pr-sm-2">{$_label_cb_dtai_immagini}</label>
              <div class="cb_field col-sm-9">
                <div class="form-control-plaintext">
                  <div class="cbSingleCntrl m-0 form-check form-check-inline">
                    <input type="checkbox" id="cb_dtai_immagini" name="cb_dtai_immagini" value="1" data-campo="{$_cb_dtai_immagini}" class="required form-check-input m-0">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row no-gutters sectiontableentry1 cbft_checkbox cb_form_line">
              <label for="cb_statuto" class="col-form-label col-sm-3 pr-sm-2">{$_label_cb_statuto_1} <a href="/area-istituzionale/statuto.html" target="_blank">{$_label_cb_statuto_2}</a><span class="text-danger">*</span></label>
              <div class="cb_field col-sm-9">
                <div class="form-control-plaintext">
                  <div class="cbSingleCntrl m-0 form-check form-check-inline">
                    <input type="checkbox" id="cb_statuto" name="cb_statuto" value="1" data-campo="{$_cb_statuto}" class="required form-check-input m-0">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row no-gutters sectiontableentry1 cbft_checkbox cb_form_line">
              <label for="cb_newsletter" class="col-form-label col-sm-3 pr-sm-2">{$_label_cb_newsletter}</label>
              <div class="cb_field col-sm-9">
                <div class="form-control-plaintext">
                  <div class="cbSingleCntrl m-0 form-check form-check-inline">
                    <input type="checkbox" id="cb_newsletter" name="cb_newsletter" value="1" data-campo="{$_cb_newsletter}" class="required form-check-input m-0">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row no-gutters sectiontableentry1 cbft_checkbox cb_form_line">
              <label for="cb_accessonutritiononline" class="col-form-label col-sm-3 pr-sm-2">{$_label_cb_accessonutritiononline}</label>
              <div class="cb_field col-sm-9">
                <div class="form-control-plaintext">
                  <div class="cbSingleCntrl m-0 form-check form-check-inline">
                    <input type="checkbox" id="cb_accessonutritiononline" name="cb_accessonutritiononline" value="1" data-campo="{$_cb_accessonutritiononline}" class="required form-check-input m-0">
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group row">
              <div class="col-sm-6 offset-sm-3 text-center">
                <button class="btn btn-large btn-registrazione">{$_label_registrazione}</button>
              </div>
            </div>

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

    public static function get_user_registration_form_asand($_ref_registrazione, $quotaStandard = 0, $quotaStudente = 0) {

        try {

            $_ret = array();

            $_title_advise = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR6');
            $_cb_nome  = 'cb_nome';
            $_cb_cognome = 'cb_cognome';
            $_cb_cf = 'cb_codicefiscale';
            $_cb_luogodinascita = 'cb_luogodinascita';
            $_cb_provinciadinascita = 'cb_provinciadinascita';
            $_cb_data_nascita = 'cb_datadinascita';
            $_cb_telefono = 'cb_telefono';
            $_cb_citta = 'cb_citta';
            $_cb_provincia = 'cb_provdiresidenza';
            $_cb_indirizzo = 'cb_indirizzodiresidenza';
            $_cb_cap = 'cb_cap';
            $_cb_piva = 'cb_piva';
            $_cb_universita = 'cb_universita';
            $_cb_anno_di_frequenza = 'cb_anno_frequenza';
            $_cb_matricola ='cb_matricola';
            $_cb_dipendente = 'cb_dipendente';
            $_cb_area_pratica = 'cb_area_pratica';
            $_cb_numero_albo = 'cb_numero_albo';
            $_cb_provincia_albo = 'cb_provincia_albo';
            $_cb_azienda_dipendente_sede = 'cb_azienda_dipendente_sede';
            $_cb_indirizzo_azienda = 'cb_indirizzo_azienda';
            $_cb_citta_azienda = 'cb_citta_azienda';
            $_cb_indirizzo_studio = 'cb_indirizzo_studio';
            $_cb_citta_studio = 'cb_citta_studio';
            // lista options da community builder
            $_cb_dipendente_options = UtilityHelper::get_cb_field_select_by_name('cb_dipendente');
            $_cb_dipendente_id = UtilityHelper::get_cb_fieldId_by_name('cb_dipendente');
            $_cb_titolo_studio_options = UtilityHelper::get_cb_field_select_by_name('cb_titolo_studio');
            $_cb_anno_di_frequenza_options = UtilityHelper::get_cb_field_select_by_name('cb_anno_frequenza');
            $_cb_anno_di_frequenza_id = UtilityHelper::get_cb_fieldId_by_name('cb_anno_frequenza');
            $_cb_titolo_studio_id = UtilityHelper::get_cb_fieldId_by_name('cb_titolo_studio');
            $_cb_provincia_options = UtilityHelper::get_cb_field_select_by_name('cb_provdiresidenza');
            $_cb_provincia_id = UtilityHelper::get_cb_fieldId_by_name('cb_provdiresidenza');
            $_cb_provincia_albo_options = UtilityHelper::get_cb_field_select_by_name('cb_provincia_albo');
            $_cb_provincia_albo_id = UtilityHelper::get_cb_fieldId_by_name('cb_provincia_albo');
            $_cb_provincia_nascita_options = UtilityHelper::get_cb_field_select_by_name('cb_provinciadinascita');
            $_cb_provincia_nascita_id = UtilityHelper::get_cb_fieldId_by_name('cb_provinciadinascita');

            $_label_registrazione = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR5');
            $_label_nome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR8');
            $_label_cognome = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR9');
            $_label_username = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR2');
            $_label_password = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR10');
            $_label_r_password = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR11');
            $_label_cf = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR12');
            $_label_email = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR13');
            $_label_indirizzo = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR14');
            $_label_citta = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR15');
            $_label_pv = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR16');
            $_label_cap = JText::_('COM_REGISTRAZIONE_ASAND_STR6');
            $_label_citta_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR8');
            $_label_pv_nascita = JText::_('COM_GGLMS_ISCRIZIONE_EVENTO_STR9');
            $_label_dt_nascita = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR18');
            $_label_tel = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR19');
            $_label_piva = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR25');
            $_label_quota_associativa = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR29');
            $_label_quota_associativa_standard = JText::_('COM_REGISTRAZIONE_ASAND_STR3');
            $_label_quota_associativa_studente = JText::_('COM_REGISTRAZIONE_ASAND_STR4');
            $_label_annualita = JText::_('COM_REGISTRAZIONE_ASAND_STR5');
            $_label_universita = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR32');
            $_label_anno_di_frequenza= JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR33');
            $_label_matricola = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR34');
            $_label_titolo_studio = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR35');
            $_label_dipendente = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR36');
            $_label_area_pratica = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR37');
            $_label_numero_albo = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR38');
            $_label_provincia_albo = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR39');
            $_label_azienda_dipendente_sede = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR40');
            $_label_indirizzo_azienda = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR41');
            $_label_citta_azienda = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR42');
            $_label_indirizzo_studio = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR43');
            $_label_citta_studio = JText::_('COM_PAYPAL_ACQUISTA_EVENTO_STR44');

            $_quota_standard = $quotaStandard > 0
              ? "&euro; " . number_format($quotaStandard, 2, ',', '')
              : "";
            $_quota_studente = $quotaStudente > 0
              ? "&euro; " . number_format($quotaStudente, 2, ',', '')
              : "";

            $_html = <<<HTML

            <!--<link href="components/com_gglms/libraries/css/custom-form.css" rel="stylesheet" />-->

            <div class="row mt-4">
                <div class="col-12">
                    <h5><span style="color: black; font-weight: bold">{$_title_advise}</span></h5>
                </div>
            </div>
            <hr />
            <div class="container-form">

              <form method="POST">

                <div class="form-group row">
                  <label for="check_quota_associativa" class="col-sm-2 col-form-label">{$_label_quota_associativa}</label>
                  <div class="col-sm-10">
                      <div class="form-check">
                        <input class="form-check-input mt-0" type="radio" name="check_quota_associativa" id="check_quota_associativa1" value="quota_standard" />
                        <label class="form-check-label mt-1" for="check_quota_associativa1">{$_label_quota_associativa_standard} {$_label_annualita} {$_quota_standard}</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input mt-0" type="radio" name="check_quota_associativa" id="check_quota_associativa2" value="quota_studente" />
                        <label class="form-check-label mt-1" for="check_quota_associativa2">{$_label_quota_associativa_studente} {$_label_annualita} {$_quota_studente}</label>
                      </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="nome_utente" class="col-sm-2 col-form-label">{$_label_nome}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="nome_utente" data-campo="{$_cb_nome}" placeholder="{$_label_nome}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="cognome_utente" class="col-sm-2 col-form-label">{$_label_cognome}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="cognome_utente" data-campo="{$_cb_cognome}" placeholder="{$_label_cognome}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="email_utente" class="col-sm-2 col-form-label">{$_label_email}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="email" id="email_utente" placeholder="{$_label_email}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="username" class="col-sm-2 col-form-label">{$_label_username}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="username" data-campo="{$_cb_nome} placeholder="{$_label_username}" />
                  </div>
                </div>

                <div class="form-group row">
                    <label for="password_utente" class="col-sm-2 col-form-label">{$_label_password}<span style="color: red">*</span></label>
                    <div class="col-sm-10">
                      <div class="input-group w-25">
                        <input class="form-control" type="password" id="password_utente" value="" />
                        <div class="input-group-addon w-auto" id="show_hide_password">
                          <i class="fa fa-eye-slash" aria-hidden="true" id="show_hide_password_icon"></i>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="form-group row">
                  <label for="ripeti_password_utente" class="col-sm-2 col-form-label">{$_label_r_password}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="password" id="ripeti_password_utente" value="" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="cf_utente" class="col-sm-2 col-form-label">{$_label_cf}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25 text-uppercase" type="text" id="cf_utente" maxlength="16" data-campo="{$_cb_cf}" placeholder="{$_label_cf}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="citta_nascita_utente" class="col-sm-2 col-form-label">{$_label_citta_nascita}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="citta_nascita_utente" data-campo="{$_cb_luogodinascita}" placeholder="{$_label_citta_nascita}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="pv_nascita" class="col-sm-2 col-form-label">{$_label_pv_nascita}<span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <select class="form-control w-25" id="pv_nascita" data-campo="{$_cb_provinciadinascita}" data-id-ref="{$_cb_provincia_nascita_id}">
                        <option value="">-</option>
                        {$_cb_provincia_nascita_options}
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="data_nascita_utente" class="col-sm-2 col-form-label">{$_label_dt_nascita}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25 datepicker" type="text" id="data_nascita_utente"  data-campo="{$_cb_data_nascita}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="indirizzo_utente" class="col-sm-2 col-form-label">{$_label_indirizzo}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="indirizzo_utente" data-campo="{$_cb_indirizzo}" placeholder="{$_label_indirizzo}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="citta_utente" class="col-sm-2 col-form-label">{$_label_citta}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="citta_utente" data-campo="{$_cb_citta}" placeholder="{$_label_citta}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="pv_utente" class="col-sm-2 col-form-label">{$_label_pv}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <select class="form-control w-25" id="pv_utente" data-campo="{$_cb_provincia}" data-id-ref="{$_cb_provincia_id}">
                      <option value="">-</option>
                      {$_cb_provincia_options}
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="cap_utente" class="col-sm-2 col-form-label">{$_label_cap}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="cap_utente" data-campo="{$_cb_cap}" placeholder="{$_label_cap}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="telefono_utente" class="col-sm-2 col-form-label">{$_label_tel}<span style="color: red">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control w-25" type="text" id="telefono_utente" data-campo="{$_cb_telefono}"  placeholder="{$_label_tel}" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="telefono_utente" class="col-sm-2 col-form-label">Autodichiarazione titolo di studio</label>
                  <div class="col-sm-10">
                      Consapevole delle sanzioni penali applicabili in caso di dichiarazioni mendaci e non veritiere che sono previste dagli articoli 75 e 76 del D.P.R 28/12/2000 n. 445 e per gli effetti dell’art. 47 del citato D.P.R. 445/2000, sotto la personale responsabilità, <b>DICHIARO</b> ai sensi dell'art. 46 D.P.R. n. 445/2000 di essere in possesso del titolo di studio sotto indicato
                  </div>
                </div>

                <div class="form-group row">
                  <label for="titolo_studio" class="col-sm-2 col-form-label">{$_label_titolo_studio}<span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <select class="form-control w-25" id="titolo_studio" data-campo="cb_titolo_studio" data-id-ref="{$_cb_titolo_studio_id}">
                        <option value="">-</option>
                        {$_cb_titolo_studio_options}
                    </select>
                  </div>
                </div>

                <div id="campi_studente" style="display: none;">

                  <div class="form-group row">
                    <label for="universita" class="col-sm-2 col-form-label">{$_label_universita}<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input class="form-control w-25 campi_studente" type="text" id="universita" data-campo="{$_cb_universita}" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="anno_di_frequenza" class="col-sm-2 col-form-label">{$_label_anno_di_frequenza}<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <select class="form-control w-25 campi_studente" id="anno_di_frequenza" data-campo="{$_cb_anno_di_frequenza}" data-id-ref="{$_cb_anno_di_frequenza_id}">
                          <option value="">-</option>
                          {$_cb_anno_di_frequenza_options}
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="matricola" class="col-sm-2 col-form-label">{$_label_matricola}<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input class="form-control w-25 campi_studente" type="text" id="matricola" data-campo="{$_cb_matricola}" />
                    </div>
                  </div>

                  <!--
                    <div class="form-group row">
                    <label for="area_pratica_studente" class="col-sm-2 col-form-label">{$_label_area_pratica}<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input class="form-control w-25 campi_studente" type="text" id="area_pratica_studente" data-campo="{$_cb_area_pratica}" />
                    </div>
                  </div>
                  -->

                </div>

                <div id="campi_professione" style="display: none;">

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Autodichiarazione posizione lavorativa</label>
                    <div class="col-sm-10">
                      <b>Dichiaro</b>, altresì, di ricoprire la seguente posizione lavorativa:
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="posizione_lavorativa" class="col-sm-2 col-form-label">{$_label_dipendente}<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <select class="form-control w-25 campi_professione" id="posizione_lavorativa" data-campo="{$_cb_dipendente}" data-id-ref="{$_cb_dipendente_id}">
                        <option value="">-</option>
                        {$_cb_dipendente_options}
                      </select>
                    </div>
                  </div>

                  <div id="campi_dipendente" style="display: none;">

                    <div id="dipendente" style="display: none;">

                      <div class="form-group row">
                        <label for="azienda" class="col-sm-2 col-form-label">{$_label_azienda_dipendente_sede}<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                          <input class="form-control w-25 campi_dipendente" type="text" id="azienda" data-campo="{$_cb_azienda_dipendente_sede}" />
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="indirizzo_azienda" class="col-sm-2 col-form-label">{$_label_indirizzo_azienda}<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                          <input class="form-control w-25 campi_dipendente" type="text" id="indirizzo_azienda" data-campo="{$_cb_indirizzo_azienda}" />
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="citta_azienda" class="col-sm-2 col-form-label">{$_label_citta_azienda}<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                          <input class="form-control w-25 campi_dipendente" type="text" id="citta_azienda" data-campo="{$_cb_citta_azienda}" />
                        </div>
                      </div>

                    </div>

                    <div id="campi_libero" style="display: none;">

                      <div class="form-group row">
                        <label for="indirizzo_studio" class="col-sm-2 col-form-label">{$_label_indirizzo_studio}<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                          <input class="form-control w-25 campi_libero" type="text" id="indirizzo_studio" data-campo="{$_cb_indirizzo_studio}" />
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="citta_studio" class="col-sm-2 col-form-label">{$_label_citta_studio}<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                          <input class="form-control w-25 campi_libero" type="text" id="citta_studio" data-campo="{$_cb_citta_studio}" />
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="piva" class="col-sm-2 col-form-label">{$_label_piva}<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                          <input class="form-control w-25 campi_libero" type="text" id="piva" data-campo="{$_cb_piva}" />
                        </div>
                      </div>

                    </div>

                    <div class="form-group row" id="area_pratica_professione_row">
                      <label for="area_pratica_professione" class="col-sm-2 col-form-label">{$_label_area_pratica}<span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input class="form-control w-25 campi_professione" type="text" id="area_pratica_professione" data-campo="{$_cb_area_pratica}" />
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="numero_albo" class="col-sm-2 col-form-label">{$_label_numero_albo}<span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input class="form-control w-25 campi_professione" type="text" id="numero_albo" data-campo="{$_cb_numero_albo}" />
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="pv_albo" class="col-sm-2 col-form-label">{$_label_provincia_albo}<span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control w-25 campi_professione" id="pv_albo" data-campo="{$_cb_provincia_albo}" data-id-ref="{$_cb_provincia_albo_id}">
                          <option value="">-</option>
                          {$_cb_provincia_albo_options}
                        </select>
                      </div>
                    </div>

                  </div>

                </div>

                <div class="form-group row">
                  <div class="col-sm-2"><b>Ho letto l'informativa <a href="/privacy-policy" target="_blank">privacy</a> e do il consenso al trattamento dei miei dati</b></div>
                  <div class="col-sm-10">
                    <div class="form-check">
                      <input class="form-check-input mt-0" type="checkbox" id="privacy_check">
                      <label class="form-check-label mt-1" for="privacy_check">
                        Accetta termini e condizioni
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-6 offset-sm-3 text-center">
                    <button class="btn btn-large btn-registrazione" data-ref="{$_ref_registrazione}">{$_label_registrazione}</button>
                  </div>
                </div>
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


}
