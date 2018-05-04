<?php
JHtml::_('bootstrap.modal');
?>
<style>
    .stato1{
        color:red;
    }
</style>
<div id="contenitoreprincipale" style="width: 100%">

    <div class="row">
        <div class="span12"><h4>ATTESTATI DI CORSI RESIDENZIALI DI:
                <span style="color: black; font-weight: bold"><?php echo $this->utente?></span>
            </h4>
        </div>
    </div>
    <?php
    if(count($this->attestati)>0) {
        foreach ($this->attestati as $attestato) { ?>
            <div>
                <?php echo $attestato; ?>
            </div>

        <?php }
    }else{echo "non sono presenti attestati";}
    ?>

    <div>
        DATI AGGIORNATI A: <?php echo $this->_params->get('data_sync')?>
    </div>

</div>


