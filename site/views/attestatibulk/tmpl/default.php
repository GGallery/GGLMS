<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>Scarica Attestati</h1>";


?>
<form autocomplete="off" id="form-genera-coupon"
      method="post" name="generaCouponForm" id="adminForm" class="form-validate">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label disabled lbl_cpn_opt" for="corso">Corso:</label>
        <div class="col-sm-10">
            <select required placeholder="Corso" type="text" class="form-control cpn_opt"
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
        <button id="btn-cerca" type="button" class="btn-block btn">SCARICA</button>
<!--        <button disabled id="btn-zip" type="button" class="btn-block btn">ZIP</button>-->
    </div>
</form>
<div id="report">




</div>
<div style="display: none" id="link_container">


</div>
<script type="application/javascript">
    jQuery(document).ready(function () {
        _scaricaattesati.init();
    });

</script>
