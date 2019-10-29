<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Genera Coupon</h1>";


?>
<form action="<?php echo JRoute::_('index.php?option=com_gglms&task=generaCoupon.generaCoupon.php'); ?>"
      method="post" name="generaCouponForm" id="adminForm" class="form-validate">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="username">Partita Iva:</label>
        <div class="col-sm-9">
            <input required placeholder="Partita IVA dell'azienda" type="text" class="form-control" id="username"
                   name="username">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="ragioneSociale">Ragione Sociale:</label>
        <div class="col-sm-9">
            <input placeholder="Ragione sociale dell'azienda" type="text" class="form-control" id="ragione_sociale"
                   name="ragione_sociale">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="email">Email:</label>
        <div class="col-sm-9">
            <input placeholder="Email del referente aziendale" type="email" class="form-control" id="email"
                   name="email">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="ateco">ATECO:</label>
        <div class="col-sm-9">
            <input placeholder="Codice ATECO" type="text" class="form-control" id="ateco" name="ateco">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="corso">Corso:</label>
        <div class="col-sm-9">
            <select required placeholder="Corso" type="text" class="form-control" id="gruppo_corsi" name="gruppo_corsi">
                <?php foreach ($this->lista_corsi as $c) { ?>
                    <option value="<?php echo $c->value; ?>">
                        <?php echo $c->text ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="qty">Quantità:</label>
        <div class="col-sm-9">
            <input required placeholder="Numero di coupon da produrre" type="number" class="form-control" id="qty" min ="1"
                   name="qty">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="vendor">Venditrice:</label>
        <div class="col-sm-9">
            <select  required placeholder="Venditrice" class="form-control" id="vendor" name="vendor">
                <?php foreach ($this->societa_venditrici as $s) { ?>
                    <option value="<?php echo $s->value; ?>">
                        <?php echo $s->text ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="username">Prefisso:</label>
        <div class="col-sm-9">
            <input placeholder="Prefisso Coupon (opzionale ma consigliato)" type="text" class="form-control"
                   id="prefisso_coupon"
                   name="prefisso_coupon">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3"> </div>
        <label class="col-sm-3 col-form-label" for="abilitato"><input type="checkbox" id="abilitato" name="abilitato">
            Abilitati</label>
        <label class="col-sm-3 col-form-label" for="attestato"><input type="checkbox" id="attestato" name="attestato">
            Attestato</label>
        <label class="col-sm-3 col-form-label " for="stampatracciato"><input type="checkbox" id="stampatracciato"
                                                                            name="stampatracciato"> Stampa
            tracciato</label>
    </div>
    <div class="form-group">
        <button id="btn-genera" type="submit" class="btn-block btn">Genera</button>
    </div>
</form>


<!--<script type="application/javascript">pippo()</script>-->
