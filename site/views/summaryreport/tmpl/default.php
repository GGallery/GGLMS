<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>".  JText::_('COM_GGLMS_SUMMARY_REPORT'). "</h1>"; ?>

<div class="mc-main">
    <div id="grid"></div>
</div>

<div id="cover-spin"></div>
<div id="notification"></div>
<div id="user-details">

    <div id="user_grid"></div>
</div>

<script type="application/javascript">

    var hide_columns_var = <?php echo $this->hide_columns_var; ?>;
    var label_coupon = '<?php echo $this->label_coupon; ?>';
    var label_nome = '<?php echo $this->label_nome; ?>';
    var label_cognome = '<?php echo $this->label_cognome; ?>';
    var label_codice_fiscale = '<?php echo $this->label_codice_fiscale; ?>';
    var label_corso = '<?php echo $this->label_corso; ?>';
    var label_azienda = '<?php echo $this->label_azienda; ?>';
    var label_stato = '<?php echo $this->label_stato; ?>';
    var label_attestato = '<?php echo $this->label_attestato; ?>';
    var label_venditore = '<?php echo $this->label_venditore; ?>';
    var colonna_datetime = '<?php echo $this->colonna_datetime; ?>';
    
    jQuery(document).ready(function () {
         _summaryreport.init();
    });

</script>
