_prenotaCoupon = (function ($, my) {

        // todo piattaforma!!


        var widgets = {
            grid: null,
            dataSource: null,
            popup: {
                window: null,
                grid: null
            }
        };

        var raw_data;

        function _init(data) {


            _kendofix();

            console.log('prenota coupon ready');
            raw_data = JSON.parse(data)[0];


            $.each(raw_data, function (name, value) {
                raw_data[name] = parseInt(value) ? parseInt(value) : value;
            });

            $("#btn_calcola").click(function (e) {

                var is_associato = $('input[name=yes_no]:checked').val() === 'on'? true:false;

                var qty = parseInt( $("#qty").val());

               _getPrice(qty,is_associato);


            });

            _manageData(raw_data);

            createNotification('#notification', 5000, true);


        }

        function _manageData(data) {
            var final_data = [];


            console.log(data);
            // console.log(raw_data);
            // \u20AC
            var row1 = {
                range: "1-" + data['range1'],
                f1: _calcRow(1, false),
                f2: _calcRow(1, true),
                // f1: +data['p1'] + "*x",
                // f2: +data['p1_associato'] + "*x"
            };


            var row2 = {
                range: data["range1"] + 1 + "-" + data['range2'],
                f1: _calcRow(2, false),
                f2: _calcRow(2, true)
            };


            var row3 = {
                range: data["range2"] + 1 + "-" + data['range3'],
                f1: _calcRow(3, false),
                f2: _calcRow(3, true)
            };

            var row4 = {
                range: "Oltre " + data["range3"],
                f1: "Da valutare con la segreteria",
                f2: "Da valutare con la segreteria"
            };


            // todo  row4

            final_data.push(row1);
            final_data.push(row2);
            final_data.push(row3);
            final_data.push(row4);

            console.log(final_data);


            _createGrid(final_data);

        }

        function _getPrice(x, is_associato) {

            var price = 0;
            var formula="";
            if (x <= raw_data["range1"]) {
                formula = _calcRow(1, is_associato);
            }

            if (x <= raw_data["range2"]) {
                formula = _calcRow(2, is_associato);
            }

            if (x <= raw_data["range3"]) {
                formula = _calcRow(3, is_associato);
            }

            var price = eval(formula);

            $("#price").text(price) ;
        }


        function _calcRow(row_number, is_associato) {

            var base = 0;


            if (row_number > 1) {
                for (i = 1; i < row_number; i++) {

                    var field_prezzo = "p" + i;
                    field_prezzo = field_prezzo + (is_associato ? "_associato" : "");
                    base = base + (raw_data["range" + i] - (raw_data["range" + (i - 1)] || 0)) * raw_data[field_prezzo];
                }


                var field_prezzo = "p" + row_number;
                field_prezzo = field_prezzo + (is_associato ? "_associato" : "");

                var re = base + " + (x - " + (raw_data["range" + (row_number - 1)] || 0) + " )* " + raw_data[field_prezzo];
            } else {
                var field_prezzo = "p1";
                field_prezzo = field_prezzo + (is_associato ? "_associato" : "");

                var re = raw_data[field_prezzo] + "*x"
            }


            return re;

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
                        title: "Numero persone da formare / coupon richiesti",
                        width: "30%"
                    }, {
                        field: "f1",
                        title: "Aziende associate",
                        width: "30%"
                    }, {
                        field: "f2",
                        title: "Aziende  non associate",
                        width: "30%"
                    }]
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

        my.init = _init;

        return my;

    }

)
(jQuery, this);

