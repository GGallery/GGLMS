_scaricaattesati = (function ($, my) {

        function _init() {

            console.log('init scarica attestati ok');

            $('#btn-genera').click(_get);
            $('#btn-download').click(_download);

        }

        function _get() {


            $.when($.get("index.php?option=com_gglms&task=attestatibulk.scaricaattestati", {id_corso: 247}))
                .done(function (data) {

                    data = JSON.parse(data);
                    console.log('done', data);

                    $.each(data, function (i, item) {
                        console.log('href', href);
                        var l = '<span><a download class="my-link" href=" ' + data + '">link</a> #</span>';
                        $('#link_container').append(l);
                    })


                })
                .fail(function (data) {
                    console.log('fail', data);

                });
            // .then(function (data) {
            //     console.log('then', data);
            //
            // });
        }

        function _download() {

            $('#link_container a').each(function (i, item) {

                $($('#link_container a')[i]).get(0).click();
                // $($('#link_container a')[1]).get(0).click();
                setTimeout(function () {
                    $(item).get(0).click();
                }, 100);
            })


            // $($('#link_container a')[0]).get(0).click();
            // $($('#link_container a')[1]).get(0).click();
            // $($('#link_container a')[2]).get(0).click();
            // $($('#link_container a')[3]).get(0).click();
            // // setTimeout(function () {
            //     $($('#link_container a')[1]).get(0).click();
            //         }, 10);


        }


        // public methods
        my.init = _init;
        return my;

    }

)(jQuery, this);

