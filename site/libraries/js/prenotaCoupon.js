_prenotaCoupon = (function ($, my) {


        // var widgets = {
        //         grid: null,
        //         dataSource: null,
        //         popup: {
        //             window: null,
        //             grid: null
        //         }
        //     };

        var raw_data;
        var info_piattaforma;

        function _init(data, piattaforma) {

            console.log('prenota coupon ready');

            raw_data = JSON.parse(data);
            info_piattaforma = JSON.parse(piattaforma);

            console.log(info_piattaforma);
            console.log(raw_data);

            $("#id_piattaforma").val(info_piattaforma.id);
            $("#id_corso").val(raw_data.id_corso);



            $('input[type=radio][name=associato]').change(_getPrice);
            $("#qty").on('input', _getPrice);
            $(".validation-lbl").hide();


            $('input[type=radio].radio-required').click(function (e) {
                var pb = $("#panelbar").data("kendoPanelBar");
                pb.collapse($("#li_" + $(this).attr('name')) , true);

            });

            _manageData(raw_data);
            _createPanelBar();


            createNotification('#notification', 5000, true);
            _kendofix();



        }

        function _manageData(data) {
            var final_data = [];


            //todo calcolare ultimo range != null


            var row1 = {
                range: "Da 1 a " + data['range1'],
                f: _calcRow(1, false),
                f_associato: _calcRow(1, true),
                p: data["p1"],
                p_associato: _calcSconto(data["p1"], data["sconto_associati"])


            };
            final_data.push(row1);

            if( data['range2']){

                var row2 = {
                    range: "Da " + (data["range1"] + 1) + " a " + data['range2'],
                    ff: _calcRow(2, false),
                    f_associato: _calcRow(2, true),
                    p: data["p2"],
                    p_associato: _calcSconto(data["p2"], data["sconto_associati"])
                };

                final_data.push(row2);

                if(data['range3']){
                    var row3 = {
                        range: "Da " + (data["range2"] + 1) + " a " + data['range3'],
                        f: _calcRow(3, false),
                        f_associato: _calcRow(3, true),
                        p: data["p3"],
                        p_associato: _calcSconto(data["p3"], true)
                    };
                    final_data.push(row3);

                }
            }



            var last_row_index = final_data.length +1;

            var last_defined_range="range" + final_data.length;
            var last_row_range ="range" + (last_row_index);
            var last_row_price="p" + (last_row_index);


            //todo NB
            //gestione corsi che non hanno un tetto oltre quale si deve mandare email -->
            // li riconosco dal fatto che hanno definiti per esempio p1 e p2 ma solo il range1
            raw_data[last_row_range] = data[last_row_price] ? data[last_row_price] : null;

            var row4 = {
                range: "Oltre " + data[last_defined_range],
                f:  _calcRow(last_row_index ,false),
                f_associato:  _calcRow(last_row_index,true),
                p:  data[last_row_price] ? data[last_row_price] :  info_piattaforma.email,
                p_associato:  data[last_row_price] ? _calcSconto (data[last_row_price], true) :  info_piattaforma.email,
            };





            final_data.push(row4);

            console.log(final_data);


            _createGrid(final_data);

        }

        function _getPrice() {


            var formula = "";
            var is_associato = $('input[name=associato]:checked').val() === 'true';
            var x = parseInt($("#qty").val());
            $("#prezzo").empty();


            if (x <= raw_data["range1"]) {
                formula = _calcRow(1, is_associato);
            } else if (x <= raw_data["range2"]) {
                formula = _calcRow(2, is_associato);
            } else if (x <= raw_data["range3"]) {
                formula = _calcRow(3, is_associato);
            }

            if (formula) {


                $('#prezzo').fadeOut(400, function () {
                    $(this).text('\u20AC\ ' + eval(formula)).fadeIn(400);
                    $("#_prezzo").val( eval(formula));
                });

                console.log('current formula check', formula);

            } else {

                if (x) {

                    $('#prezzo').fadeOut(400, function () {
                        $("#prezzo").empty();
                        $("#_prezzo").val('da valutare con la segreteria corsi ');
                        $(this).append("<span> Da valutare con la segreteria corsi  <a href='" + info_piattaforma.email + " '>" + info_piattaforma.email + " </a></span>").fadeIn(400)
                    });

                }
            }


        }

        function _calcRow(row_number, is_associato) {

            var base = 0;

            // calcolo su range precedenti
            if (row_number > 1) {

                for (i = 1; i< row_number; i++) {

                    var field_prezzo = "p" + i;
                    var prezzo = _calcSconto(raw_data[field_prezzo], is_associato);

                    base = base + (raw_data["range" + i] - (raw_data["range" + (i - 1)] || 0)) * prezzo;
                }
            }

            // calcolo range corrente
            var field_prezzo = "p" + row_number;
            var prezzo = _calcSconto(raw_data[field_prezzo], is_associato);

            var re = base + " + (x - " + (raw_data["range" + (row_number - 1)] || 0) + " )* " + prezzo;


            return re;

        }

        function _calcSconto(prezzo, is_associato) {

            var sconto = is_associato ? raw_data["sconto_associati"] : 0;
            return Math.round(prezzo - (prezzo * sconto));

        }

        function _createGrid(data) {


            $("#grid").kendoGrid({
                dataSource: {
                    data: data
                },
                // height: 300,
                groupable: false,
                sortable: false,
                pageable: false,
                scrollable: false,
                columns: [
                    {
                        field: "range",
                        title: "Numero coupon richiesti",
                        width: "30%"

                    }, {
                        field: "f1",
                        hidden: true
                    }, {
                        field: "f2",
                        hidden: true
                    },
                    {
                        title: "Prezzi a coupon",
                        columns: [
                            {
                                field: "p",
                                title: "Aziende non associate",
                                width: "30%",
                                template: function (dataItem) {

                                    if (!parseInt(dataItem.p)) {
                                        return "Da valutare con la segreteria corsi  <a href='" + info_piattaforma.email + " '>" + info_piattaforma.email + " </a>";
                                    } else {

                                        return "<span> " + '\u20AC' + " " + dataItem.p + "</span>"
                                    }

                                }
                            }, {
                                field: "p_associato",
                                title: "Aziende  associate a " + info_piattaforma.name + "  - sconto del " + raw_data["sconto_associati"] * 100 + ' %',
                                width: "30%",
                                template: function (dataItem) {

                                    if (!parseInt(dataItem.p_associato)) {
                                        return "Da valutare con la segreteria corsi  <a href='" + info_piattaforma.email + " '>" + info_piattaforma.email + " </a>";
                                    } else {

                                        return "<span> " + '\u20AC' + " " + dataItem.p_associato + "</span>"
                                    }

                                }
                            }
                        ]
                    }

                ]

            });


        }

        function _createPanelBar() {
            $("#panelbar").kendoPanelBar({});
            var pb = $("#panelbar").data("kendoPanelBar");

            pb.expand(".k-item")

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

        return my;

    }

)
(jQuery, this);

