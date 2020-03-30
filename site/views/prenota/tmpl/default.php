<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>
<h3> Prenota Coupon </h3>

<div class="info-corso">
    <p class="main-info"><strong> TITOLO: </strong>  <b> <?= $this->info_corso["titolo_corso"] ?> </b></br>
   <strong> CODICE CORSO: </strong>  <b> <?= $this->info_corso["codice_corso"] ?> </b></p>
<!--   <?//= $this->info_corso["descrizione_corso"] ?> -->
</div>

<div class="mc-main">
    <div id="grid"></div>


    <div id="wrapper">
        <form autocomplete="off" id="form-genera-coupon"
              action="<?php echo('index.php?option=com_gglms&task=prenotacoupon.prenotacoupon'); ?>"
              method="post" name="prenotaCouponForm" id="prenotaCouponForm" class="form-validate">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="yes_no">Sei associato
                    a <?= $this->info_piattaforma["name"] ?> ? *</label>
                <div class="col-sm-9">
                    <input type="radio" name="yes_no" value="false" checked> No </input>
                    <input type="radio" name="yes_no" value="true"> Sì </input>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="qty" required>Quanti Coupon? *</label>
                <div class="col-sm-9">
                    <input style="width: 50%" id="qty" type="number" min="1"
                           placeholder="Inserisci il numero dei coupon per ottenere il prezzo">
                    <!--                    <button  id="btn_calcola"  disabled class="k-primary k-state-disabled" type="button" >Calcola Prezzo</button>-->

                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="prezzo">Prezzo: </label>
                <div class="col-sm-9">
                    <span id="price"></span>
                </div>
            </div>
            <hr>
            <h5> Dati per la fatturazione </h5>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="username">Partita Iva: *</label>
                <div class="col-sm-9">
                    <input required placeholder="Partita IVA dell'azienda" type="number" class="form-control" id="username"
                           name="username">
                    <small class="validation-lbl" id="piva-msg"> Inserisici la partita iva dell'azienda</small>
                </div>
<!--                <div class="col-sm-4">-->
<!--                    <button type="button" id="confirm_piva" class="btn btn-sm">Conferma</button>-->
<!--                    <button type="button" id="change_piva" class="btn btn-sm"> Reset</button>-->
<!--                </div>-->

            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label  lbl_company_opt" for="ragioneSociale">Ragione Sociale: *</label>
                <div class="col-sm-9">
                    <input required  placeholder="Ragione sociale dell'azienda" type="text"
                           class="form-control company_opt" id="ragione_sociale"
                           name="ragione_sociale">
                    <small class="validation-lbl" id="societa-msg"> Inserisici la ragione sociale dell'azienda</small>
                </div>
            </div>
            <div class="form-group row">
                <label disabled class="col-sm-3 col-form-label  lbl_company_opt" for="email">Email referente
                    Aziendale: *</label>
                <div class="col-sm-9">
                    <input required  placeholder="Email del referente aziendale" type="email"
                           class="form-control company_opt" id="email"
                           name="email">

                    <small class="validation-lbl" id="email-msg"> Inserisici l'email del referente aziendale</small>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label  lbl_company_opt" for="ateco">ATECO: *</label>
                <div class="col-sm-9">
                    <input  placeholder="Codice ATECO" type="text" class="form-control company_opt" id="ateco"
                           name="ateco">
                    <small class="validation-lbl" id="ateco-msg"> Inserisici il codice ATECO dell'azienda</small>
                </div>
            </div>
            <div class="form-group">
                <button id="btn-prenota" type="submit" disabled class="btn-block btn">Prenota Coupon</button>
            </div>
        </form>

    </div>
</div>


<script type="application/javascript">

    var data = '<?= json_encode($this->prezzi) ?>';
    var piattaforma = '<?= json_encode($this->info_piattaforma) ?>';

    jQuery(document).ready(function () {
        _prenotaCoupon.init(data, piattaforma);
    });

</script>
