<?php
/**
 * @brief: Mail Debug Strategy sends all debug logs to one or more mail address.
 *
 * @author Diego Brondo <jamesbrond [at] gmail [dot] com>
 * @version 0.0
 * @package ErrorHandler
 * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
 */
class debugMail extends debugStrategy {

    private $_to;

    private $_from;

	private $_logs;
	
	private $_extra_headers;

	/**
	 * The options are:
	 * - debug_level: level of debug if it's not specified it's set by debugObject to DEBUG_DEFAULT_LEVEL
	 * - to: an array of mail addresses which are the recipients of the mail. It must be set
	 * - from: mail address that sends the mail. If it's not set 'noreplay@$_SERVER['SERVER_NAME']' value will be used
	 *
	 * @param array $options
	 * @throws BadMethodCallException
	 * @throws DomainException
	 */
    public function __construct($options=null) {
		if (!isset($options['to'])) {
			throw new BadMethodCallException('The recipients must be set in order to send the mail', E_USER_ERROR);
		}
		if (!is_array($options['to'])) {
            $options['to'] = array($options['to']);
            trigger_error('Recipients must be a valid array', E_USER_NOTICE);
		}
		
		for ($i=0,$tot=count($options['to']); $i<$tot; $i++) {
			$to = $this->_validate_mail($options['to'][$i]);
            if (is_null($to)) 
                unset($options['to'][$i]);
            else 
                $options['to'][$i] = $to;
        }
        $this->_to = $options['to'];

		if (empty($this->_to)) {
			trigger_error('No recipients is a valid mail address', E_USER_WARNING);
		}
		
		$this->_from = $this->_validate_mail($options['from']);
		if (is_null($this->_from)) {
			$this->_from = DEBUG_DEFAULT_MAIL_FROM;
		} 
		
		parent::__construct($options);
		$this->_logs = array();
		
		$this->_extra_headers = 'MIME-Version: 1.0' . PHP_EOL .
			'Message-ID: <msg'.md5(time()) . '@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL .
			'X-Mailer: DebugMail PHP v' . phpversion() . PHP_EOL .
			'Date: ' . date('r', time()) . PHP_EOL.
			'From: ' . $this->_from . PHP_EOL .
			'Content-Type: text/plain; charset=UTF-8' . PHP_EOL;
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
			if (DEBUG_INFO & $level) {
				$pre = 'INFO: ';
			} elseif (DEBUG_ERROR & $level || DEBUG_STRICT & $level) {
				$pre = 'ERROR: ';
			} elseif (DEBUG_WARNING & $level) {
				$pre = 'WARNING: ';
			} elseif (DEBUG_NOTICE & $level) {
					$pre = 'NOTICE: ';
			} else {
				$pre = '';
			}
			$t = microtime(true);
			$micro = round(($t - floor($t)) * 1000000);
			$d = new DateTime(date('Y-m-d H:i:s.'.$micro,$t));
			
			mail(join(',', $this->_to), '[debug] Error message from ' . $_SERVER['SERVER_NAME'], 'In date: ' . $d->format('Y-m-d H:i:s.u') . ' the following error was detected: ' . PHP_EOL.(is_array($msg) ? var_export($msg, true) : $msg), $this->_extra_headers);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::exception()
	 */
	public function exception(Exception $e, $trace=DEBUG_DEFAULT_TRACE, $source=DEBUG_DEFAULT_SOURCE, $lines=DEBUG_DEFAULT_SOURCE_LINES) {
		if (error_reporting() & $e->getCode()) {
			$msg = PHP_EOL.'[ERROR]'.PHP_EOL.(string)$e;
			if ($trace) {
				$msg .= PHP_EOL.PHP_EOL.'[TRACE]'.PHP_EOL.$e->getTraceAsString();
			}
			if ($source && method_exists($e,'get_source_str')) {
				$msg .= PHP_EOL.PHP_EOL.'[SOURCE]'.PHP_EOL.$e->get_source_str($lines);
			}
			$this->msg($msg, $e->getCode());
			unset ($msg);
		}
	}

	/**
	 * Ritorna un indirizzo mail valido o null.
	 * Utilizza  FILTER_SANITIZE_EMAIL per rimuovere caratteri illegali
	 * e FILTER_VALIDATE_EMAIL per validare l'indirizzo.
	 * 
	 * @param unknown_type $addr
	 */
	private function _validate_mail($addr) {
		return filter_var($addr, FILTER_SANITIZE_EMAIL);
	}
}
 // ~@:-]
?>
