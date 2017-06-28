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
JHtml::_('behavior.formvalidation');
?>

<style type="text/css">
    body{
        font-size : 11px !important;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_gglms&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate form-horizontal">
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_GGLMS_FILE_PANEL', true)); ?>
    <div class="row-fluid">
        <div class="span12">

            <div class="span6">
                <div class="row-fluid">
                    <?php echo $this->form->renderField('id'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('name'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('filename'); ?>
                </div>
            </div>

            <div>
                <input type="hidden" name="task" value="file.edit" />

                <?php echo JHtml::_('form.token'); ?>
            </div>
        </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'advanced', JText::_('COM_GGLMS_FILE_ADVANCEDPANEL', true)); ?>
    <div class="row-fluid">
        <div class="span12">
            <fieldset class="adminform">
                <?php //echo JHtml::_('sliders.start', 'content-sliders-' . $this->item->id, array('useCookie' => 1)); ?>
                <?php // Do not show the publishing options if the edit form is configured not to.   ?>

                <?php //echo JHtml::_('sliders.panel', JText::_('COM_WEBTV_WEBTV_MULTIMEDIA'), 'Multimedia'); ?>
                <fieldset class="panelform">


                    <input id="idelemento" type="hidden" name="idelemento" value="<?php echo $this->item->id; ?>" size = "50px">
                    <input id="path" type="hidden" name="path" value="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/mediagg/files/<?php echo $this->item->id; ?>/" size = "50px">
                    <input id="subpath" type="hidden" name="subpath" value="" size = "50px">
                    <input id="url" type="hidden" name="url" value="<?php echo $_SERVER['SERVER_NAME']; ?>/mediagg/files/<?php echo $this->item->id; ?>/" size = "50px">
                    <input id="tipologia" type="hidden" name="tipologia" value="" size = "50px">



                    <!-- QUI INIZIA IL FILE UPLOAD     -->
                    <?php if ($this->item->id) { ?>

                        <div class="containerUpload">

                            <!-- The fileinput-button span is used to style the file input field as button -->
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span><?php echo JText::_('COM_GGLMS_CONTENT_CARICAFILE', true); ?></span>
                            <!-- The file input field used as target for the file upload widget -->
                            <input id="fileuploadallegati" type="file" name="files[]" >
                        </span>


                            <br>
                            <br>
                            <!-- The global progress bar -->
                            <div id="progress" class="progress">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>
                            <!-- The container for the uploaded files -->
                            <div id="files" class="files"></div>
                            <br>
                        </div>
                        <?php
                    } else
                        echo "Salva il contenuto prima di caricare i file.";
                    ?>




                    <!-- QUI FINISCE IL FILE UPLOAD -->

                </fieldset>


                <?php //echo JHtml::_('sliders.end'); ?>
            </fieldset>
        </div>
    </div>

    <?php echo JHtml::_('bootstrap.endTab'); ?>

</form>