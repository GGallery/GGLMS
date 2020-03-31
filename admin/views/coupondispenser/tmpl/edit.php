<?php
/**
 * @package        Joomla.Tutorials
 * @subpackage    Component
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license        License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>


<form action="<?php echo JRoute::_('index.php?option=com_gglms&view=coupondispenser&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate form-horizontal">
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_GGLMS_UNITA_PANEL', true)); ?>
    <?php echo JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', null, ""); ?>
    <div class="row-fluid">
        <div class="span12">

            <div class="span12">
                <div class="row-fluid">
                    <?php echo $this->form->renderField('id'); ?>
                </div>


                <div class="row-fluid">
                    <?php echo $this->form->renderField('titolo'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('id_iscrizione'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('descrizione'); ?>
                </div>


            </div>

            </fieldset>
            <div>
                <input type="hidden" name="task" value="unita.edit"/>
                <?php echo JHtml::_('form.token'); ?>
            </div>

        </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
</form>
