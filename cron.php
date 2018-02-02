<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 12:59
 */


/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_COMPONENT . '/models/syncdatareport.php';




    try{

        $syncdatareport=new gglmsModelSyncdatareport();
        $syncdatareport->sync();
        /* $ch = curl_init("https://www.unicollege.it/home/index.php?option=com_gglms&task=crontask.do_it");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        fwrite(STDOUT,$output);*/

        }catch (Exception $ex){
//         fwrite(STDOUT,$ex->getMessage());
    }
