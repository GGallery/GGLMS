<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;


foreach ($this->userReport as $user){

	echo "<tr>";
	echo "<td>".$user->info->nome." ".$user->info->cognome."(".$user->id_utente.")</td>";

	foreach ($this->header as $unit){
		foreach ($unit->contenuti as $contenuto) {
			echo "<td class='valueTableReport'>";
			if(array_key_exists($contenuto->id, $user->report ))
				if($user->report[$contenuto->id]['stato'])
					echo "<span class=\"icon-publish large-icon\"> </span>";

			echo "</td>";
		}
	}
	echo "</tr>";
}

?>
