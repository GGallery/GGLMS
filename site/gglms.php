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

///// Aggiungi qui le stringhe che devono essere localizzate anche in javascript
JText::script('COM_GGLMS_GLOBAL_COUPON');
JText::script('COM_GGLMS_GLOBAL_USER');
JText::script('COM_GGLMS_GLOBAL_CREATION_DATE');
JText::script('COM_GGLMS_GLOBAL_USE_DATE');
JText::script('COM_GGLMS_GLOBAL_VENDITORE');
JText::script('COM_GGLMS_GLOBAL_CORSO');
JText::script('COM_GGLMS_GLOBAL_RECORD');
JText::script('COM_GGLMS_REPORT_COGNOME');
JText::script('COM_GGLMS_REPORT_NOME');
JText::script('COM_GGLMS_REPORT_DATA_INIZIO');
JText::script('COM_GGLMS_REPORT_DATA_FINE');
JText::script('COM_GGLMS_REPORT_DETTAGLI');
JText::script('COM_GGLMS_REPORT_ATTESTATI');
JText::script('COM_GGLMS_GLOBAL_STATO');
JText::script('COM_GGLMS_REPORT_FIELDS');
JText::script('COM_GGLMS_REPORT_TIPO_CORSO');
JText::script('COM_GGLMS_REPORT_TIPO_UNITA');
JText::script('COM_GGLMS_REPORT_TIPO_CONTENUTO');
JText::script('COM_GGLMS_GLOBAL_STATO_ANY');
JText::script('COM_GGLMS_REPORT_COMPLETATI');
JText::script('COM_GGLMS_REPORT_NON_COMPLETATI');
JText::script('COM_GGLMS_REPORT_IN_SCADENZA');
JText::script('COM_GGLMS_REPORT_ATTESTATI_HIDDEN');
JText::script('COM_GGLMS_REPORT_USER_SCADUTO');
JText::script('COM_GGLMS_GLOBAL_DELETE');
JText::script('COM_GGLMS_GLOBAL_ATTESTATO');
JText::script('COM_GGLMS_GLOBAL_COMPANY');
JText::script('COM_GGLMS_REPORT_USERDETAIL');
JText::script('COM_GGLMS_GLOBAL_COMPLETED');
JText::script('COM_GGLMS_GLOBAL_NOT_COMPLETED');
JText::script('COM_GGLMS_GLOBAL_COMPLETED');
JText::script('COM_GGLMS_GLOBAL_STATO_LBERI');
JText::script('COM_GGLMS_GLOBAL_ALL');
JText::script('COM_GGLMS_REPORT_THIS_PAGE');
JText::script('COM_GGLMS_REPORT_ALL_PAGES');
JText::script('COM_GGLMS_REPORT_DATE_LTE');
JText::script('COM_GGLMS_REPORT_DATE_GTE');
JText::script('COM_GGLMS_REPORT_DATE_ISNULL');
JText::script('COM_GGLMS_REPORT_DATE_ISNOTNULL');
JText::script('COM_GGLMS_REPORT_CONTENT');
JText::script('COM_GGLMS_REPORT_LAST_VISIT');
JText::script('COM_GGLMS_REPORT_PERMANENZA');
JText::script('COM_GGLMS_REPORT_VISUALIZZAZIONI');
JText::script('COM_GGLMS_GLOBAL_FIELD');
JText::script('COM_GGLMS_GLOBAL_VALUE');
JText::script('COM_GGLMS_CB_DATA_NASCITA');
JText::script('COM_GGLMS_CB_LUOGO_NASCITA');
JText::script('COM_GGLMS_CB_PROVINICIA_NASCITA');
JText::script('COM_GGLMS_CB_INDIRIZZO');
JText::script('COM_GGLMS_CB_PROVINCIA_RESIDENZA');
JText::script('COM_GGLMS_CB_TELEFONO');
JText::script('COM_GGLMS_CB_CODICE_FISCALE');
JText::script('COM_GGLMS_CB_USERNAME');
JText::script('COM_GGLMS_CB_EMAIL');
JText::script('COM_GGLMS_ATTESTATI_BULK_MAX_LIMIT');
JText::script('COM_GGLMS_ATTESTATI_BULK_ERROR_MSG');













