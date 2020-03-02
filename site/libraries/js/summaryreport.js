_summaryreport = (function ($, my) {


        // todo colonna piattaforma e venditore visibile solo se tutor piattaforma
        //todo datasource schema anc custom filters and format per le date

        var columns = [
            {
                field: 'coupon',
                title: 'Coupon',
                width: 320

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

            },
            {
                field: 'id_user',
                title: '',
                hidden:true

            },
            {
                field: 'id_corso',
                title: 'Attestati',
                template: "<a href='http://gglms.base.it/home/index.php?option=com_gglms&task=attestatibulk.dwnl_attestati_by_corso&id_corso=#=id_corso#&user_id=#=id_user#' class='k-button k-grid-attestato'><span class='glyphicon glyphicon-download'></span></a>",


            }

            ];

        var gridDataSource = null;
        var grid = null;

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
                dataBound: function(e) {

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
                        else{
                            editButton.attr('href','http:\\google.com');
                        }
                    }
                }

            });


            grid = $("#grid").data('kendoGrid');
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

        function downloadAttestato(e) {

            e.preventDefault();

            var dataItem = this.dataItem($(e.currentTarget).closest("tr"));

            console.log('you clicked downaload atetstato',dataItem);
        }


// public methods
        my.init = _init;

        return my;

    }

)
(jQuery, this);

