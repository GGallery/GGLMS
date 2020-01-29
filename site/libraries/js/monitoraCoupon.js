_monitoraCoupon = (function ($, my) {

        var loadreportoffset = 15; // quanti per pagina
        var loadreportlimit = 0;
        var maxNofpages;
        var columns = [

            {
                field: 'coupon',
                title: 'Coupon'
            },
            {
                field: 'user',
                title: 'Utente'
            },
            {
                field: 'creation_time',
                title: 'Data Creazione'
            },
            {
                field: 'data_utilizzo',
                title: 'Data Utilizzo'
            },
            {
                field: 'corso',
                title: 'Corso'
            },
            {
                field: 'venditore',
                title: 'Venditore'
            }

        ];

        //per aggiungere una colonna basta aggiungere all'array columns

        function _init() {

            $.each(columns, function (i, item) {

                $(".header-row").append('<th>' + item.title + '</th>')
            });


            $("#form-monitora-coupon select").change(_loadData);
            $('#coupon').keyup(_delay(_loadData, 500));
            // $("#btn_monitora_coupon").click(_loadData);

            $("#btn_export_csv").click(loadCsv);
            $('.button-page').on('click', _pagination_click);

            _toggle_table(false);

            _loadData(null);


        }

        function _loadData(sender) {


            // manage pagination
            if (sender != 'pagination') {
                $("a[data-page='1']").html('1');
                $("a[data-page='2']").html('2');
                $("a[data-page='3']").html('3');
                $("a[data-page='4']").html('4');
                $("a[data-page='5']").html('5');
                actualminpage = 1;
            }

            var param = {
                id_gruppo_azienda: parseInt($("#id_gruppo_azienda").val()),
                id_gruppo_corso: parseInt($("#id_gruppo_corso").val()),
                stato: parseInt($("#stato_coupon").val()),
                coupon: $("#coupon").val(),
                limit: sender !== 'pagination' ? 0 : loadreportlimit,
                offset: loadreportoffset
            };


            // show spinner
            $('#cover-spin').show(0);
            $.when($.get("index.php?option=com_gglms&task=monitoracoupon.getcouponlist", param))
                .done(function (data) {

                    data = JSON.parse(data);
                    // console.log('done', data);


                    _resetGridaAndPagination(data['rowCount']);

                    if (data['rowCount'] > 0) {

                        _fill_grid(data);
                        _toggle_table(true);


                    } else {
                        _toggle_table(false);

                    }


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

        function _toggle_table(show) {


            if (show) {
                $('#pagination-container').show();
                $('#coupon-table').show();
                $('#no-data-msg').hide();
            } else {

                $('#pagination-container').hide();
                $('#no-data-msg').show();
                $('#coupon-table').hide();

            }
        }

        function _fill_grid(data) {

            var offset = new Date().getTimezoneOffset();
            $.each(data, function (i, item) {

                if (item.coupon) {

                    var new_row = item.scaduto == 0 ? $('<tr></tr>') : $('<tr class="expired"></tr>');


                    $.each(columns, function (i, c) {


                        if ((c.field === 'data_utilizzo' || c.field === 'creation_time') && item[c.field] !== null) {

                            // convert data from utc to local
                            var utc = new Date(item[c.field]);
                            utc.setMinutes(utc.getMinutes() - offset);
                            item[c.field] = utc.toLocaleDateString() + ' ' + utc.toLocaleTimeString()

                        }

                        new_row.append('<td>' + item[c.field] + '</td>');


                    });

                    $("#coupon-table tbody").append(new_row);

                }


            });


        }

        function _resetGridaAndPagination(rowCount) {

            maxNofpages = parseInt((rowCount / loadreportoffset) + 1);
            $('#coupon-table tbody').empty();
            $('#totalcount').empty();
            $('#totalcount').html('record totali:' + rowCount);
        }

        function _pagination_click() {

            switch ($(this).attr('data-page')) {

                case 'first':
                    $("a[data-page='1']").html('1');
                    $("a[data-page='2']").html('2');
                    $("a[data-page='3']").html('3');
                    $("a[data-page='4']").html('4');
                    $("a[data-page='5']").html('5');
                    actualminpage = 1;
                    break;

                case 'prev':
                    if (actualminpage > 1) {
                        $("a[data-page='1']").html(parseInt(jQuery("a[data-page='1']").html()) - 1);
                        $("a[data-page='2']").html(parseInt(jQuery("a[data-page='2']").html()) - 1);
                        $("a[data-page='3']").html(parseInt(jQuery("a[data-page='3']").html()) - 1);
                        $("a[data-page='4']").html(parseInt(jQuery("a[data-page='4']").html()) - 1);
                        $("a[data-page='5']").html(parseInt(jQuery("a[data-page='5']").html()) - 1);
                        actualminpage--;
                    }
                    break;

                case 'next':


                    $("a[data-page='1']").html(parseInt(jQuery("a[data-page='1']").html()) + 1);
                    $("a[data-page='2']").html(parseInt(jQuery("a[data-page='2']").html()) + 1);
                    $("a[data-page='3']").html(parseInt(jQuery("a[data-page='3']").html()) + 1);
                    $("a[data-page='4']").html(parseInt(jQuery("a[data-page='4']").html()) + 1);
                    $("a[data-page='5']").html(parseInt(jQuery("a[data-page='5']").html()) + 1);
                    actualminpage++;
                    break;

                case 'last':

                    $("a[data-page='1']").html(maxNofpages - 4);
                    $("a[data-page='2']").html(maxNofpages - 3);
                    $("a[data-page='3']").html(maxNofpages - 2);
                    $("a[data-page='4']").html(maxNofpages - 1);
                    $("a[data-page='5']").html(maxNofpages);
                    actualminpage = maxNofpages - 4;
                    break;

                default:
                    loadreportlimit = (parseInt($(this).html()) * loadreportoffset) - loadreportoffset;
                    $('.button-page.selected').removeClass('active');
                    $(this).addClass('active');
                    _loadData("pagination");
            }
        }

        function loadCsv() {


            console.log('columns', columns);

            var url = "index.php?option=com_gglms&task=monitoracoupon.exportCsv";
            url = url + "&id_gruppo_azienda=" + $("#id_gruppo_azienda").val();
            url = url + "&id_gruppo_corso=" + $("#id_gruppo_corso").val();
            url = url + "&stato=" + $("#stato_coupon").val();
            url = url + "&coupon=" + $("#coupon").val();
            url = url + "&columns=" + columns.map(function (c) { // colonne da esportare
                return c.field
            }).toString();

            location.href = url;

        }

        function _delay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }


        // public methods
        my.init = _init;


        return my;

    }

)(jQuery, this);

