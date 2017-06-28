<?php
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

echo "<h1>".$this->contenuto->titolo."</h1>";
?>

<div class="contenuto_testuale">
    <?php echo $this->contenuto->descrizione; ?>
</div>
