<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>" . JText::_('COM_GGLMS_GENERA_COUPON_TITLE') ."</h1>";


?>
<form autocomplete="off" id="form-genera-coupon"
      action="<?php echo('index.php?option=com_gglms&task=generacoupon.generacoupon'); ?>"
      method="post" name="generaCouponForm" id="adminForm" class="form-validate">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="username"><?php echo $this->label_partita_iva; ?></label>
        <div class="col-sm-5">
            <input required placeholder="<?php echo $this->label_partita_iva; ?>" type="text" class="form-control" id="username"
                   name="username">
            <small id="piva-msg"><?php echo $thi->label_partita_iva_missing; ?> </small>
        </div>
        <div class="col-sm-4">
            <button onclick="openModal" type="button" title="Cerca Partita Iva" id="search_piva" class="btn btn-xs"> <span class="glyphicon glyphicon-zoom-in"></span></button>
            <button type="button" id="confirm_piva" class="btn btn-sm"><?php echo  JText::_('COM_GGLMS_GLOBAL_CONFERMA') ?> </button>
            <button type="button" id="change_piva" class="btn btn-sm"> <?php echo  JText::_('COM_GGLMS_GLOBAL_RESET') ?></button>
        </div>
    </div>

    <?php /*
        JText::_('COM_GGLMS_GENERA_COUPON_COMPANYNAME')
        JText::_('COM_GGLMS_GENERA_COUPON_COMPANYNAME_PH')
    */ ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_company_opt" for="ragioneSociale"> <?php echo $this->label_ragione_sociale; ?> : </label>
        <div class="col-sm-9">
            <input required disabled placeholder="<?php echo $this->label_ragione_sociale; ?> " type="text"
                   class="form-control company_opt" id="ragione_sociale"
                   name="ragione_sociale">
        </div>
    </div>

    <?php /*
         JText::_('COM_GGLMS_GENERA_COUPON_EMAIL_TUTOR_AZ')
         JText::_('COM_GGLMS_GENERA_COUPON_EMAIL_TUTOR_AZ_PH')
    */ ?>
    <div class="form-group row">
        <label disabled class="col-sm-3 col-form-label disabled lbl_company_opt" for="email"> <?php echo $this->label_email_tutor_aziendale; ?> :</label>
        <div class="col-sm-9">
            <input required disabled placeholder="<?php echo $this->label_email_tutor_aziendale; ?>" type="email"
                   class="form-control company_opt" id="email"
                   name="email">
        </div>
    </div>
    <?php if ($this->genera_coupon_visualizza_ateco == 1) { ?>
    <div class="form-group row">
        <label disabled class="col-sm-3 col-form-label disabled lbl_company_opt" for="ateco"><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_ATECO') ?> :</label>
        <div class="col-sm-9">
            <input disabled placeholder="<?php echo  JText::_('COM_GGLMS_GENERA_COUPON_ATECO_PH') ?>" type="text" class="form-control company_opt" id="ateco"
                   name="ateco">
        </div>
    </div>
    <?php } ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_company_opt" for="id_piattaforma"><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_PIATTAFORMA') ?> :</label>
        <div class="col-sm-9">
            <select disabled required placeholder="<?php echo  JText::_('COM_GGLMS_GENERA_COUPON_PIATTAFORMA_PH') ?> " class="form-control company_opt" id="id_piattaforma"
                    name="id_piattaforma">
                <?php foreach ($this->societa_venditrici as $s) { ?>
                    <option value="<?php echo $s->value; ?>">
                        <?php echo $s->text ?>
                    </option>
                <?php } ?>

            </select>
            <div id="piattaforma_warning" style="display: none" class="alert alert-warning">
                <?php echo  JText::_('COM_GGLMS_GENERA_COUPON_COMPANY_DUPLICATED') ?>
            </div>

        </div>
    </div>
    <hr>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="corso"> <?php echo  JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label>
        <div class="col-sm-9">
            <select required disabled required placeholder="<?php echo  JText::_('COM_GGLMS_GLOBAL_CORSO') ?>" type="text" class="form-control cpn_opt"
                    id="gruppo_corsi" name="gruppo_corsi">
                <?php foreach ($this->lista_corsi as $c) { ?>
                    <option value="<?php echo $c->value; ?>">
                        <?php echo $c->text ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="qty"><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_QTY') ?> :</label>
        <div class="col-sm-9">
            <input class="cpn_opt" disabled required placeholder="<?php echo  JText::_('COM_GGLMS_GENERA_COUPON_QTY_PH') ?>" type="number"
                   class="form-control" id="qty" min="1"
                   name="qty">
        </div>
    </div>

    <?php if ($this->specifica_durata == 1) { ?>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="durata"><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_DURATION') ?>:</label>
            <div class="col-sm-9">
                <input class="cpn_opt" disabled required placeholder="<?php echo  JText::_('COM_GGLMS_GENERA_COUPON_DURATION_PH') ?>" type="number"
                       class="form-control" id="durata" min="1"
                       name="durata">
            </div>
        </div>
    <?php } ?>

    <?php if ($this->genera_coupon_visualizza_venditore == 1) { ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="venditore"><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_SELLER') ?>:</label>
        <div class="col-sm-9">
            <input class="cpn_opt typeahead" disabled placeholder="<?php echo  JText::_('COM_GGLMS_GENERA_COUPON_SELLER_PH') ?>" type="text"
                   class="form-control" id="venditore"
                   name="venditore">
        </div>
    </div>
    <?php } ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="venditore"><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_RECEIVER') ?></label>
        <div class="col-sm-9">
            <input placeholder="<?php echo  JText::_('COM_GGLMS_GENERA_COUPON_RECEIVER_PH') ?> "
                   type="email"
                   class="form-control cpn_opt" id="email_coupon"
                   name="email_coupon">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"><label class="col-form-label disabled lbl_cpn_opt" for=""><?php echo  JText::_('COM_GGLMS_GLOBAL_OPTION') ?>:</label></div>

        <?php if ($this->specifica_abilitazione == 1) { ?>
            <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="abilitato"><input class="cpn_opt" disabled
                                                                                               type="checkbox"
                                                                                               id="abilitato"
                                                                                               name="abilitato">
                <?php echo  JText::_('COM_GGLMS_GENERA_COUPON_ABILITATI') ?></label>
        <?php } ?>

        <?php if ($this->genera_coupon_visualizza_stampa_tracciato == 1) { ?>
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt " for="stampatracciato"><input class="cpn_opt"
                                                                                                  disabled
                                                                                                  type="checkbox"
                                                                                                  id="stampatracciato"
                                                                                                  name="stampatracciato">
            <?php echo  JText::_('COM_GGLMS_GENERA_COUPON_TRACKLOG') ?>
         </label>
        <?php } ?>

        <?php if ($this->check_coupon_attestato == 1) { ?>
            <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="attestato"><input class="cpn_opt" disabled
                                                                                               type="checkbox"
                                                                                               id="attestato"
                                                                                               name="attestato">
                <?php echo  JText::_('COM_GGLMS_GLOBAL_ATTESTATO') ?></label>
        <?php } ?>
    </div>
    <?php if ($this->show_trial == 1) { ?>
    <div class="form-group row">
        <div class="col-sm-3"><label class="col-form-label disabled lbl_cpn_opt" for="">Trial:</label></div>

        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="trial"><input class="cpn_opt" disabled
                                                                                       type="checkbox"
                                                                                       id="trial"
                                                                                       name="trial">
            <?php echo  JText::_('COM_GGLMS_GENERA_COUPON_TRIAL') ?></label>
        <?php } ?>
    </div>
    <div class="form-group">
        <button id="btn-genera" type="submit" disabled class="btn-block btn"> <?php echo  JText::_('COM_GGLMS_GENERA_COUPON_GENERA') ?></label></button>
    </div>
</form>


<!-- Modal Details-->
<div id="modalDetails_genera" class="modal fade" role="dialog" >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeModal()" ">&times;</button>
                <h4 class="modal-title"> <?php echo  JText::_('COM_GGLMS_GENERA_COUPON_COMPANYNAME') ?> </h4>
                <h6><?php echo  JText::_('COM_GGLMS_GENERA_COUPON_POPUP_INSTRUCTION') ?></h6>
            </div>
            <div class="modal-body">
                <table id="details_grid">
                    <thead>
                    <tr class="header-row">
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-default btn-close" >Close</button>
            </div>
        </div>

    </div>
</div>


<script type="application/javascript">
    jQuery(document).ready(function () {
        _generaCoupon.init();
    });

</script>
