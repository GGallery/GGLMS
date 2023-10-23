<?php

if ($this->_html != "") {
    echo $this->_html;
    return;
}

?>

<div class="container">

    <div class="mt-3 pl-3 pt-2">
        <!-- Presidente -->
        <h4>
            <span class="text-uppercase">Risultato votazioni</span>
        </h4> 
    </div>

    <?php 
    
    if (is_array($this->votingResults[1]) 
        && isset($this->votingResults[1]['presidente']) 
        && count($this->votingResults[1]['presidente']))
        echo outputHelper::get_voti_candidato($this->votingResults[1]['presidente']);

    if (is_array($this->votingResults[2]) 
        && isset($this->votingResults[2]['medico']) 
        && count($this->votingResults[2]['medico']))
        echo outputHelper::get_voti_candidato($this->votingResults[2]['medico'], 'Medico');

    if (is_array($this->votingResults[2]) 
        && isset($this->votingResults[2]['infermiere']) 
        && count($this->votingResults[2]['infermiere']))
        echo outputHelper::get_voti_candidato($this->votingResults[2]['infermiere'], 'Infermiere');

    if (is_array($this->votingResults[2]) 
        && isset($this->votingResults[2]['farmacista']) 
        && count($this->votingResults[2]['farmacista']))
        echo outputHelper::get_voti_candidato($this->votingResults[2]['farmacista'], 'Farmacista');

    if (is_array($this->votingResults[2]) 
        && isset($this->votingResults[2]['dietista']) 
        && count($this->votingResults[2]['dietista']))
        echo outputHelper::get_voti_candidato($this->votingResults[2]['dietista'], 'Dietista');
    
    /*

    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Presidente -->
        <h4 class="border-bottom">
            <b>Risultato votazioni Presidente</b>
        </h4>   

        <div class="d-flex flex-column mb-3">
            <?php foreach($this->votingResults[1]['presidente'] as $singleVote){
                ?>
                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><?php echo $singleVote['nome'] ?></div>
                    <div class="p-2"><?php echo $singleVote['cognome'] ?></div>
                    <div class="p-2 font-weight-bold"><?php echo $singleVote['numero_voti'] ?> <?php echo $singleVote['numero_voti'] > 1 ? 'voti' : 'voto'; ?> </div>
                </div>
            <?php
            } ?>
        </div>
    </div>

    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Medico -->
        <h4 class="border-bottom">
            <b>Risultato votazioni Consigliere medico</b>
        </h4>   

        <div class="d-flex flex-column mb-3">
            <?php foreach($this->votingResults[2]['medico'] as $singleVote){
                ?>
                
                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><?php echo $singleVote['nome'] ?></div>
                    <div class="p-2"><?php echo $singleVote['cognome'] ?></div>
                    <div class="p-2 font-weight-bold"><?php echo $singleVote['numero_voti'] ?> <?php echo $singleVote['numero_voti'] > 1 ? 'voti' : 'voto'; ?> </div>
                </div>
                
            <?php
            } ?>
        </div>
    </div>

    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Infermieire -->
        <h4 class="border-bottom">
            <b>Risultato votazioni Consigliere medico</b>
        </h4>   

        <div class="d-flex flex-column mb-3">
            <?php foreach($this->votingResults[2]['infermiere'] as $singleVote){
                ?>
                
                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><?php echo $singleVote['nome'] ?></div>
                    <div class="p-2"><?php echo $singleVote['cognome'] ?></div>
                    <div class="p-2 font-weight-bold"><?php echo $singleVote['numero_voti'] ?> <?php echo $singleVote['numero_voti'] > 1 ? 'voti' : 'voto'; ?> </div>
                </div>
                
            <?php
            } ?>
        </div>
    </div>

    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Farmacista -->
        <h4 class="border-bottom">
            <b>Risultato votazioni Consigliere medico</b>
        </h4>   

        <div class="d-flex flex-column mb-3">
            <?php foreach($this->votingResults[2]['farmacista'] as $singleVote){
                ?>
                
                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><?php echo $singleVote['nome'] ?></div>
                    <div class="p-2"><?php echo $singleVote['cognome'] ?></div>
                    <div class="p-2 font-weight-bold"><?php echo $singleVote['numero_voti'] ?> <?php echo $singleVote['numero_voti'] > 1 ? 'voti' : 'voto'; ?> </div>
                </div>
                
            <?php
            } ?>
        </div>
    </div>

    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Dietista -->
        <h4 class="border-bottom">
            <b>Risultato votazioni Consigliere medico</b>
        </h4>   

        <div class="d-flex flex-column mb-3">
            <?php foreach($this->votingResults[2]['dietista'] as $singleVote){
                ?>
                
                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><?php echo $singleVote['nome'] ?></div>
                    <div class="p-2"><?php echo $singleVote['cognome'] ?></div>
                    <div class="p-2 font-weight-bold"><?php echo $singleVote['numero_voti'] ?> <?php echo $singleVote['numero_voti'] > 1 ? 'voti' : 'voto'; ?> </div>
                </div>
                
            <?php
            } ?>
        </div>
    </div>

    */?>

</div>

