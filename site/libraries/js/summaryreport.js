_summaryreport = (function ($, my) {


    // todo colonna piattaforma e venditore visibile solo se tutor piattaforma

        var columns = [
            {
                field: 'coupon',
                title: 'Coupon',
                width:320

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
                title: 'Azienda',


            },
            {
                field: 'stato',
                title: 'Stato',
                // filterable:{
                //     ui: function(element){
                //         element.kendoDropDownList({
                //             dataSource: [{ value: 1, text: "completato" }, { value: false, text: "non completato" }],
                //             optionLabel: "--Select--",
                //             dataTextField: "text",
                //             dataValueField: "value"
                //         });
                //     }
                // }


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


            _kendofix();
            console.log('summary report ready');
            kendo.culture("it-IT");
            // console.log(culture.name); // outputs "en-US"
            _createGrid();
            _loadData();
        }

        function _createGrid() {
            $("#grid").kendoGrid({
                height: 550,
                sortable: true,
                resizable: true,
                groupable: true,
                filterable: {
                    mode: " row",
                    extra:false
                },
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


                })
                .then(function (data) {
                    // console.log('then', data);

                });
        }


        function _kendofix(){

            kendo.ui.Grid.prototype._positionColumnResizeHandle= function() {
                var that = this,
                    indicatorWidth = that.options.columnResizeHandleWidth,
                    lockedHead = that.lockedHeader ? that.lockedHeader.find("thead:first") : $();

                that.thead.add(lockedHead).on("mousemove" + ".kendoGrid", "th", function (e) {
                    var th = $(this);
                    if (th.hasClass("k-group-cell") || th.hasClass("k-hierarchy-cell")) {
                        return;
                    }
                    that._createResizeHandle(th.closest("div"), th);
                });
            };

        }


// public methods
        my.init = _init;

        return my;

    }

)
(jQuery, this);

