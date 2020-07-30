<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>".  JText::_('COM_GGLMS_SUMMARY_REPORT'). "</h1>"; ?>

<div class="mc-main">
    <div id="grid"></div>
</div>

<div id="cover-spin"></div>
<div id="notification"></div>
<div id="user-details">

    <div id="user_grid"></div>
</div>

<script type="application/javascript">

    jQuery(document).ready(function () {
         _summaryreport.init();
    });

</script>
