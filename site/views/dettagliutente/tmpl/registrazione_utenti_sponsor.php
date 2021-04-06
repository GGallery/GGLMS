<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 31/03/2021
 * Time: 18:20
 */
defined('_JEXEC') or die('Restricted access');
?>

    <div class="container-fluid">

        <?php
            echo $this->_html;
        ?>

    </div>


    <script type="text/javascript">

        jQuery(function() {

            // richiesta di login oppure registrazione
            jQuery('.btn-request').on('click', function (e) {
                var pHref = jQuery(this).attr("data-ref");
                window.location.href = pHref;
            });

        });

    </script>
