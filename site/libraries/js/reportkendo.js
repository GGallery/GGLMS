_reportkendo = (function ($, my) {


        // colonne base che ci sono sempre nella prima visualizzazione iniziale(tipo report =  corso)
        var _base_columns = [
            {
                field: 'id_anagrafica',
                title: '',
                hidden: true,
                visibility: -1,

            },
            {
                field: 'cognome',
                title: 'Cognome',
                width: 200,
                hidden: true, // nascosto per medicasa, sempre vuoto
                visibility: -1
            },
            {
                field: 'nome',
                title: 'Nome',
                width: 200,
                visibility: -1

            },
            {
                field: 'fields',
                title: 'Dettagli Utente',
                visibility: -1,
                width: 200,
                template: "<button class='k-button k-grid-button k-grid-user'><span class='glyphicon glyphicon-user'></span></button>",
                attributes: {
                    style: "text-align: center"
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
                },
                visibility: 0


            },
            {
                field: 'data_inizio',
                title: 'Data Inizio',
                width: 200,
                visibility: 0
            },
            {
                field: 'data_fine',
                title: 'Data Fine',
                width: 200,
                visibility: 0
            },
            {
                field: 'scadenza',
                title: 'In scadenza',
                width: 200,
                visibility: 0,
                hidden: true // nascosto per medicasa non hanno la scadenza
            },
            {
                field: 'attestati_hidden',
                title: 'Attestati',
                width: 150,
                visibility: 0,
                template: function (dataItem) {

                    // console.log(dataItem);
                    var id_corso = widgets.filters.corso_id.dataItem().id;
                    var id_utente = JSON.parse(dataItem.fields).user_id;
                    var href = window.location.hostname + "/home/index.php?option=com_gglms&task=attestatibulk.dwnl_attestati_by_corso&id_corso=" + id_corso + "&user_id=" + id_utente;
                    return "<a href='" + href + "' class='k-button k-grid-button k-grid-attestato'><span class='glyphicon glyphicon-download'></span></a>";

                },

            }


        ];
        var user_details_fields = {};

        //////////////

        var widgets = {
            grid: null,
            dataSource: null,
            filters: {
                corso_id: null,
                tipo_report: null,
                usergroups: null,
                filter_stato: null,
                startdate: null,
                finishdate: null
            },
            popup: {
                window: null,
                grid: null
            }
        };
        var filterdata = {
            corsi: null,
            usergroups: null,
            tipo: [{value: 0, text: 'Per Corso'},
                {value: 1, text: 'Per Unità'},
                {value: 2, text: 'Per Contenuto'}],
            stato: [
                {value: 0, text: 'Qualsiasi stato'},
                {value: 1, text: 'Solo Completati'},
                {value: 2, text: 'Solo NON Completati'},
                {value: 3, text: 'In scadenza'}]
        };

        // 0:per corso 1:per unita 2:per contenuto
        var current_report_type = 0;

        function _init() {

            _kendofix();
            console.log('report kendo ready');
            kendo.culture("it-IT");
            $('#cover-spin').show(0);


            _createSplitter();
            _getFilterData();
            _createFilters();

            _get_export_column();


            // _createGrid(_base_columns);


            // _isLoggedUser_tutorAz();
            // _loadData();
        }


        function _getFilterData() {

            $.when($.get("index.php?option=com_gglms&task=reportkendo.get_filter_data", null))
                .done(function (data) {

                    var data = JSON.parse(data);
                    filterdata.corsi = data.corsi;
                    filterdata.usergroups = data.usergroups;

                    console.log('filter_data', data);
                    _populateFilters();
                    get_new_grid_config();


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

        function _createFilters() {

            createFilter('#corso_id', 'dropdownlist', 'id', 'titolo');
            setFilter('#corso_id', 'dropdownlist', 'change', _changeReportType);
            widgets.filters.corso_id = $('#corso_id').data('kendoDropDownList');

            createFilter('#tipo_report', 'dropdownlist', 'value', 'text');
            setFilter('#tipo_report', 'dropdownlist', 'change', _changeReportType);
            widgets.filters.tipo_report = $('#tipo_report').data('kendoDropDownList');

            createFilter('#usergroups', 'dropdownlist', 'id', 'title');
            setFilter('#usergroups', 'dropdownlist', 'change', _loadData);
            widgets.filters.usergroups = $('#usergroups').data('kendoDropDownList');

            createFilter('#filterstato', 'dropdownlist', 'value', 'text');
            setFilter('#filterstato', 'dropdownlist', 'change', _loadData);
            widgets.filters.filter_stato = $('#filterstato').data('kendoDropDownList');

            createFilter('#startdate', 'DatePicker');
            setFilter('#startdate', 'DatePicker', 'change', _loadData);
            widgets.filters.startdate = $('#startdate').data('kendoDatePicker');

            createFilter('#finishdate', 'DatePicker');
            setFilter('#finishdate', 'DatePicker', 'change', _loadData);
            widgets.filters.finishdate = $('#finishdate').data('kendoDatePicker');


            widgets.filters.searchPhrase = $('#searchPhrase');
        }

        function _populateFilters() {

            populateFilter('#corso_id', 'dropdownlist', filterdata.corsi);
            widgets.filters.corso_id.select(0);

            // widgets.filters.corso_id.select(4);
            // widgets.filters.corso_id.trigger('change');

            populateFilter('#tipo_report', 'dropdownlist', filterdata.tipo);
            widgets.filters.tipo_report.select(0);

            populateFilter('#usergroups', 'dropdownlist', filterdata.usergroups);
            widgets.filters.usergroups.select(0);
            // widgets.filters.usergroups.select(6);

            populateFilter('#filterstato', 'dropdownlist', filterdata.stato);
            widgets.filters.filter_stato.select(0);

            setFilter('#filterstato', 'dropdownlist', 'change', function (e) {
                var value = this.value();
                console.log(value);

                if (parseInt(value) === 1) {

                    // show date picker per completati
                    $('#calendar_startdate_div').show();
                    $('#calendar_finishdate_div').show();

                } else {
                    $('#calendar_startdate_div').hide();
                    $('#calendar_finishdate_div').hide();
                }


            });

            _loadData();

        }

        function _createSplitter() {
            var panes = [{collapsible: true, size: "30%"}, {collapsible: false, size: '70%'}];
            createSplitter('#splitter', 'veritcal', panes);

        }

        function _createGrid(columns) {


            $("#grid").kendoGrid({
                toolbar: ["excel"],
                columns: columns,
                excel: {
                    allPages: true,
                    filterable: true,
                    fileName: "Report Utenti.xlsx"

                },

                height: '90%',
                scrollable: true,
                sortable: true,
                resizable: true,
                groupable: false,
                selectable: true,
                filterable: false,
                pageable: true,
                dataBound: function (e) {
                    // style delle colonne dinamiche, non posso darlo direttamente alla colonnna come attributo
                    // perchè le colonne dinamiche non so se sono di "stato" o altri campi testo
                    $('td:has(span.glyphicon-ok)').addClass('cell-with-icon');
                },
                excelExport: function (e) {

                    // kendo.ui.progress(this.element, false);
                    var sheet = e.workbook.sheets[0];
                    var grid = e.sender;
                    var columns = grid.columns;


                    /////////////////////////////////////////////// colonne aggiuntive export ////////////////////////////////////////////////////////////////

                    // dalle colonne visbili della griglia trovo l'indice di fields
                    var visibile_columns = columns.filter(function (value) {
                        return value.hidden !== true
                    });


                    // // colonne diamniche con template
                    // var columns_with_template = visibile_columns.filter(function (value) {
                    //     return  value.template && value.template !== null && value.field !== 'attestati_hidden' & value.field !== 'fields';
                    // });
                    //
                    // console.log(columns_with_template);


                    // indidce colonna fields
                    var col_fields_index = visibile_columns.findIndex(function (value) {
                        return value.field === 'fields'
                    });

                    // indidce colonna stato
                    var col_stato_index = visibile_columns.findIndex(function (value) {
                        return value.field === 'stato'
                    });

                    // ricavo le colonne di fields che vanno esportate (in questo caso tutte ma mi lascio la porta aperta in caso di modifiche)
                    var col_fields_toexport = Object.values(user_details_fields).filter(function (val) {
                        return val.toexport === true
                    });


                    $.each(col_fields_toexport, function (i, item) {
                        // aggiungerle a sheet.columns
                        sheet.columns.push({width: 200, autoWidth: false});
                        // aggiungerle all'header
                        var override = i === 0 ? 1 : 0; // se è la prima override = sovrascrivo il campo fields, le altre le aggiungo senza sovrascrivere
                        sheet.rows[0].cells.splice(col_fields_index + i, override, {
                            background: "#7a7a7a",
                            color: "#fff",
                            value: item.titolo,
                            colSpan: 1,
                            rowSpan: 1
                        });

                    });

                    for (var rowIndex = 1; rowIndex < sheet.rows.length; rowIndex++) {
                        var row = sheet.rows[rowIndex];
                        var fields_value = JSON.parse(row.cells[col_fields_index].value);

                        if (col_stato_index !== -1) {

                            // se la colonna stato è presente la formatto
                            row.cells[col_stato_index].value = parseInt(row.cells[col_stato_index].value) === 1 ? 'Completato' : 'Non completato';

                        }

                        // inserisco colonne da esportare
                        $.each(col_fields_toexport, function (i, item) {
                            var override = i === 0 ? 1 : 0;
                            row.cells.splice(col_fields_index + i, override, {value: fields_value[item.field]});
                        });
                    }
                }

            });


            widgets.grid = $("#grid").data('kendoGrid');
            $('#cover-spin').hide(0);

            // $(".k-grid-excel")[0].onmousedown = function (e){
            //     $('#cover-spin').show(0);
            // }


            // add tooltip to long column
            $("#grid").kendoTooltip({
                filter: "th",//".k-header span",
                position: "top",
                content: function (e) {

                    return $(e.target[0]).data('title'); // set the element text as content of the tooltip
                }
            });

            // bind popup
            $("#grid").on("click", ".k-grid-user", _openUserDetails);


        }


        ///////// popup dettagli utente //////////////////////////

        function _openUserDetails() {

            var row = $(this).closest("tr");
            var dataItem = widgets.grid.dataItem(row);


            var data = JSON.parse(dataItem.fields);
            console.log('USERFIELDS', data);

            // console.log(data);
            if (widgets.popup.window === null) {

                widgets.popup.window = createPopup('#user-details', '450', '350', true, ["Minimize", "Maximize", "Close"]);
                _createUserDetailGrid();
            }

            _poppulateDetailUserGrid(data);
            openPopup('#user-details', 'Dettagli Utente', false, true, true)


        }

        function _createUserDetailGrid() {

            // console.log(data);

            $("#user_grid").kendoGrid({
                height: 300,
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

        function _poppulateDetailUserGrid(data) {
            var user_data = [];

            // pivot dei dati
            $.each(data, function (field, value) {

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
                        // url: window.location.hostname + "/home/index.php?option=com_gglms&task=api.get_report",
                        url: "index.php?option=com_gglms&task=api.get_report",
                        dataType: "json"
                    },
                    parameterMap: function (data, type) {
                        //prima di eseguire la request al server passa di qua

                        var params = _getParams();
                        params.limit = data.pageSize;
                        params.offset = data.page === 1 ? 0 : (data.page - 1) * data.pageSize;      //page per kendo parte da 1

                        // console.log(data, params);
                        return params;
                    }

                },
                serverPaging: true,
                serverFiltering: true,
                // serverSorting: true,
                pageSize: 15,
                schema: {
                    data: function (response) {
                        console.log(response, current_report_type);

                        return response.rows;
                    },
                    total: "rowCount"

                }
            });

            widgets.dataSource.fetch(function () {
                widgets.grid.setDataSource(widgets.dataSource);

            });


        }

        function _getParams() {


// parametri chiamata api già esistente
            var params = {
                corso_id: null,
                startdate: null,
                finishdate: null,
                filterstato: null,
                usergroups: null,
                tipo_report: null,
                searchPhrase: null,
                limit: null,
                offset: null
            };

            var corsoobj = widgets.filters.corso_id.dataItem();
            params.corso_id = corsoobj.id + '|' + corsoobj.id_contenuto_completamento;

            params.startdate = _formatDate(widgets.filters.startdate.value());
            params.finishdate = _formatDate(widgets.filters.finishdate.value());
            params.filterstato = widgets.filters.filter_stato.value();
            params.usergroups = widgets.filters.usergroups.value();
            params.tipo_report = widgets.filters.tipo_report.value();
            params.searchPhrase = widgets.filters.searchPhrase.val();

            console.log(params);
            return params;


        }

        function _changeReportType() {

            current_report_type = widgets.filters.tipo_report.value() === "" ? 0 : widgets.filters.tipo_report.value();

            // destroy current widget
            $("#grid").empty();
            widgets.grid = null;
            widgets.dataSource = null;


            get_new_grid_config();

            if (current_report_type > 0) {
                // /nascondo stato corso

            }

        }

        function _get_export_column() {

            $.when($.get("index.php?option=com_gglms&task=api.get_export_columns"))
                .done(function (data) {

                    var data = JSON.parse(data);
                    if (data[0] !== 'no_column') {
                        $.each(data, function (i, item) {

                            var obj = {
                                titolo: item,
                                field: item,
                                toexport: true

                            };

                            user_details_fields[item] = obj;
                        });
                    }

                    // console.log(data);
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


        function get_new_grid_config() {


            var params = _getParams();

            $.when($.get("index.php?option=com_gglms&task=api.get_report_columns", params))
                .done(function (data) {
                    var dbcolumns = JSON.parse(data);
                    var columns = _manageColumns(dbcolumns);


                    _createGrid(columns);
                    _loadData();

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


        function _manageColumns(dbcolumns) {

            // filtro per le colonne che hanno visibility === report type + visibility = -1
            var report_type = widgets.filters.tipo_report.value();

            // prendo le colonne visbili per il tipo di report selezionato
            var tmp_column = _base_columns.filter(function (c) {
                return (c.visibility === -1 || c.visibility === parseInt(report_type))
            });


            $.each(dbcolumns.columns, function (i, item) {

                    if (item !== "" && item !== "no_column") {


                        // è già nelle colonne base?
                        var already_in = tmp_column.find(function (c) {
                            return c.field === item
                        });

                        // se non c'è lo aggiungo
                        if (!already_in) {

                            // le quadre servono a gestire il fatto che i contenuti possono avere spazi e numeri nei fields
                            var field = '["' + item + '"]';
                            var col = {
                                field: field,
                                title: item,
                                width: 200,
                                // hidden: false,
                                attributes: {style: null}
                            };

                            // sto guardando il report per unità oppue per contenuto?
                            if (current_report_type > 0) {
                                // se report per unit o per contenuto aggiorno il template delle colonne dinamiche

                                col.template = function (dataItem) {


                                    if (parseInt(dataItem[item]) === 1 || parseInt(dataItem[item]) === 0) {

                                        // template custom per le colonne dinamiche che rappresentano  unità e contenuti

                                        var _class = dataItem[item] == 1 ? 'glyphicon glyphicon-ok' : '';
                                        return "<span class= '" + _class + "'></span>";
                                    } else {
                                        return "<span>" + dataItem[item] + "</span>";
                                    }
                                };
                            }

                            tmp_column.push(col);
                        }

                    }
                }
            );

            return tmp_column;


        }

        function _formatDate(date) {

            if (date) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;
            }

            return date ? [year, month, day].join('-') : null;
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
        my.loadData = _loadData;
        my.secondsTohhmmss = _secondsTohhmmss;

        return my;

    }

)
(jQuery, this);

