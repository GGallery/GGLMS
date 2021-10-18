function UserLog(id_utente, id_contenuto, supporto) {

    var _this = this;
    console.log("USER_LOG: " + id_utente + " - " + id_contenuto);
    var uniqid = Math.floor(Math.random() * 10000000);


    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&id_utente=" + id_utente + "&id_contenuto=" + id_contenuto + "&supporto=" + supporto + "&uniqid=" + uniqid + "&task=report.insertUserLog"))
        .done(function (data) {
            data = JSON.parse(data);
            if (data == 'true') {
                setInterval(function () {
                    //_this.updateUserLog(uniqid);
                    _this.updateUserLog(uniqid, id_utente, id_contenuto);
                }, 10000);
            } else {
            }
        }).fail(function (data) {
    })
        .then(function (data) {
        });
}

function updateUserLog(uniqid, id_utente, id_contenuto) {

    if (typeof(window.sessionStorage) != 'object')
        oldUpdateUserLog(uniqid);
    else
        newUpdateUserLog(uniqid, id_utente, id_contenuto)

}

function newUpdateUserLog(uniqid, id_utente, id_contenuto) {

    console.log("NEW -> " + uniqid);
    window.sessionStorage.setItem('update_user_log_'  + id_utente + '_' + id_contenuto, uniqid);


}

function getUpdateSessionStorage(id_utente, id_contenuto) {

    if (window.sessionStorage.getItem('update_user_log_' + id_utente + '_' + id_contenuto) != null
        && window.sessionStorage.getItem('update_user_log_' + id_utente + '_' + id_contenuto) != "") {
        oldUpdateUserLog(window.sessionStorage.getItem('update_user_log_' + id_utente + '_' + id_contenuto));
    }

}

function oldUpdateUserLog(uniqid) {
    console.log(uniqid);
    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&uniqid=" + uniqid + "&task=report.updateUserLog"))
        .done(function (data) {
            data = JSON.parse(data);
            if (data == 'true') {

            } else {

            }
        }).fail(function (data) {
    })
        .then(function (data) {

        });

}

/*
function updateUserLog(uniqid) {
    console.log(uniqid);
    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&uniqid=" + uniqid + "&task=report.updateUserLog"))
        .done(function (data) {
            data = JSON.parse(data);
            if (data == 'true') {

            } else {

            }
        }).fail(function (data) {
    })
        .then(function (data) {

        });

}
*/
