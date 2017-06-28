<?php
/**
 * Set di funzioni per ottenere informazioni sul server e su PHP
 */

/**
 * Outputs a large amount of information about the current state of PHP. 
 * This includes information about PHP compilation options and extensions, 
 * the PHP version, server information and environment (if compiled as a module), 
 * the PHP environment, OS version information, paths, master and local 
 * values of configuration options, HTTP headers, and the PHP License.
 * 
 * Parts of the information displayed are disabled when the {@link http://it.php.net/manual/en/ini.core.php#ini.expose-php expose_php} 
 * configuration setting is set to off.
 *  
 * @param int $what The output may be customized by passing one or more of the constants 
 * you can read about at {@link http://it.php.net/manual/en/function.phpinfo.php phpinfo()}. 
 * Bitwise values summed together in the optional what parameter. One can also combine 
 * the respective constants or bitwise values together with the or operator. (Default is INFO_ALL).
 * @return array 
 */
function phpinfo_array($what=INFO_ALL) {
	ob_start();
	phpinfo($what);
	$info_arr = array();
	$info_lines = explode("\n", strip_tags(ob_get_clean(), '<tr><td><h2>'));
	$cat = 'General';
	foreach($info_lines as $line) {
		preg_match('~<h2>(.*)</h2>~', $line, $title) ? $cat = $title[1] : null;
		if(preg_match('~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~', $line, $val)) {
			$info_arr[$cat][trim($val[1])] = $val[2];
		} elseif(preg_match('~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~', $line, $val)) {
			$info_arr[$cat][$val[1]] = array('local' => $val[2], 'master' => $val[3]);
		}
	}
	return $info_arr;
}
// ~@:-]
?>