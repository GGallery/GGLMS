<?php
/**
 * @brief: Classe per la cifratura
 *
 * @package Security
 * @subpackage Cypher
 * @author Diego Brondo <jamesbrond [at] gmail [dot] com>
 * @version 0.1
 * @since PHP 5.3
 * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
 */
class crypto {
	/**
	 * Chiave per la criptazione
	 * 
	 * @var string
	 */
	private $_key;
	
	/**
	 * L'encriptatore
	 * @var resource 
	 */
	private $_cipher;

	/**
	 * Costruttore.
	 * Instanzia la classe impostando iv
	 * I parametri possono essere espressi attraverso le {@link http://it.php.net/manual/en/mcrypt.ciphers.php costanti definite in MyCrypt}
	 * 
	 * @param string $algorithm L'algoritmo da usare. Default: MCRYPT_TRIPLEDES.
	 * @param string $mode Modalita' di uso. Default MCRYPT_MODE_CBC.
	 */
	public function __construct($key, $algorithm=MCRYPT_TRIPLEDES, $mode=MCRYPT_MODE_ECB) {
		try {
			if (!extension_loaded('mcrypt'))
				throw new exceptions('mcrypt extension is not loaded. Enable it in your php.ini file.', E_ERROR);
			
			if (false === ($this->_cipher = mcrypt_module_open($algorithm, '', $mode, ''))) 
				throw new UnexpectedValueException('Could not use MyCrypt', E_USER_WARNING);
			
			$this->_key = substr($key, 0, mcrypt_enc_get_key_size($this->_cipher));
		} catch (Exception $e) {
			debug::exception($e);
			$this->_cipher = null;
		}
	}

	/**
	 * Distruttore.
	 * Chiude il modulo mcrypt.
	 */
	public function __destruct() {
		if (isset($this->_cipher))
			mcrypt_module_close($this->_cipher);
		unset($this->_key);
		unset($this->_cipher);
	}

	/**
	 * Ritorna la string codificata.
	 * Se il costruttore ha fallito nell'inizializzazione dell'algoritmo di cifratura, la stringa viene
	 * restituita in chiaro; altrimenti la codifica consiste nella cifratura con l'algoritmo scelto.
	 * 
	 * @param string $str Se non viene passata nessuna stringa ritorna una stringa vuota.
	 * @return string
	 */
	public function encrypt($str) {
		try {		
			$str = filter_var($str, FILTER_SANITIZE_STRING);
			if (empty($str))
				throw new InvalidArgumentException('Empty or not valid string as agrument', E_USER_NOTICE);
			if (!isset($this->_cipher)) 
				throw new UnexpectedValueException('Cypher error', E_USER_WARNING);
			if (false === ($iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->_cipher), MCRYPT_RAND)))
				throw new UnexpectedValueException('Cannot initialize vector IV', E_USER_WARNING);
				
			$s = mcrypt_generic_init($this->_cipher, $this->_key, $iv);
			if ((0 > $s) || (false === $s))
				throw new exceptions('Encrypt failure', E_USER_WARNING);
			$enc = mcrypt_generic($this->_cipher, $str);
			mcrypt_generic_deinit($this->_cipher);
			return $enc;
		} catch (Exception $e) {
			debug::exception($e);
			return $str;
		} 
	}

	/**
	 * Ritorna la stringa decodificata.
	 * La decodifica consiste nella decifratura della stringa generata da @see crypto::encrypt().
	 * In caso di errore ritorna una stringa vuota.
	 * 
	 * @param string $str Se non viene passata nessuna stringa ritorna una stringa vuota.
	 * @return strig
	 */
	public function decrypt($str) {
		try {
			if (empty($str))
				throw new InvalidArgumentException('Empty or not valid string as agrument', E_USER_NOTICE);
			if (!isset($this->_cipher))
				throw new UnexpectedValueException('Cypher error', E_USER_WARNING);
			if (false === ($iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->_cipher), MCRYPT_RAND)))
				throw new UnexpectedValueException('Cannot initialize vector IV', E_USER_WARNING);
			$s = mcrypt_generic_init($this->_cipher, $this->_key, $iv);
			if ((0 > $s) || (false === $s))
				throw new exceptions('Decrypt failure', E_USER_WARNING);
			$dec = rtrim(mdecrypt_generic($this->_cipher, $str), "\0");
			mcrypt_generic_deinit($this->_cipher);
			return $dec;
		} catch (Exception $e) {
			debug::exception($e);
			return '';
		}
	}

	/**
	 * Ritorna vero se la stringa contiene solo caratteri da tastiera e quindi in qualche modo e' in chiaro.
	 * 
	 * @param string $str
	 * @return bool
	 */
	public function is_clear($str) {
		return preg_match('/^[\x20-\x7f\xe0\xe8\xe8\xe9\xec\xf2\xf9]+$/', utf8_decode($str));
	}
	
	/**
	 * Ritorna la stringa codificata e convertita in base 64
	 * @param string $str
	 * @return string
	 */
	public function encrypt_64($str) {
		return base64_encode($this->encrypt($str));
	}
	
	/**
	 * Ritorna la string codificata in base 64 decifrata
	 * @param string $str
	 * @return Ambigous <strig, string>
	 */
	public function decrypt_64($str) {
		return $this->decrypt(base64_decode($str));
	}
}
// ~@:-]
?>
