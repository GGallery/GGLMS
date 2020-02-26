_generaCoupon = (function ($, my) {

    var details_column = [
        {
            field: 'azienda',
            title: 'Ragione Sociale',
            type: 'standard'
        },
        {
            field: 'piva',
            title: 'Partita Iva',
            type: 'action'
        }
        // ,{
        //     field: 'action',
        //     title: 'Copia Piva',
        //     type: 'action'
        // }


    ];

    var lista_societa = null;

    function _init() {
        console.log('genera Coupon ready');
        console.log($('#id_piattaforma').val());


        $('#venditore').typeahead({
            source: function (txt_venditore, result) {

                console.log($('#id_piattaforma').val());
                $.ajax({
                    url: "index.php?option=com_gglms&task=generacoupon.load_matching_venditori_list",
                    data: {txt_venditore: txt_venditore, id_piattaforma: $('#id_piattaforma').val()},
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                        result($.map(data, function (item) {
                            return item;
                        }));
                    }
                });
            }
        });


        $('#confirm_piva').click(_checkUsername);
        $('#change_piva').click(reset);

        $('#change_piva').hide();
        $("#piva-msg").hide();


        $('#search_piva').click(_openModal);

        // //seleziono il primo valore per  filtrare i corsi per piattaforma
        // $("#id_piattaforma").val($("#id_piattaforma option:first").val());
        // $('#id_piattaforma').trigger('change');
    }


    function _checkUsername() {

        var piva = $("#username").val().toString();
        if (piva) {

            // blocco piva
            $("#username").prop('readonly', true);
            $("label[for='username']").addClass('disabled');
            $("#piva-msg").hide();
            $("#piattaforma_warning").hide();


            //mostro botone reset, nascondo conferma
            $('#confirm_piva').hide();
            $('#change_piva').show();


            $.get("index.php?option=com_gglms&task=generacoupon.check_username", {username: piva},
                function (data) {

                    console.log(data);

                    if (data) {
                        //piva già esistente
                        // se data.id_piattaforma non appartiene alle option della select
                        // significa che la partita iva esista già ma è sotto una piattaforma
                        // che l'utente non può vedere.


                        // aggiorno il valore dei campi azienda
                        $("#ragione_sociale").val(data.name);
                        $("#email").val(data.email);
                        $("#ateco").val(data.cb_ateco);
                        $("#vendor").val(data.cb_ateco);

                        if ($("#id_piattaforma option[value='" + data.id_piattaforma + "']").length > 0) {

                            // l'opzione è presente, l'utente è abilitato a vedere la piattaforma
                            $("#id_piattaforma").val(data.id_piattaforma);
                            $("#id_piattaforma").trigger('change');

                            // sblocco campi coupon
                            $(".cpn_opt").prop('disabled', false);
                            $("label.lbl_cpn_opt").removeClass("disabled");

                            $("#btn-genera").prop('disabled', false);
                        } else {
                            // l'opzione NON è presente
                            // Joomla.renderMessages({'success': ['This has a title!'], 'custom_alert': ['This has a title!']});
                            $("#piattaforma_warning").show();
                        }


                    } else {

                        // nuova azienda, sblocco i campi dell'azienda
                        $(".company_opt").prop('disabled', false);
                        $("label.lbl_company_opt").removeClass("disabled");

                        // sblocco campi coupon
                        $(".cpn_opt").prop('disabled', false);
                        $("label.lbl_cpn_opt").removeClass("disabled");

                        $("#btn-genera").prop('disabled', false);

                    }


                }, 'json');

        } else {

            // partita iva non inserita!
            $("#piva-msg").show();
        }

    }

    function reset() {

        $("#username").val("");
        $("#username").prop('readonly', false);
        $("label[for='username']").removeClass('disabled');

        //blocco tutti i campoi tranne partita iva
        $(".company_opt").prop('disabled', true);
        $("label.lbl_company_opt").addClass("disabled");
        $(".company_opt").val('');

        $(".cpn_opt").prop('disabled', true);
        $("label.lbl_cpn_opt").addClass("disabled");


        $('#change_piva').hide();
        $("#confirm_piva").show();

        $("#btn-genera").prop('disabled', true);

        $("#venditore").val("");

        $("#piattaforma_warning").hide();


    }


    /////////
    function _openModal() {


        if (lista_societa == null) {
            $.ajax({
                url: "index.php?option=com_gglms&task=generacoupon.get_lista_piva",
                dataType: "json",
                type: "POST",
                success: function (data) {

                    lista_societa = data;

                    $.each(details_column, function (i, c) {
                        $(".header-row").append('<th>' + c.title + '</th>')
                    });

                    $.each(data, function (i, item) {
                        var new_row = $('<tr class="myrow"></tr>');


                        $.each(details_column, function (a, c) {

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
                                    new_row.append('<td class="copy">' + item[c.field] + '</td>');
                                    break;
                                case 'standard':
                                default:
                                    new_row.append('<td>' + item[c.field] + '</td>');
                                    break;
                            }


                        });

                        $("#details_grid").append(new_row);
                    });

                    $("#modalDetails_genera").modal("show");
                    $("#modalDetails_genera").appendTo("body");

                    $('.myrow').click(selectPiva);

                }
            });

        } else {
            $("#modalDetails_genera").modal("show");
            $("#modalDetails_genera").appendTo("body");
        }


    }

    function _closeModal() {

        $("#modalDetails_genera").modal("hide");
    }

     function selectPiva(e) {

        reset();
         // var piva = $($(e.target)).text();



             var piva= $(e.target).next('.copy').text()  === "" ?  $(e.target).closest('.copy').text() : $(e.target).next('.copy').text();

         //     // var piva2 = $(e.target).closest('.copy').text();
         // console.log(piva_, 'aaaaaaaaaaaaaaaaaaaaaaaaaaa');
         // console.log(piva2, 'BBBBBBBBBBBBBBBBBBBBBBBBBBBBBB');

         $("#username").val(piva);

         _checkUsername();
         // $('#confirm_piva').click(_checkUsername);
         // $('#change_piva').click(reset);

         $("#modalDetails_genera").modal("hide");

    }


    // public methods
    my.init = _init;
    my.openModal = _openModal;
    my.closeModal = _closeModal;


    return my;

})(jQuery, this);

