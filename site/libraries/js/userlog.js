function StartLog(id_utente,id_contenuto,supporto,uniqid){

    var _this=this;
    // console.log("USER_LOG: "+id_utente+" - "+id_contenuto);


    jQuery.ajaxSetup({ cache: false });
    jQuery.ajaxSetup({ async: false });
    //jQuery.when(jQuery.get("/home/index.php?option=com_gglms&id_utente="+id_utente+"&id_contenuto="+id_contenuto+"&supporto="+supporto+"&uniqid="+uniqid+"&task=report.insertUserLog"))
    jQuery.when(jQuery.get("index.php?option=com_gglms&id_utente="+id_utente+"&id_contenuto="+id_contenuto+"&supporto="+supporto+"&uniqid="+uniqid+"&task=report.insertUserLog"))
        .done(function(data){
            data=JSON.parse(data);
            if(data === 'true') {

                // setInterval(function(){_this.updateUserLog(uniqid);},10000);
                window.onunload=function(){
                    EndLog(uniqid);
                };

                window.onbeforeunload=function(){
                    EndLog(uniqid);
                }
            }
        });
}


function EndLog(uniqid) {
    console.log(uniqid);
    jQuery.ajaxSetup({ cache: false });
    //jQuery.when(jQuery.get("/home/index.php?option=com_gglms&uniqid="+uniqid+"&task=report.updateUserLog"))
    jQuery.when(jQuery.get("index.php?option=com_gglms&uniqid="+uniqid+"&task=report.updateUserLog"))
        .done(function(data){
            // data=JSON.parse(data);
        });

}
