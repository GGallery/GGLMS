<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * versione del file rielaborata per impostare la costante DOMINIO usata nel sistema e definita anche in controller.php di site
 * qui è necessario specificare la definizione trattando anche la modalità di accesso php CLI
 */

defined('_JEXEC') or die;

// Global definitions
$parts = explode(DIRECTORY_SEPARATOR, JPATH_BASE);

// Defines.
define('JPATH_ROOT',          implode(DIRECTORY_SEPARATOR, $parts));
define('JPATH_SITE',          JPATH_ROOT);
define('JPATH_CONFIGURATION', JPATH_ROOT);
define('JPATH_ADMINISTRATOR', JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator');
define('JPATH_LIBRARIES',     JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries');
define('JPATH_PLUGINS',       JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins');
define('JPATH_INSTALLATION',  JPATH_ROOT . DIRECTORY_SEPARATOR . 'installation');
define('JPATH_THEMES',        JPATH_BASE . DIRECTORY_SEPARATOR . 'templates');
define('JPATH_CACHE',         JPATH_BASE . DIRECTORY_SEPARATOR . 'cache');
define('JPATH_MANIFESTS',     JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'manifests');

// se non sto eseguendo "qualcosa" da php CLI altimenti va in errore
if (php_sapi_name() != "cli") {
    $_https = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    $hostname = parse_url($_https . "://" . $_SERVER["HTTP_HOST"], PHP_URL_HOST);

    $_arr_host = explode(".", $hostname);
    // indirizzi tipo https://dominio.it
    if (count($_arr_host) < 3) {
        $hostname = $_arr_host[0] . "." . $_arr_host[1];
    }
    // altri tipo www.dominio.it oppure terzo.dominio.it
    else {
        $hostname = $_arr_host[1] . "." . $_arr_host[2];
    }

    define('DOMINIO', $hostname);
}
