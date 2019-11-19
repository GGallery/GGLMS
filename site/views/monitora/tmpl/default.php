<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Monitora Coupon</h1>"; ?>

<div class="mc-main">
    <div id="filtri" class="filtri">
        <form  id="form-monitora-coupon" action="<?php echo('index.php?option=com_gglms&task=monitoracoupon.getcouponlist'); ?>"
               method="post" name="form-monitora-coupon"  class="form-validate">
            <div class="form-group">
                <label for="id_azienda">Azienda:</label>
                <select placeholder="Azienda" class="form-control" id="id_azienda" name="id_azienda">
                    <option value="0">...</option>
                    <?php foreach ($this->societa as $s) { ?>
                        <option value="<?php echo $s->id; ?>">
                            <?php echo $s->title ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_azienda">Corso:</label>
                <select placeholder="Corso" class="form-control" id="id_gruppo_corso" name="id_gruppo_corso">
                    <option value="0">...</option>
                    <?php foreach ($this->lista_corsi as $s) { ?>
                        <option value="<?php echo $s->value; ?>">
                            <?php echo $s->text ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button class="btn btn-primary">Aggiorna Dati</button>
        </form>
    </div>
    <div class="table-container">
        div2
    </div>

</div>


<script type="application/javascript">
    // jQuery(document).ready(function () {
    //     _generaCoupon.init();
    // });

</script>
