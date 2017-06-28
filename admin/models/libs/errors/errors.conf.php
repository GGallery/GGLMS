<?php
/**
 * This directive informs PHP of which errors, warnings and notices you would like it to take action for.
 * We avoid the user to see error messages in HTML output. Sensitive date (file paths, MySQL tables, etc..) that could affect
 * security system. Set a low level of error reporting.
 * If set to 0 no error message will be displayed.
 * The tips is set to E_ALL (or better to E_ALL | E_STRICT) during application development, and then put it to 0 in production.
 * It 'also recommended to set environment variables (dev = in development; prod = production):
 * - display_errors: dev on; prod off
 * - display_startup_errors: dev on; prod off
 * - error_reporting:  dev E_ALL | E_STRICT; prod E_ALL & ~E_DEPRECATED & ~E_NOTICE
 * - html_errors: dev on; prod off
 * - log_errors: dev on; prod on (using @see debugLog:: or @see debugMail:: or @see debugSql:: as debug messages handler)
 * Set it to E_ALL|E_STRICT during development, while in production set it to E_ALL|~E_NOTICE 
 */
define('ERROR_REPORTING', E_ALL|E_STRICT);

define('DEBUG_DEFAULT_TIMEZONE', 'Europe/Rome');

/**#@+
* Dedug level constants
*/
define('DEBUG_ERROR', E_ERROR|E_USER_ERROR|E_COMPILE_ERROR|E_CORE_ERROR|E_PARSE);
define('DEBUG_WARNING', E_WARNING|E_USER_WARNING|E_COMPILE_WARNING|E_CORE_WARNING);
define('DEBUG_NOTICE', E_NOTICE|E_USER_NOTICE|E_DEPRECATED|E_USER_DEPRECATED);
define('DEBUG_STRICT', E_STRICT);
define('DEBUG_INFO', 32768); // 2^15
define('DEBUG_LOG', 65536); // 2^16
define('DEBUG_DUMP', 131072); // 2^17

/**
 * Max verbosita' di debug consigliata in fase di sviluppo. ATTENZIONE: non usare in produzione. Leggi @see exceptions:: per
 * maggiori informazioni sul livello di error reporting e sulle impostazioni di sistema.
 * @var int
 */
define('DEBUG_DEV', E_ALL|E_STRICT|DEBUG_INFO|DEBUG_LOG|DEBUG_DUMP);

/**
 * Parametri di default per la gestione degli errori
 */
define('DEBUG_DEFAULT_LEVEL', DEBUG_DEV);
define('DEBUG_DEFAULT_TRACE', true);
define('DEBUG_DEFAULT_SOURCE', false);
define('DEBUG_DEFAULT_SOURCE_LINES', 11);
define('DEBUG_DEFAULT_LOGFILE', './debug.log');
define('DEBUG_DEFAULT_LOG_APPEND', true);
define('DEBUG_DEFAULT_MAIL_FROM', 'noreply@localhost');
define('DEBUG_DEFAULT_CSV_DELIMITER', ';');
define('DEBUG_DEFAULT_CSV_ENCLOSURE', '"');    
define('DEBUG_DEFAULT_MYSQL_DSN', 'mysqli://username:password@host/database');
define('DEBUG_DEFAULT_MYSQL_TABLE', 'debug');
// ~@:-]
?>
