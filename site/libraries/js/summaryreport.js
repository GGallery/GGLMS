_summaryreport = (function ($, my) {


        // todo
        // 1) dettagli utente --> ok
        //-------------------------------------------
        // 2) export excell formatao stato --> ok
        // -----------------------------
        // 5)coupon scaduti (calcolo ed evidenza) --> ok
        //----------------------------------------
        //?) export details se sei un tutor aziendale -->ok
        /////////////////////////////////////////////////
        // 3) cancella coupon (tutor piattaforma, coupon liberi)
        //------------------------------------------
        // 4)invia coupon per mail (tutor az, tutor p)
        //--------------------------------------------
        // 6)disiscrivi utente (solo super admin)

        var columns = [
            {
                field: 'coupon',
                title: 'Coupon',
                width: 310,
                hidden: false,
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
                hidden: false,
                filterable: {
                    cell: {
                        showOperators: false
                    }
                }

            },
            {
                field: 'id_user',
                title: 'Dettagli utente',
                width: 100,
                filterable: false,
                hidden: false,
                template: "<button class='k-button k-grid-button k-grid-user'><span class='glyphicon glyphicon-user'></span></button>",
                attributes: {
                    style: "text-align: center"
                }


            },
            {
                field: 'titolo_corso',
                title: 'Corso',
                hidden: false,
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
                hidden: false,
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
                hidden: false,
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
                hidden: false,
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
                hidden: false,
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
                hidden: false,
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
                field: 'id_corso',
                title: 'Attestati',
                hidden: false,
                width: 100,
                filterable: false,
                template: "<a href='#  window.location.hostname # /home/index.php?option=com_gglms&task=attestatibulk.dwnl_attestati_by_corso&id_corso=#=id_corso#&user_id=#=id_user#' class='k-button k-grid-button k-grid-attestato'><span class='glyphicon glyphicon-download'></span></a>",
                attributes: {
                    style: "text-align: center"
                }


            },
            {
                field: 'venditore',
                title: 'Venditore',
                width: 120,
                hidden: false,
                filterable: {
                    cell: {
                        showOperators: false

                    }
                }

            },
            {
                field: 'scaduto',
                title: 'Scaduto',
                hidden: true


            }

        ];
        var fields = {
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
            data_fine: {type: "date"},
            venditore: {type: "string"},
            scaduto: {type: "number"}
        };
        var user_details_fields = {

            "cb_datadinascita": {titolo: "Data di nascita"},
            "cb_luogodinascita": {titolo: "Luogo di nascita"},
            "cb_provinciadinascita": {titolo: "Provinxcia di nascita"},
            "cb_indirizzodiresidenza": {titolo: "Indirizzo di residenza"},
            "cb_provdiresidenza": {titolo: "Provincia di residenza"},
            "cb_cap": {titolo: "Cap"},
            "cb_telefono": {titolo: "Telefono"},
            "cb_codicefiscale": {titolo: "Codice Fiscale"},
            "cb_username": {titolo: "Username"},
            "email": {titolo: "Email"}

        };
        var widgets = {
            grid: null,
            dataSource: null,
            popup: {
                window: null,
                grid: null
            }
        };
        var logged_tutor_az = false;
        var detailExportPromises;
        var list = {};
        var export_details = false;


        function _init() {
            _isLoggedUser_tutorAz();
            _kendofix();
            console.log('summary report ready');
            kendo.culture("it-IT");
            $('#cover-spin').show(0);

            _createGrid();
            // _loadData();


            _loadData();
        }

        function _createGrid() {
            $("#grid").kendoGrid({
                // toolbar: ["excel"],
                toolbar: [
                    {
                        template: '<a class="k-button k-button-icontext k-grid-excel " href="\\#" onclick="return excel_with_details()">Current page with details</a>'
                    }, {
                        template: '<a class="k-button k-button-icontext k-grid-excel " href="\\#" onclick="return excel_all_pages()">All pages no details</a>'
                    }
                ],
                excel: {
                    allPages: false
                },
                height: 550,
                scrollable: true,
                sortable: true,
                resizable: true,
                groupable: false,
                selectable: true,
                filterable: {
                    mode: " row",
                    extra: false
                },
                pageable: true,
                columns: columns,
                dataBound: function (e) {

                    detailExportPromises = [];
                    // this.expandRow(this.tbody.find("tr.k-master-row").first());

                    // bottone scarica attestato  visibile solo se corso è completato
                    widgets.grid = $("#grid").data('kendoGrid');
                    var gridData = widgets.grid.dataSource.view();


                    for (var i = 0; i < gridData.length; i++) {
                        var currentUid = gridData[i].uid;

                        var currenRow = widgets.grid.table.find("tr[data-uid='" + currentUid + "']");
                        var attestati_btn = $(currenRow).find(".k-grid-attestato");
                        var user_btn = $(currenRow).find(".k-grid-user");

                        //hide attestati based on stato
                        if (parseInt(gridData[i].stato) !== 1) {
                            attestati_btn.hide();
                        }

                        // hide expand icon  e detail user if user not enrolled
                        if (parseInt(gridData[i].stato) === -1) {
                            currenRow.find(".k-hierarchy-cell").html("");
                            user_btn.hide();
                        }

                        // scaduti e non completati in rosso
                        if (gridData[i].scaduto && parseInt(gridData[i].scaduto) === 1 && parseInt(gridData[i].stato) !== 1) {
                            currenRow.addClass("scaduto");
                        }


                    }

                    // se è tutor aziendale esport con i dettagli --> faccio la richiesta dei dettagli mano a mano che mi arrivano
                    if (export_details) {

                        console.log('expand all rows');
                        $(".k-master-row").each(function (index) {
                            console.log('expandrow', index);
                            widgets.grid.expandRow(this);
                            widgets.grid.collapseRow(this);
                        });
                    }


                },
                detailInit: detailInit,
                excelExport: function (e) {


                    var sheet = e.workbook.sheets[0];
                    var grid = e.sender;
                    var columns = grid.columns;

                    var col_stato_index = columns.filter(function (value) {
                        return value.hidden !== true
                    }).findIndex(function (value) {
                        return value.field === "stato"
                    });


                    if (export_details) {

                        // EXPORT WITH DETAILS ---> tutor aziendale

                        e.preventDefault();
                        _exportWithDetails(e, col_stato_index);

                    } else {

                        // EXPORT STANDARD ---> tutor piattaforma

                        for (var rowIndex = 1; rowIndex < sheet.rows.length; rowIndex++) {
                            var row = sheet.rows[rowIndex];
                            _formatStatoForExport(row.cells[col_stato_index]);

                        }
                    }


                }

            });


            $('#cover-spin').hide(0);


            // bind popup
            $("#grid").on("click", ".k-grid-user", _openUserDetails);

        }

        function excel_with_details() {

            export_details = true;
            widgets.grid.setOptions({
                excel: {
                    allPages: false,
                    filterable: false,
                    collapsible: true
                }
            });

            // if (logged_tutor_az) {

            console.log('expand all rows');
            $(".k-master-row").each(function (index) {
                widgets.grid.expandRow(this);
                widgets.grid.collapseRow(this);
            });
            // }
            widgets.grid.saveAsExcel();

        }


        function excel_all_pages() {

            export_details = false;
            widgets.grid.setOptions({
                excel: {
                    allPages: true,
                    filterable: true,
                    collapsible: false
                }
            });
            widgets.grid.saveAsExcel()
        }

        function _exportWithDetails(e, col_stato_index) {

            var workbook = e.workbook;
            detailExportPromises = [];

            var masterData = e.data;

            for (var rowIndex = 0; rowIndex < masterData.length; rowIndex++) {
                exportChildData(rowIndex, masterData[rowIndex].coupon);
            }
            var col_stato_index = col_stato_index + 1; // +1 to compensate empty column for detail export


            $.when.apply(null, detailExportPromises)
                .then(function () {


                    // Get the export results.
                    var detailExports = $.makeArray(arguments);

                    // Sort by masterRowIndex.
                    detailExports.sort(function (a, b) {
                        return a.masterRowIndex - b.masterRowIndex;
                    });

                    // Add an empty column.
                    workbook.sheets[0].columns.unshift({
                        width: 30
                    });


                    // Prepend an empty cell to each row.
                    for (var i = 0; i < workbook.sheets[0].rows.length; i++) {
                        workbook.sheets[0].rows[i].cells.unshift({});
                    }

                    // PERMANENZA
                    var col_permanenza_index = 3;

                    // Merge the detail export sheet rows with the master sheet rows.
                    // Loop backwards so the masterRowIndex does not need to be updated.
                    for (var i = detailExports.length - 1; i >= 0; i--) {
                        var masterRowIndex = detailExports[i].masterRowIndex + 1; // compensate for the header row

                        var sheet = detailExports[i].sheet;


                        // Prepend an empty cell to each row.
                        for (var ci = 0; ci < sheet.rows.length; ci++) {
                            if (sheet.rows[ci].cells[0].value) {
                                sheet.rows[ci].cells.unshift({});

                                if (sheet.rows[ci].type === 'data') {
                                    // format permanenza
                                    sheet.rows[ci].cells[col_permanenza_index].value = _secondsTohhmmss(sheet.rows[ci].cells[col_permanenza_index].value);

                                    $.each(sheet.rows[ci].cells, function (i, item) {
                                        if (i > 0) {

                                            item.background = "#daeef3";
                                        }
                                    });

                                }
                            }
                        }


                        // se ha dettagli li appendo altrimenti salto
                        if (sheet.rows.length > 1) {
                            // Insert the detail sheet rows after the master row.
                            [].splice.apply(workbook.sheets[0].rows, [masterRowIndex + 1, 0].concat(sheet.rows));
                        }


                        // format master row
                        var current_master_row = workbook.sheets[0].rows[masterRowIndex];

                        _formatStatoForExport(current_master_row.cells[col_stato_index]);

                        //format master row
                        $.each(current_master_row.cells, function (i, item) {
                            item.bold = true;
                            item.color = "#ffffff";
                            item.background = "#4397db";

                        })

                    }

                    // Save the workbook.
                    kendo.saveAs({
                        dataURI: new kendo.ooxml.Workbook(workbook).toDataURL(),
                        fileName: "Export.xlsx"
                    });

                });

        }

        function exportChildData(rowIndex, coupon) {

            // console.log('rowIndex', rowIndex);
            var deferred = $.Deferred();

            detailExportPromises.push(deferred);


            var exporter = new kendo.ExcelExporter({
                columns: [{
                    field: "titolo_contenuto",
                    title: 'Contenuto'
                }, {
                    field: "last_visit",
                    title: 'Ultima visita'
                }, {
                    field: "permanenza",
                    title: 'Permanenza'
                }, {
                    field: "visualizzazioni",
                    title: 'Visualizzazioni'
                }],
                dataSource: list[coupon]
            });

            exporter.workbook().then(function (book, data) {
                deferred.resolve({
                    masterRowIndex: rowIndex,
                    sheet: book.sheets[0]
                });
            });
        }

        function _formatStatoForExport(cell) {

            switch (cell.value) {
                case -1:
                case null:
                    var newValue = "Libero";
                    break;
                case 1:
                    var newValue = "Completato";
                    break;
                case 0:
                    var newValue = "Non Completato";
                    break;
            }

            cell.value = newValue;
        }

        //////////////// popup dettagli utente //////////////////////////

        function _createUserDetailGrid() {

            // console.log(data);

            $("#user_grid").kendoGrid({
                height: 480,
                scrollable: true,
                sortable: true,
                resizable: true,
                groupable: false,
                selectable: true,
                filterable: false,
                pageable: false,
                columns: [{
                    field: 'label',
                    title: 'Campo'

                }, {
                    field: 'value',
                    title: 'Valore'

                }]
            });

            $('#cover-spin').hide(0);
            widgets.popup.grid = $("#user_grid").data('kendoGrid');


        }

        function _openUserDetails() {

            var row = $(this).closest("tr");
            var dataItem = widgets.grid.dataItem(row);
            var params = {user_id: dataItem.id_user};

            $.when($.get("index.php?option=com_gglms&task=summaryreport.get_user_detail", params))
                .done(function (data) {

                    data = JSON.parse(data);

                    // console.log(data);
                    if (widgets.popup.window === null) {

                        widgets.popup.window = createPopup('#user-details', '450', '500', true, ["Minimize", "Maximize", "Close"]);
                        _createUserDetailGrid();
                    }

                    _poppulateDetailUserGrid(data);
                    openPopup('#user-details', 'Dettagli Utente', false, true, true)
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

        function _poppulateDetailUserGrid(data) {
            var user_data = [];

            // pivot dei dati
            $.each(JSON.parse(data.fields), function (field, value) {

                var c = user_details_fields[field];
                if (c) {
                    var obj = {
                        label: c.titolo,
                        value: value
                    };

                    user_data.push(obj);
                }

            });
            var dataSource = new kendo.data.DataSource({

                data: user_data
            });

            dataSource.fetch(function () {
                widgets.popup.grid.setDataSource(dataSource);
            });
        }

        //////////////////////////////////////////////////////////////////

        function _loadData() {


            widgets.dataSource = new kendo.data.DataSource({
                transport: {
                    read: {
                        url: window.location.hostname + "/home/index.php?option=com_gglms&task=summaryreport.getData",
                        dataType: "json"
                    },


                    parameterMap: function (data, type) {
                        //prima di eseguire la request al server passa di qua


                        if (data.filter) {
                            $.each(data.filter.filters, function (i, f) {
                                // leggo dallo schema se è un campo di tipo data formatto la data prima di mandarla al server per il filtraggio
                                if (fields[f.field].type === 'date') {
                                    var val = _formatDate(f.value);
                                    f.value = val;
                                }

                                // se è un campo di tipo string sostituisco il default equals con con like
                                if (fields[f.field].type === 'string') {
                                    f.operator = 'like'
                                }
                            });
                        }

                        return data;
                    }

                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                pageSize: 50,
                schema: {
                    data: 'data',
                    total: "total",
                    model: {
                        fields: fields
                    }
                }
            });

            widgets.dataSource.fetch(function () {
                // console.log(testDataSource.view());
                widgets.grid.setDataSource(widgets.dataSource);
            });
        }

        function _isLoggedUser_tutorAz() {

            $.when($.get("index.php?option=com_gglms&task=summaryreport.is_tutor_aziendale"))
                .done(function (data) {


                    if (data == "true") {
                        logged_tutor_az = true;
                        console.log('is_tutor_az', logged_tutor_az);
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
            var coupon = data.coupon;

            $.when($.get("index.php?option=com_gglms&task=summaryreport.getDetails", params))
                .done(function (data) {

                    data = JSON.parse(data);

                    $('#cover-spin').show(0);
                    //data type of the field {number|string|boolean|date} default is string
                    var detailsDataSource = new kendo.data.DataSource({
                        data: data
                    });

                    // console.log('insert in list', coupon);
                    list[coupon] = detailsDataSource;


                    $("<div/>").appendTo(e.detailCell).kendoGrid({
                        dataSource: detailsDataSource,
                        // toolbar: ["excel"],
                        scrollable: false,
                        resizable: true,
                        sortable: true,
                        pageable: false,
                        columns: [
                            {field: "id_contenuto", title: "", hidden: true},
                            {field: "titolo_contenuto", title: "Contenuto", width: 120},
                            {field: "last_visit", title: "Ultima visita", width: 80},
                            {
                                field: "permanenza",
                                title: "Permanenza",
                                width: 80,
                                template: '<span> #= secondsTohhmmss(data.permanenza) # </span>'
                            },
                            {field: "visualizzazioni", title: "Visualizzazioni", width: 80}
                        ],
                        excelExport: function (e) {
                            // Prevent the saving of the file.
                            e.preventDefault();

                        }
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

        }

        function _formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
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

        my.init = _init;
        my.secondsTohhmmss = _secondsTohhmmss;
        my.excel_all_pages = excel_all_pages;
        my.excel_with_details = excel_with_details;

        return my;

    }

)
(jQuery, this);

