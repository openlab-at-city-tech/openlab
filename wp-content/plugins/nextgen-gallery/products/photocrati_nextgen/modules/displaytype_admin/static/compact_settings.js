jQuery(function($){
    $('input[name="photocrati-nextgen_basic_compact_album[override_thumbnail_settings]"]')
        .nextgen_radio_toggle_tr('1', $('#tr_photocrati-nextgen_basic_compact_album_thumbnail_dimensions'))
        .nextgen_radio_toggle_tr('1', $('#tr_photocrati-nextgen_basic_compact_album_thumbnail_crop'));
});
