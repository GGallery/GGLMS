<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;
$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);

?>

<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php //echo JHtml::_('searchtools.sort', '', 'm.ordinamento', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2','adminForm'); ?>
          <?php echo JHtml::_('grid.sort', '', 'm.ordinamento', $listDirn, $listOrder); ?>
    </th>
	<th width="5">
      <?php echo JText::_('COM_GGLMS_ID'); ?>

	</th>
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkall'); ?>

	</th>
	<th>
		<?php echo JText::_('COM_GGLMS_TITOLO'); ?>
	</th>
	<th>
		Pubblicato
	</th>

	<th>
		<?php // echo JText::_('COM_GGLMS_DESCRIZIONE'); ?>
	</th>
</tr>
