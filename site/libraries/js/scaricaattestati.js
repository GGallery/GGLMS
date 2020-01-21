_scaricaattesati = (function ($, my) {

        function _init() {

            console.log('init scarica attestati ok');

            $('#btn-cerca').click(_get);
            // $('#btn-download').click(_download);

        }

        function _get() {

            var id_corso = $("#id_corso").val();
            console.log('id_corso', id_corso);
            $.when($.get("index.php?option=com_gglms&task=attestatibulk.downloadAttestati_multiple", {id_corso: id_corso}))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('done', data);

                })
                .fail(function (data) {
                    console.log('fail', data);

                });

        }

        // function _download() {
        //     $('#link_container a').each(function (i, item) {
        //         setTimeout(function () {
        //             $(item).get(0).click();
        //         }, 200);
        //     })
        // }

        // function _createLinkList(link_list) {
        //     $.each(link_list, function (i, item) {
        //         // console.log('href', href);
        //         var l = '<span><a download class="my-link" href=" ' + item + '">link</a> #</span>';
        //         $('#link_container').append(l);
        //     })
        // }


        // public methods
        my.init = _init;
        return my;

    }

)(jQuery, this);

