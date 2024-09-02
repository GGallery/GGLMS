<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;


HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
?>
<head>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        jQuery(document).ready(function() {
            jQuery('#sortableTable').sortable();
            jQuery('.draggable').draggable({
                    stop:function (ev, ui) {
                        console.log('qui')
                        var items=jQuery('.draggable')
                        items.each(
                            function(index){
                                console.log('posizione: '+index+' ID: '+jQuery(this).attr('id'))
                                var unit_id=jQuery(this).attr('id');
                                var pos=index;
                                jQuery.ajax({
                                    url:'index.php?option=com_gglms&task=unita.setUnitOrdinamento&unit_id='+unit_id+'&pos='+index
                                }).done(function(data){
                                    console.log('ordinamento registrato')

                                })
                            }

                        )

                    }
                }

            )
        })
    </script>
</head>

<form action="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate form-horizontal">
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_GGLMS_UNITA_PANEL', true)); ?>
    <?php echo JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', null,""); ?>
    <div class="row-fluid">
        <div class="span12">

            <div class="span4">
                <div class="row-fluid">
                    <?php  echo $this->form->renderField('id'); ?>
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
                        echo $this->form->renderField('id_box'); ?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('id_piattaforme_abilitate'); ?>
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

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('usa_coupon'); ?>
                </div>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('prefisso_coupon'); ?>
                </div>


                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('mobile'); ?>
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

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'ordering', 'Ordinamento', true); ?>

    <div class="row-fluid">
        <div class="span12">
            <table class="table table-striped" id="itemList" name="itemList" id="sortableTable">
                <?php foreach ($this->childs as $i => $item): ?>

                    <tr class="row<?php echo $i % 2; ?>, draggable" id="<?php echo $item->id; ?>">

                        <td class="order nowrap center hidden-phone">

                            <?php

                            //$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                            $iconClass='';
                            ?>
                            <span class="sortable-handler <?php echo $iconClass ?>">
                     <span class="icon-menu"></span>
                 </span>

                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->id; ?>" class="width-20 text-area-order " />


                        </td>

                        <td>
                            <?php echo $item->id; ?>
                        </td>

                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&layout=edit&id=' . $item->id); ?>">
                                <?php echo $item->titolo; ?>
                            </a>
                        </td>

                        <td>
                            <?php echo $item->ordinamento;?>
                        </td>



                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'Vendita', JText::_('COM_GGLMS_UNITA_SALE', true)); ?>

    <div class="row-fluid">
        <div class="span12">

            <div class="span4">

                <h4>Generale</h4>

                <div class="row-fluid">
                    <?php
                    if($this->item->id)
                        echo $this->form->renderField('on_sale'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('disponibile_dal'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('disponibile_al'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('prezzo'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sconti_particolari'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('riduzione_webinar'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('prezzo_webinar_fisso'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_webinar_perc'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('disabilita_aquisto_presenza'); ?>
                </div>

                <h4>Scontistica per data</h4>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_da_data'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_a_data'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_valore_data'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_a_data_gruppi'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_valore_data_gruppi'); ?>
                </div>

                <h4>Scontistica per gruppo</h4>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_a_gruppi'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_valore_gruppi'); ?>
                </div>

                <h4>Scontistica per campo integrazione (CB/EB)</h4>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_custom_cb'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_semaforo_custom_cb'); ?>
                </div>

                <div class="row-fluid">
                    <?php echo $this->form->renderField('sc_valore_custom_cb'); ?>
                </div>


            </div>

        </div>
    </div>

    <?php echo JHtml::_('bootstrap.endTab'); ?>


    <input id="idelemento" type="hidden" name="idelemento" value="<?php echo $this->item->id; ?>" size = "150px">
    <input id="path" type="hidden" name="path" value="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/mediagg/images/unit/" size = "150px">
    <input id="subpath" type="hidden" name="subpath" value="" size = "150px">
    <input id="url" type="hidden" name="url" value="<?php echo JURI::root(); ?>/contenuti/<?php echo $this->item->id; ?>/" size = "150px">
    <input id="tipologia" type="hidden" name="tipologia" value="" size = "150px">

</form>
