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
<tr>
    <th width="5">
        #
    </th>
    <th width="1%" class="center">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th>
        Username
    </th>

    <?php
    foreach ($this->gruppi as $key => $val) {
        echo "<th>$val</th>";
    }
    ?>

</tr>
