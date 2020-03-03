_summaryreport = (function ($, my) {


        // todo colonna piattaforma e venditore visibile solo se tutor piattaforma
        //todo datasource schema anc custom filters and format per le date

        var columns = [
            {
                field: 'coupon',
                title: 'Coupon',
                width: 256,
                filterable: {
                    cell: {
                         showOperators: false

                    }
                }

            },
            {
                field: 'user_',
                title: 'Utente'

            },
            {
                field: 'titolo_corso',
                title: 'Corso'

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
                field: 'id_piattaforma',
                title: '',
                hidden: true

            },
            {
                field: 'azienda',
                title: 'Azienda',
                cell: {
                    showOperators: false,
                    template: function (cell) {


                        cell.element.kendoDropDownList({
                            dataSource: grid.dataSource.view(),
                            // optionLabel: "--Select--",
                            dataTextField: "text",
                            dataValueField: "value",
                            change: function (e) {
                                // console.log('eeeeeeeeeee', e);
                                console.log('azienda', this.value());
                            }
                        });
                    }
                }


            },
            {
                field: 'stato',
                title: 'Stato',
                width: 150,
                filterable: {
                    cell: {
                        showOperators: false,
                        template: function (cell) {
                            cell.element.kendoDropDownList({
                                dataSource: [{value: "1", text: "Completato"}, {value: "0", text: "Non completato"}],

                                dataTextField: "text",
                                dataValueField: "value",
                                valuePrimitive: true,
                                optionLabel: 'Tutti'

                            });
                        }
                    }
                },
                template: '<span type="checkbox" class=" #= stato == 1 ? "glyphicon glyphicon-ok" : "" # "" ></span>'


            },
            {
                field: 'data_inizio',
                title: 'Data Inizio'

            },
            {
                field: 'data_fine',
                title: 'Data Fine'

            },
            {
                field: 'id_user',
                title: '',
                hidden: true

            },
            {
                field: 'id_corso',
                title: 'Attestati',
                filterable:false,
                template: "<a href='http://gglms.base.it/home/index.php?option=com_gglms&task=attestatibulk.dwnl_attestati_by_corso&id_corso=#=id_corso#&user_id=#=id_user#' class='k-button k-grid-attestato'><span class='glyphicon glyphicon-download'></span></a>",


            }

        ];
        var gridDataSource = null;
        var grid = null;

        function boolFilterTemplate(input) {
            input.kendoDropDownList({
                dataSource: {
                    data: [
                        {text: "True", value: '0'},
                        {text: "False", value: '1'}
                    ]
                },
                dataTextField: "text",
                dataValueField: "value",
                valuePrimitive: true,
                optionLabel: "All"
            });
        }

        function _init() {

            _kendofix();
            console.log('summary report ready');
            kendo.culture("it-IT");
            $('#cover-spin').show(0);

            _createGrid();
            _loadData();
        }

        function _createGrid() {
            $("#grid").kendoGrid({
                toolbar: ["excel"],
                height: 550,
                sortable: true,
                resizable: true,
                groupable: true,
                selectable: true,
                filterable: {
                    mode: " row",
                    extra: false
                },
                pageable: {
                    refresh: true,
                    pageSizes: true,
                    buttonCount: 5
                },
                columns: columns,
                dataBound: function (e) {

                    // bottone scarica attestato  visibile solo se corso è completato
                    var grid = $("#grid").data("kendoGrid");
                    var gridData = grid.dataSource.view();

                    for (var i = 0; i < gridData.length; i++) {
                        var currentUid = gridData[i].uid;
                        var editButton = $(currenRow).find(".k-grid-attestato");
                        if (parseInt(gridData[i].stato) !== 1) {
                            var currenRow = grid.table.find("tr[data-uid='" + currentUid + "']");
                            editButton.hide();
                        }

                    }
                }

            });


            grid = $("#grid").data('kendoGrid');
            // grid.autoFitColumn(0);
            // grid.autoFitColumn(1);
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
                    $('#cover-spin').hide(0);
                    // console.log('then', data);

                });
        }

// fix per chrome perchè abbiamo una versione con un bug, mostra la  maniglia resize column
        function _kendofix() {

            kendo.ui.Grid.prototype._positionColumnResizeHandle = function () {
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

