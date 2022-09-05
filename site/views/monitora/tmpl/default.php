<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1>".  JText::_('COM_GGLMS_MONITORA_COUPON_TITLE'). "</h1>"; ?>

<style>
    table.table-bordered thead th {
        background-color: white !important;
    }
</style>

<div class="container-fluid">
    <div id="toolbar" class="container-fluid" style="border:1px solid blue;border-radius: 4px;">
        <h4 class="text-left ml-2 mt-0" style="color: #325482"><?php echo  JText::_('COM_GGLMS_GLOBAL_FILTRI') ?></h4>
         <div class="row">
            <div class="form-group col-sm-3">
                <label for="id_gruppo_azienda"><?php echo  JText::_('COM_GGLMS_GLOBAL_COMPANY') ?>:</label>
                <select  placeholder="<?php echo  JText::_('COM_GGLMS_GLOBAL_COMPANY') ?>" id="id_gruppo_azienda" name="id_gruppo_azienda" class="form-control">
                    <?php foreach ($this->societa as $s) { ?>
                        <option value="<?php echo $s->id; ?>">
                            <?php echo $s->title ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="id_gruppo_corso"><?php echo  JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label>
                <select placeholder="Corso" class="form-control" id="id_gruppo_corso" name="id_gruppo_corso">
                    <option value="-1"><?php echo  JText::_('COM_GGLMS_GLOBAL_ALL_CORSI') ?></option>
                    <?php foreach ($this->lista_corsi as $s) { ?>
                        <option value="<?php echo $s->value; ?>">
                            <?php echo $s->text ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="stato_coupon"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO') ?>:</label>
                <select placeholder="<?php echo  JText::_('COM_GGLMS_GLOBAL_STATO') ?>" class="form-control" id="stato_coupon" name="stato_coupon">
                    <option value="-1"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_ANY') ?></option>
                    <option value="0"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_LBERI') ?></option>
                    <option value="1"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_ASSEGNATI') ?></option>
                    <option value="2"><?php echo  JText::_('COM_GGLMS_GLOBAL_STATO_SCADUTI') ?></option>
                </select>
            </div>

             <div class="form-group col-sm-2">
                 <label for="export_csv"><br></label>
                 <button type="button" id="export_csv" class="form-group btn mt-4" style="background-color: #325482;border: none"><?php echo  JText::_('COM_GGLMS_GLOBAL_EXPORT_CSV') ?></button>
             </div>
         </div>
         <div class="row">

             <div class="form-group col-sm-3">
                 <label for="coupon"><?php echo  JText::_('COM_GGLMS_MONITORA_FIND_COUPON') ?>:</label>
                 <input placeholder="<?php echo  JText::_('COM_GGLMS_MONITORA_FIND_COUPON') ?>" class="form-control" type="text" id="coupon" name="coupon">
             </div>

            <div id="venditore1" class="form-group col-sm-3">
                <label for="venditore"><?php echo  JText::_('COM_GGLMS_MONITORA_FIND_SELLER') ?>:</label>
                <input placeholder="<?php echo  JText::_('COM_GGLMS_MONITORA_FIND_SELLER') ?>" class="form-control" type="text" id="venditore" name="venditore">
            </div>

            <div class="form-group col-sm-3">
                <label for="utente"><?php echo  JText::_('COM_GGLMS_MONITORA_FIND_USER') ?>:</label>
                <input placeholder="<?php echo  JText::_('COM_GGLMS_MONITORA_FIND_USER') ?>" class="form-control" type="text" id="utente" name="utente">
            </div>
             <div class="form-group col-sm-1">
                 <label for="btn_cerca"><br></label>
                 <button type="button" id="btn_cerca" class="form-group btn mt-4" style="background-color: #325482;border: none"><?php echo  JText::_('COM_GGLMS_GLOBAL_SEARCH') ?></button>
             </div>
             <div class="form-group col-sm-1">
                 <label for="btn_reset"><br></label>
                 <button type="button" id="btn_reset" class="form-group btn mt-4" style="background-color: #325482;border: none"><?php echo  JText::_('COM_GGLMS_GLOBAL_RESET') ?></button>
             </div>
         </div>
    </div>
    <table
            id="tbl_monitora"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-side-pagination="server"
            data-pagination="true"
            data-show-export="true"
            data-page-list="[10, 25, 50, 100, 200, All]"
    >
        <thead>
        <tr>
            <th data-field="coupon" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_COUPON'); ?></th>
            <th data-field="user" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_USER'); ?></th>
            <th data-field="creation_time" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_CREATION_DATE'); ?></th>
            <th data-field="data_utilizzo" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_USE_DATE'); ?></th>
            <th data-field="corso" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_CORSO'); ?></th>
            <th data-class="showColumn" data-field="venditore" data-sortable="true"><?php echo JText::_('COM_GGLMS_GLOBAL_VENDITORE'); ?></th>
        </tr>
        </thead>
    </table>


    <script type="text/javascript">

        var pTable = jQuery('#tbl_monitora');

        function ajaxRequest(params) {

            var pTable = jQuery('#tbl_monitora');


            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=monitoracoupon.get_var_monitora_coupon",
                // You are expected to receive the generated JSON (json_encode($data))

                success: function (data) {

                    if (data == "") {
                        alert("Parameter object is not valid");
                        return;
                    }
                    else {

                        data = JSON.parse(data);

                        // gestione tutor aziendale
                        var pIsTutor = data.is_tutor_aziendale;

                        if (pIsTutor == true) {
                            // utente collegato ? tutor aziendale nascondo le info relative a venditore

                            pTable.bootstrapTable('hideColumn', 'venditore');
                            $("#venditore1").hide();
                        }

                        // } else {
                        //     // columns = columns.filter(function (obj) {
                        //     //     return obj.field !== 'mailto';
                        //     // });
                        // }
                        //commentata perche la funzione c gia su summary coupon

                        // gestione disattiva coupon
                        // var pDisattivaCoupon = data.show_disattiva_coupon;
                        //
                        // if (parseInt(pDisattivaCoupon) != 1) {
                        //     // columns = columns.filter(function (obj) {
                        //     //     return obj.field !== 'disattiva';
                        //     // });
                        // }

                    }

                    // $.each(columns, function (i, item) {
                    //
                    //     $(".header-row").append('<th>' +Joomla.JText._(item.title)   + '</th>')
                    // });

                    // $("#form-monitora-coupon select").change(_loadData);
                    // $('#coupon').keyup(_delay(_loadData, 500));
                    // $('#venditore').keyup(_delay(_loadData, 500));
                    // $('#utente').keyup(_delay(_loadData, 500));
                    // $("#btn_monitora_coupon").click(_loadData);
                    // $('.button-page').on('click', _pagination_click);
                    //$('.disattiva-coupon').on('click', _disattivaCoupon);

                     _loadData(params);


                },
                error: function (er) {
                    params.error(er);
                }
            });
        }

        function _loadData(params) {


            // preparo i params
            var pAzienda = jQuery('#id_gruppo_azienda').val().trim();
            params.data.id_gruppo_azienda = parseInt(pAzienda);

            var pCorso = jQuery('#id_gruppo_corso').val().trim();
            params.data.id_gruppo_corso = parseInt(pCorso);

            var pStato = jQuery('#stato_coupon').val().trim();
            params.data.stato = parseInt(pStato);

            params.data.coupon = jQuery('#coupon').val().trim();

            params.data.venditore = jQuery('#venditore').val().trim();

            params.data.utente = jQuery('#utente').val().trim();


            jQuery.ajax({
                type: "GET",
                url: "index.php?option=com_gglms&task=monitoracoupon.getcouponlist",
                // You are expected to receive the generated JSON (json_encode($data))
                data: params.data,
                dataType: "json",
                success: function (data) {

                    // controllo errore
                    if (typeof data != "object") {
                        params.error(data);
                        return;
                    }
                    else if (typeof data.error != "undefined") {
                        params.error(data.error);
                        return;
                    }
                    else {
                        params.success({
                            // By default, Bootstrap table wants a "rows" property with the data
                            "rows": data.rows,
                            // You must provide the total item ; here let's say it is for array length
                            "total": data.rowCount


                        })

                    }
                },
                error: function (er) {
                    params.error(er);
                }

            });

        }

        //esporto i dati in csv
        jQuery('#export_csv').on('click', function() {
            pTable.tableExport({
                type: 'csv',
                escape: false,
                fileName: 'monitora_coupon',
                exportDataType: 'all',
                refreshOptions: {
                    exportDataType: 'all'
                }
            });
        });

       //ricerca su una colonna
        jQuery('#btn_cerca').on('click',function () {
                var pUtente = jQuery('#utente').val();
                var pCoupon = jQuery('#coupon').val();
                var pVenditore = jQuery('#venditore').val();
                var pCorso = jQuery('#id_gruppo_corso').val();


                pTable.bootstrapTable('filterBy', {
                    coupon: pCoupon,
                    user: pUtente,
                    corso: pCorso,
                    venditore: pVenditore,

                })
        })

        //resetta la tabella
          jQuery('#btn_reset').on('click',function () {
            jQuery('#coupon').val('');
            jQuery('#utente').val('');
            jQuery('#venditore').val('');
            jQuery('#stato_coupon').val('-1');
            jQuery('#id_gruppo_corso').val('-1');

            pTable.bootstrapTable('refresh');
        })



    </script>
</div>


<!--<div id="cover-spin"></div>-->
<!--    <script type="application/javascript">-->
<!--        jQuery(document).ready(function () {-->
<!--            _monitoraCoupon.init();-->
<!--        });-->
<!---->
<!--    </script>-->

<!-- Modal Corso Disabilitato-->
<!--<div id="modalMail" class="modal fade" role="dialog" data-backdrop="static">-->
<!--    <div class="modal-dialog">-->
<!---->
<!--       Modal content-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <button type="button" class="close" data-dismiss="modal">&times;</button>-->
<!--                <h4 class="modal-title">Invia Coupon </h4>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <form>-->
<!--                    <div class="form-group">-->
<!--                        <label class="col-sm-1 col-form-label" for="to">Da:</label>-->
<!--                        <div class="col-sm-11">-->
<!--                            <input type="email" id="to" name="to">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <label class="col-sm-1 col-form-label" for="from">A:</label>-->
<!--                        <div class="col-sm-11">-->
<!--                            <input type="email" id="from" name="from">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <label class="col-sm-1 col-form-label" for="subject">Obj:</label>-->
<!--                        <div class="col-sm-11">-->
<!--                            <input type="text" id="subject" name="subject">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!---->
<!--                        <div class="col-sm-12">-->
<!--                            <div style ="min-height: 350px" contenteditable="true" class="form-control"  id="body" name="body">-->
<!--                            </div>-->
<!---->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!--                </form>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button  class="btn" type="button" id="btn_invia_coupon">Invia</button>-->
<!--                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--    </div>-->
<!--</div>-->


