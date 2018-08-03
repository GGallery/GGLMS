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
        <div class="span12">
            <h4>ATTESTATI DI CORSI RESIDENZIALI DI:
                <span style="color: black; font-weight: bold"><?php echo $this->utente?></span>
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="span6" style="font-size: medium; color:#0095ad; font-weight: bold">DOCUMENTAZIONE ESMA</div>
        <div class="span6" style="font-size: medium; color:#0095ad; font-weight: bold">ALTRI ATTESTATI RESIDENZIALI</div>
    </div>
    <div class="row">
        <div class="col-md-6" style="margin-left: -32px;">
            <ul class="list-group">
                <?php
                if(count($this->attestatiesma)>0) {
                    foreach ($this->attestatiesma as $attestato) { ?>
                        <li class="list-group-item">
                            <?php echo $attestato; ?>
                        </li>

                    <?php }
                }else{echo "non sono presenti attestati";}
                ?>
            </ul>
        </div>

        <div class="col-md-6">
            <ul class="list-group">
                <?php
                if(count($this->attestati)>0) {
                    foreach ($this->attestati as $attestato) { ?>
                        <li class="list-group-item" >
                            <?php echo $attestato; ?>
                        </li>

                    <?php }
                }else{echo "non sono presenti attestati";}
                ?>
            </ul>
        </div>


    </div>
    <div>
        DATI AGGIORNATI A: <?php echo $this->_params->get('data_sync')?>
    </div>

</div>


