<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>Scarica Attestati</h1>";


?>
<div>
    <form autocomplete="off" id="form-genera-coupon"
          method="post" name="downloadAttForm" id="dwlForm" style="padding: 20px 100px;" class="form-validate">
        <div class="form-group row">
            <label class="col-sm-2" for="id_corso">Corso:</label>
            <div class="col-sm-10">
                <select required placeholder="Corso" type="text" class="form-control cpn_opt"
                        id="id_corso" name="id_corso">
                    <option value="-1">
                        Seleziona un corso
                    </option>
                    <?php foreach ($this->lista_corsi as $c) { ?>
                        <option value="<?php echo $c->value; ?>">
                            <?php echo $c->text ?>
                        </option>
                    <?php } ?>

                </select>
            </div>
        </div>
        <div class="form-group row" id="calendar_startdate_div">
            <label class="col-sm-2" for="startdate">Completato dal:</label>
            <div class="col-sm-10">
                <?php echo JHTML::calendar('', 'startdate', 'startdate', '%Y-%m-%d', array('onChange' => '_scaricaattesati.getDays()')); ?>
            </div>
        </div>

        <div class="form-group row" id="calendar_finishdate_div">
            <label class="col-sm-2" for="finishdate">Completato al:</label>
            <div class="col-sm-10">
                <?php echo JHTML::calendar('NOW', 'finishdate', 'finishdate', '%Y-%m-%d', array('onChange' => '_scaricaattesati.getDays()')); ?>
            </div>
        </div>

        <div class="form-group row">
            <span id="msg" class="alert alert-danger" style="display: none; width: 100%"></span>
        </div>

        <div class="form-group" style="text-align: center">
            <a id="btn-download" style="padding: 2px 32px;" type="button" target="_blank" href=""
               class="btn btn-lg disabled">SCARICA</a>
        </div>
    </form>


</div>

<script type="application/javascript">
    jQuery(document).ready(function () {
        _scaricaattesati.init();
    });


</script>
