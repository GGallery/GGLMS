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



?>
<style>
    .details{

        display:none;
    }

</style>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

    <div class="span12">
        <form action="<?php echo JRoute::_('index.php?option=com_gglms'); ?>" method="post" name="adminForm" id="adminForm">

            <div id="span10 j-toggle-main">
                <div clas="js-stools clearfix">
                    <div class="clearfix">
                        <div class="js-stools-container-bar">


                        </div>
                    </div>
                    <div>
                          <h3>  <?php echo $this->items[0]->nome?>

                            <?php echo $this->items[0]->cognome?>

                            corso:<?php echo $this->items[0]->corso?></h3>
                    </div>
                    <table class="table table-striped">
                        <thead><?php echo $this->loadTemplate('head'); ?></thead>
                        <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
                        <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
                    </table>

                        <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
                </div>

                <div>
                    <input type="hidden" name="view" value="usercontents" />
                    <input type="hidden" name="task" value="usercontents" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <?php echo JHtml::_('form.token'); ?>
                </div>
        </form>
    </div>
</div>

