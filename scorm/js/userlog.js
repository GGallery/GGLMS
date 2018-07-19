function UserLog(id_utente,id_contenuto,supporto){

var _this=this;
   //console.log("USER_LOG: "+id_utente+" - "+id_contenuto);
   var uniqid=Math.floor(Math.random()*10000000);

    jQuery.ajaxSetup({ cache: false });
    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&id_utente="+id_utente+"&id_contenuto="+id_contenuto+"&supporto="+supporto+"&uniqid="+uniqid+"&task=report.insertUserLog"))
        .done(function(data){
      //      console.log(data);
            data=JSON.parse(data);
        //    console.log(data);
            if(data=='true') {

                setInterval(function(){_this.updateUserLog(uniqid);},10000);

            }else{

            }
        }).fail(function(data){
    })
        .then(function (data) {

        });

}


function updateUserLog(uniqid) {
    //console.log(uniqid);
    jQuery.ajaxSetup({ cache: false });
    jQuery.when(jQuery.get("/home/index.php?option=com_gglms&uniqid="+uniqid+"&task=report.updateUserLog"))
        .done(function(data){
            data=JSON.parse(data);
            if(data=='true') {

            }else{

            }
        }).fail(function(data){
    })
        .then(function (data) {

        });

}
