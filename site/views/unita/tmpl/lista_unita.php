<?php
// no direct access


defined('_JEXEC') or die('Restricted access');




/*
<ul itemscope="" itemtype="https://schema.org/BreadcrumbList" class="breadcrumb">
    <li class="active"> <span class="divider icon-location"></span> </li>

    <li itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
        <a itemprop="item" href="/home/formazione/i-miei-corsi.html" class="pathway"><span itemprop="name">Corsi</span></a>
            <span class="divider icon-chevron-right">  </span>
            <meta itemprop="position" content="1">
    </li>

    <li itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="active">
        <span itemprop="name"> LA FARMACIA </span>
        <meta itemprop="position" content="2">
    </li>
</ul>
*/
$counter = 1;
$bread_count = count($this->breadcrumbs);

var_dump($this->breadcrumbs);

echo <<<HTML
<ul itemscope="" itemtype="https://schema.org/BreadcrumbList" class="breadcrumb">
    <li class="active">
        <span class="divider icon-location"></span>
    </li>
HTML;
foreach ($this->breadcrumbs as $key => $element) {

    $element = (array) $element;
    $cls_pathway = !is_null($element['link']) ? 'pathway' : '';
    $cls_active = ($counter <= ($bread_count-1)) ? '' : 'active';

    $element_link = "";
    if (!is_null($element['link']))
        $element_link = <<<HTML
        <a itemprop="item" href="{$element['link']}" class="{$cls_pathway}">
            <span itemprop="name">{$element['name']}</span>
        </a>
HTML;
    else
        $element_link = <<<HTML
        <span itemprop="name">{$element['name']}</span>
HTML;

    echo <<<HTML
        <li itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="{$cls_active}">
            {$element_link}
HTML;
        if ($counter <= ($bread_count-1))
            echo <<<HTML
            <span class="divider icon-chevron-right">  </span>
            <meta itemprop="position" content="{$counter}">
HTML;
        echo <<<HTML
        </li>
HTML;

    $counter++;

}

echo <<<HTML
    </ul>
HTML;


?>



<div id="unita" class="row">

    <?php
    if (!is_array($this->box_corsi)
        || count($this->box_corsi) == 0) : ?>

    <p class="my-0">
        <small><?php echo  JText::_('COM_GGLMS_UNITA_NON_DISPONIBILE') ?></small>
    </p>

    <?php else:

        foreach ($this->box_corsi as $key_corso => $sub_corso) {

            // cast array per object
            $sub_corso = (array) $sub_corso;

            $obj_unita = $this->model_unita->getUnita($sub_corso['id']);

            if ($obj_unita->_params->get('visualizza_solo_mieicorsi') && !$obj_unita->access())
                continue;
            else if (
                ($obj_unita->is_corso == 1 && $obj_unita->is_visibile_today($obj_unita))
                || $obj_unita->is_corso != 1) {

                    $is_unit_completed = $obj_unita->isUnitacompleta($obj_unita->id);
                    $corso_class = $obj_unita->get_access_class($obj_unita);
                    $corso_is_disabled = $corso_class == 'disabled';

                    // se webinar verifico se la data di inizio è superiore ad oggi, nel caso non visualizzo il box
                    if (isset($obj_unita->modalita)
                    && $obj_unita->modalita == 1
                    && !is_null($obj_unita->data_inizio)
                    && $obj_unita->data_inizio != ""
                    && $obj_unita->data_inizio < date('Y-m-d')
                    )
                    continue;

                    // verifico se è un webinar, quindi coloro il box
                    $box_bg = "";
                    if (isset($obj_unita->modalita)
                        && $obj_unita->modalita == 1)
                        $box_bg = 'style="background: #B7D7D5 !important;"';

                    $u_path = '/mediagg/images/unit/' . $obj_unita->id . '.jpg';
                    $u_file = $_SERVER['DOCUMENT_ROOT'] . $u_path;
                    // carico l'immagine per indirizzo assoluto
                    if (file_exists($u_file)) {
                        $img = $this->url_base . $u_path;
                    } else
                        $img = 'components/com_gglms/libraries/images/immagine_non_disponibile.png';

                    $box_corner = '';
                    if ($is_unit_completed)
                        $box_corner = 'green';
                    else if ($corso_is_disabled)
                        $box_corner = 'grey';
                    else
                        $box_corner = 'yellow';

                    $unita_route = JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $obj_unita->alias);
                    $lbl_durata = JText::_('COM_GGLMS_GGLMS_DURATA');

                    echo <<<HTML
                     <div class="col-sm-3 py-3 d-flex">
                        <div class="card d-flex">
                        <div class="corner corner_{$box_corner}"></div>
HTML;
                    if (!$corso_is_disabled)
                        echo <<<HTML
                        <!-- visualizzazione corsi abilitati-->
                        <a href="{$unita_route}">
                            <img class="card-img-top img-fluid"  src="{$img}" alt="{$img}" />
                        </a>

                        <div class="card-body my-0 px-0 py-0">
                            <a href="{$unita_route}">
                                <div class="card-title text-center my-0" {$box_bg}>
                                    <p class="my-0"><b>{$obj_unita->titolo}</b></p>
                                </div>
                            </a>
                        </div>
HTML;
                    else
                        echo <<<HTML
                         <!--visualizzazione corsi abilitati-->
                        <a data-toggle="modal" data-target="#exampleModal">
                            <img class="card-img-top img-fluid"  src="{$img}" alt="{$img}">
                        </a>
                        <div class="card-body my-0 px-0 py-0">
                            <a>
                                <div class="card-title text-center my-0" {$box_bg}>
                                    <p class="my-0"><b>{$obj_unita->titolo}</b></p>
                                </div>
                            </a>
                        </div>
HTML;
                    if ($obj_unita->_params->get('visibilita_durata_unita'))
                        echo <<<HTML
                        <div class="card-footer px-0 py-0">
                            <p class="my-0">
                                <small>{$lbl_durata}
                                        : {$obj_unita->get_durata_unita($obj_unita->id)}</small></p>
                        </div>
HTML;
                echo <<<HTML
                    </div>
                </div>
HTML;

            } // corso visibile
            else {
                continue;
            }

        } // loop box

        ?>



    <?php endif; ?>

</div>

