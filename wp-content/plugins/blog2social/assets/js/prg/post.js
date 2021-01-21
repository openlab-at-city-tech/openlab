function showFilter(typ) {
    if (typ == 'show') {
        jQuery('.filterShow').hide();
        jQuery('.form-inline').show();
        jQuery('.filterHide').show();
    } else {
        jQuery('.filterShow').show();
        jQuery('.form-inline').hide();
        jQuery('.filterHide').hide();
    }
}
