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
        <div class="span12" style="padding-left:28px"><h4>HOME LEARNING DI:
                <span style="color: black; font-weight: bold"><?php echo $this->utente?></span>
            </h4>
        </div>
    </div>


    <div class="span8">
            <div class="span12">

                <table id="grid-basic" class="table table-condensed table-hover table-striped ">

                    <?php
                    if(count($this->tempi)>0) {
                        echo '<TR><TH>MESE</TH><TH>TEMPO ACCUMULATO</TH></TR>';
                        foreach ($this->tempi as $row) { ?>
                            <TR>
                                <?php echo '<TD>'.$row->mese." ".$row->anno."</TD><TD>".$row->totale.'</TD>';?>
                            </TR>

                        <?php }
                    }else{echo "non sono presenti tempi registrati";}
                    ?>

                </table>


            </div>
        dati aggiornati al mese precedente
    </div>
</div>


