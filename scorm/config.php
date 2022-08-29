<?php

define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
// fix path api SCORM
//define( 'JPATH_BASE',  $_SERVER['DOCUMENT_ROOT'] .DS . 'home');
$pBase = $_SERVER['DOCUMENT_ROOT'] .DS . 'home';
$pBase = is_dir($pBase) ? $pBase : $_SERVER['DOCUMENT_ROOT'] . DS;
define( 'JPATH_BASE',  $pBase);

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
