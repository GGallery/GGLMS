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
<?php foreach ($this->items as $i => $item): ?>
    <tr>
        <td>
           <?php echo $item->titolo; ?>
        </td>
        <td>
            <?php echo $item->stato; ?>
        </td>

        <td>
            <?php echo $item->data_inizio; ?>

        </td>
        <td>
            <?php echo $item->data_fine; ?>
        </td>

    </tr>
<?php endforeach; ?>
