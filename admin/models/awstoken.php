<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 13:01
 */


/**
 * @package        Joomla.Tutorials
 * @subpackage    Component
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class gglmsModelAwsToken extends JModelList{
    public function setToken()
    {
        try{

        $db = JFactory::getDBO();
        $query = 'SELECT config_value 
        FROM #__gg_configs 
        WHERE config_key=\'aws_token\'';

        $db->setQuery($query);
        $result = $db->loadResult();

        if (isset($result)||$result!=''){
            return true;
        }

        $token=uniqid();
            $settoken = "REPLACE #__gg_configs ( config_key,config_value ) 
            VALUES ('aws_token','".$token."')";
            $db->setQuery($settoken);
            $result = $db->execute();
            return true;
    }catch(Exception $e){
        
    }

    }
}