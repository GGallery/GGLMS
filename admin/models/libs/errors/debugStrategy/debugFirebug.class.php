<?php
require_once('FirePHPCore/fb.php');

/**
  * @brief {@link http://www.firephp.org/ FirePHP} Debug Strategy
  * It displays all debug messages into the {@link http://getfirebug.com/ FireBug} console.
  * FirePHP doesn't work if no output is generated becouse no header is sent to the browser.
  *
  * @author diego.brondo
  * @version 0.0.1
  * @since 23/dec/2011
  * @package ErrorHandler
  * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
  */
class debugFirebug extends debugStrategy {

    private $_max_depth;

    private $_show_line;

	/**
	 * The possible options are:
    * - debug_level: level of debug if it's not specified it's set by debugObject to DEBUG_DEFAULT_LEVEL
    * - max_depth (int): maximum depth to traverse objects and arrays (Default 10).
    * - show_line (bool): include File and Line information in message (Default true).
	 *
	 * @param array $options
	 */
	public function __construct($options=null) {
		parent::__construct($options);

        $this->_max_depth = isset($options['max_depth']) ? filter_var($options['max_depth'], FILTER_VALIDATE_INT, array('options'=>array('default'=>10, 'min_range'=>0))) : 10;
        $this->_show_line = isset($options['show_line']) ? filter_var($options['show_line'], FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => true))) : true;
		$FBoptions = array(
			'maxObjectDepth' => $this->_max_depth,
			'maxArrayDepth' => $this->_max_depth,
			'maxDepth' => $this->_max_depth,
			'useNativeJsonEncode' => true,
			'includeLineNumbers' => $this->_show_line
		);
		FB::setEnabled(true);
		FB::setOptions($FBoptions);
		$this->msg('Debug Console started at ' . date('c', time()) . ' from ' . gethostbyaddr($_SERVER['REMOTE_ADDR']), DEBUG_INFO);
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::__destruct()
	 */
	public function __destruct() {
		while(!empty($this->_chronotrigger)) {
			$this->chrono_stop();
		}
		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::msg()
	 */
	public function msg($msg, $level=DEBUG_LOG) {
		if (!empty($msg) && ($this->_level & $level)) {
			if (DEBUG_INFO & $level) {
				if (is_array($msg)) {
					FB::group(current($msg),array('Collapsed' => true));
					FB::info($msg);
					FB::groupEnd();
				} else {
					FB::info($msg);
				}
			} elseif (DEBUG_ERROR & $level || DEBUG_STRICT & $level) {
				if (is_array($msg)) {
					FB::group(current($msg), array('Collapsed' => true, 'Color' => '#FF0000'));
					FB::error($msg);
					FB::groupEnd();
				} else {
					FB::error($msg);
				}
			} elseif (DEBUG_WARNING & $level) {
				if (is_array($msg)) {
					FB::group(current($msg), array('Collapsed' => true, 'Color' => '#FF0000'));
					FB::warn($msg);
					FB::groupEnd();
				} else {
					FB::warn($msg);
				}
			} else {
				if (is_array($msg)) {
					FB::group(current($msg),array('Collapsed' => true));
					FB::log($msg);
					FB::groupEnd();
				} else {
					FB::log($msg);
				}
			}
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::vardump()
	 */
	public function vardump($var, $name=null) {
		if ($this->_level & DEBUG_LOG) {
			FB::send($var, $name);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::exception()
	 */
	public function exception(Exception $e, $trace=null, $source=null, $lines=null) {
		if (error_reporting() & $e->getCode()) { 
			$this->msg('exception \''.get_class($e).'\' with message \'['.$e->getCode().']: '.$e->getMessage(). '\' in '.$e->getFile().':'.$e->getLine(), $e->getCode());
			if (isset($trace) || $this->_trace) {
				$trace_array = $e->getTrace();
				if (!empty($trace_array)) {
					$trace_table = array();
					$trace_table[] = array('File', 'Line', 'Instruction');
					foreach ($e->getTrace() as $t) {
						if ($t['function'] == 'PokemonErrorHandler') { // elimino il 4^ argomento dell'error handler (in questo caso la funzione PokemonErrorHandler) 
							unset($t['args'][4]);					   // perche' da' problemi di ricorsione in quanto contine riferiemnto a $GLOBAL
						}
						$trace_table[] = array(
							isset($t['file']) ? $t['file'] : '-',
							isset($t['line']) ? $t['line'] : '-',
							(isset($t['class']) ? $t['class'].'::' : '').
								(isset($t['function']) ? $t['function'].'('.
									(isset($t['args']) ? array_reduce($t['args'], array($this, 'args2string')) : '').')' : '-')
						);
					}
					FB::table('TRACE', $trace_table);
				}
			}
            if ((isset($source) || $this->_source)  && method_exists($e,'get_source')) {
                $lines = isset($lines) ? $lines : $this->_source_lines;
				$line = $e->getLine() - floor($lines/2);
				$source_table = array();
				$source_table[] = array('Line', 'Code');
				foreach ($e->get_source($lines) as $l) {
					$source_table[] = array($line++, $l);
				}
				FB::table('SOURCE', $source_table);
			}
			unset ($msg);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see debugStrategy::headers()
	 * Elimina tutte le intestazioni di FirePHP
	 */
	public function headers() {
		$headers = headers_list();
		for ($i=0,$tot=count($headers); $i<$tot; $i++)
			if (preg_match('/^X-Wf/', $headers[$i])) unset($headers[$i]);
		$this->msg($headers, DEBUG_LOG);
	}
}
// ~@:-]
?>
