<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');


//Get companie options
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
//$categorie = JFormHelper::loadFieldType('categorie', false);
//$categorieOptions = $categorie->getOptions(); 
//
//$congressi = JFormHelper::loadFieldType('congressi', false);
//$congressiOptions = $congressi->getOptions(); 
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form action="<?php echo JRoute::_('index.php?option=com_gglms'); ?>" method="post" name="adminForm" id="adminForm">

   
    <table class="table table-striped">
        <thead><?php echo $this->loadTemplate('head'); ?></thead>
        <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
        <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
 </div>