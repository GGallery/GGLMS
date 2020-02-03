_generaCoupon = (function ($, my) {


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

        $('#id_piattaforma').change(function () {

            console.log($('#id_piattaforma').val());
            // ricarico la lista dei corsi visibili per la piattaforma corrente
            var p = $('#id_piattaforma').val();
            $.get("index.php?option=com_gglms&task=generacoupon.get_corsi_by_piattaforma", {id_piattaforma: p},
                function (data) {
                    // rimuovo le option correnti
                    $('#gruppo_corsi option').remove();

                    $(data).each(function (i,item) {
                        $('#gruppo_corsi').append('<option value="' + item.value +' ">' + item.text + '</option>');
                    });

                }, 'json');
        })
    }

    function _checkUsername() {

        var piva = $("#username").val().toString();
        if (piva) {

            // blocco piva
            $("#username").prop('readonly', true);
            $("label[for='username']").addClass('disabled');
            $("#piva-msg").hide();



            //mostro botone reset, nascondo conferma
            $('#confirm_piva').hide();
            $('#change_piva').show();


            $.get("index.php?option=com_gglms&task=generacoupon.check_username", {username: piva},
                function (data) {

                    console.log(data);

                    if (data) {
                        //piva già esistente

                        // aggiorno il valore dei campi azienda
                        $("#ragione_sociale").val(data.name);
                        $("#email").val(data.email);
                        $("#ateco").val(data.cb_ateco);
                        $("#vendor").val(data.cb_ateco);
                        $("#id_piattaforma").val(data.id_piattaforma);
                        $("#id_piattaforma").trigger('change');


                    } else {

                        // nuova azienda, sblocco i campi dell'azienda
                        $(".company_opt").prop('disabled', false);
                        $("label.lbl_company_opt").removeClass("disabled");

                    }


                    // sblocco campi coupon
                    $(".cpn_opt").prop('disabled', false);
                    $("label.lbl_cpn_opt").removeClass("disabled");

                    $("#btn-genera").prop('disabled', false);


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


    }

    // public methods
    my.init = _init;


    return my;

})(jQuery, this);

