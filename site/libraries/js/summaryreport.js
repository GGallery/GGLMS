_summaryreport = (function ($, my) {


        // todo colonna piattaforma e venditore visibile solo se tutor piattaforma

        var columns = [
            {
                field: 'coupon',
                title: 'Coupon',
                width: 310,
                filterable: {
                    cell: {
                        showOperators: false

                    }
                }

            },
            {
                field: 'user_',
                title: 'Utente',
                width: 200,
                filterable: {
                    cell: {
                        showOperators: false
                    }
                }

            },
            {
                field: 'titolo_corso',
                title: 'Corso',
                width: 200,
                filterable: {
                    cell: {
                        showOperators: false
                    }
                }

            },
            {
                field: 'data_creazione',
                title: 'Data Creazione',
                format: "{0: dd-MM-yyyy HH:mm}",
                width: 150,
                filterable: {
                    operators: {
                        date: {
                            lte: "Prima o uguale del",
                            gte: "Dopo o uguale del"
                            // , eq: "Uguale"
                        }
                    }
                }

            },
            {
                field: 'data_utilizzo',
                title: 'Data Utilizzo',
                width: 150,
                format: "{0: dd-MM-yyyy HH:mm}",
                filterable: {
                    operators: {
                        date: {
                            lte: "Prima o uguale del",
                            gte: "Dopo o uguale del",
                            isnull: "E' nulla",
                            isnotnull: "Non è nulla"
                            // , eq: "Uguale"
                        }
                    }
                }

            },
            {
                field: 'id_piattaforma',
                title: '',
                hidden: true

            },
            {
                field: 'id_azienda',
                title: '',
                hidden: true

            },
            {
                field: 'azienda',
                title: 'Azienda',
                width: 150,
                filterable: {
                    cell: {
                        showOperators: false
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
                                dataSource: [{value: "1", text: "Completato"}, {
                                    value: "0",
                                    text: "Non completato"
                                }, {value: "-1", text: "Liberi"}],
                                dataTextField: "text",
                                dataValueField: "value",
                                valuePrimitive: true,
                                optionLabel: 'Tutti'

                            });
                        }
                    }
                },

                template: '<span  class=" #= stato == 1 ? "glyphicon glyphicon-ok" : ( stato == 0 ) ? "glyphicon glyphicon-log-in"   : "" # "" ></span>',
                attributes: {
                    style: "text-align: center; font-size: 18px"
                }


            },
            {
                field: 'data_inizio',
                title: 'Data Inizio',
                width: 150,
                format: "{0: dd-MM-yyyy }",
                filterable: {
                    operators: {
                        date: {
                            lte: "Prima o uguale del",
                            gte: "Dopo o uguale del",
                            isnull: "E' nulla",
                            isnotnull: "Non è nulla"
                        }
                    }
                }

            },
            {
                field: 'data_fine',
                title: 'Data Fine',
                width: 150,
                format: "{0: dd-MM-yyyy}",
                filterable: {
                    operators: {
                        date: {
                            lte: "Prima o uguale del",
                            gte: "Dopo o uguale del",
                            isnull: "E' nulla",
                            isnotnull: "Non è nulla"

                        }
                    }
                }

            },
            {
                field: 'id_user',
                title: '',
                hidden: true

            },
            {
                field: 'id_corso',
                title: 'Attestati',
                width: 200,
                filterable: false,
                template: "<a href='http://gglms.base.it/home/index.php?option=com_gglms&task=attestatibulk.dwnl_attestati_by_corso&id_corso=#=id_corso#&user_id=#=id_user#' class='k-button k-grid-attestato'><span class='glyphicon glyphicon-download'></span></a>",
                attributes: {
                    style: "text-align: center"
                }


            },
            {
                field: 'venditore',
                title: 'Venditore',
                width: 100,
                hidden: false,
                filterable: {
                    cell: {
                        showOperators: false

                    }
                }

            }

        ];
        var gridDataSource = null;
        var grid = null;


        function _init() {

            _kendofix();
            console.log('summary report ready');
            kendo.culture("it-IT");
            $('#cover-spin').show(0);


            _isLoggedUser_tutorAz();
            _loadData();
        }

        function _createGrid(dataSource) {
            $("#grid").kendoGrid({
                dataSource: dataSource,
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

                        console.log('quiiiiiiii');
                        if (parseInt(gridData[i].stato) === -1 || gridData[i].stato == null) {

                            currenRow.find(".k-hierarchy-cell").html("");

                        }


                    }
                },
                detailInit: detailInit,

            });

            $('#cover-spin').hide(0);
            grid = $("#grid").data('kendoGrid');


            $("[data-text-field=coupon]").attr("placeholder", 'filtra coupon');
            $("[data-text-field=user_]").attr("placeholder", 'filtra utente');
            $("[data-text-field=titolo_corso]").attr("placeholder", 'filtra corso');
            $("[data-text-field=azienda]").attr("placeholder", 'filtra azienda');

            // .k-state-default
            // grid.autoFitColumn(0);
            // grid.autoFitColumn(1);
        }

        function _loadData() {


            // $('#cover-spin').show(0);
            $.when($.get("index.php?option=com_gglms&task=summaryreport.getData"))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('data', data);

                    //data type of the field {number|string|boolean|date} default is string
                    gridDataSource = new kendo.data.DataSource({
                        data: data,
                        schema: {
                            model: {
                                fields: {
                                    coupon: {type: "string"},
                                    data_creazione: {type: "date"},
                                    data_utilizzo: {type: "date"},
                                    id_corso: {type: "number"},
                                    titolo_corso: {type: "string"},
                                    id_azienda: {type: "number"},
                                    azienda: {type: "string"},
                                    id_piattaforma: {type: "number"},
                                    piattaforma: {type: "string"},
                                    user_: {type: "string"},
                                    stato: {type: "number"},
                                    data_inizio: {type: "date"},
                                    data_fine: {type: "date"}
                                }
                            }
                        }
                    });
                    _createGrid(gridDataSource);


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

        function _isLoggedUser_tutorAz() {

            $.when($.get("index.php?option=com_gglms&task=summaryreport.is_tutor_aziendale"))
                .done(function (data) {


                    if (data == "true") {
                        // utente collegato ? tutor aziendale nascondo le info relative a venditore
                        columns = $.each(columns, function (i, item) {
                            if (item.field === 'venditore') {
                                item.hidden = true;
                            }
                        });

                    }


                })
                .fail(function (data) {
                    console.log('fail', data);
                    $('#cover-spin').hide(0);

                })
                .then(function (data) {
                    // console.log('then', data);
                    // $('#cover-spin').hide(0);
                });

        }

        function detailInit(e) {
            var detailRow = e.detailRow;
            var data = e.data;

            var params = {
                id_user: data.id_user,
                id_gruppo_azienda: data.id_azienda,
                id_corso: data.id_corso
            };

            $.when($.get("index.php?option=com_gglms&task=summaryreport.getDetails", params))
                .done(function (data) {

                    data = JSON.parse(data);

                    $('#cover-spin').show(0);
                    //data type of the field {number|string|boolean|date} default is string
                    var detailsDataSource = new kendo.data.DataSource({
                        data: data
                    });

                    $("<div/>").appendTo(e.detailCell).kendoGrid({
                        dataSource: detailsDataSource,
                        scrollable: false,
                        sortable: true,
                        pageable: true,
                        columns: [
                            {field: "id_contenuto", title: "", hidden: true},
                            {field: "titolo_contenuto", title: "Contenuto", width:300},
                            {field: "last_visit", title: "Ultima visita", width:100},
                            {field: "permanenza",
                                title: "Permanenza",
                                width:100,
                                template: '<span>#= _secondsTohhmmss(permanenza) #</span>'
                            },
                            {field: "visualizzazioni", title: "Visualizzazioni", width:80}
                        ]
                    });


                })
                .fail(function (data) {
                    console.log('fail', data);
                    $('#cover-spin').hide(0);


                })
                .then(function (data) {
                    $('#cover-spin').hide(0);
                    // console.log('then', data);

                });
            //
            //   $("<div/>").appendTo(e.detailCell).kendoGrid({
            //     dataSource: {
            //         type: "odata",
            //         transport: {
            //             read: "http://gglms.base.it/home/index.php?option=com_gglms&task=tracklog.getDetails&id_user=894&id_gruppo_azienda=91&id_corso=248"
            //         },
            //         serverPaging: true,
            //         serverSorting: true,
            //         serverFiltering: true,
            //         pageSize: 7
            //         // ,filter: { field: "EmployeeID", operator: "eq", value: e.data.EmployeeID }
            //     },
            //     scrollable: false,
            //     sortable: true,
            //     pageable: true,
            //     columns: [
            //         { field: "id_contenuto", title:"", hidden: true},
            //         { field: "titolo_contentuto", title:"Contenuto"},
            //         { field: "last_visit", title:"Ultima visita" },
            //         { field: "permanenza", title:"Permanenza" },
            //         { field: "visualizzazioni", title: "Visualizzazioni"}
            //     ]
            // });
        }

        function _secondsTohhmmss(totalSeconds) {
            var totalSeconds = parseInt(totalSeconds);
            if (totalSeconds == 0) {
                result = 0;
            } else {
                var hours = Math.floor(totalSeconds / 3600);
                var minutes = Math.floor((totalSeconds - (hours * 3600)) / 60);
                var seconds = totalSeconds - (hours * 3600) - (minutes * 60);

                // round seconds
                seconds = Math.round(seconds * 100) / 100;

                var result = (hours < 10 ? "0" + hours : hours);
                result += ":" + (minutes < 10 ? "0" + minutes : minutes);
                result += ":" + (seconds < 10 ? "0" + seconds : seconds);
            }

            return result;
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

