<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/controllers/libretto.php';
JHtml::_('bootstrap.modal');
//$user = JFactory::getUser();
//var_dump($user);
$libretto=new gglmsControllerLibretto();
$data=$libretto->get_libretto();
$utente=$libretto->get_user();


?>

<div id="contenitoreprincipale" style="width: 100%">

    <div class="row">
        <div class="span12"><h4>LIBRETTO FORMATIVO DI:
            <span style="color: black; font-weight: bold"><?php echo $utente['nome']?> <?php echo $utente['cognome']?></span>
            </h4>
        </div>
    </div>
    <?php foreach ($data['rows'] as $row) {?>
        <div class="card text-center">
            <div class="card-header">

            </div>
            <div class="card-block">
                <h4 class="card-title"><?php echo $row['corso'] ?></h4>
                <p class="card-text">data fine corso:<?php echo $row['data_fine'] ?> &nbsp &nbsp
                    durata del corso:<?php echo $row['durata'] ?> giorni</p>

            </div>
            <div class="card-footer text-muted">

            </div>
        </div>

        <div><hr style="border-top: 1px dashed #8c8b8b;"></div>
    <?php } ?>
    <div style="width: 100%"><a href="index.php?option=com_gglms&task=pdf.generate_libretto&user_id=<?php echo $utente['id_user']; ?>"  class="btn btn-primary" style="margin-left: 45%">SCARICA PDF</a></div>


</div>


<script type="text/javascript">


</script>
