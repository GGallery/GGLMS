function UserLog(id_utente, id_contenuto, supporto) {

    var _this = this;
    console.log("USER_LOG: " + id_utente + " - " + id_contenuto);
    var uniqid = Math.floor(Math.random() * 10000000);


    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&id_utente=" + id_utente + "&id_contenuto=" + id_contenuto + "&supporto=" + supporto + "&uniqid=" + uniqid + "&task=report.insertUserLog"))
        .done(function (data) {
            data = JSON.parse(data);
            if (data == 'true') {
                /*
                setInterval(function () {
                    _this.updateUserLog(uniqid);
                }, 10000);
                */
                _this.updateUserLog(uniqid, id_utente, id_contenuto);
            } else {
                console.log("UserLog DONE - fallito update log:" + data);
            }
        }).fail(function (data) {
            console.log("UserLog FAIL: " + data);
        })
        .then(function (data) {
            // nothing to do...
        });
}

// se esiste window.sessionStorage procedo uso il nuovo metodo altrimenti vado avanti con il vecchio che aggiorna ogni 10 sec
function updateUserLog(uniqid, id_utente, id_contenuto) {

    if (typeof(window.sessionStorage) != 'object') {
        //oldUpdateUserLog(uniqid);
        setInterval(function() {
            oldUpdateUserLog(uniqid);
        },10000);
    }
    else
        newUpdateUserLog(uniqid, id_utente, id_contenuto);

}

// ogni 10 secondi scrive la sessionStorage
function newUpdateUserLog(uniqid, id_utente, id_contenuto) {

    console.log("NEW -> " + uniqid);
    window.sessionStorage.setItem('update_user_log_'  + id_utente + '_' + id_contenuto, uniqid);

}

// aggiorno il gg_log con la vecchia chiamata
function getUpdateSessionStorage(id_utente, id_contenuto) {

    if (window.sessionStorage.getItem('update_user_log_' + id_utente + '_' + id_contenuto) != null
        && window.sessionStorage.getItem('update_user_log_' + id_utente + '_' + id_contenuto) != "") {

        oldUpdateUserLog(window.sessionStorage.getItem('update_user_log_' + id_utente + '_' + id_contenuto));

    }

}

// la vecchia chiamata
function oldUpdateUserLog(uniqid) {

    console.log("OLD -> " + uniqid);
    /*
    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&uniqid=" + uniqid + "&task=report.updateUserLog"))
        .done(function (data) {
            data = JSON.parse(data);
            if (data == 'true') {
                // aggiornata gg_log
            } else {
                console.log("oldUpdateUserLog DONE - fallito update log:" + data);
            }
        }).fail(function (data) {
            console.log("oldUpdateUserLog FAIL: " + data);
        })
        .then(function (data) {
            // nothing to do...
        });
    */

    var data = null;
    var pAsync = get_async_call();
    data = {async: pAsync};

    jQuery.ajax({
        url: "/home/index.php?option=com_gglms&task=report.updateUserLog",
        data: {
            "uniqid": uniqid,
        },
        async: data.async,
        success: function () {
            console.log("oldUpdateUserLog success");
        }
    });

}

function get_async_call() {

    var data = null;
    if(/Firefox[\/\s](\d+)/.test(navigator.userAgent) && new Number(RegExp.$1) >= 4) {
        console.log("firefox - sincrona");
        return false;
    }
    else {
        console.log("non-firefox - asincrona");
        return true;
    }

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
