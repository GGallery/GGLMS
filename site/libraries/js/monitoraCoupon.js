_monitoraCoupon = (function ($, my) {


        var loadreportoffset = 15; // quanti per pagina
        var loadreportlimit = 0;
        var maxNofpages;
        var columns = [

            {
                field: 'coupon',
                title: 'COM_GGLMS_GLOBAL_COUPON',
                type: 'standard'

            },
            {
                field: 'user',
                title: 'COM_GGLMS_GLOBAL_USER',
                type: 'standard'
            },
            {
                field: 'creation_time',
                title: 'COM_GGLMS_GLOBAL_CREATION_DATE',
                type: 'date'
            },
            {
                field: 'data_utilizzo',
                title: 'COM_GGLMS_GLOBAL_USE_DATE',
                type: 'date'
            },
            {
                field: 'corso',
                title: 'COM_GGLMS_GLOBAL_CORSO',
                type: 'standard'
            },
            {
                field: 'venditore',
                title: 'COM_GGLMS_GLOBAL_VENDITORE',
                type: 'standard'
            },
            {
                field: 'disattiva',
                title: 'COM_GGLMS_GLOBAL_DISATTIVA',
                type: 'js'
            }
            // ,{
            //     field: 'mailto',
            //     title: 'Invia',
            //     type: 'action'
            // }


        ];
        //per aggiungere una colonna basta aggiungere all'array columns

        var body_mail = "<span>Spettabile utente," +
            " </br> ti invitiamo a svolgere il corso <b>{{corso}}</b>. " +
            "</br></br> Registrati, o se hai già effettuato una registrazione, accedi con le credenziali scelte su <a href = '{{piattaforma}}'>la piattaforma</a>, clicca sulla voce di menù CODICE COUPON  e inserisci il codice <span style='font-family:monospace; font-weight: bold '>{{coupon}}</span> per sbloccare l'iscrizione.</span>" +
            "</br></br>Troverai il corso alla voce I MIEI CORSI: leggi la scheda e consulta i contenuti nell'ordine in cui sono presentati." +
            "</br> Ti ricordiamo che dal momento dell'iscrizione hai {{durata}} giorni per completare il corso e scaricare il tuo attestato." +
            "</br></br>Cordiali saluti," +
            "</br> Il tutor ";
        var subject_mail = "Iscrizione corso {{corso}}";

        function _init() {

            //$.when($.get("index.php?option=com_gglms&task=monitoracoupon.is_tutor_aziendale"))
            $.when($.get("index.php?option=com_gglms&task=monitoracoupon.get_var_monitora_coupon"))
                .done(function (data) {

                    /*
                    if (data == "true") {
                        // utente collegato ? tutor aziendale nascondo le info relative a venditore
                        columns = columns.filter(function (obj) {
                            return obj.field !== 'venditore';
                        });

                        $("#venditore").hide();
                        $("label[for=venditore]").hide();
                    } else {
                        columns = columns.filter(function (obj) {
                            return obj.field !== 'mailto';
                        });
                    }
                    */

                    if (data == "") {
                        alert("Parameter object is not valid");
                        return;
                    }
                    else {

                        data = JSON.parse(data);

                        // gestione tutor aziendale
                        var pIsTutor = data.is_tutor_aziendale;

                        if (pIsTutor == "true") {
                            // utente collegato ? tutor aziendale nascondo le info relative a venditore
                            columns = columns.filter(function (obj) {
                                return obj.field !== 'venditore';
                            });

                            $("#venditore").hide();
                            $("label[for=venditore]").hide();
                        } else {
                            columns = columns.filter(function (obj) {
                                return obj.field !== 'mailto';
                            });
                        }

                        // gestione disattiva coupon
                        var pDisattivaCoupon = data.show_disattiva_coupon;

                        if (parseInt(pDisattivaCoupon) != 1) {
                            columns = columns.filter(function (obj) {
                                return obj.field !== 'disattiva';
                            });
                        }

                    }


                    $.each(columns, function (i, item) {

                        $(".header-row").append('<th>' +Joomla.JText._(item.title)   + '</th>')
                    });

                    $("#form-monitora-coupon select").change(_loadData);
                    $('#coupon').keyup(_delay(_loadData, 500));
                    $('#venditore').keyup(_delay(_loadData, 500));
                    $('#utente').keyup(_delay(_loadData, 500));
                    // $("#btn_monitora_coupon").click(_loadData);

                    $("#btn_export_csv").click(loadCsv);
                    $('.button-page').on('click', _pagination_click);
                    //$('.disattiva-coupon').on('click', _disattivaCoupon);

                    _toggle_table(false);
                    _loadData(null);


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
                coupon: $("#coupon").val().trim(),
                venditore: $("#venditore").val().trim(),
                utente: $("#utente").val().trim(),
                limit: sender !== 'pagination' ? 0 : loadreportlimit,
                offset: loadreportoffset
            };


            // show spinner
            $('#cover-spin').show(0);
            $.when($.get("index.php?option=com_gglms&task=monitoracoupon.getcouponlist", param))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('data', data);

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


                        switch (c.type) {
                            case'date':

                                // controllo valori non consoni alla conversione per evitare valore del tipo Invalid Date
                                var itemRef = item[c.field];
                                //var itemRefLower = itemRef.toLowerCase();
                                // convert data from utc to local
                                if (itemRef !== null
                                    && itemRef != '0000-00-00 00:00:00'
                                    && itemRef.indexOf('invalid') == -1) {
                                    var utc = new Date(itemRef);
                                    utc.setMinutes(utc.getMinutes() - offset);
                                    itemRef = utc.toLocaleDateString() + ' ' + utc.toLocaleTimeString();
                                }
                                else
                                    itemRef = '';

                                new_row.append('<td>' + itemRef + '</td>');
                                break;
                            case'action':

                                console.log(item);
                                if (!item['user']) {
                                    // class="btn btn-envelope"
                                    new_row.append('<td><button class="btn btn-xs btn-default command-edit" data-coupon="' + item['coupon'] + '"  data-corso = "' + item['corso'] + '" data-durata="' + item["durata"] + '" type="button" title="Invia coupon"><span class="glyphicon glyphicon-envelope"></span></button> </td>');
                                } else {
                                    new_row.append('<td></td>');
                                }

                                break;
                            case 'js':
                                if (c.field == 'disattiva') {
                                    new_row.append('<td><button class="btn btn-xs btn-default" onclick="_disattivaCoupon(\'' + item['coupon'] + '\')"><span class="glyphicon glyphicon-minus-sign"></span></button></td>');
                                }
                                break;
                            case 'standard':
                            default:
                                var itemRef = item[c.field];

                                if (itemRef == null
                                    || itemRef == "null"
                                    || itemRef == undefined
                                    || itemRef == "undefined")
                                    itemRef = '';

                                new_row.append('<td>' + itemRef + '</td>');
                                break;
                        }

                    });

                    $("#coupon-table tbody").append(new_row);

                }


            });


            $("button.btn-envelope").click(openModal);


        }

        function openModal(e) {
            $("#modalMail").modal("show");
            $("#modalMail").appendTo("body");
            // $(".modal-backdrop")[0].hide(); // workaround , crea due modalbackdrop non so perchè

            var coupon = $($(e.target).closest('button')).data('coupon');
            var corso = $($(e.target).closest('button')).data('corso');
            var durata = $($(e.target).closest('button')).data('durata');
            var origin = window.location.origin;

            var body = body_mail.replace('{{coupon}}', coupon).replace('{{corso}}', corso).replace('{{piattaforma}}', origin).replace('{{durata}}', durata);
            var subject = subject_mail.replace('{{corso}}', corso);

            $("#body").empty();
            $("#body").append(body);

            $("#subject").empty();
            $("#subject").val(subject);


        }

        function _resetGridaAndPagination(rowCount) {

            maxNofpages = parseInt((rowCount / loadreportoffset) + 1);
            $('#coupon-table tbody').empty();
            $('#totalcount').empty();
            $('#totalcount').html(Joomla.JText._('COM_GGLMS_GLOBAL_RECORD') + ":" + rowCount);
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


            var pCols = columns.filter(function (obj) {
                return obj.field !== 'disattiva';
            });

            //console.log('columns', columns);
            console.log('columns', pCols);

            var url = "index.php?option=com_gglms&task=monitoracoupon.exportCsv";
            url = url + "&id_gruppo_azienda=" + $("#id_gruppo_azienda").val();
            url = url + "&id_gruppo_corso=" + $("#id_gruppo_corso").val();
            url = url + "&stato=" + $("#stato_coupon").val();
            url = url + "&coupon=" + $("#coupon").val();
            //url = url + "&columns=" + columns.map(function (c) { // colonne da esportare
            url = url + "&columns=" + pCols.map(function (c) { // colonne da esportare
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


        function _getTutorType() {

            $('#cover-spin').show(0);


        }


        // public methods
        my.init = _init;


        return my;

    }

)(jQuery, this);

function _disattivaCoupon(couponCode) {

    var c = confirm(Joomla.JText._('COM_GGLMS_GLOBAL_CONFIRM_DISATTIVA'));

    var param = {
        codice_coupon : couponCode
    };

    if (c) {
        jQuery.get("index.php?option=com_gglms&task=monitoracoupon.disattivazione_coupon", param)
            .done(function (data) {

                // controllo errore
                if (data == ""
                    || typeof data != "string") {
                    alert(Joomla.JText._('COM_GGLMS_GLOBAL_ERROR_DISATTIVA') + ' ' + data);
                    return;
                }
                else {

                    data = JSON.parse(data);

                    if (typeof data != "object") {
                        alert(Joomla.JText._('COM_GGLMS_GLOBAL_ERROR_DISATTIVA') + ' ' + data);
                        return;
                    }
                    else if (typeof data.error != "undefined"
                        || data.error != undefined) {
                        alert(data.error);
                        return;
                    }
                    else {

                        alert(Joomla.JText._('COM_GGLMS_GLOBAL_SUCCESS_DISATTIVA'));
                        _monitoraCoupon.init();
                    }

                }

            })
            .fail(function (data) {
                alert(Joomla.JText._('COM_GGLMS_GLOBAL_ERROR_DISATTIVA') + ' ' + data);
                return;
            });

    }

}
