<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/*
?>
<tr>
	<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>

// override default page limit - prende come riferimento quello impostanto nella configurazione globale del sito Limite liste predefinito

<?php */
?>
<tr>
    <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
        <?php echo $this->pagination->getListFooter(); ?>
    </td>
</tr>
