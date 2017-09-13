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


}
