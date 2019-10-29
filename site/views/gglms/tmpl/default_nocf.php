<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
$data_decode = json_decode(base64_decode($this->data));

?>

<h3 class="alert alert-error">Il codice fiscale inserito da
    <?php echo $data_decode->nome . ' ' . $data_decode->cognome ?> non risulta valido!
    Per procedere puoi inviargli una mail  <a href=mailto:<?php echo strtoupper($data_decode->email); ?> > cliccando qui </a>
</h3>

<!--<div class="g-grid">-->
<!---->
<!--</div>-->
