<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.calendar');

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

    <div id="j-main-container">
        <!--        <form action="--><?php //echo JRoute::_('index.php?option=com_gglms&task=generacoupon&layout=edit') ?><!--" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">-->

        <form action="<?php echo JRoute::_('index.php?option=com_gglms&layout=edit'); ?>"
              method="post"
              name="adminForm"
              id="adminForm"
              class="form-validate form-horizontal">

            <input type="hidden" name="task" value="generacoupon" />

            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Quantita</label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-group" id="quantita" name="quantita">
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Prefisso</label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-group" id="prefisso" name="prefisso">
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Unita da abilitare</label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-group" id="course_id" name="course_id">
                    </div>
                </div>
            </div>


            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Descrizione</label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-group" id="transition_id" name="transition_id">
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Attestato</label>
                    </div>
                    <div class="controls">
<!--                        <input type="text" class="form-group" id="attestato" name="attestato">-->
                        <select class="form-group" id="attestato" name="attestato">
                            <option value="0">NO</option>
                            <option value="1">SI</option>
                        </select>

                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Durata</label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-group" id="durata" name="durata">
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="control-group">
                    <div class="control-label">
                        <label>Indirizzo email a cui mandare questi coupon</label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-group" id="mail_destinatario" name="mail_destinatario">
                    </div>
                </div>
            </div>





        </form>
    </div>