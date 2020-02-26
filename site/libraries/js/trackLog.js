_tracklog = (function ($, my) {

        var loadreportoffset = 15; // quanti per pagina
        var loadreportlimit = 0;
        var maxNofpages;
        var columns = [
            {
                field: 'user',
                title: 'Utente',
                type: 'standard'
            },
            {
                field: 'corso',
                title: 'Corso',
                type: 'standard'
            },
            {
                field: 'stato',
                title: 'Stato',
                type: 'standard'
            },
            {
                field: 'details',
                title: 'Dettagli',
                type: 'action'
            },
            {
                field: 'id_corso',
                title: '',
                type: 'hidden'
            }


        ];
        var details_column = [{
            field: 'titolo_contenuto',
            title: 'Contenuto',
            type: 'standard'
        },
            {
                field: 'permanenza',
                title: 'Permanenza',
                type: 'standard'
            },
            {
                field: 'last_visit',
                title: 'Ultimo accesso',
                type: 'standard'
            },
            {
                field: 'visualizzazioni',
                title: '# Visite',
                type: 'standard'
            }


        ];

        //per aggiungere una colonna basta aggiungere all'array columns


        function _init() {

            $.each(columns, function (i, item) {
                if (item.type != 'hidden') {

                    $(".header-row").append('<th>' + item.title + '</th>')
                }
            });


            $("#id_gruppo_azienda").change(_loadData);
            $("#id_corso").change(_loadData);
            $("#stato").change(_loadData);

            $('#utente').keyup(_delay(_loadData, 500));

            $("#btn_export_csv").click(loadCsv);
            $('.button-page').on('click', _pagination_click);


            _loadData();

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
            // $("#id_corso").val()
            var title = $("#id_corso option:selected").text().trim();
            $(".title_corso").text("Tracciamenti " + title);

            var param = {
                id_gruppo_azienda: parseInt($("#id_gruppo_azienda").val()),
                id_corso: parseInt($("#id_corso").val()),
                stato: parseInt($("#stato").val()),
                utente: $("#utente").val().trim(),
                limit: sender !== 'pagination' ? 0 : loadreportlimit,
                offset: loadreportoffset
            };


            // show spinner
            $('#cover-spin').show(0);
            $.when($.get("index.php?option=com_gglms&task=tracklog.getData", param))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('data', data);

                    _resetGridaAndPagination(data['rowCount']);

                    if (data['rowCount'] > 0) {

                        _fill_grid(data.data);
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
                $('#data-table').show();
                $('#no-data-msg').hide();
            } else {

                $('#pagination-container').hide();
                $('#no-data-msg').show();
                $('#data-table').hide();

            }
        }

        function _fill_grid(data) {

            var offset = new Date().getTimezoneOffset();
            $.each(data, function (i, item) {


                var new_row = item.stato == 1 ? $('<tr></tr>') : $('<tr class="completed"></tr>');


                $.each(columns, function (i, c) {


                    switch (c.type) {
                        case'date':

                            // convert data from utc to local
                            if (item[c.field] !== null) {
                                var utc = new Date(item[c.field]);
                                utc.setMinutes(utc.getMinutes() - offset);
                                item[c.field] = utc.toLocaleDateString() + ' ' + utc.toLocaleTimeString();
                            }
                            new_row.append('<td>' + item[c.field] + '</td>');
                            break;
                        case'action':


                            new_row.append('<td><button class="btn btn-details"  data-user="' + item["id_user"] + '" type="button" title="Dettagli" class="btn btn-xs btn-default command-edit"><span class="\n' +
                                'glyphicon glyphicon-zoom-in"></span></button> </td>');

                            break;
                        case 'hidden':
                            // hidden non fare nulla
                            break;
                        case 'standard':
                        default:
                            new_row.append('<td>' + item[c.field] + '</td>');
                            break;
                    }

                });

                $("#data-table tbody").append(new_row);


            });


            $("button.btn-details").click(openModal);


        }

        function openModal(e) {


            var user = $($(e.target).closest('button')).data('user');
            var origin = window.location.origin;

            $.each(details_column, function (i, item) {
                $(".header-row-details").append('<th>' + item.title + '</th>')
            });


            var param = {
                id_user: user,
                id_gruppo_azienda: parseInt($("#id_gruppo_azienda").val()),
                id_corso: parseInt($("#id_corso").val())
            };

            // show spinner
            $('#cover-spin').show(0);
            $.when($.get("index.php?option=com_gglms&task=tracklog.getDetails", param))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('details-data', data);

                    $.each(data, function (i, item) {
                        var new_row = $('<tr></tr>');


                        $.each(details_column, function (a, c) {

                            var val = item[c.field];
                            if (c.field === "permanenza") {
                                val = _secondsTohhmmss(val);
                            }

                            new_row.append('<td>' + val + '</td>');
                        });
                        $("#details_grid").append(new_row);
                    });

                    $("#modalDetails").modal("show");
                    $("#modalDetails").appendTo("body");

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

        function _closeModal() {

            $(".header-row-details").empty();

            console.log($("#details_grid tbody"));
            $("#details_grid > tbody").empty();

            $("#modalDetails").modal("hide");
        }

        function _resetGridaAndPagination(rowCount) {

            maxNofpages = parseInt((rowCount / loadreportoffset) + 1);
            $('#data-table tbody').empty();
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


            var url = "index.php?option=com_gglms&task=tracklog.exportCsv";
            url = url + "&id_gruppo_azienda=" + $("#id_gruppo_azienda").val();
            url = url + "&id_corso=" + $("#id_corso").val();
            url = url + "&stato=" + $("#stato").val();

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


        function _secondsTohhmmss(totalSeconds) {

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


        // public methods
        my.init = _init;
        my.closeModal = _closeModal;


        return my;

    }

)(jQuery, this);

