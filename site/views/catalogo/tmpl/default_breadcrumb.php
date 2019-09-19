<ul itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumb">
    <li class="active"> <span class="divider icon-location"></span> </li>

    <?php
    end($this->breadcrumbs);
    $last_item_key   = key($this->breadcrumbs);
    prev($this->breadcrumbs);
    $penult_item_key = key($this->breadcrumbs);

    // Make a link if not the last item in the breadcrumbs
    $show_last = $this->_params->get('visualizza_ultimo');
    $show_first = $this->_params->get('visualizza_primo_item');
    $separator = "";

    // Generate the trail
    foreach ($this->breadcrumbs as $key => $item) {

        //PRIMO ELEMENTO
        if ($key == 0 ) {
            if($show_first){
                ?>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item"
                       href="<?php
                       if($this->_params->get('customizza_link_primo_item', 0)){
                       echo JRoute::_($this->_params->get('customizza_link_primo_item')) ?>"
                       class="pathway"><span itemprop="name"><?php echo $this->_params->get('customizza_testo_primo_item'); ?></span></a>
                    <?php
                    }else {
                        echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $item->alias) ?>"
                        class="pathway"><span itemprop="name"><?php echo $item->titolo; ?></span></a>
                    <?php }?>

                    <?php if (($key !== $penult_item_key) || $show_last) { ?>
                        <span class="divider icon-chevron-right"> <?php echo $separator; ?> </span>
                    <?php } ?>

                    <meta itemprop="position" content="<?php echo $key + 1; ?>">

                </li>

                <?php
            }
        }
        else {
            if ($key !== $last_item_key) {

                //ELEMENTI INTERMEDI
                ?>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item"
                       href="<?php echo JRoute::_('index.php?option=com_gglms&view=unita&alias=' . $item->alias) ?>"
                       class="pathway"><span itemprop="name"><?php echo $item->titolo; ?></span></a>

                    <?php if (($key !== $penult_item_key) || $show_last) { ?>
                        <span class="divider icon-chevron-right"> <?php echo $separator; ?> </span>
                    <?php } ?>

                    <meta itemprop="position" content="<?php echo $key + 1; ?>">

                </li>
                <?php
            }

            elseif ($show_last) {
                // ULTIMO ELEMENTO
                ?>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="active">
                    <span itemprop="name"> <?php echo $item->titolo; ?> </span>
                    <meta itemprop="position" content="<?php echo $key + 1; ?>">
                </li>
            <?php };
        }
    }?>
</ul>