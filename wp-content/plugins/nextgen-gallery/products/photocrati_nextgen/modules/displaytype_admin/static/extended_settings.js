jQuery(function($){
    $('input[name="photocrati-nextgen_basic_extended_album[override_thumbnail_settings]"]')
        .nextgen_radio_toggle_tr('1', $('#tr_photocrati-nextgen_basic_extended_album_thumbnail_dimensions'))
        .nextgen_radio_toggle_tr('1', $('#tr_photocrati-nextgen_basic_extended_album_thumbnail_crop'));
});
