<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>".  JText::_('COM_GGLMS_MONITORA_COUPON_TITLE'). "</h1>"; ?>

<div class="mc-main">
    <div id="filtri" class="filtri">
        <h5><?php echo  JText::_('COM_GGLMS_GLOBAL_FILTRI') ?></h5>
        <form id="form-monitora-coupon" name="form-monitora-coupon" class="form-validate">
            <div class="form-group">
                <label for="id_gruppo_azienda"><?php echo  JText::_('COM_GGLMS_GLOBAL_COMPANY') ?>:</label>
                <select placeholder="<?php echo  JText::_('COM_GGLMS_GLOBAL_COMPANY') ?>" class="form-control" id="id_gruppo_azienda" name="id_gruppo_azienda">
                    <?php foreach ($this->societa as $s) { ?>
                        <option value="<?php echo $s->id; ?>">
                            <?php echo $s->title ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_gruppo_corso"><?php echo  JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label>
                <select placeholder="Corso" class="form-control" id="id_gruppo_corso" name="id_gruppo_corso">
                    <option value="-1"><?php echo  JText::_('COM_GGLMS_GLOBAL_ALL_CORSI') ?></option>
                    <?php foreach ($this->lista_corsi as $s) { ?>
                        <option value="<?php echo $s->value; ?>">
                            <?php echo $s->text ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stato_coupon"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO') ?>:</label>
                <select placeholder="<?php echo  JText::_('COM_GGLMS_GLOBAL_STATO') ?>" class="form-control" id="stato_coupon" name="stato_coupon">
                    <option value="-1"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_ANY') ?></option>
                    <option value="0"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_LBERI') ?></option>
                    <option value="1"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_ASSEGNATI') ?></option>
                    <option value="2"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_SCADUTI') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="coupon"><?php echo  JText::_('COM_GGLMS_MONITORA_FIND_COUPON') ?>:</label>
                <input placeholder="<?php echo  JText::_('COM_GGLMS_MONITORA_FIND_COUPON') ?>" class="" type="text" id="coupon" name="coupon">
            </div>

            <div class="form-group">
                <label for="venditore"><?php echo  JText::_('COM_GGLMS_MONITORA_FIND_SELLER') ?>:</label>
                <input placeholder="<?php echo  JText::_('COM_GGLMS_MONITORA_FIND_SELLER') ?>" class="" type="text" id="venditore" name="venditore">
            </div>

            <div class="form-group">
                <label for="utente"><?php echo  JText::_('COM_GGLMS_MONITORA_FIND_USER') ?>:</label>
                <input placeholder="<?php echo  JText::_('COM_GGLMS_MONITORA_FIND_USER') ?>" class="" type="text" id="utente" name="utente">
            </div>

            <button type="button" id="btn_export_csv" class="btn btn-primary"><?php echo  JText::_('COM_GGLMS_GLOBAL_EXPORT_CSV') ?></button>
        </form>
    </div>
    <div class="data">
        <h5>Coupon</h5>
        <div class="table-container">

            <span id="no-data-msg"><?php echo  JText::_('COM_GGLMS_MONITORA_NO_COUPON') ?></span>
            <table id="coupon-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr class="header-row">
                </tr>
                </thead>
                <tbody>
                </tbody>

            </table>

            <div id="pagination-container">
                <ul class="pagination">
                    <li class="first" aria-disabled="true">
                        <a data-page="first" class="button button-page">«</a></li>
                    <li class="prev" aria-disabled="true">
                        <a data-page="prev" class="button button-page">&lt;</a></li>
                    <li class="page-1" aria-disabled="false" aria-selected="false">
                        <a data-page="1" class="button button-page">1</a></li>
                    <li class="page-2" aria-disabled="false" aria-selected="false">
                        <a data-page="2" class="button button-page">2</a></li>
                    <li class="page-3" aria-disabled="false" aria-selected="false">
                        <a data-page="3" class="button button-page">3</a></li>
                    <li class="page-4" aria-disabled="false" aria-selected="false">
                        <a data-page="4" class="button button-page">4</a></li>
                    <li class="page-5" aria-disabled="false" aria-selected="false">
                        <a data-page="5" class="button button-page">5</a></li>
                    <li class="next" aria-disabled="false ">
                        <a data-page="next" class="button button-page">&gt;</a></li>
                    <li class="last" aria-disabled="false">
                        <a data-page="last" class="button button-page">»</a></li>
                    <li class="last" aria-disabled="false">
                        <span id="totalcount"></span></li>
                </ul>

            </div>
        </div>

    </div>

</div>
<div id="cover-spin"></div>

<!-- Modal Corso Disabilitato-->
<!--<div id="modalMail" class="modal fade" role="dialog" data-backdrop="static">-->
<!--    <div class="modal-dialog">-->
<!---->
<!--       Modal content-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <button type="button" class="close" data-dismiss="modal">&times;</button>-->
<!--                <h4 class="modal-title">Invia Coupon </h4>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <form>-->
<!--                    <div class="form-group">-->
<!--                        <label class="col-sm-1 col-form-label" for="to">Da:</label>-->
<!--                        <div class="col-sm-11">-->
<!--                            <input type="email" id="to" name="to">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <label class="col-sm-1 col-form-label" for="from">A:</label>-->
<!--                        <div class="col-sm-11">-->
<!--                            <input type="email" id="from" name="from">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <label class="col-sm-1 col-form-label" for="subject">Obj:</label>-->
<!--                        <div class="col-sm-11">-->
<!--                            <input type="text" id="subject" name="subject">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!---->
<!--                        <div class="col-sm-12">-->
<!--                            <div style ="min-height: 350px" contenteditable="true" class="form-control"  id="body" name="body">-->
<!--                            </div>-->
<!---->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!--                </form>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button  class="btn" type="button" id="btn_invia_coupon">Invia</button>-->
<!--                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--    </div>-->
<!--</div>-->


<script type="application/javascript">
    jQuery(document).ready(function () {
        _monitoraCoupon.init();
    });

</script>
