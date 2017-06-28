<?php
define('RCPT_TO', 		1);
define('RCPT_CC', 		2);
define('RCPT_BCC', 		4);
define('MIME_PLAIN',	1);
define('MIME_HTML',		2);

/**
 * @brief: Classe che invia mail.
 *
 * @author $Author$
 * @version $Revision$
 * @since $Date$
 * @filesource $Id$
 * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
 */
class sendmail {

	/**
	 * Mittente della mail. Un array('name' => 'Mario Rossi', 'mail' => 'mailaddress@domin.com', 'replyto' => 'null')
	 * @var array
	 */
	private $_from;

	/**
	 * Array di destinatari. Ongi elemento dell'array e' un array('mail'=> 'mailaddress@domin.com', 'mode'=> RCPT_TO);
	 * @var array
	 */
	private $_to;

	/**
	 * Oggetto della mail
	 * @var string
	 */
	private $_subject;

	/**
	 * Corpo della mail
	 * @var string
	 */
	private $_body;

	/**
	 * 
	 * @var string
	 */
	private $_mime;

	/**
	 * Codifica default UTF-8
	 */
	private $_charset;

	/**
	 * Separatore per gli allegati e i contenuti nelle mail html o multipart
	 * @var string
	 */
	private $_boundary;

	/**
	 * Gli allegati alla mail.
	 * Anche le immagini da visualizzare online sono allegati
	 * @var array
	 */
	private $_attach;

	public function __construct($mime=MIME_PLAIN) {
		$this->_from = array();
		$this->_to = array();
		$this->_subject = '';
		$this->_body = '';
		$this->mime($mime);
		$this->charset('UTF-8');
		$this->_attach = array();
		$this->_boundary = '_x'.md5(uniqid('', true)).'x';
	}

	public function __destruct() {
		unset($this->_from);
		unset($this->_to);
		unset($this->_subject);
		unset($this->_body);
		unset($this->_mime);
		unset($this->_attach);
		unset($this->_boundary);
		unset($this->_charset);
	}

	/**
	 * Imposta la codifica del carattere della mail.
	 *
	 * @param string $charset
	 */
	public function charset($charset) {
		$this->_charset = $charset;
	}

	/**
	 * Imposta il mittente
	 * @param string $name
	 * @param string $mail Indirizzo mail valido.
	 * @param string $replyto Indirizzo mail valido. Se non specificato viene usato lo stesso di $mail.
	 */
	public function from($name, $mail, $replyto=null) {
		try {
			if (false === ($mail = $this->valid_mail($mail)))
				throw new DomainException('From: "'.$mail.'" is not a valid mail address.', E_USER_ERROR);
			if (isset($replyto)) 
				if (false === ($replyto = $this->valid_mail($replyto)))
					throw new DomainException('Replay-To: "'.$replyto.'" is not a valid mail address', E_USER_NOTICE);
		} catch (Exception $e) {
			debug::exception($e);
		}
		$this->_from = array('name' => $name, 'mail' => $mail, 'replyto' => $replyto);
	}

	/**
	 * Aggiunge una voce all'elenco dei destinatari.
	 * @param string $mail Indirizzo mail valido.
	 * @param int $mode Uno dei modi possibili di aggiungere il destinatario: RCPT_TO, RCPT_CC, RCPT_BCC.
	 * @throws exceptions
	 */
	public function to($mail, $mode) {
		try {
			if (false === ($mail = $this->valid_mail($mail)))
				throw new DomainException('To: "'.$mail.'" is not a valid mail address', E_USER_WARNING);
			if (!(($mode==RCPT_TO) | ($mode==RCPT_CC) | ($mode==RCPT_BCC))) 
				throw new DomainException('Field To, Cc o Bcc is not set correctly', E_USER_WARNING);
			$this->_to[] = array('mail' => $mail, 'mode' => $mode);
			return 1;
		} catch (Exception $e) {
			debug::exception($e);
		}
		return 0;
	}

	/**
	 * Oggetto della mail
	 * @param string $str
	 */
	public function subject($str) {
		$this->_subject = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES|FILTER_FLAG_STRIP_LOW);
	}

	/**
	 * Corpo della mail
	 * @param string $str
	 */
	public function body($str) {
		$this->_body = str_replace(array("\r\n","\n","\r"), PHP_EOL, $str);
	}

	/**
	 * Imposta il mime fra plain o html.
	 * @param int $mime
	 */
	public function mime($mime) {
		try {
			if (!(($mime==MIME_HTML) || ($mime==MIME_PLAIN))) 
				throw new DomainException('Mine not valid: set it to plain/text.', E_USER_NOTICE);
			$this->_mime = $mime;
		} catch (Exception $e) {
			debug::exception($e);
			$this->_mime = MIME_PLAIN;
		}
	}

	/**
	 * Crea l'intestazione della mail.
	 * @param array $cc Array contenete l'elenco dei destinatari in Copy Carbon
	 * @param array $bcc Array contenete l'elenco dei destinatari in Black Copy Carbon
	 * @throws exceptions
	 * @return string
	 */
	private function header($cc, $bcc) {
		if (!isset($this->_from['mail']))
			throw new exceptions('From not set.', E_USER_ERROR);
		// from
		$header = 'From: '.$this->_from['name'].' <'.$this->_from['mail'].'>'.PHP_EOL;
		if (isset($this->_from['replyto']))
			$header .= 'Reply-To: '.$this->_from['replyto'].PHP_EOL.
			'Return-Path: '.$this->_from['replyto'].PHP_EOL;
		// cc
		if (!empty($cc)) {
			$header .= 'Cc: '.join(',', $cc).PHP_EOL;
		}
		// bcc
		if (!empty($bcc)) {
			$header .= 'Bcc: '.join(',', $bcc).PHP_EOL;
		}
		// message-id
		$header .= 'Message-ID: <msg'.md5(time()).'@'.(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost').'>'.PHP_EOL;
		// x-mailer
		$header .= 'X-Mailer: JBrond SendMail PHP v'.phpversion().PHP_EOL;
		// date
		$header .= 'Date: '.date('r', time()).PHP_EOL;
		// imposto l'header per Html / plai o multi (se ho allegati)
		$header .= 'MIME-Version: 1.0'.PHP_EOL;
		if (empty($this->_attach)) {
			if ($this->_mime == MIME_HTML) {
				$header .= 'Content-Type: multipart/alternative;'.PHP_EOL.
					"\t".'boundary="==alternative'.$this->_boundary.'";'.PHP_EOL.PHP_EOL;
			} else {
				$header .= 'Content-Type: text/plain; charset="'.$this->_charset.'"'.PHP_EOL.
					'Content-Transfer-Encoding: 7bit'.PHP_EOL.PHP_EOL;
			}
		} else {
			$header .= 'Content-Type: multipart/mixed;'.PHP_EOL.
				"\t".'boundary="==mixed'.$this->_boundary.'";'.PHP_EOL.PHP_EOL;
		}
		return $header;
	}

	/**
	 * Funzione che verifica la validita' di un indirizzo mail.
	 * 
	 * @param string $str Indirizzo mail.
	 * @return string|false Ritorna l'indirizzo mail se valido, false altrimenti.
	 */
	private function valid_mail($str) {
		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Allega un file alla mail. Il file di default viene aggiunto come attachment e non inline.
	 *
	 * @param string $filepath Percorso al file da includere.
	 * @param bool $inline Se a vero l'oggetto viene inserito nel corpo della mail. Ad esempio un immagine può essere usata nel codice HTML.
	 */
	public function attach($filepath, $inline=false) {
		try {
			$basename = basename($filepath);
			if (!is_readable($filepath)) {
				throw new exceptions('Cannot read file '.$basename, E_USER_ERROR);
			}
			$md5_basename = md5($basename);
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($finfo, realpath($filepath));
			finfo_close($finfo);
			$this->_attach[$basename] = array(
				'header' => 'Content-Type: '.$mimetype.';'. PHP_EOL .
					'Content-Transfer-Encoding: base64'. PHP_EOL .
					'Content-ID: <'.$md5_basename.'>'. PHP_EOL .
					(!$inline ? 'Content-Disposition: attachment; filename="'.$basename.'"' : ''),
				'data' => chunk_split(base64_encode(file_get_contents($filepath)))
			);
		} catch (Exception $e) {
			debug::exception($e);
		}
	}

	/**
	 * Invia la mail
	 * @return bool
	 */
	public function send() {
		try {
			if (empty($this->_to))
				throw new exceptions('No receivers specified', E_USER_ERROR);
			if (empty($this->_body)) 
				throw new exceptions('No body specified', E_USER_ERROR);
			
			$to = $cc = $bcc = array();
			for ($i=0,$tot=count($this->_to); $i<$tot; $i++) {
				switch ($this->_to[$i]['mode']) {
					case RCPT_BCC:
						$bcc[] = $this->_to[$i]['mail'];
						break;
					case RCPT_CC:
						$cc[] = $this->_to[$i]['mail'];
						break;
					case RCPT_TO:
					default:
						$to[] = $this->_to[$i]['mail'];
				}
			}
			$header = $this->header($cc, $bcc);

			$body = '';
			if ($this->_mime == MIME_PLAIN) {
				if (empty($this->_attach)) {
					$body = $this->_body;
				} else {
					$body = '--==mixed'.$this->_boundary.PHP_EOL.
						'Content-Type: text/html; charset="'.$this->_charset.'"'.PHP_EOL.
						'Content-Transfer-Encoding: quoted-printable'.PHP_EOL.
						$this->_body. PHP_EOL . PHP_EOL;
				}
			} else {
				if (!empty($this->_attach)) {
					$body .= '--==mixed'.$this->_boundary.PHP_EOL.
						'Content-Type: multipart/alternative;'.PHP_EOL.
						"\t".'boundary="==alternative'.$this->_boundary.'"'.PHP_EOL.PHP_EOL;
				}
				$body .= '--==alternative'.$this->_boundary . PHP_EOL.
					'Content-Type: text/plain; charset="'.$this->_charset.'"' . PHP_EOL.
					'Content-Transfer-Encoding: 7bit' . PHP_EOL.PHP_EOL.
					strip_tags($this->_body) . PHP_EOL . PHP_EOL;

				// inizia la seconda parte del messaggio in formato html
				$body .= '--==alternative'.$this->_boundary . PHP_EOL.
					'Content-Type: text/html; charset="'.$this->_charset.'"' . PHP_EOL.
					'Content-Transfer-Encoding: 7bit' . PHP_EOL.PHP_EOL;

				if (!empty($this->_attach)) {
					foreach (array_keys($this->_attach) as $id) {
						$this->_body = preg_replace('/'.$id.'/ie', "'cid:'.md5('$0')", $this->_body);
					}
				}
				$body .= $this->_body . PHP_EOL .
				'--==alternative'.$this->_boundary .'--'. PHP_EOL;
			}
			// gli allegati
			foreach ($this->_attach as $id => $attach) {
				$body .= '--==mixed'.$this->_boundary . PHP_EOL.
				$attach['header'] . PHP_EOL.
				$attach['data'] . PHP_EOL . PHP_EOL;
			}
			if (!empty($this->_attach)) {
				$body .= '--==mixed'.$this->_boundary.'--'.PHP_EOL;
			}

			if (mail(join(',',$to), $this->_subject, $body, $header)) {
				return 1;
			} else {
				$error = error_get_last();
				throw new exceptions($error['message'], $error['type']);
			}
		} catch (Exception $e) {
			debug::exception($e);
		}
		return 0;
	}
}
// ~@:-]
?>