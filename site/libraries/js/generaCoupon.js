_generaCoupon = (function ($, my) {


    function _init() {

        // $('#username').keyup(_delay(_checkUsername, 500));
        $('#confirm_piva').click(_checkUsername);
        $('#change_piva').click(reset);

        $('#change_piva').hide();
        $("#piva-msg").hide();
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



                    } else {

                        // nuova azienda, sblocco i campi dell'azienda
                        $(".company_opt").prop('disabled', false);
                        $("label.lbl_company_opt").removeClass("disabled");

                    }


                    // sblocco campi coupon
                    $(".cpn_opt").prop('disabled', false);
                    $("label.lbl_cpn_opt").removeClass("disabled");


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


    }

    // function _delay(callback, ms) {
    //     var timer = 0;
    //     return function () {
    //         var context = this, args = arguments;
    //         clearTimeout(timer);
    //         timer = setTimeout(function () {
    //             callback.apply(context, args);
    //         }, ms || 0);
    //     };
    // }


    // public methods
    my.init = _init;


    return my;

})(jQuery, this);

