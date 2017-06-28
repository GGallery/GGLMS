<?php
require_once('MDB2.php');

/**
 * @brief: DataBase Debug Strategy uses PEAR::MDB2 to store debug log into a database.
 * The debug table:
 * CREATE TABLE debug_table (
 * 	level INT(3) UNSIGNED NOT NULL,
 * 	message TEXT NOT NULL,
 * 	time CHAR(26) NOT NULL
 * );
 * Time is stored in the format year-month-day hours:minutes:seconds.microseconds  
 *
 * @author Diego Brondo <jamesbrond [at] gmail [dot] com>
 * @version 0.1
 * @package ErrorHandler
 * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
 */
class debugDb extends debugStrategy {

   /**
    * MDB2 Object for database access
    *
    * @var MDB2
    */
    private $_mdb2;

    private $_dsn;
    private $_table;
    private $_mdb2_options;

   /**
    * The options are:
    * - debug_level: level of debug if it's not specified it's set by debugObject to DEBUG_DEFAULT_LEVEL
    * - dsn: the Data Source Name to connect to a database through PEAR::MDB2 {@link http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php}.
    *   The DSN must be provided as an associative array or as a string.
    *   The array format is preferred, since it doesn't require a further parsing step (see the {@link http://pear.php.net/manual/en/package.database.mdb2.intro-connect.php Connecting
    *   chapter} for an example). The string format of the supplied DSN is in its fullest form:
    *   phptype(dbsyntax)://username:password@protocol+hostspec/database?option=value 
    *   Examples:
    *   $dsn =  'mysqli://themaster:thepowerofthepower@localhost/masterdb'
    *   $dsn = array(
    *		'phptype'  => 'mysqli',
    *		'username' => 'themaster',
    *		'password' => 'thepowerofthepower',
    *		'hostspec' => 'localhost',
    *		'database' => 'masterdb'
    *	); 
    * - mdb2_options: can contain runtime configuration settings for the MDB2 package (see the {@link http://pear.php.net/manual/en/package.database.mdb2.intro-connect.php Connecting} for more details).
    * - table: the name of the table where debug logs are stored. 
    *
    * @param array $options
    * @throws BadMethodCallException
    * @throws exceptions
    */
    public function __construct($options=null) {
        $this->_dsn = isset($options['dsn']) ? $options['dsn'] : DEBUG_DEFAULT_MYSQL_DSN;
        $this->_table = isset($options['table']) ? $options['table'] : DEBUG_DEFAULT_MYSQL_TABLE;
        $this->_mdb2_options = isset($options['mdb2_options']) ? $options['mdb2_options'] : array();
        parent::__construct($options);

        $this->_mdb2 =& MDB2::factory($this->_dsn, $this->_mdb2_options);
        if (PEAR::isError($this->_mdb2)) {
            throw new exceptions($this->_mdb2->getMessage(), $this->_mdb2->getCode());
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
      $this->_mdb2->disconnect();
      unset($this->_mdb2);
      parent::__destruct();
   }

   /**
    * (non-PHPdoc)
    * @see debugObject::msg()
    * @throws exceptions
    */
   public function msg($msg, $level=DEBUG_INFO) {
      $msg = filter_var(trim($msg), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
      if (!empty($msg) && ($_level & $level)) {
         $t = microtime(true);
         $micro = round(($t - floor($t)) * 1000000);
         $d = new DateTime(date('Y-m-d H:i:s.'.$micro,$t));
         $results = $this->_mdb2->exec('INSERT INTO ' . $this->_table . '
            (level, message, time)
            VALUES ('. $this->_mdb2->quote($level, 'integer') . ', ' // error code
                     . $this->_mdb2->quote($msg, 'text') . ', ' // error message
                     . $this->_mdb2->quote($d->format('Y-m-d H:i:s.u'), 'text') . ')'); // error date with microtime ex. 2010-02-14 14:52:05.611046
         if (PEAR::isError($results)) {
            throw new exceptions($results->getMessage(), $results->getCode());
         }
      }
   }
}
 // ~@:-]
?>
