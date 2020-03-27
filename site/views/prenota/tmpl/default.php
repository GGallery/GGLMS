<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Prenota Coupon </h1> "

?>

<div class="mc-main">
    <div id="grid"></div>


    <div id="wrapper">
        <label for="yes_no">Sei associato?</label>
        <p>
            <input type="radio" name="yes_no" value="true"> Yes</input>
            <input type="radio" name="yes_no" value="false" checked >No</input>
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

    jQuery(document).ready(function () {
        _prenotaCoupon.init(data);
    });

</script>
