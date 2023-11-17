<div class="container-fluid">

    <div id="toolbar">
        <div class="form-inline" role="form">
            <div class="form-group ml-2">
                <input id="search" name="search" class="form-control" type="text" placeholder="Ricerca" style="height: inherit; line-height:24px; font-family: sans-serif; display:block;" />
            </div>
        </div>
    </div>

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
        data-sort-name="data_inizio"
        data-sort-order="asc"
        data-page-list="[10, 25, 50, 100, 200, Tutte]">
            <thead>
                <tr>
                    <th data-field="titolo" data-sortable="true">Corso</th>
                    <th data-field="data_inizio" data-sortable="true">Data inizio</th>
                    <th data-field="report_extra" data-sortable="false" data-align="center">Report</th>
                </tr>
                </thead>
    </table>

    <div class="row loading text-center" style="display: none;">
        <div class="col-md-12">
            <i class="fa fa-circle-o-notch fa-spin fa-2x"></i> Attendere...
        </div>
    </div>

</div>

<script type="text/javascript">

    var pTable = jQuery('#tbl_eventi');

    jQuery(function () {

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
            id_unita: {
                css: {background: '#00313C', color: '#fff'}
            },
            titolo: {
                css: {background: '#00313C', color: '#fff'}
            },
            data_inizio: {
                css: {background: '#00313C', color: '#fff'}
            },
            report_extra: {
                css: {background: '#00313C', color: '#fff'}
            }
        }[column.field]
    }

    function loadingTemplate(message) {
        return '<i class="fa fa-spinner fa-spin fa-fw fa-2x"></i>';
    }

    function ajaxRequest(params) {

        // aggiunto tipologia socio
        var pGruppoIDPiattaforma = '<?php echo $this->group_id_piattaforma; ?>';
        params.data.gruppo_id_piattaforma = pGruppoIDPiattaforma;

        var pSearch = jQuery('#search').val();
        if (pSearch != "")
            params.data.search = pSearch;

        // data you may need
        console.log(params.data);

        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_gglms&task=api.get_rows_partecipazione_corsi",
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

