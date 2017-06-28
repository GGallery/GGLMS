<?php
/**
  * @brief Debug messages are dumped to video.
  * 
  * @author diego.brondo
  * @version $Version$
  * @since 20/ott/2011
  * @package ErrorHandler
  * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
  */
class debugDump extends debugStrategy {
	
	/**
	 * The possible options are:
     * - debug_level: level of debug if it's not specified it's set by debugObject to DEBUG_DEFAULT_LEVEL
	 *
	 * @param array $options
	 */
	public function __construct($options=null) {
        parent::__construct($options);
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::__destruct()
	 */
	public function __destruct() {
		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see debugObject::msg()
	 */
	public function msg($msg, $level=DEBUG_INFO) {
        if (!empty($msg) && ($this->_level & $level)) {
            var_dump($msg);
		}
	}
}
// ~@:-]
?>
