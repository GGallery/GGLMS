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
        <div class="span12"><h4>REPORT FORMATIVO DI:
                <span style="color: black; font-weight: bold"><?php echo $this->utente['nome']?> <?php echo $this->utente['cognome']?></span>
            </h4>
        </div>
    </div>
    <?php
      if($this->data['rows']) {
          foreach ($this->data['rows'] as $row) { ?>
              <div class="card text" style="margin-top: 10px;">
                  <div class="card-header">
                  </div>
                  <div class="card-block">
                      <h6 class="card-title">CORSO:&nbsp<?php echo $row['corso'] ?></h6>
                      <span class="card-text">data fine corso:<?php echo $row['data_superamento'] ?> &nbsp &nbsp
                    stato del corso:&nbsp</span>
                      <span class="stato<?php echo $row['stato'] ?>"><?php if ($row['stato'] == 1) {
                              echo 'completato';
                          } else {
                              echo 'non completato';
                          } ?></span>&nbsp &nbsp&nbsp &nbsp
                      <?php if ($row['stato'] == 0) {
                          echo ' <span class="card-text">stato completamento:' . $row['percentuale_completamento'] . '%</span>';
                      } ?>

                      <?php if ($row['stato'] == 1){ ?><span class="card-text">
                    scarica il relativo attestato cliccando sull'icona qui a fianco
                          <!--<a href="index.php?option=com_gglms&task=reportutente.generateAttestato&unita_id=<?php echo $row['id_corso'] ?>&user_id=<?php echo $this->_filterparam->user_id ?>&data_superamento=<?php echo $row['data_superamento'] ?>"-->

 <a href="index.php?option=com_gglms&task=pdf.generateAttestato&content=<?php echo $row['attestato_id']; ?>" ><img
             src="components/com_gglms/libraries/images/icona_pdf.png" style="width:5%;"></a>

                          <?php } ?>
                  </div>
              </div>
          <?php }
      }else{echo "non sono presenti attestati finali";}?>

    <?php if($this->_params->get('ulteriori_attestati')){?>
        <h6 class="card-title">ULTERIORI ATTESTATI</h6>
        <?php
        if($this->data['attestati_intermedi']) {
            foreach ($this->data['attestati_intermedi'] as $row) { ?>
                <div class="card text" style="margin-top: 10px;">
                    <div class="card-header">
                        <?php echo $row['titolo']; ?>
                    </div>
                    <div class="card-block">

             <span class="card-text">scarica il relativo attestato cliccando sull'icona qui a fianco
                     <a href="index.php?option=com_gglms&task=pdf.generateAttestato&content=<?php echo $row['id']; ?>"><img
                                 src="components/com_gglms/libraries/images/icona_pdf.png" style="width:5%;"></a>


                    </div>
                </div>
            <?php }
        }else{echo "non sono presenti attestati intermedi";}
    }?>
    <div>
        DATI AGGIORNATI A: <?php echo $this->_params->get('data_sync')?>
    </div>

</div>


