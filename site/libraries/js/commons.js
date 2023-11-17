
function customAlertifyAlertSimple(pMsg) {
    alertify.alert()
        .setting({
            'title': 'Attenzione!',
            'label':'OK',
            'message': pMsg
        }).show();
}