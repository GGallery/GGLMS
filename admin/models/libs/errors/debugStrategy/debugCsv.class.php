<?php
/**
 * @brief: CSV Debug Strategy writes to a CSV file all error logs.
 * Each error fields are stored in a line separated by a field delimiter:
 * time;code;message;
 * No trace or source are allowed.
 *
 * @author Diego Brondo <jamesbrond [at] gmail [dot] com>
 * @version 0.1
 * @package ErrorHandler
 * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
 */
class debugCsv extends debugStrategy {
    
    private $_logfile;
    private $_append;
    private $_delimiter;
    private $_enclosure;

	/**
	 * The possible options are:
     * - debug_level: level of debug if it's not specified it's set by debugObject to DEBUG_DEFAULT_LEVEL
     * - logfile (string): path to the logging file (Default: @see debugCsv::DEFAULT_DEBUG_CSV_PATH).
     * - append (bool): if true new debug information are appended at the end of the file;
     *   if it's false the file is deleted and recreated each time (Default: true).
     * - delimiter (char): sets the field delimiter (one character only). Default: @see DEBUG_DEFAULT_CSV_DELIMITER.  
	 * - enclosure (char): the optional enclosure parameter sets the field enclosure (one character only). Default: @see DEBUG_DEFAULT_CSV_ENCLOSURE.
	 * @param array $options
	 */
	public function __construct($options=null) {
		parent::__construct($options);
        $this->_logfile = !isset($options['logfile']) ? $options['logfile'] : DEBUG_DEFAULT_LOGFILE;
		$this->_append = filter_var($options['append'], FILTER_VALIDATE_BOOLEAN, array('options' => array('default'=>DEBUG_DEFAULT_LOG_APPEND)));
        $this->_delimiter = (!isset($options['delimiter']) || !is_char($options['delimiter'])) ? DEBUG_DEFAULT_CSV_DELIMITER : $options['delimiter'];
        $this->_enclosure = (!isset($options['enclosure']) || !is_char($options['enclosure'])) ? DEBUG_DEFAULT_CSV_ENCLOSURE : $options['enclosure'];
        
		if (!$_append) {
			$fd = @fopen($this->_logfile, 'w');
			fclose($fd);
		}
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
	public function msg($msg, $level=DEBUG_INFO) {
		if (!empty($msg) && ($this->_level & $level)) {
			$t = microtime(true);
			$micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $d = new DateTime(date('Y-m-d H:i:s.'.$micro,$t));
            $fields = array($d->format('Y-m-d H:i:s.u'), $level, filter_var((is_array($msg) ? var_export($msg, true) : $msg), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES|FILTER_FLAG_STRIP_LOW));
            error_log($this->array2csv($fields, $this->_delimiter, $this->_enclosure, false) . PHP_EOL, 3, $this->_logfile);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::exception()
	 */
	public function exception(Exception $e, $trace=DEBUG_DEFAULT_TRACE, $source=DEBUG_DEFAULT_SOURCE, $lines=DEBUG_DEFAULT_SOURCE_LINES) {
		if (error_reporting() & $e->getCode()) {
			$this->msg((string)$e, $e->getCode());
		}
	}

    /**
     * Convert an array in a CSV string
     *
     * @param array $fields An array of values. 
     * @param char $delimiter The optional delimiter parameter sets the field delimiter (one character only). 
     * @param char $enclosure The optional enclosure parameter sets the field enclosure (one character only). 
     * @param bool $encloseAll Enclose all field
     */
    private function array2csv(&$fields, $delimiter, $enclosure, $encloseAll) {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        $output = array();
        foreach ($fields as $field) {
            if ($encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            } else {
                $output[] = $field;
            }
        }
        return join($delimiter, $output);
    }
}
// ~@:-]
?>
