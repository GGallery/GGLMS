<style>
    .progress-bar {
        background-color: green;
    }

    .override-inputbox{
        width: 700px;
    }
</style>


<?php
// No direct access to this file
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
//JHtml::_('behavior.calendar');

JHtml::_('behavior.formvalidator');
//JHtml::_('formbehavior.chosen', 'select');

$fieldsets = $this->form->getFieldsets();
?>

<form action="<?php echo JRoute::_('index.php?option=com_gglms&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate form-horizontal">

    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_GGLMS_CONTENT_PANEL', true)); ?>
    <div class="row-fluid">

            <div class="span6">
                <div class="row-fluid">
                    <?php echo $this->form->renderField('id'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('titolo'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('categoria');
                    ?>
                </div>

            </div>

            <div class="span6">

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                    echo $this->form->renderField('alias');
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
                    echo $this->form->renderField('tipologia'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('orientamento'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                    echo $this->form->renderField('path'); ?>
                </div>
                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('attestato_path'); ?>
                </div>
                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('id_quizdeluxe'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('mod_track'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('datapubblicazione'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('durata'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('meta_tag'); ?>
                </div>
            </div>




        </div>
    <div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
<?php echo JHtml::_('bootstrap.endTab'); ?>

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'advanced', JText::_('Prerequisiti/Files', true)); ?>
    <div class="span4" style="width: 100%;">

        <div class="row-fluid">
            <?php
            if($this->item->id)
                echo $this->form->renderField('prerequisiti'); ?>
        </div>


        <div class="row-fluid">
            <?php
            if($this->item->id)
                echo $this->form->renderField('files'); ?>
        </div>



    </div>

<?php echo JHtml::_('bootstrap.endTab'); ?>


<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('Descrizione/Abstract', true)); ?>
    <div class="row-fluid">
        <div class="span12">

            <fieldset>
                <div class="span6">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('descrizione'); ?>
                </div>
                <div class="span6">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('abstract'); ?>
                </div>
            </fieldset>




        </div>
    </div>


<?php echo JHtml::_('bootstrap.endTab'); ?>


<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'files', 'Upload files'); ?>


    <div class="row-fluid">
        <div class="span6">

            <input id="idelemento" type="hidden" name="idelemento" value="<?php echo $this->item->id; ?>" size = "50px">
            <input id="path" type="hidden" name="path" value="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/mediagg/contenuti/<?php echo $this->item->id; ?>/" size = "50px">
            <input id="subpath" type="hidden" name="subpath" value="" size = "50px">
            <input id="url" type="hidden" name="url" value="<?php echo $_SERVER['SERVER_NAME']; ?>/mediagg/contenuti/<?php echo $this->item->id; ?>/" size = "50px">
            <input id="tipologia" type="hidden" name="tipologia" value="" size = "50px">

            <!-- QUI INIZIA IL FILE UPLOAD     -->
            <?php if ($this->item->id) { ?>

                <div class="containerUpload">
                    <?php //echo JHtml::_('sliders.panel', JText::_('Caricamento file multimediali'), 'Upload'); ?>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-success fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i> 
                        <span><?php echo JText::_('COM_GGLMS_CONTENT_CARICAFILE'); ?></span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="fileupload" type="file" name="files[]" multiple>
                    </span>

                    <span class="btn btn-info fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i> 
                        <span><?php echo JText::_('COM_GGLMS_CONTENT_CARICASLIDE'); ?></span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="fileuploadslide" type="file" name="files[]" multiple>
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
        </div>
    </div>



    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.endTabSet'); ?>


</form>