_monitoraCoupon = (function ($, my) {

        var loadreportoffset = 15; // qunti per pagina
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
            }
        ];

        function _init() {

            console.log(' monitora coupon ready');

            // $('#example').DataTable();
            $("#btn_monitora_coupon").click(_loadData);

            $('.button-page').on('click', _pagination_click);

            _toggle_table(false);


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
                limit: sender !== 'pagination' ? 0 : loadreportlimit,
                offset: loadreportoffset
            };


            $.when(jQuery.get("index.php?option=com_gglms&task=monitoracoupon.getcouponlist", param))
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
                    // console.log('fail', data);
                })
                .then(function (data) {
                    // console.log('then', data);
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


            $.each(data, function (i, item) {

                if (item.coupon) {

                    var new_row = $('<tr></tr>');
                    $.each(columns, function (i, c) {

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


        // public methods
        my.init = _init;


        return my;

    }

)(jQuery, this);

