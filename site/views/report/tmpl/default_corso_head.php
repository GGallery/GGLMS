<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

?>
<!--<tr>-->
<!--	--><?php
//	if(!$this->state->get('id_corso'))
//		echo "<th> Corso </th>";
//	
//	if(!$this->state->get('id_unita') && $this->state->get('id_corso') )
//		echo "<th> Unita </th>";
//
//	if($this->state->get('id_unita') || $this->state->get('id_corso'))
//	echo "<th> Contenuto </th>";
//	?>
<!---->
<!--	<th>-->
<!--		Utente-->
<!--	</th>-->
<!---->
<!--	<th>-->
<!--		Stato-->
<!--	</th>-->
<!---->
<!--	<th>-->
<!--		Data-->
<!--	</th>-->
<!---->
<!---->
<!--</tr>-->
<tr>
	<th>UNITA</th>

	<?php

	foreach ($this->header as $unit){
		echo "<td colspan='".count($unit->contenuti)."'>$unit->titolo</td>";
	}


	?>
</tr>
<tr>
	<th width="200px">Utente</th>

	<?php

	foreach ($this->header as $unit){
		if(!count($unit->contenuti))
			echo "<th></th>";
		else {
			foreach ($unit->contenuti as $contenuto) {
				echo "<td width='50px'>$contenuto->titolo</td>";
			}
		}

	}


	?>
</tr>