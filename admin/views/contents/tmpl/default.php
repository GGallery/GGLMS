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

//$congressi = JFormHelper::loadFieldType('congressi', false);
//$congressiOptions = $congressi->getOptions();

$this->corsi = $this->get('Corsi');
//$temp=$this->get('Unitas');
//$this->unitas=$temp[0];
//$this->contenuti=$temp[1];
//var_dump($this->contenuti);
$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
$saveOrderingUrl = 'index.php?option=com_gglms&task=contents.saveOrderAjax&tmpl=component';
//il modulo che consente il d&d è caricato solo se non sono presenti filtri di ricerca
if ($this->state->get('filter.search')==null)
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl)
?>

<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

    <form action="<?php echo JRoute::_('index.php?option=com_gglms'); ?>" method="post" name="adminForm" id="adminForm">
        <div id="span10 j-toggle-main">
            <div clas="js-stools clearfix">
                <div class="clearfix">
                    <div class="js-stools-container-bar">
                        <div class="js-stools-container-bar">
                            <label class="element-invisible" for="filter_search">
                                <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></label>

                            <div class=""js-stools-container-bar">

                                <select id="corsi" name="corsi">

                                     <?php echo '<option value=null> - filtra per corso - </option>';
                                          foreach ($this->corsi as $corso){
                                         $selected=null;
                                         //var_dump( JFactory::getApplication()->input->get('corsi','',''));die;
                                         if( $corso->id==JFactory::getApplication()->input->get('corsi','','')){
                                             $selected='selected="selected"';
                                         }
                                        echo '<option value="'.$corso->id.'"'.$selected.'>'.$corso->titolo."</option>";
                                     }?>
                                </select>

                            </div>
                            <div class="btn-wrapper input-append">

                                <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->searchterms); ?>" title="<?php echo JText::_('COM_GGLMS_SELEZIONATITOLODESCRIZIONEETC'); ?>" />

                                <button type="submit" class="btn hasTooltip">
                                    <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
                                </button>
                                <button type="button" class="btn hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();">
                                    <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
                                </button>

                            </div>




<!--                            <select name="filter_categoria" class="chzn-done" onchange="this.form.submit()">-->
<!--                                <option value=""> - --><?php //echo JText::_('COM_GGLMS_SELEZIONAAREATEMATICA'); ?><!-- - </option>-->
<!--                                --><?php //echo JHtml::_('select.options', $categorieOptions, 'value', 'text', $this->state->get('filter.categoria')); ?>
<!--                            </select>-->

                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="itemList" name="itemList">
                    <thead><?php echo $this->loadTemplate('head'); ?></thead>
                    <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
                    <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
                </table>
            </div>

            <div>
                <input type="hidden" name="view" value="contents" />
                <input type="hidden" name="task" value="contents" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
    </form>
</div>
</div>


<script type="text/javascript">
    corsi.onchange= function () {
        this.form.submit();

    }
    unita.onchange= function () {
        this.form.submit();

    }
</script>
