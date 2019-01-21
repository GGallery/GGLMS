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
            <h4>DOCUMENTAZIONE ESMA DI:
                <span style="color: black; font-weight: bold"><?php echo $this->utente?></span>
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6" style="margin-left: -32px;">
            <ul class="list-group">
            <?php
            if(count($this->docs)>0) {
                foreach ($this->docs as $doc) { ?>
                    <li class="list-group-item">
                        <?php echo $doc; ?>
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


