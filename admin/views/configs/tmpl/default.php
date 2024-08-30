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
JHtml::_('bootstrap.tooltip');
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
        <form action="<?php echo JRoute::_('index.php?option=com_gglms&task=configs&layout=edit') ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

            <?php
            echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'Generale'));

            echo JHtml::_('bootstrap.addTab', 'myTab', 'Generale', JText::_('Generale', true));
            include(dirname(__FILE__).DS.'generale.php');
            echo JHtml::_('bootstrap.endTab');

            echo JHtml::_('bootstrap.addTab', 'myTab', 'Breadcrumb', JText::_('Breadcrumb', true));
            include(dirname(__FILE__).DS.'breadcrumb.php');
            echo JHtml::_('bootstrap.endTab');

            echo JHtml::_('bootstrap.addTab', 'myTab', 'Unita', JText::_('Unita', true));
            include(dirname(__FILE__).DS.'unita.php');
            echo JHtml::_('bootstrap.endTab');

            echo JHtml::_('bootstrap.addTab', 'myTab', 'Attestato', JText::_('Attestato', true));
            include(dirname(__FILE__).DS.'attestato.php');
            echo JHtml::_('bootstrap.endTab');

            echo JHtml::_('bootstrap.addTab', 'myTab', 'Report', JText::_('Report', true));
            include(dirname(__FILE__).DS.'report.php');
            echo JHtml::_('bootstrap.endTab');

            echo JHtml::_('bootstrap.addTab', 'myTab', 'Contenuto', JText::_('Contenuto', true));
            include(dirname(__FILE__).DS.'contenuto.php');
            echo JHtml::_('bootstrap.endTab');

            /**
            * Rimosso a favore di Personalizzazione
            echo JHtml::_('bootstrap.addTab', 'myTab', 'Coupon', JText::_('Coupon', true));
            include(dirname(__FILE__).DS.'coupon.php');
            echo JHtml::_('bootstrap.endTab');
             *
             */

            echo JHtml::_('bootstrap.addTab', 'myTab', 'HelpDesk', JText::_('HelpDesk', true));
            include(dirname(__FILE__).DS.'helpDesk.php');
            echo JHtml::_('bootstrap.endTab');

            // gestione personalizzazioni extra
            echo JHtml::_('bootstrap.addTab', 'myTab', 'Personalizzazione', JText::_('Personalizzazioni', true));
            include(dirname(__FILE__).DS.'personalizzazioni.php');
            echo JHtml::_('bootstrap.endTab');

            // gestione paypal
            echo JHtml::_('bootstrap.addTab', 'myTab', 'PayPal', JText::_('PayPal', true));
            include(dirname(__FILE__).DS.'paypal.php');
            echo JHtml::_('bootstrap.endTab');

            // gestione zoom
            echo JHtml::_('bootstrap.addTab', 'myTab', 'Zoom', JText::_('Zoom', true));
            include(dirname(__FILE__).DS.'zoom.php');
            echo JHtml::_('bootstrap.endTab');

            // interfacciamento rete2000 xml
            echo JHtml::_('bootstrap.addTab', 'myTab', 'XML', JText::_('XML', true));
            include(dirname(__FILE__).DS.'xml.php');
            echo JHtml::_('bootstrap.endTab');
            ?>

            <input type="hidden" name="task" value=""/>

        </form>
    </div>
