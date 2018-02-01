<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class gglmsControllerContents extends JControllerAdmin
{
    private $model;
	public function getModel($name = 'content', $prefix = 'gglmsModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function saveOrderAjax(){
        $pks = $this->input->post->get('cid', array(), 'array');
        $i=0;

        foreach ($pks as $pk){
            $this->updateOrderValue($pk,$i);
            $i++;
        }

    }

    private  function updateOrderValue($pk,$i){

	    $model=$this->getModel('contents');
	    return $model->updateOrderValue($pk,$i);
    }


}
