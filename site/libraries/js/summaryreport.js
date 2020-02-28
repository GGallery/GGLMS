_summaryreport = (function ($, my) {


        var columns = [
            {
                field: 'coupon',
                title: 'Coupon'

            },
            {
                field: 'titolo_corso',
                title: 'Corso'

            },
            {
                field: 'id_corso',
                title: '',
                hidden: true

            },
            {
                field: 'data_creazione',
                title: 'Data Creazione'

            },
            {
                field: 'data_utilizzo',
                title: 'Data Utilizzo'

            },

            {
                field: 'user_',
                title: 'Utente'

            },
            {
                field: 'id_piattaforma',
                title: '',
                hidden: true

            },
            {
                field: 'azienda',
                title: 'Azienda'

            },
            {
                field: 'stato',
                title: 'Stato'

            },
            {
                field: 'data_inizio',
                title: 'Data Inizio'

            },
            {
                field: 'data_fine',
                title: 'Data Fine'

            }];


        var gridDataSource = null;


        function _init() {


            console.log('summary report ready');

            // var culture = kendo.culture("IT");
            kendo.culture("it-IT");
            // console.log(culture.name); // outputs "en-US"
            _createGrid();
            _loadData();
        }

        function _createGrid() {
            $("#grid").kendoGrid({
                dataSource: {},
                height: 550,
                groupable: true,
                sortable: true,
                filtrable:true,
                pageable: {
                    refresh: true,
                    pageSizes: true,
                    buttonCount: 5
                },
                columns: columns
            });
        }


        function _loadData(sender) {


            // $('#cover-spin').show(0);
            $.when($.get("index.php?option=com_gglms&task=summaryreport.getData"))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('data', data);

                    gridDataSource = new kendo.data.DataSource({data: data});
                    var grid = $("#grid").data("kendoGrid");
                    grid.setDataSource(gridDataSource);

                })
                .fail(function (data) {
                    console.log('fail', data);
                    $('#cover-spin').hide(0);

                })
                .then(function (data) {
                    // console.log('then', data);
                    $('#cover-spin').hide(0);
                });
        }


// public methods
        my.init = _init;

        return my;

    }

)
(jQuery, this);

