<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>
<h3> Prenota Coupon  </h3>

<div class="info-corso">
    <p> Titolo corso: <b style="color:indianred"> <?=$this->prezzi["titolo"] ?></b> </p>
    <p>Codice Corso:  <b style="color:indianred"> <?=$this->prezzi["codice_corso"] ?> </b></p>
</div>


<div class="mc-main">
    <div id="grid"></div>


    <div id="wrapper">
        <label for="yes_no">Sei associato?</label>
        <p>
            <input type="radio" name="yes_no" value="false" checked >No</input>
            <input type="radio" name="yes_no" value="true"> Yes</input>
        </p>

    </div>
    <div>
        <label for="qty">Quanti Coupon?</label>
        <input id="qty" type="number" min="1"> <button id="btn_calcola">Calcola Prezzo</button>
        <div id="price"> </div>
    </div>

</div>





<script type="application/javascript">

    var data = '<?= json_encode($this->prezzi) ?>';
    var piattaforma = '<?= json_encode($this->info_piattaforma) ?>';

    jQuery(document).ready(function () {
        _prenotaCoupon.init(data, piattaforma);
    });

</script>
