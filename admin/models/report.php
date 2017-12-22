<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 13:01
 */


/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class gglmsModelReport extends JModelList {


    public function empty_tables(){

        if($this->empty_table('#__gg_report_users')==true){

            if($this->empty_table('#__gg_report')==true){

                if($this->azzera_data_sync()){
                return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
        return true;
    }

    private function empty_table($table_name){

        $db= JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->delete($table_name);
        $db->setQuery($query);
        $result=$db->execute();
        return $result;
    }

    private function azzera_data_sync(){

        $db= JFactory::getDBO();
        $query = 'UPDATE #__gg_configs SET config_value=null WHERE config_key=\'data_sync\'';

        $db->setQuery($query);
        $result=$db->execute();
        return $result;

    }

    public function allinea_tabella($tabella,$modalita=null){

        $db= JFactory::getDBO();
        $query = $db->getQuery(true);
        if($tabella=='scormvars')
            $query=$this->query_scormvars($query,$modalita);
        if($tabella=='unit_map')
            $query=$this->query_unit_map($query,$modalita);
        $db->setQuery($query);
        if($modalita=='delete') {
            $result = $db->execute();

        }else{
            $result = $db->loadResult();

        }

        return $result;

    }

    private function query_scormvars($query,$modalita=null){

        if($modalita=='delete') {
            $query->delete('#__gg_scormvars');
        }else{
            $query->select('count(*)');
            $query->from('#__gg_scormvars');
        }
        $query->where ('scoid not in (select id from #__gg_contenuti)');

               return $query;

    }

    private function query_unit_map($query,$modalita=null){

        if($modalita=='delete') {
            $query->delete('#__gg_unit_map');
        }else{
            $query->select('count(*)');
            $query->from('#__gg_unit_map');
        }
        $query->where ('idunita not in (select id from #__gg_unit)');

        return $query;
    }
}