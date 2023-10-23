<?php

if ($this->_html != "") {
    echo $this->_html;
    return;
}

?>

<div class="container">

    <div class="mt-3 pl-3 pt-2">
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
    
    ?>

</div>

