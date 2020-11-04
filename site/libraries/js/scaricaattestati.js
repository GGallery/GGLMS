_scaricaattesati = (function ($, my) {
        var diff_days = 0;
        var diff_days_thresold = 31;

        function _init() {


            console.log('init scarica attestati ok');
            $("#btn-download").click(_downloadAttestati);



            $("#id_corso").change(function () {

                var id_corso = $('#id_corso').val();
                if (id_corso != -1) {

                    $("#btn-download").removeClass('disabled');
                } else {
                    $("#btn-download").addClass('disabled');

                }

            });

            //var today = new Date();
            // $("#startdate").val(formatDate(today).split('-')[0] + '-' + formatDate(today).split('-')[1]);
            // $("#startdate").change(getDays);

            var g_start = moment().format('YYYY-MM-DD');
            var g_end = moment(g_start).add(2,'months').format('YYYY-MM-DD');

            $("#startdate").val(g_start);
            $("#enddate").val(g_end);

        }

        function _downloadAttestati() {


            var id_corso = $('#id_corso').val();
            var id_azienda = "";
            var salva_come = "";

            if ($('#id_azienda').length > 0)
                id_azienda = $('#id_azienda').val();

            if ($('#salva_come').length > 0)
                salva_come = $('#salva_come').val();
            var url = "index.php?option=com_gglms&task=attestatibulk.dwnl_attestati_by_corso&id_corso= " + id_corso
                                    + '&id_azienda=' + id_azienda
                                    + '&salva_come=' + salva_come;

            //var start = $("#startdate").val();
            // console.log(start);

            //var date = new Date(start);
            //var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            //var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            //
            // console.log(formatDate(firstDay));
            // console.log(lastDay);

            var startdate = $("#startdate").val();
            var enddate = $("#enddate").val();

            //url =  url + "&startdate=" + formatDate(firstDay) ;
            //url = url + "&enddate=" + formatDate(lastDay) ;

            url =  url + "&startdate=" + startdate;
            url = url + "&enddate=" + enddate;

            $("#btn-download").removeClass('disabled');
            $("#btn-download").attr("href", url);


        }



       function formatDate(date, when) {
            var d = new Date(date);
            var month = '' + (d.getMonth() + 1);
            if (when == "end") {
                var e_month = d.getMonth() + 3;
                if (e_month > 12)
                    e_month = 12;

                month = '' + e_month;
            }

            day = '' + d.getDate();
            year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
            //return [day, month, year].join('/');
        }

        function _getDays() {

            // alert('getDays');

            var start = $("#startdate").val();
            console.log(start);

            var date = new Date(start);
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

            console.log(firstDay);
            console.log(lastDay);

        }

        function _showMsg(text){
            $("#msg").text('');
            $("#msg").text(text);
            $("#msg").show();

        }


        // public methods
        my.init = _init;
        my.getDays = _getDays;
        return my;

    }

)(jQuery, this);

