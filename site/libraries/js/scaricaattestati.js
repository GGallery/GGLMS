_scaricaattesati = (function ($, my) {

        function _init() {

            console.log('init scarica attestati ok');
            $("#id_corso").change(function () {

                var id_corso = $('#id_corso').val();
                if (id_corso != -1) {

                    $("#btn-download").removeClass('disabled');
                    $("#btn-download").attr("href", "index.php?option=com_gglms&task=attestatibulk.downloadAttestati_multiple&id_corso=" + id_corso)
                } else {
                    $("#btn-download").addClass('disabled');

                }

            })

        }

        // public methods
        my.init = _init;
        return my;

    }

)(jQuery, this);

