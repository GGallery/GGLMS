<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Genera Coupon</h1>";


?>
<form id="form-genera-coupon" action="<?php echo('index.php?option=com_gglms&task=generacoupon.generacoupon'); ?>"
      method="post" name="generaCouponForm" id="adminForm" class="form-validate">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="username">Partita Iva:</label>
        <div class="col-sm-6">
            <input required placeholder="Partita IVA dell'azienda" type="number" class="form-control" id="username"
                   name="username">
            <small id="piva-msg"> Inserisici la partita iva dell'azienda</small>
        </div>
        <div class="col-sm-3">
            <button type="button" id="confirm_piva" class="btn btn-sm"> Conferma P. iva</button>
            <button type="button" id="change_piva" class="btn btn-sm"> Reset</button>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_company_opt" for="ragioneSociale">Ragione Sociale:</label>
        <div class="col-sm-9">
            <input required disabled placeholder="Ragione sociale dell'azienda" type="text"
                   class="form-control company_opt" id="ragione_sociale"
                   name="ragione_sociale">
        </div>
    </div>
    <div class="form-group row">
        <label disabled class="col-sm-3 col-form-label disabled lbl_company_opt" for="email">Email:</label>
        <div class="col-sm-9">
            <input required disabled placeholder="Email del referente aziendale" type="email"
                   class="form-control company_opt" id="email"
                   name="email">
        </div>
    </div>
    <div class="form-group row">
        <label disabled class="col-sm-3 col-form-label disabled lbl_company_opt" for="ateco">ATECO:</label>
        <div class="col-sm-9">
            <input disabled placeholder="Codice ATECO" type="text" class="form-control company_opt" id="ateco"
                   name="ateco">
        </div>
    </div>
    <hr>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="corso">Corso:</label>
        <div class="col-sm-9">
            <select required disabled required placeholder="Corso" type="text" class="form-control cpn_opt"
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
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="vendor">Piattaforma:</label>
        <div class="col-sm-9">
            <select disabled required placeholder="Piattaforma" class="form-control cpn_opt" id="vendor" name="vendor">
                <?php foreach ($this->societa_venditrici as $s) { ?>
                    <option value="<?php echo $s->value; ?>">
                        <?php echo $s->text ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="qty">Quantità:</label>
        <div class="col-sm-9">
            <input class="cpn_opt" disabled required placeholder="Numero di coupon da generare" type="number"
                   class="form-control" id="qty" min="1"
                   name="qty">
        </div>
    </div>

    <?php if ($this->specifica_durata == 1) { ?>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="durata">Durata Coupon:</label>
            <div class="col-sm-9">
                <input class="cpn_opt" disabled required placeholder="Durata coupon (gg)" type="number"
                       class="form-control" id="durata" min="1"
                       name="durata">
            </div>
        </div>
    <?php } ?>
    <div class="form-group row">
        <div class="col-sm-3"><label class="col-form-label disabled lbl_cpn_opt" for="">Opzioni:</label></div>

        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="abilitato"><input class="cpn_opt" disabled
                                                                                           type="checkbox"
                                                                                           id="abilitato"
                                                                                           name="abilitato">
            Abilitati</label>

        <label class="col-sm-3 col-form-label disabled lbl_cpn_opt " for="stampatracciato"><input class="cpn_opt"
                                                                                                  disabled
                                                                                                  type="checkbox"
                                                                                                  id="stampatracciato"
                                                                                                  name="stampatracciato">
            Stampa
            tracciato</label>

        <?php if ($this->check_coupon_attestato == 1) { ?>
            <label class="col-sm-3 col-form-label disabled lbl_cpn_opt" for="attestato"><input class="cpn_opt" disabled
                                                                                               type="checkbox"
                                                                                               id="attestato"
                                                                                               name="attestato">
                Attestato</label>
        <?php } ?>
    </div>
    <div class="form-group">
        <button id="btn-genera" type="submit" class="btn-block btn">Genera</button>
    </div>
</form>

<script type="application/javascript">
    jQuery(document).ready(function () {
        _generaCoupon.init();
    });

</script>
