<?php
/**
 * Ritorna l'indirizzo IP del client.
 * Verifica una serie di variabili d'ambiente e tenta di estrarne l'indirizzo IP.
 * Usa filter_var per validare l'indirizzo IP prima di restituirlo. In caso di insuccesso ritorna FALSE.
 * 
 * @return string|FALSE 
 */
function get_client_ip() {
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
		if (array_key_exists($key, $_SERVER)) 
			foreach (explode(',', $_SERVER[$key]) as $ip)
				if (false !== filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
	return false;
}

/**
 * Ritorna la stringa dello User Agent che si collega.
 * Solitamente e' l'intestazione che il browser invia al server. 
 * Se non riesce a recuperare l'informazione ritorna FALSE.
 * 
 * @return string\FALSE
 */
function get_user_agent_string() {
	return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false;
}
// ~@:-]
?>