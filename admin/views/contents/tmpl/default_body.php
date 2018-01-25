<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
$saveOrder = $listOrder == 'm.ordinamento';

?>

<?php if($this->items!=false):?>

    <?php foreach ($this->items as $i => $item): ?>

    <tr class="row<?php echo $i % 2; ?>">
        <td class="order nowrap center hidden-phone">

                <?php
                $iconClass = '';
                if (!$saveOrder || $this->state->get('filter.search')!=null) {//la seconda condizione rende attiva l'icona d&d solo se il campo cerca è vuoto
                    $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                }
                ?>
                <span class="sortable-handler <?php echo $iconClass ?>">
                     <span class="icon-menu"></span>
                 </span>
                <?php if ($saveOrder) : ?>
                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->id; ?>" class="width-20 text-area-order " />
                <?php endif; ?>

        </td>
        <td>
            <?php echo $item->id; ?>
        </td>
        <td>
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>

        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option=com_gglms&task=content.edit&id=' . $item->id); ?>">

                <?php
                $imgurl="../../mediagg/contenuti/". $item->id ."/" . $item->id .".jpg";
                if(file_exists($imgurl))
                    echo "<img width='70px' src='$imgurl'/>";
                else
                    echo "<img width='70px' src='components/com_gglms/images/immagine_non_disponibile.png'/>";
                ?>

                <?php echo $item->titolo; ?>
            </a>
        </td>
        
        <td>
            <?php if($item->pubblicato)
                echo '<span class="icon-publish"> </span>';
            else
                echo '<span class="icon-minus-sign"> </span>';
            ?>
        </td>

        <td>
            <?php //echo substr($item->descrizione,0, 150 )."...  "  ; ?>
        </td>

    </tr>
<?php endforeach; ?>
<?php else:?>
non ci sono contenuti che soddisfano i filtri immessi
<?php endif;?>
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />


