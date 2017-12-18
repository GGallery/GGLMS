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

                return true;
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


}