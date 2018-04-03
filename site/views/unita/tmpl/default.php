<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
$count = 0;

if($this->unita->_params->get('abilita_breadcrumbs' , 1))
    echo $this->loadTemplate('breadcrumb');

echo "<h1>".$this->unita->titolo."</h1>";

echo "<h5>".$this->unita->descrizione."</h5>";


if($this->sottounita) {

    if ($this->unita->_params->get('titolo_unita_visibile')) {
        echo "<h5>".$this->unita->_params->get('nomenclatura_unita')."</h5>";
    }

    echo "<div id='unita' class='box g-grid'>";

    foreach ($this->sottounita as $unita) {


        if($this->unita->_params->get('visualizza_solo_mieicorsi') && !$unita->access()){
//            echo "non puoi vedere". $unita->titolo;
        }
        elseif(($unita->data_inizio<=date("Y-m-d") && $unita->data_fine>=date("Y-m-d"))||$unita->is_corso!=1 || $this->unita->_params->get('filtro_date_corsi')==0){

            try {
                $count++;

                //var_dump($this->unita->isUnitacompleta());

                ?>
                <div class="g-block <?php echo $this->unita->_params->get('larghezza_box_unita') ?> ">
                    <div class="g-block interno">

                        <?php
                        if (file_exists('../mediagg/images/unit/' . $unita->id . '.jpg'))
                            $img = '../mediagg/images/unit/' . $unita->id . '.jpg';
                        else
                            $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';

                        $unitaObj = new gglmsModelUnita();
                        if ($unitaObj->isUnitacompleta($unita->id))

                            echo '<div class="corner corner_green"></div>';
                        else
                            echo '<div class="corner corner_yellow"></div>';

                        ?>

                        <a href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $unita->alias) ?>">
                            <div class="rt-image" style="background-image:url(<?php echo $img; ?>)">
                            </div>
                        </a>


                        <a href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $unita->alias) ?>">
                            <div class="title boxinfo_unita "><b><?php echo $unita->titolo; ?></b></div>
                        </a>


                        <?php
                        if ($this->unita->_params->get('visibilita_durata_unita')) {
                            ?>
                            <div class='g-grid'>
                                <div class="g-box size-50">Durata: <?php echo $unita->get_durata_unita($unita->id); ?></div>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                </div>
                <?php
            }catch(Exception $ex){
                echo $ex->getMessage();
            }
        }
    }
    echo "</div>";
    echo "<hr>";
}

if($this->contenuti) {
    if ($this->unita->_params->get('titolo_moduli_visibile')) {
        echo "<h5>".$this->unita->_params->get('nomenclatura_moduli')."</h5>";
    }
    echo "<div id='contenuti' class='box g-grid'>";
    foreach ($this->contenuti as $contenuto) {
        $count++;
        ?>

        <div class="g-block <?php echo $this->unita->_params->get('larghezza_box_contenuti') ?> ">
            <div class="g-block interno">

                <?php
                if (file_exists('../mediagg/contenuti/' . $contenuto->id . '/' . $contenuto->id . '.jpg'))
                    $img = '../mediagg/contenuti/' . $contenuto->id . '/' . $contenuto->id . '.jpg';
                else
                    $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';

                $stato = $contenuto->getStato();

                if($contenuto->getPropedeuticita()) {

                    if($stato->completato)
                        echo '<div class="corner corner_green"></div>';
                    else
                        echo '<div class="corner corner_yellow"></div>';
                    ?>

                    <a <?php echo $contenuto->getUrlLink(); ?>/>
                    <div class="rt-image" style="background-image:url(<?php echo $img; ?>)"></div>
                    </a>

                    <a <?php echo $contenuto->getUrlLink();  ?>/>
                    <div class="title"><b><?php echo $contenuto->titolo; ?></b></div>
                    </a>


                    <div class="boxinfo_contenuti">
                        <?php

                        if (in_array($contenuto->tipologia, explode(",",$this->unita->_params->get('visibilita_durata'))))
                        {
                            ?>
                            <div class='g-grid'>
                                <div class="g-box size-100">Durata: <?php echo  $this->unita->convertiDurata($contenuto->durata); ?></div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class='g-grid'>
                            <div class="g-box size-100">Stato: <?php echo $stato->descrizione; ?></div>
                        </div>

                    </div>
                    <?php
                }

                else
                {

                    ?>

                    <div class="corner corner_red"></div>

                    <div class="rt-image trasparenza_inaccessibilita" title="Completa i contenuti propedeutici" style="background-image:url(<?php echo $img; ?>)"></div>
                    <div class="title"><b><?php echo $contenuto->titolo; ?></b></div>

                    <div class="boxinfo">
                        <?php
                        if (in_array($contenuto->tipologia, explode(",",$this->unita->_params->get('visibilita_durata'))))
                        {
                            ?>
                            <div class='g-grid'>
                                <div class="g-box size-100">Durata: <?php echo  $this->unita->convertiDurata($contenuto->durata); ?></div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class='g-grid'>
                            <div class="g-box size-100">Stato: Non ancora disponibile</div>
                        </div>

                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    echo "</div>";
}

if(!$count)
    echo "<h3>Non ci sono contenuti per te visualizzabili in questa unità</h3>";









