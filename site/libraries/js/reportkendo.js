_reportkendo = (function ($, my) {


        // todo
        // 1) dettagli utente
        //-------------------------------------------
        // 2) export excell formatao stato
        // -----------------------------
        // 5)coupon scaduti (calcolo ed evidenza)
        // 3) cancella coupon (tutor piattaforma, coupon liberi)
        // 4)invia coupon per mail (tutor az, tutor p)
        // 6)disiscrivi utente (solo super admin)

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
                // template: '<span>#= user_ # <button type="button" class="k-button user-details" ><span class="glyphicon glyphicon-user"></span></button></span>',
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
                // template: "<a href='#  window.location.hostname # /home/index.php?option=com_gglms&task=summaryreport.get_user_detail&user_id=#=id_user#' class='k-button k-grid-button  k-grid-user'><span class='glyphicon glyphicon-user'></span></a>",
                template: "<button class='k-button k-grid-button k-grid-user'><span class='glyphicon glyphicon-user'></span></button>",
                attributes: {
                    style: "text-align: center"
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
                field: 'id_corso',
                title: 'Attestati',
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


        function _init() {

            _kendofix();
            console.log('report kendo ready');
            kendo.culture("it-IT");
            $('#cover-spin').show(0);

            _createGrid();

            // _isLoggedUser_tutorAz();
            // _loadData();
        }

        function _createGrid() {
            $("#grid").kendoGrid({
                toolbar: ["excel"],
                excel: {
                    allPages: true
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
                columns: columns

            });

            $('#cover-spin').hide(0);




        }



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
                pageSize: 100,
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

        return my;

    }

)
(jQuery, this);

