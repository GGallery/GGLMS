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
    <tr>

        <td width="25%">
            <?php echo $item->titolo; ?>
        </td>

        <td width="25%">
            <?php echo $item->stato; ?>

        </td>
        <td width="10%">
            <?php echo $item->data; ?>
        </td>
        <td width="40%" style="text-align: left;">
            <a href="#" class="btn btn-info plusminus">+</a><br>
            <span class="details"><?php foreach ($item->logcontenuti as $log){
                echo $log["data_accesso"]." - ".$log["permanenza"]." - ".$log["permanenza_conteggiabile"]."<br>";
            }; ?></td>
        </td>
    </tr>

<?php  endforeach; ?>
<script type="text/javascript">
    jQuery(".plusminus").click(function(event){
        if(jQuery(event.target).text()=="+"){
            jQuery(event.target).text("-")
        }else{
            jQuery(event.target).text("+")
        }
        jQuery(event.target).parent().find(".details").toggle();
    });

</script>