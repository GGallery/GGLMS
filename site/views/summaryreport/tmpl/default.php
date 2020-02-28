<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h3 style='text-align: center; margin-top: 5px'>Monitora Coupon</h3>"; ?>

<div class="mc-main">

    <div id="grid"></div>
</div>



<div id="grid"></div>

<script type="application/javascript">
    jQuery(document).ready(function () {
         _summaryreport.init();
    });

    // jQuery("#grid").kendoGrid({
    //     columns: [
    //         { field: "name", filterable: false },
    //         { field: "age" }
    //     ],
    //     filterable: true,
    //     dataSource: [ { name: "Jane", age: 30 }, { name: "John", age: 33 }]
    // });

</script>
