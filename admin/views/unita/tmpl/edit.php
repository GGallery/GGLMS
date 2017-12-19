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
JHtml::_('formbehavior.chosen', 'select');
?>


<form action="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate form-horizontal">
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_GGLMS_UNITA_PANEL', true)); ?>
    <div class="row-fluid">
        <div class="span12">

            <div class="span4">
                <div class="row-fluid">
                    <?php echo $this->form->renderField('id'); ?>
                </div>


                <div class="row-fluid">
                    <?php echo $this->form->renderField('titolo'); ?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('alias'); ?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id )
                        if($this->item->id != 1)
                            echo $this->form->renderField('unitapadre');

                    ?>

                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('pubblicato'); ?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('is_corso'); ?>
                </div>


                <div class="row-fluid">
                    <?php

                    echo $this->form->renderField('id_contenuto_completamento');?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('accesso'); ?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('id_event_booking'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('data_inizio'); ?>
                </div>
                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('data_fine'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('id_gruppi_abilitati'); ?>
                </div>


            </div>

            <div class="span4">
                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('descrizione'); ?>
                </div>
            </div>

            <div class ="span4">

                <?php
                $imgurl="../../mediagg/images/unit/" . $this->item->id .".jpg";
                if(file_exists($imgurl))
                    echo "<img width='350' src='$imgurl'/>";
                else
                    echo "<img width='350px' src='components/com_gglms/images/immagine_non_disponibile.png'/>";
                ?>

            </div>

            </fieldset>
            <div>
                <input type="hidden" name="task" value="unita.edit" />
                <?php echo JHtml::_('form.token'); ?>
            </div>

        </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'Advanced', JText::_('COM_GGLMS_UNITA_ADVANCEDPANEL', true)); ?>
    <div class="row-fluid">
        <div class="span12">
            <fieldset class="adminform">

                <fieldset class="panelform">
                    <!-- QUI INIZIA IL FILE UPLOAD     -->
                    <?php if ($this->item->id) { ?>

                        <div class="containerUpload">

                            <!-- The fileinput-button span is used to style the file input field as button -->
                            <span class="btn btn-success fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span><?php echo JText::_('COM_GGLMS_UNITA_CARICAIMMAGINE', true); ?></span>
                                <!-- The file input field used as target for the file upload widget -->
                                <input id="fileupload" type="file" name="files[]" multiple>
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

        </div>
    </div>

    <?php echo JHtml::_('bootstrap.endTab'); ?>


    <input id="idelemento" type="hidden" name="idelemento" value="<?php echo $this->item->id; ?>" size = "150px">
    <input id="path" type="hidden" name="path" value="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/mediagg/images/unit/" size = "150px">
    <input id="subpath" type="hidden" name="subpath" value="" size = "150px">
    <input id="url" type="hidden" name="url" value="<?php echo JURI::root(); ?>/contenuti/<?php echo $this->item->id; ?>/" size = "150px">
    <input id="tipologia" type="hidden" name="tipologia" value="" size = "150px">

</form>