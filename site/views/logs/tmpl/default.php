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


?>

<div id="j-main-container" class="span10">

    <div class="span12">


            <div id="span10 j-toggle-main">
                <div clas="js-stools clearfix">

                    <div>
                            <h2><?php echo $this->items[0]->nome; ?>

                            <?php echo $this->items[0]->cognome; ?></h2>
                    </div>
                    <table class="table table-striped">
                        <thead><?php echo $this->loadTemplate('head'); ?></thead>
                        <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
                        <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
                    </table>
                </div>


    </div>
</div>

