<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h3 style='text-align: center; margin-top: 5px'> Report Utenti </h3>"; ?>

<div class="mc-main">
<!--    <input id="test" />-->
    <div id="grid"></div>
</div>

<div id="cover-spin"></div>
<div id="user-details">
    <div id="user_grid"></div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function () {
         _reportkendo.init();
    });

</script>
