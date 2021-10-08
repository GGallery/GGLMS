<div class="container-fluid">

    <div id="toolbar">
        <div class="form-inline" role="form">
            <div class="form-group">
                <span>Modalità: </span>
                <select id="modalita" class="form-control">
                    <option value="">Tutte</option>
                    <?php
                    $categorie_evento = utilityHelper::get_categorie_evento();
                    foreach ($categorie_evento as $id_cat => $text_cat) {
                        echo <<<HTML
                                <option value="{$id_cat}">{$text_cat}</option>
HTML;
                    }
                    ?>
                </select>
            </div>
            <div class="form-group ml-2">
                <input id="search" name="search" class="form-control" type="text" placeholder="Ricerca" style="height: inherit;" />
            </div>

        </div>
    </div>
    <small>
        <table
            id="tbl_eventi"
            data-toggle="table"
            data-toolbar="#toolbar"
            data-ajax="ajaxRequest"
            data-side-pagination="server"
            data-pagination="true"
            data-toolbar-align="right"
            data-pagination-h-align="left"
            data-pagination-detail-h-align="right"
            data-loading-template="loadingTemplate"
            data-header-style="headerStyle"
            data-row-style="rowStyle"
            data-sort-name="data"
            data-sort-order="asc"
            data-page-list="[10, 25, 50, 100, 200, Tutte]"
        >
            <thead>
            <tr>
                <th data-field="evento" data-sortable="true">Evento</th>
                <th data-field="area" data-sortable="true">Area</th>
                <th data-field="data" data-sortable="true">Data</th>
                <th data-field="orario" data-sortable="true">Orario</th>
                <th data-field="destinatari" data-sortable="true">Destinatari</th>
                <th data-field="modalita" data-sortable="true">Modalit&agrave;</th>
                <th data-field="sede" data-sortable="true">Sede</th>
                <th data-field="report_extra" data-sortable="false" data-align="center">Report</th>
            </tr>
            </thead>
        </table>
    </small>

    <div class="row loading text-center" style="display: none;">
        <div class="col-md-12">
            <i class="fa fa-circle-o-notch fa-spin fa-2x"></i> Attendere...
        </div>
    </div>

</div>

<script type="text/javascript">

    var pTable = jQuery('#tbl_eventi');

    jQuery(function () {

        jQuery('#ok').on('click', function (event) {
            jQuery('#tbl_eventi').bootstrapTable('refresh');
        });

        jQuery('#modalita').on('change', function (event) {
            jQuery('#tbl_eventi').bootstrapTable('refresh');
        });

        jQuery('#search').on('keyup', function (event) {
            jQuery('#tbl_eventi').bootstrapTable('refresh');
        });

    });

    function rowStyle(row, index) {

        if (index % 2 !== 0) {
            return {
                css: {
                    background: '#e3f4f3',
                    color: '#6F6F6F'
                }
            }
        }
        return {
            css: {
                background: '#ffffff',
                color: '#6F6F6F'
            }
        }
    }

    function headerStyle(column) {
        return {
            evento: {
                css: {background: '#00313C', color: '#fff'}
            },
            area: {
                css: {background: '#00313C', color: '#fff'}
            },
            data: {
                css: {background: '#00313C', color: '#fff'}
            },
            orario: {
                css: {background: '#00313C', color: '#fff'}
            },
            destinatari: {
                css: {background: '#00313C', color: '#fff'}
            },
            modalita: {
                css: {background: '#00313C', color: '#fff'}
            },
            sede: {
                css: {background: '#00313C', color: '#fff'}
            },
            report_extra: {
                css: {background: '#00313C', color: '#fff'}
            }
        }[column.field]
    }

    function showLoading(w) {

        if (w == 's') {
            jQuery('.loading').show();
            jQuery('.fixed-table-pagination').hide();
            jQuery('#toolbar').hide();
            jQuery('#tbl_eventi').hide();
        }
        else {
            jQuery('.loading').hide();
            jQuery('.fixed-table-pagination').show();
            jQuery('#toolbar').show();
            jQuery('#tbl_eventi').show();
        }
    }

    function customAlertifyAlertSimple(pMsg) {
        alertify.alert()
            .setting({
                'title': 'Attenzione!',
                'label':'OK',
                'message': pMsg
            }).show();
    }

    function loadingTemplate(message) {
        return '<i class="fa fa-spinner fa-spin fa-fw fa-2x"></i>';
    }

    function ajaxRequest(params) {

        // aggiunto tipologia socio
        var pDominio = '<?php echo $this->dominio; ?>';
        params.data.dominio = pDominio;

        var pModalita = jQuery('#modalita').val();
        if (pModalita != "")
            params.data.modalita = parseInt(pModalita);

        var pSearch = jQuery('#search').val();
        if (pSearch != "")
            params.data.search = pSearch;

        params.data.before_today = true;

        // data you may need
        console.log(params.data);

        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_rows_tabella_corsi",
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
                        "total": data.total_rows
                    })
                }
            },
            error: function (er) {
                params.error(er);
            }
        });
    }

</script>
