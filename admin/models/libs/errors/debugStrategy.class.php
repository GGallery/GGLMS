<?php

require_once('exceptions.class.php');
require_once(__DIR__ . '/../utils/phpinfo.lib.php');
require_once(__DIR__ . '/../utils/clientinfo.lib.php');

defined('DEBUG_DEFAULT_LEVEL') || define('DEBUG_DEFAULT_LEVEL', E_ALL|E_STRICT);
defined('DEBUG_DEFAULT_TRACE') || define('DEBUG_DEFAULT_TRACE', true);
defined('DEBUG_DEFAULT_SOURCE') || define('DEBUG_DEFAULT_SOURCE', false);
defined('DEBUG_DEFAULT_SOURCE_LINES') || define('DEBUG_DEFAULT_SOURCE_LINES', 11);

/**
  * @brief Base abstract class which defines all methods of the debug strategy.
  *
  * @abstract
  * @author diego.brondo
  * @version $Version$
  * @package ErrorHandler
  * @since 20/ott/2011
  * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
  * @author jbrond
  */
abstract class debugStrategy {

    protected $_level;
    protected $_trace;
    protected $_source;
    protected $_source_lines;

	/**
	 * Stack o stopwatches; it's an associative array string name => long microtime.
	 *
	 * @var array
	 */
	protected $_chronotrigger;

	/**
	 * Class Constructor
	 * The debug strategy can be configured at initialization time with a set of options.
	 * Each strategy has a "debug_level" option which set the debug level; if not specified the default level is
	 * DEBUG_DEFAULT_LEVEL.
	 *
	 * @param array $options Runtime options; each strategy has it's own configuration options.
	 */
	public function __construct($options=null) {
        $this->_chronotrigger = array();
        $this->_level = isset($options['debug_level']) ? filter_var($options['debug_level'], FILTER_VALIDATE_INT, array('options' => array('default' => DEBUG_DEFAULT_LEVEL))) : DEBUG_DEFAULT_LEVEL;
        $this->_trace = isset($options['trace']) ? filter_var($options['trace'], FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => DEBUG_DEFAULT_TRACE))) : DEBUG_DEFAULT_TRACE;
        $this->_source = isset($options['source']) ? filter_var($options['source'], FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => DEBUG_DEFAULT_SOURCE))) : DEBUG_DEFAULT_SOURCE;
        $this->_source_lines = isset($options['source_lines']) ?filter_var($options['source_lines'], FILTER_VALIDATE_INT, array('options' => array('default' => DEBUG_DEFAULT_SOURCE_LINES))) : DEBUG_DEFAULT_SOURCE_LINES;
	}

	/*
	 * Class destructor
	 */
	public function __destruct() {
		while(!empty($this->_chronotrigger)) {
			$this->chrono_stop();
		}
		unset($this->_chronotrigger);
        unset($this->_level);
        unset($this->_trace);
        unset($this->_source);
        unset($this->_source_lines);
	}

	/**
	 * Write a debug message
	 *
	 * @param mixed $msg The message sent to the debug; if it's an array the output of @see var_export() will be used.
	 * @param int $level Level of debug message (default DEBUG_LOG).
	 */
	abstract public function msg($msg, $level=DEBUG_LOG);

	/**
	 * It writes the type (@see gettype()) and the value (@see var_export()) of the variable passed as first argument.
	 * The second argument of the method is a label printed in the debug with the value.
	 * Default debug level is DEBUG_DUMP.
	 *
	 * @param mixed $var The variable.
	 * @param string $name A label displayed in debug.
	 */
	public function vardump($var, $name=null) {
		$this->msg((isset($name) ? '$'.$name.': ' : '').var_export($var, true), DEBUG_DUMP);
	}


    public function objdump($obj) {
        if (is_object($obj)) {
            $class = get_class($obj);
            $this->vardump(get_class_methods($obj), $class . ' Methods:');
            $this->vardump(get_object_vars($obj), $class . ' Vars');
        }
    }

	/**
	 * Inserisce un checkpoint nella debug console. Un checkpoint e' una stringa che riporta file e riga in cui questa funzione viene chiamata.
	 * E' possibile inoltre specificare un messaggio aggiuntivo da inserire nel checkpoint.
	 * IL livello di debug e' DEBUG_LOG.
	 *
	 * @param string $msg (default '').
	 */
	public function checkpoint($msg='') {
		if ($this->_level & DEBUG_LOG) {
			$backtrace = debug_backtrace();
			$this->msg('Checkpoint: '.$msg.' in "'.$backtrace[1]['file'].'" at line '.$backtrace[1]['line'], DEBUG_LOG);
			unset($backtrace);
		}
	}

	/**
	 * La classe debug permette di cronometrare porzioni di codice PHP con i metodi
	 * @see chrono_start() e @see chrono_stop().
	 * Ongi qualvolta @see chrono_start() e' chiamata, un nuovo cronometro viene aggiunto
	 * nello stack dei cronometri della debug console.
	 * Inoltre un messaggio viene salvato nella debug console.
	 * Il livello di debug e' per default DEBUG_LOG.
	 *
	 * @param string $name Parametro opzionale con cui identificare il cronometro (default='').
	 */
	public function chrono_start($name='') {
		$this->msg('Chrono start "'.$name.'"', DEBUG_LOG);
		$this->_chronotrigger[] = array($name, microtime(true));
	}

	/**
	 * Ferma l'ultimo cronometro inserito e scrive nella debug console un messaggio con
	 * quanto tempo e' trascorso.
	 */
	public function chrono_stop() {
		if (!empty($this->_chronotrigger)) {
			list($name, $time) = array_pop($this->_chronotrigger);
			$this->msg('Chrono stop "'.$name.'" '.(string)(microtime(true) - $time).'s', DEBUG_LOG);
		}
	}

	/**
	 * Aggiunge all'output di debug il totale delle memoria utilizzata nel momento in cui 
	 * questa funzione viene chiamta. Il risutlato in byte è archiviato con livello DEBUG_INFO 
	 * Utilizza la funzione PHP memory_get_usage().
	 * Il livello di debug e' per default DEBUG_INFO.
	 *
	 * @param string $msg Eventuale messaggio.
	 */
	public function memory($msg='') {
		$usage = memory_get_usage();
		$limit = ini_get('memory_limit');
		if (preg_match('/(\d+)(\w+)/', $limit, $matches)) { 
			if ('' === $matches[2]) $limit = $matches[1] * 104857;
			elseif ('K' === $matches[2]) $limit = $matches[1] * 1024;
			$perc = sprintf('%.2f', ($usage*100)/$limit);
		} else {
			$perc = '?';
		}
		$this->msg('emory usage '.$msg.' '.$usage.' ['.$perc.'%]', DEBUG_INFO);
		unset($limit);
		unset($usage);
		unset($perc);
	}

	/**
	 * Ritorna la dimensione di memeoria calcolando i picchi di memoria allocati dallo script PHP.
	 * @param string $msg
	 */
	public function memorypeak($msg='') {
		$usage = memory_get_peak_usage();
		$limit = ini_get('memory_limit');
		if (preg_match('/(\d+)(\w+)/', $limit, $matches)) {
			if ('' === $matches[2]) $limit = $matches[1] * 104857;
			elseif ('K' === $matches[2]) $limit = $matches[1] * 1024;
			$perc = sprintf('%.2f', ($usage*100)/$limit);
		} else {
			$perc = '?';
		}
		$this->msg('Peak memory usage '.$msg.' '.$usage.' ['.$perc.'%]', DEBUG_INFO);
		unset($limit);
		unset($usage);
		unset($perc);
	}
	
	/**
	 * Raccoglie un'eccezione e inserisce il messaggio nella console di debug.
	 * Utilizza la classe @see exceptions::
	 * Il livello di debug e' definito dal codice di errore dell'eccezione.
	 *
	 * @param Exception $exception Oggetto della classe @see Exception::
	 * @param bool $trace Se vera inserisce il backtrace nella console di debug (defautl DEBUG_TRACE).
	 * @param bool $source Se vera inserisce anche il codice nella debug console (defautl DEBUG_SOURCE). Solo @see exceptions::
	 * supporta l'output di codice nella debug console.
	 * @param int $lines Numero di righe da prelevare se $source e' vera (default DEBUG_SOURCE_LINES).
     */
	public function exception(Exception $e, $trace=null, $source=null, $lines=null) {
		if (error_reporting() & $e->getCode()) {
			$msg = (string)$e;
			if (isset($trace) || $this->_trace) {
				$msg .= PHP_EOL . $e->getTraceAsString();
			}
            if ((isset($source) || $this->_source)  && method_exists($e,'get_source_str')) {
				$msg .= PHP_EOL . $e->get_source_str(isset($lines) ? $lines : $this->_source_lines);
			}
			$this->msg($msg, $e->getCode());
			unset ($msg);
		}
	}
	
	/**
	 * Scrive un messaggio di log indicando il file e la riga in cui e' stato inserito il break point.
	 * Il livello di log e' DEBUG_LOG
	 */
	public function breakpoint() {
		if ($this->_level & DEBUG_LOG) {
			$backtrace = debug_backtrace();
			$this->msg('Break Point in "'.$backtrace[1]['file'].'" at line '.$backtrace[1]['line'], DEBUG_LOG);
			unset($backtrace);
		}
	}
	
	/**
	 * Scrive tutte le intestazioni inviate o pronte per essere inviate al browser.
	 * Il livello di debug e' DEBUG_DUMP 
	 */
	public function headers() {
		$this->msg(headers_list(), DEBUG_DUMP);
	}
	
	/**
	 * Scrive alcune informazioni sul client:
	 * - UserAgent
	 * - indirizzo IP
	 * Il livello di debug e' DEBUG_DUMP. 
	 */
	public function client_info() {
		$info = array(
			'useragent'	=> get_user_agent_string(),
			'clientip'	=> get_client_ip()
		);
		$this->vardump($info, 'CLIENT INFO');
		unset($info);
	}
	
	/**
	 * Scrive alcune informazioni sul server su cui viene eseguito lo script:
	 * - sistema operativo
	 * - data di compilazione
	 * - server API
	 * - versione PHP
	 * - web server
	 * - indirizzo IP
	 * - nome host
	 * Il livello di debug e' DEBUG_DUMP.
	 */
	public function server_info() {
		$phpinfo = phpinfo_array(INFO_GENERAL|INFO_MODULES);
		$info = array(
			'system'	=> isset($phpinfo['General']['System']) ? $phpinfo['General']['System'] : 'Unknown',
			'build'		=> isset($phpinfo['General']['Build Date']) ? $phpinfo['General']['Build Date'] : 'Unknown',
			'sapi'		=> isset($phpinfo['General']['Server API']) ? $phpinfo['General']['Server API'] : 'Unknown',
			'phpversion'=> isset($phpinfo['Core']['PHP Version']) ? $phpinfo['Core']['PHP Version'] : 'Unknown',
			'serversw'	=> isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknoun',
			'serverip'	=> isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'Unknown',
			'servername'=> isset($_SERVER['SERVER_NAE']) ? $_SERVER['SERVER_NAME'] : 'Unknown'
		);
		$this->vardump($info, 'SERVER INFO');
		unset($phpinfo);
		unset($info);
	}

	/**
	 * Dato un array applica se stessa ricorsivamente per ridurre l'array in una stringa i cui elementi
	 * sono separati da ",". Usare array_reduce($array, array($this, 'args2string'))
	 *
	 * @param string $arg
	 * @param mixed $a
	 * @return string
	 */
	protected function args2string($args, $a) {
		$ret = !empty($args) ? $args.',' : '';
		switch(gettype($a)){
			case 'boolean':
				$ret .= ($a) ? 'true' : 'false';
				break;
			case 'integer':
			case 'double':
				$ret .= $a;
				break;
			case 'string':
				$ret .= '"'.$a.'"';
				break;
			case 'array':
				$ret .= 'Array('.array_reduce($a, array($this, 'args2string')).')';
				break;
			case 'object':
				$ret .= get_class($a).'::';
				break;
			case 'resource':
				$ret .= 'resource:'.get_resource_type($a);
				break;
			case 'NULL':
				$ret .= 'NULL';
				break;
			default:
				$ret .= '** unknown args **';
		}
		return $ret;
	}
}
// ~@:-]
?>
