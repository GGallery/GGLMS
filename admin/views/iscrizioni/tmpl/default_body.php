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
    <tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php echo $item->id; ?>
        </td>
        <td>
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>

        <td>
            <?php echo $item->username; ?>
        </td>

        <?php


        foreach ($this->gruppi as $key => $val) {
            $p = $item->id . '-' . $key;


            if(!in_array( $p, $this->iscrizioni)){
                echo "<td><a class='btn button' href='index.php?option=com_gglms&task=iscrizioni.addUserToGroup&user_id=$item->id&group_id=$key&course_name=$val&email=$item->email'>Iscrivi</a></td>";
            }else
                echo "<td class='text-center'> V </span> </td>";
        }
        ?>
    </tr>
<?php endforeach; ?>
