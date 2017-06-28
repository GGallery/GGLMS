<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
$data_decode = json_decode(base64_decode($this->data));
 
?>

<h3 class="alert alert-error">Il codice fiscale inserito (<b><?php echo strtoupper($data_decode->codicefiscale); ?></b>) non risulta valido!</h3>
<p>Per procedere è necessario correggerlo, puoi farlo direttamente qui sotto</p>

<div class="g-grid">
    <div class="g-block size-20">


        <form action="index.php" method="post">

            <input type="text" name="cfcorretto" id="cf" value="" size="16">
            <input type="hidden" name="data" value="<?php echo $this->data; ?>">
            <input type="hidden" name="option" value="com_gglms">
            <input type="hidden" name="task" value="gglms.updatecf">

            <button type="submit" >Conferma</button>

        </form>
    </div>

</div>