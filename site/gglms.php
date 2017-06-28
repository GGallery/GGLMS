<?php
/**
 * @version		1
 * @package		webtv
 * @author 		antonio
 * @author mail	tony@bslt.it
 * @link		
 * @copyright	Copyright (C) 2011 antonio - All rights reserved.
 * @license		GNU/GPL
 */

// asino chi legge
 
// no direct access
defined('_JEXEC') or die('Restricted access');
 
// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

// Require specific controller if requested
if($controller = JRequest::getCmd('controller')) 
{
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if ( file_exists( $path ) ) {
		require_once( $path );
	} else {
		$controller = '';
	}
}

// Create the controller
 $controller = JControllerLegacy::getInstance('gglms');

//$classname	= 'gglmsController' . ucfirst($controller);
//$controller = new $classname();

 
// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task')); //RS $controller->execute(JRequest::getCmd('task'));


$controller->redirect();