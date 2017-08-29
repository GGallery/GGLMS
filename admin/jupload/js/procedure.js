jQuery(function ($) {
    'use strict';
    // Change this to the location of your server-side upload handler:
    
    
    var url = 'components/com_gglms/jupload/server/php/';
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            alert("Caricamento completato!");
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
            });
        },
        progressall: function (e, data) {
            console.log("prograss");

            var progress = parseInt(data.loaded / data.total * 100, 10);
            
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
                );
        },
        fail: function (e, data) {
            console.log(data);
        }
    }).prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled')
    .click(function(){
        $('#tipologia').val("");
        $('#subpath').val("");
    });




    $('#fileuploadslide').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
                );
        }
    }).prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled')
    .click(function(){
        $('#tipologia').val("slide");
        $('#subpath').val("/slide/");
    });

    $('#fileuploadallegati').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
                $('#jform_filename').val(file.name);
                alert("Caricamento completato!");
                console.log(file);
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
                );
        },
        fail: function (e, data) {
            //console.log(data.errorThrown);
            //alert("Fail");
        }
    }).prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled')
    .click(function(){
        $('#tipologia').val("allegati");
        $('#subpath').val("");
    });







});
