<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Genera Coupon</h1>";


?>

<!--<form method="post" name="form_genera_coupon">-->

    <form action="<?php echo JRoute::_('index.php?option=com_gglms&task=generaCoupon.generaCoupon.php'); ?>"
          method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-group">
        <label for="ragioneSociale">Ragione Sociale:</label>
        <input placeholder="Ragione sociale dell'azienda" type="text" class="form-control" id="ragioneSociale">
    </div>
    <div class="form-group">
        <label for="username">Username:</label>
        <input required placeholder="Partita IVA dell'azienda" type="text" class="form-control" id="username">
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input required placeholder="Email del referente aziendale" type="email" class="form-control" id="email">
    </div>
    <div class="form-group">
        <label for="ateco">ATECO:</label>
        <input required placeholder="Codice ATECO" type="text" class="form-control" id="ateco">
    </div>
    <div class="form-group">
        <label for="corso">Corso:</label>
        <select required placeholder="Corso" type="text" class="form-control" id="corso">
            <?php foreach ($this->lista_corsi as $c){ ?>
            <option value="<?php echo $c->value; ?>">
                <?php echo $c->text ?>
            </option>
            <?php }?>

        </select>
    </div>
    <div class="form-group">
        <label for="qty">Quantità:</label>
        <input required placeholder="Numero di coupon da produrre" type="number" class="form-control" id="qty">
    </div>
    <div class="form-group">
        <label for="vendor">Venditrice:</label>
        <select required placeholder="Venditrice" class="form-control" id="vendor">
            <?php foreach ($this->societa_venditrici as $s){ ?>
                <option value="<?php echo $s->value; ?>">
                    <?php echo $s->text ?>
                </option>
            <?php }?>

        </select>
    </div>
    <div class="form-group">
        <label for="attestato"><input type="checkbox" id="attestato"> Attestato</label>
        <label for="stampatracciato"><input type="checkbox" id="stampatracciato"> Stampa tracciato</label>
    </div>


    <button type="submit" class="btn btn-default">Genera</button>
</form>


<!--<script type="application/javascript">pippo()</script>-->
