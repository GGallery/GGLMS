<?php
defined('_JEXEC') or die;
?>

<style>
    table.table-bordered thead th {
        background-color: white !important;
        text-align: center;
    }

    .table th, .table td {
        text-align: center !important;
        line-height: 14px !important;
    }


</style>

<div class="container-fluid">

    <h3 class="text-left ml-2 mt-5 my-5 " style="color: #325482"><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR26') ?></h3>
    <table
            id="tbl_pagamenti"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-side-pagination="server"
            data-pagination="true"
            data-page-size="5"
            data-force-hide="true"
            data-page-list="[5, 10, 20, 50, 100, All]"
    >
        <thead>
        <tr>
            <th data-field="numero_fattura" data-sortable="true"><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR20') ?></th>
            <th data-field="data_pagamento" data-sortable="true"><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR21') ?></th>
            <th data-field="tipo_pagamento" data-sortable="true"><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR22') ?></th>
            <th data-field="totale" data-sortable="true"><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR23') ?></th>
            <th data-field="stato" data-sortable="true"><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR24') ?></th>
            <th data-field="fattura" ><?php echo  JText::_('COM_REGISTRAZIONE_ASAND_STR25') ?></th>
        </tr>
        </thead>
    </table>

<script type="text/javascript">


    function ajaxRequest(params) {

        // preparo i params
        var user_id = '';

         user_id =  <?php echo $this->id_utente; ?>;


        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_fatture",
            // You are expected to receive the generated JSON (json_encode($data))
            data: {"user_id" : user_id},
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



   </script>
</div>
