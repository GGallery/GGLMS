<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>


<h1><?php echo  JText::_('COM_GGLMS_ATTESTATI_BULK_TITOLO') ?></h1>
<div>
    <form autocomplete="off" id="form-genera-coupon"
          method="post" name="downloadAttForm" id="dwlForm" style="padding: 20px 100px;" class="form-validate">
        <div class="form-group row">
            <label class="col-sm-2" for="id_corso"><?php echo  JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label>
            <div class="col-sm-10">
                <select required placeholder="Corso" type="text" class="form-control cpn_opt"
                        id="id_corso" name="id_corso">
                    <option value="-1">
                        <?php echo  JText::_('COM_GGLMS_GLOBAL_SCEGLI_CORSO') ?>
                    </option>
                    <?php foreach ($this->lista_corsi as $c) { ?>
                        <option value="<?php echo $c->value; ?>">
                            <?php echo $c->text ?>
                        </option>
                    <?php } ?>

                </select>
            </div>
        </div>
        <?php
        // integrazione scelta azienda nello scaricamento degli attestati
        if (isset($this->lista_azienda)) : ?>
            <div class="form-group row">
                <label class="col-sm-2" for="id_azienda"><?php echo  JText::_('COM_GGLMS_GLOBAL_COMPANY') ?>:</label>
                <div class="col-sm-10">
                    <select required placeholder="Azienda" type="text" class="form-control cpn_opt"
                            id="id_azienda" name="id_azienda">
                        <option value="">
                            <?php echo  JText::_('COM_GGLMS_GLOBAL_SCEGLI_AZIENDA') ?>
                        </option>
                        <?php foreach ($this->lista_azienda as $key => $az) { ?>
                            <option value="<?php echo $az['id_gruppo']; ?>">
                                <?php echo $az['azienda']; ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group row" id="calendar_startdate_div">
            <label class="col-sm-2" for="startdate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_FROM') ?></label>
            <div class="col-sm-10">
                <input type="date" id="startdate" name="startdate"  min="" >
            </div>
        </div>

        <div class="form-group row" id="calendar_startdate_div">
            <label class="col-sm-2" for="enddate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_TO') ?></label>
            <div class="col-sm-10">
                <input type="date" id="enddate" name="enddate"  min="" >
            </div>
        </div>

        <div class="form-group row">
            <span id="msg" class="alert alert-danger" style="display: none; width: 100%"></span>
        </div>

        <div class="form-group" style="text-align: center">
            <a id="btn-download" style="padding: 2px 32px;" type="button" target="_blank" href=""
               class="btn btn-lg disabled"><?php echo  JText::_('COM_GGLMS_GLOBAL_DOWNLOAD') ?></a>
        </div>
    </form>


</div>

<script type="application/javascript">
    jQuery(document).ready(function () {
        _scaricaattesati.init();
    });


</script>
