_reportkendo = (function ($, my) {


        // colonne base che ci sono sempre nella prima visualizzazione iniziale(tipo report =  corso)
        //

        // 0: "id_anagrafica"
        // 1: "cognome"
        // 2: "nome"
        // 3: "stato"
        // 4: "data_inizio"
        // 5: "data_fine"
        // 6: "scadenza"
        // 7: "fields"
        // 8: "attestati_hidden"


        var _base_columns = [
            {
                field: 'id_anagrafica',
                title: '',
                hidden: true
            },
            {
                field: 'cognome',
                title: 'Cognome',
                width: 200,
                visibility: -1


            },
            {
                field: 'nome',
                title: 'Nome',
                width: 200,
                visibility: -1

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
                visibility: 0
            },
            {
                field: 'attestati_hidden',
                title: 'Attestati',
                width: 150,
                visibility: 0

            },
            {
                field: 'fields',
                title: '',
                hidden: true,
                visibility: -1

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

        function _init() {

            _kendofix();
            console.log('report kendo ready');
            kendo.culture("it-IT");
            $('#cover-spin').show(0);


            _createSplitter();
            _getFilterData();
            _createFilters();
            _createGrid(_base_columns);


            // _createGrid();

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
            setFilter('#corso_id', 'dropdownlist', 'change', _loadData);
            widgets.filters.corso_id = $('#corso_id').data('kendoDropDownList');

            createFilter('#tipo_report', 'dropdownlist', 'value', 'text');
            setFilter('#tipo_report', 'dropdownlist', 'change', _loadData);
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

        //TODO attenzione scommentare le selezioni per sviluppo per avere dati
        function _populateFilters() {

            populateFilter('#corso_id', 'dropdownlist', filterdata.corsi);
            // widgets.filters.corso_id.select(0);

            widgets.filters.corso_id.select(4);
            widgets.filters.corso_id.trigger('change');

            populateFilter('#tipo_report', 'dropdownlist', filterdata.tipo);
            widgets.filters.tipo_report.select(0);

            populateFilter('#usergroups', 'dropdownlist', filterdata.usergroups);
            // widgets.filters.usergroups.select(0);
            widgets.filters.usergroups.select(6);

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
                columns: columns,// _base_columns,
                excel: {
                    allPages: true
                },
                height: 550,
                scrollable: true,
                sortable: true,
                resizable: true,
                groupable: false,
                selectable: true,
                filterable: false,
                // filterable: {
                //     mode: " row",
                //     extra: false
                // },
                pageable: true,
                columns: _base_columns

            });

            widgets.grid = $("#grid").data('kendoGrid');
            $('#cover-spin').hide(0);


        }

        //todo check paginazione
        function _loadData( ) {


            widgets.dataSource = new kendo.data.DataSource({
                transport: {
                    read: {
                        url: window.location.hostname + "/home/index.php?option=com_gglms&task=api.get_report",
                        dataType: "json"
                    },
                    parameterMap: function (data, type) {
                        //prima di eseguire la request al server passa di qua

                        // todo occhio che page per kendo parte da 1
                        // allineare skip and take a offset e limit

                        var params = _getParams();
                        params.limit = 0;
                        params.offset = data.take;


                        console.log(data, params);
                        return params;
                    }

                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                pageSize: 15,
                schema: {
                    data: function (response) {
                        console.log(response);

                        // prendo le response.column
                        //le aggiungo alle base column


                            _manageColumns(response.columns);


                        return response.rows;
                    },
                    total: "rowCount"
                    // ,model: {
                    //     fields: fields
                    // }
                }
            });



                widgets.dataSource.fetch(function () {
                    widgets.grid.setDataSource(widgets.dataSource);
                });


        }

        function _manageColumns(dbcolumns) {

            // filtro per le colonne che hanno visibility === report type + visibility = -1
            var report_type = widgets.filters.tipo_report.value();
            var tmp_column = _base_columns.filter(function (c) {
                return c.visibility === -1 || c.visibility === parseInt(report_type)
            });

            $.each(dbcolumns.columns, function (i, item) {

                var already_in = tmp_column.find(function (c) {
                    return c.field === item
                });

                if (!already_in) {

                    tmp_column.push({field: item, title: item})
                }
            });

            console.log(tmp_column);

            // widgets.grid.destroy(); // Destroy the Grid.
            // widgets.grid = null;
            // _createGrid(tmp_column);
            // widgets.dataSource.fetch(function () {
            //     widgets.grid.setDataSource(widgets.dataSource);
            // });

            // widgets.grid.setOptions({columns: tmp_column});
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

