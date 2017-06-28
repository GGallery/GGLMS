<?php
require('errors.conf.php');
require_once('debugStrategy.class.php');

/**
 * This set the directives that inform PHP of which errors, warnings and notices you would like it to take action for.
 */
error_reporting(ERROR_REPORTING);

/**
  * @brief debugStrategy:: definisce i metodi per debuggare uno script PHP.
  * Uno script puo' usare una (o piu') strategia di debug definite come @see debugStrategy::
  * Le possibili scelte possono essere:
  * - FireBug Console utilizzando FirePHP (@see debugFirebug::)
  * - logging su file (@see debugLog::)
  * - messaggi via mail (@see debugMail::)
  * - logging su database usa MDB2 (@see debugSql::)
  *
  * Le strategie possono essere usate in parallelo: ciascuna strategia viene eseguita contemporaneamente; o innestate: solo le
  * strategie figlie vengono eseguite finche' non vengono fermate (e riprendo le strategie precedentemente messe in pausa).
  *
  * Esempio di uso:
  * <code>
  * debug::init();
  * debug::start('firebug', array('debug_level'=>DEBUG_DEV);
  * debug::start('log', array('debug_level'=>E_ALL);
  * try {
  *    debug::msg('test eccezione', DEBUG_INFO);
  *	   throw new exceptions('Eccezione', E_USER_ERROR);
  * } catch (Exception $e) {
  *     debug::exception($e);
  * }
  * debug::chuck_norris();
  * </code>
  * Nell'esempio vengono usati due metodi di debug in parallelo: console FireBug e file di log. Il primo cattura sia il messaggio che l'eccezione
  * visualizzandole nella console di FireBug (@see debugFirebug::); il secondo cattura solo l'eccezione perche' il messaggio ha
  * un livello che non viene catturato. Per i possibili livelli vedere @see debugObject::
  *
  * @author diego.brondo
  * @version $Version$
  * @package ErrorHandler
  * @since 20/ott/2011
  * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
  */
class debug {
	
	/**
     * Instanza della classe debug per il pattern singleton.
     * Assicura l'esistenza di una sola istanza di debug durante l'esecuzione 
     * dello script anche se inizializzato in più punti.
	 * @var debug
	 */
	private static $_instance;

	/**
  	 * Array di instanze di @see debugObject::
	 * L'array e' composto da istanze di strategie di debug indicizzate con la chiave <strategia>_<nome>
	 * @var array di @see debugStrategy::
	 */
	private static $_strategies;

	/**
	 * Stack contenente le stratgie usate.
	 * @var array
	 */
	private static $_strategies_stack;

	/**
	 * Indice che tiene traccia della testa dello stack
	 * @var int
	 */
	private static $_stack_head;

	/**
	 * Costruttore dichiarato privato per implementare il pattern singleton.
	 * Imposta le funzioni @see PokemonExceptionHandler() e @see PokemonErrorHandler()
	 * rispettivamente come gestore delle eccezioi e degli errori (vedi {@link http://it.php.net/manual/en/function.set-exception-handler.php set_exception_handler()} 
	 * e {@link http://it.php.net/manual/en/function.set-error-handler.php set_error_handler()}). 
	 */
	private function __construct() {
		self::$_strategies = array();
		self::$_strategies_stack = array();
		self::$_stack_head = -1;
		set_exception_handler('PokemonExceptionHandler');
		set_error_handler('PokemonErrorHandler', ERROR_REPORTING);
        if (!ini_get('date.timezone'))
            ini_set('date.timezone', DEBUG_DEFAULT_TIMEZONE);
	}

	/**
     * Distruttore dichiarato privato per implementare il pattern singleton.
     * Ripristina le funzioni di gestione delle eccezioni e degli errori .
	 */
	private function __destruct() {
		restore_exception_handler();
		restore_error_handler();
		foreach (array_keys(self::$_strategies) as $strategy) {
			unset(self::$_strategies[$strategy]);
		}
		self::$_strategies = null;
		self::$_strategies_stack = null;
		self::$_stack_head = -1;
	}

	/**
     * Inizializza la classe se non esiste altrimenti ritorna l'istanza di se stessa.
     * Implementa il pattern singleton.
	 */
	public static function init() {
		if (!isset(self::$_instance)) {
			self::$_instance = new debug();
		}
		return self::$_instance;
	}

	/**
	 * Si occupa di distruggere la classe singleton
	 */
	public static function chuck_norris() {
		self::$_instance = null;
	}

	/**
	 * Aggiunge una nuova strategia come innestata in quella precedente.
	 * Innestare una strategia significa che la vecchia sara' sospesa e sara' utilizzata la nuova
	 * fino a che non verra' terminata con @see end().
	 *
	 * @param string $strategy Nome della strategia (es. 'firebug', 'log', 'SQL', 'CSV', 'mail').
	 * @param array $options Array di opzioni passato al costruttore della strategia di debug.
	 * @param string $name Nome delle strategia, se non specificato verra' usato il valore di $strategy. Se usato il nome della strategia sara': $strategy_$name.
	 * @return bool
	 */
	public static function startNested($strategy, $options=null, $name=null) {
		$strategy = ucfirst(strtolower($strategy));
		$strategy_name = $strategy . '_' . $name;
		if (!isset(self::$_strategies[$strategy_name])) { // se non esiste creo la nuova strategia
			$classname = 'debug' . $strategy; 
			if (!class_exists($classname)) { // non esiste la classe? provo a includere il file
				if (!include_once('debugStrategy/' . $classname . '.class.php')) {
					return 0;
				}
			}
			self::$_strategies[$strategy_name] = new $classname($options);
		}
		self::$_strategies_stack[++self::$_stack_head] = array($strategy_name);
		return 1;
	}

	/**
	 * Aggiunge una nuova strategia.
	 * Se ne esiste gia' una questa viene aggiunta in parallelo.
	 * Si possono avere piu' strategie in parallelo ognuna con opzioni differenti.
	 *
	 * @param string $strategy Nome della strategia (es. 'firebug', 'log', 'SQL', 'CSV', 'mail').
	 * @param array $options Array di opzioni passato al costruttore dellastrategia di debug.
	 * @param string $name Nome delle strategia, se non specificato verra' usato il valore di $strategy. Se usato il nome della strategia sara': $strategy_$name.
	 * @return bool
	 */
	public static function start($strategy, $options=null, $name=null) {
		$strategy = ucfirst(strtolower($strategy));
		$strategy_name = $strategy . '_' . $name;
		if (!isset(self::$_strategies[$strategy_name])) { // se non esiste creo la nuova strategia
			$classname = 'debug' . $strategy;
			if (!class_exists($classname)) { // non esiste la classe? provo a includere il file
				if (!include_once('debugStrategy/' . $classname . '.class.php')) {
					return 0;
				}
			}
			self::$_strategies[$strategy_name] = new $classname($options);
		}
		if (-1 === self::$_stack_head) { // se non ci sono altre strategie attive
			self::$_strategies_stack[++self::$_stack_head] = array($strategy_name);
		} else {
			self::$_strategies_stack[self::$_stack_head][] = $strategy_name;
		}
		return 1;
	}

	/**
	 * Termina la stategie di debug $strategy.
	 * Se la strategia aveva un nome questo deve essere specificato in $name.
	 *
	 * @param string $strategy Nome della strategia (es. 'firebug' o 'log', ecc.).
	 * @param string $name Nome delle strategia, se non specificato verrà usato il valore di $strategy. Se usato il nome della strategia sarà: $strategy_$name.
	 * @return bool
	 */
	public static function end($strategy, $name=null) {
		$return = 0;
		$strategy_name = ucfirst(strtolower($strategy)) . '_' . $name;
		if (self::$_stack_head) {
			for ($i=0,$tot=count(self::$_strategies_stack[self::$_stack_head]); $i<$tot; $i++) {
				if (self::$_strategies_stack[self::$_stack_head][$i] == $stategy_name) {
					unset(self::$_strategies_stack[self::$_stack_head][$i]);
                    $return = 1;
					break;
				}
			}
			if (empty(self::$_strategies_stack[self::$_stack_head])) { // se vuoto
				unset(self::$_strategies_stack[self::$_stack_head--]); // razo via dalla pila dello stack e diminuisco inidice
			}
		}
		return $return;
	}

	/**
	 * Ritorna il numero di strategie correntemente attive.
	 *
	 * @return int
	 */
	public static function active_num() {
		return (-1 === self::$_stack_head) ? 0 : count(self::$_strategies_stack[self::$_stack_head]);
	}


	/**
	 * Ritorna un array contenente il nome delle strategie attive.
	 *
	 * @return array
	 */
	public static function active() {
		return (-1 === self::$_stack_head) ? array() : self::$_strategies_stack[self::$_stack_head];
	}

	/**
	 * Write a debug message
	 *
	 * @param mixed $msg The message sent to the debug; if it's an array the output of @see var_export() will be used.
	 * @param int $level Level of debug message (default DEBUG_LOG).
	 */
	public static function msg($msg, $level=DEBUG_LOG) {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->msg($msg, $level);
		}
	}

	/**
	 * It writes the type (@see gettype()) and the value (@see var_export()) of the variable passed as first argument.
	 * The second argument of the method is a label printed in the debug with the value.
	 * Default debug level is DEBUG_LOG.
	 *
	 * @param mixed $var The variable.
	 * @param string $name A label displayed in debug.
	 */
	public static function vardump($var, $name=null) {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->vardump($var, $name);
		}
	}

    /**
     * Debug di un oggetto PHP
     *
     * @param $obj Oggetto PHP
     */
    public static function objdump($obj) {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->objdump($obj);
		}
    }

	/**
	 * Inserisce un checkpoint nella debug console. Un checkpoint e' una stringa che riporta file e riga in cui questa funzione viene chiamata.
	 * E' possibile inoltre specificare un messaggio aggiuntivo da inserire nel checkpoint.
	 *
	 * @param string $msg (default '').
	 * @param int $level Il livello di debug e' per default DEBUG_INFO
	 */
	public static function checkpoint($msg='', $level=DEBUG_LOG) {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->checkpoint($msg, $level);
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
	 public static function chrono_start($name='') {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->chrono_start($name);
		}
	}

	/**
	 * Ferma l'ultimo cronometro inserito e scrive nella debug console un messaggio con
	 * quanto tempo e' trascorso.
	 */	
	public static function chrono_stop() {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->chrono_stop();
		}
	}
	
	/**
	 * Aggiunge all'output di debug il totale delle memoria utilizzata in byte.
	 * Utilizza la funzione PHP memory_get_usage().
	 * Il livello di debug e' per default DEBUG_INFO.
	 *
	 * @param string $msg Eventuale messaggio.
	 */
	public static function memory($msg='') {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->memory($msg);
 		}
	}
	
	/**
	 * Ritorna la vera dimensione di memeoria calcolando i picchi di memoria allocati dallo script PHP.
	 * @param string $msg
	 */
	public static function memorypeak($msg='') {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->memorypeak($msg);
		}
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
	public static function exception(Exception $e, $trace=null, $source=null, $lines=null) {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->exception($e, $trace, $source, $lines);
		}
	}
	
	/**
	 * Inserisce un punto di interruzione nell'esecuzione dello script. 
	 * Ogni strategia di debug a livello di DEBUG_LOG segnala l'interruzione.
	 * Eventuali buffer di output aperti vengono chiusi e svuotati; lo script terminato.
	 */
	public static function breakpoint() {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->breakpoint();
		}
		while(@ob_end_clean());
		@flush();
		exit('DEBUG BREAK POINT');
	}
	
	/**
	 * Scive a log tutte le intestazioni inviate o pronte ad essere inviate al browser.
     * Il livello di debug è+ DEBUG_DUMP
	 * Utilizza la funzione headers_list().
	 */
	public static function headers() {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->headers();
		}
	}
	
	/**
	 * Scrive alcune informazioni sul client:
	 * - UserAgent
	 * - indirizzo IP
	 * Il livello di debug e' DEBUG_DUMP. 
	 */
	 public static function client_info() {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->client_info();
		}
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
	public static function server_info() {
		foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->server_info();
		}
	}
    
    /**
     * Dump dei parametri della classe di debug.
     */
    public static function info() {
        foreach (self::active() as $strategy) {
			self::$_strategies[$strategy]->vardump(self::$_strategies[$strategy], 'DEBUG INFO');
		}
    }

   /**
	* Non e' possibile clonare un oggetto singleton
	*
	* @throws BadFunctionCallException
	*/
	public function __clone() {
		throw new BadFunctionCallException('To clone a singleton object is not allowed', E_USER_WARNING);
	}
	
	/**
	 * Non e' possibile serializzare un oggetto singleton
	 *
	 * @throws BadFunctionCallException
	 */
	public function __wakeup() {
		throw new BadFunctionCallException('To serialize a singleton object is not allowed', E_USER_WARNING);
    }
}

/**
 * Funzione di default per gestire le eccezioni non catturate da un blocco try-catch. Gotta Catch 'Em All.
 * @param Exception $e Una qualsiasi eccezione che non viene catturata da un blocco try-catch; la classe di appartenenza deve essere
 * della famiglia @see Exception:: di PHP.
 */
function PokemonExceptionHandler(Exception $e) {
	debug::exception($e);
}

/**
 * Funzione di default per la gestione degli errori. Gotta Catch 'Em All.
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param string $errline
 * @param array $errcontext
 */
function PokemonErrorHandler($errno, $errstr, $errfile=null, $errline=null, $errcontext=null) {
	debug::exception(new exceptions($errstr, $errno, null, $errline, $errfile));
}
// ~@:-]
?>
