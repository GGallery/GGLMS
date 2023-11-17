<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 14/02/2023
 * Time: 13:15
 */
echo "<h1>".  JText::_('COM_GGLMS_DETTAGLI_UTENTE_DETTAGLI_STR38'). "</h1>";

?>

<div class="container-fluid">
    <div id="toolbar" class="container-fluid" style="border:1px solid blue;border-radius: 4px;">
        <h4 class="text-left ml-2 mt-0" style="color: #325482; margin-bottom: 30px !important; margin-top: 40px !important;"><?php echo  JText::_('COM_GGLMS_GLOBAL_FILTRI') ?></h4>
        <div class="row" style="margin-bottom: 30px;">
            <div class="form-group col-md-3">
                <label for="startdate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_FROM_SHORT') ?>:</label>
                <input type="date" id="startdate" min="" style="width: 250px;"/>
            </div>

            <div class="form-group col-md-3">
                <label for="enddate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_TO_SHORT') ?>:</label>
                <input type="date" id="enddate" min="" style="width: 250px;"/>
            </div>


            <div class="form-group col-md-3">
                <label for="export_csv"><br></label>
                <button type="button" id="export_csv" class="form-group btn" style="background-color: #17a2b8;border: none;font-size: 16px; font-weight : bold ;">SCARICA REPORT</button>
            </div>
        </div>

    </div>

    <div class="row loading text-center" style="display: none;">-->
        <div class="col-xs-12">
            <i class="fa fa-circle-o-notch fa-spin"></i> Caricamento...
        </div>
    </div>


    <script type="text/javascript">

        function showLoading(w) {

            if (w == 's')
                jQuery('.loading').show();
            else
                jQuery('.loading').hide();

        }

        function clearShowing() {
            jQuery('.to_show').hide();
        }

        jQuery('.to_show').hide();

        jQuery(function() {


            jQuery('#enddate').on('change', function (e) {

                var pEnd = jQuery(this).val();
                var pStart = jQuery('#startdate').val();

                showLoading('s');
                if (pStart == ""
                    || pStart == 0) {
                    customAlertifyAlertSimple('Nessun data inizio selezionata');
                    showLoading('h');
                    clearShowing();
                    return;
                }

                if (pEnd == ""
                    || pEnd == 0) {
                    customAlertifyAlertSimple('Nessun data fine selezionata');
                    showLoading('h');
                    clearShowing();
                    return;
                }
                showLoading('h');


            });

            // clicca bottone report
            jQuery('#export_csv').on('click', function (e) {

                var pStart = jQuery('#startdate').val();
                var pEnd = jQuery('#enddate').val();

                window.open("index.php?option=com_gglms&task=api.get_report_per_farmacie&dal=" + pStart + "&al=" + pEnd, "_blank");

            });
        });

    </script>

</div>
