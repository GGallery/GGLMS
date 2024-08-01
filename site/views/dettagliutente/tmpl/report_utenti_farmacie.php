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
        <div class="row" style="margin-bottom: 30px; display:flex; align-items: center; justify-content: space-between;">
            <div class="form-group row col-md-3">
                <label for="startdate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_FROM_SHORT') ?>:</label>
                <input type="date" id="startdate" min="" style="height: inherit; line-height:24px; font-family: sans-serif; display:block;"/>
            </div>

            <div class="form-group row col-md-3">
                <label for="enddate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_TO_SHORT') ?>:</label>
                <input type="date" id="enddate" min="" style="height: inherit; line-height:24px; font-family: sans-serif; display:block;"/>
            </div>


            <div class="form-group col-md-3">
                <label for="export_csv"><br></label>
                <button type="button" id="export_csv" class="form-group btn" style="background-color: #17a2b8;border: none;font-size: 16px; font-weight : bold ;">GENERA REPORT</button>
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

                if (pStart && pEnd) {
                    var startDate = new Date(pStart);
                    var endDate = new Date(pEnd);
                    
                    //controllo che la data di inizio sia inferiore
                    if (startDate > endDate) {
                        customAlertifyAlertSimple("La data di inizio dovrebbe essere inferiore alla data di fine");
                        showLoading('h');
                        clearShowing();
                        return;
                    } else {
                        // Calcolo la differenza in mesi
                        var monthsDifference = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());
                        
                        // controllo su i giorni in caso il giorno della data di fine sia inferiore al giorno della data di inizio
                        if (endDate.getDate() < startDate.getDate()) {
                            monthsDifference--;
                        }

                        if (monthsDifference >= 3) {
                            customAlertifyAlertSimple("L'intervallo massimo è di 3 mesi");
                            showLoading('h');
                            clearShowing();
                            return;
                        }
                    }
                    //window.open("index.php?option=com_gglms&task=api.set_report_farmacie_queue&dal=" + pStart + "&al=" + pEnd );

                    let params = {};
                    params.dal = pStart;
                    params.al = pEnd;
                    jQuery.ajax({
                        type:"GET",
                        url:"index.php?option=com_gglms&task=api.set_report_farmacie_queue",
                        data:params,
                        dataType:"json",
                        success:function(data){
                            if (typeof data != "object") {
                                showLoading('h');
                                customAlertifyAlertSimple(data);
                                return;
                            }
                            else if (typeof data.error != "undefined") {
                                showLoading('h');
                                customAlertifyAlertSimple(data.error);
                                return;
                            } else {
                            customAlertifyAlertSimple("La richiesta di generazione del report è stata presa in carico, riceverai una mail su "+data.success+" quando sarà pronto");
                            showLoading('h');
                            clearShowing();
                        }},
                        error:function(error){
                            console.log(error)
                            customAlertifyAlertSimple(error.error);
                            showLoading('h');
                            clearShowing();
                        }

                        }
                    )
                }


            });
        });

    </script>

</div>
