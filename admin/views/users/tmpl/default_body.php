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

            <td>
                <?php echo $item->name; ?>
            </td>

            <td>
                <?php echo $item->email; ?>
            </td>

            <td>
                <?php
                echo gglmsHelper::getUserGroupName($item->id, true);
                ?>
            </td>

            <td>
                <?php
                $data = "id=".$item->id."&username=".$item->username."&password=".$item->password."&email=".$item->email;
                $urllogin = JUri::root().JRoute::_('/index.php?option=com_gglms&task=users.login&'.$data);
                $urlreset = JUri::root().JRoute::_('/index.php?option=com_gglms&task=users.reset&'.$data);
                $urlresetsend = JUri::root().JRoute::_('/index.php?option=com_gglms&task=users.resetsend&'.$data);
                ?>
                <a class="btn btn-small btn-success" href="<?php  echo $urllogin; ?>" target="_blank">LOGIN</a>
            </td>

            <td>
                <a class="btn btn-small btn-danger" href="<?php  echo $urlreset; ?>" target="_blank">RESET </a>
                <a class="btn btn-small btn-danger" href="<?php  echo $urlresetsend; ?>" target="_blank">RESET & SEND</a>

            </td>

        </tr>
    <?php endforeach; ?>
