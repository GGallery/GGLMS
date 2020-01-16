<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>Scarica Attestati</h1>";


?>
<form autocomplete="off" id="form-genera-coupon" action="<?php echo('index.php?option=com_gglms&task=attestatibulk.scaricaattestati'); ?>"
      method="post" name="generaCouponForm" id="adminForm" class="form-validate">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label disabled lbl_cpn_opt" for="corso">Corso:</label>
        <div class="col-sm-10">
            <select required  placeholder="Corso" type="text" class="form-control cpn_opt"
                    id="id_corso" name="id_corso">
                <?php foreach ($this->lista_corsi as $c) { ?>
                    <option value="<?php echo $c->value; ?>">
                        <?php echo $c->text ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>

    <div class="form-group">
        <button id="btn-genera" type="submit" class="btn-block btn">SCARICA</button>
    </div>
</form>

<script type="application/javascript">
    jQuery(document).ready(function () {
        _scaricaattesati.init();
    });

</script>
