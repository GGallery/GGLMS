<?php
// no direct access

defined('_JEXEC') or die('Restricted access');
$count = 0;

if ($this->unita->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

if( $this->unita->id == 1 )
{echo "<h1>" . JText::_('COM_GGLMS_GLOBAL_CORSI') . "</h1>";
}
else{
    echo "<h1>" . $this->unita->titolo . "</h1>";

}


echo "<h5>" . $this->unita->descrizione . "</h5>";


if ($this->sottounita) {
    if ($this->unita->_params->get('titolo_unita_visibile')) {
        echo "<h5>" . $this->unita->_params->get('nomenclatura_unita') . "</h5>";
    }

    echo "<div id='unita' class='box g-grid'>";

    foreach ($this->sottounita as $unita) {
        if ($this->unita->_params->get('visualizza_solo_mieicorsi') && !$unita->access()) {
//            echo "non puoi vedere". $unita->titolo;
        } elseif (($unita->is_corso == 1 && $unita->is_visibile_today($unita)) || $unita->is_corso != 1) {
            try {
                $count++;
                $unitaObj = new gglmsModelUnita();

                $is_unit_completed = $unitaObj->isUnitacompleta($unita->id);
                $corso_class = $unita->get_access_class($unita);
                $corso_is_disabled = $corso_class == 'disabled';

                ?>
                <div class="g-block <?php echo $this->unita->_params->get('larghezza_box_unita') ?> <?php echo $corso_class ?>">
                    <div class="g-block interno">

                        <?php
                        // revisione caricamento immagini di background delle unità
                        /*
                        if (file_exists('../mediagg/images/unit/' . $unita->id . '.jpg'))
                            $img = '../mediagg/images/unit/' . $unita->id . '.jpg';
                        else
                            $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';
                        */
                        $u_path = '/mediagg/images/unit/' . $unita->id . '.jpg';
                        $u_file = JPATH_ROOT . $u_path;
                        // carico l'immagine per indirizzo assoluto
                        if (file_exists($u_file))
                            $img = JURI::base() . $u_path;
                        else
                            $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';

//                        $is_corso_disabled = $corso_class == 'disabled';
                        $unitaObj = new gglmsModelUnita();

                        if ($unitaObj->isUnitacompleta($unita->id))

                            echo '<div class="corner corner_green"></div>';
                        else if ($corso_is_disabled)

                            echo '<div class="corner corner_grey"></div>';
                        else
                            echo '<div class="corner corner_yellow"></div>';

                        ?>

                        <?php

                        if (!$corso_is_disabled) { ?>

                            <!-- visualizzazione corsi abilitati-->
                            <a href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $unita->alias) ?>">
                                <div class="rt-image" style="background-image:url(<?php echo $img; ?>)">
                                </div>
                            </a>


                            <a href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $unita->alias) ?>">
                                <div class="title boxinfo_unita "><b><?php echo $unita->titolo; ?></b></div>
                            </a>
                            <?php

                        } else { ?>

                            <!--visualizzazione corsi abilitati-->
                            <a data-toggle="modal" data-target="#exampleModal">
                                <div class="rt-image" style="background-image:url(<?php echo $img; ?>)">
                                </div>
                            </a>

                            <a>
                                <div class="title boxinfo_unita "><b><?php echo $unita->titolo; ?></b></div>
                            </a>
                        <?php } ?>
                        <?php
                        if ($this->unita->_params->get('visibilita_durata_unita')) {
                            ?>
                            <div class='g-grid'>
                                <div class="g-box size-50">
                                    <?php echo  JText::_('COM_GGLMS_GGLMS_DURATA') ?>: <?php echo $unita->get_durata_unita($unita->id); ?></div>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                </div>
                <?php
            } catch
            (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {

        }
    }
    echo "</div>";
    echo "<hr>";
}

if ($this->contenuti) {
    if ($this->unita->_params->get('titolo_moduli_visibile')) {
        echo "<h5>" . $this->unita->_params->get('nomenclatura_moduli') . "</h5>";
    }
    echo "<div id='contenuti' class='box g-grid'>";
    foreach ($this->contenuti as $contenuto) {
        $count++;
        ?>

        <div class="g-block <?php echo $this->unita->_params->get('larghezza_box_contenuti') ?> ">
            <div class="g-block interno">

                <?php
                // revisione caricamento immagini di background dei contenuti
                /*
                if (file_exists('../mediagg/contenuti/' . $contenuto->id . '/' . $contenuto->id . '.jpg'))
                    $img = '../mediagg/contenuti/' . $contenuto->id . '/' . $contenuto->id . '.jpg';
                else
                    $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';
                */
                $c_path = '/mediagg/contenuti/' . $contenuto->id . '/' . $contenuto->id . '.jpg';
                $c_file = JPATH_ROOT . $c_path;
                // carico l'immagine da indirizzo assoluto
                if (file_exists($c_file))
                    $img = JURI::base() . $c_path;
                else
                    $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';

                $stato = $contenuto->getStato();

                if ($contenuto->getPropedeuticita()) {

                    if ($stato->completato)
                        echo '<div class="corner corner_green"></div>';
                    else
                        echo '<div class="corner corner_yellow"></div>';
                    ?>

                    <a <?php echo $contenuto->getUrlLink(); ?>/>
                    <div class="rt-image" style="background-image:url(<?php echo $img; ?>)"></div>
                    </a>

                    <a <?php echo $contenuto->getUrlLink(); ?>/>
                    <div class="title"><b><?php echo $contenuto->titolo; ?></b></div>
                    </a>


                    <div class="boxinfo_contenuti">
                        <?php

                        if (in_array($contenuto->tipologia, explode(",", $this->unita->_params->get('visibilita_durata')))) {
                            ?>
                            <div class='g-grid'>
                                <div class="g-box size-100">
                                    <?php echo  JText::_('COM_GGLMS_GGLMS_DURATA') ?>: <?php echo $this->unita->convertiDurata($contenuto->durata); ?></div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class='g-grid'>
                            <div class="g-box size-100"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO') ?>: <?php echo $stato->descrizione; ?></div>
                        </div>

                    </div>
                    <?php
                } else {

                    ?>

                    <div class="corner corner_red"></div>

                    <div class="rt-image trasparenza_inaccessibilita" title="<?php echo  JText::_('COM_GGLMS_UNITA_COMPLETA_CONTENUTI') ?>"
                         style="background-image:url(<?php echo $img; ?>)"></div>
                    <div class="title"><b><?php echo $contenuto->titolo; ?></b></div>

                    <div class="boxinfo">
                        <?php
                        if (in_array($contenuto->tipologia, explode(",", $this->unita->_params->get('visibilita_durata')))) {
                            ?>
                            <div class='g-grid'>
                                <div class="g-box size-100">
                                    <?php echo  JText::_('COM_GGLMS_GGLMS_DURATA') ?>: <?php echo $this->unita->convertiDurata($contenuto->durata); ?></div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class='g-grid'>
                            <div class="g-box size-100"><?php echo  JText::_('COM_GGLMS_UNITA_NON_DISPONIBILE') ?></div>
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

if (!$count)
    echo "<h3>" .  JText::_('COM_GGLMS_UNITA_CONTENUTI_NON_DISPONIBILI') . "</h3>";
//    echo "<h3>Non ci sono contenuti per te visualizzabili in questa unità</h3>";
?>


<!-- Modal Corso Disabilitato-->
<div id="exampleModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo  JText::_('COM_GGLMS_UNITA_DETTAGLI_CORSO') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo  JText::_('COM_GGLMS_UNITA_CORSO_SCADUTO') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"> <?php echo  JText::_('COM_GGLMS_GLOBAL_CLOSE') ?></button>
            </div>
        </div>

    </div>
</div>


