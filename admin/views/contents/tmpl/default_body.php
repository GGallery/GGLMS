<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item): ?>
    <tr class="row<?php echo $i % 2; ?>">
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
