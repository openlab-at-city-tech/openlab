jQuery( function ($) {
    // Browse for file
    jQuery( 'body' ).on( 'click', 'a.dlm_insert_download', function () {

        tb_show( dlm_id_strings.insert_download, 'media-upload.php?type=add_download&amp;from=wpdlm01&amp;TB_iframe=true&amp;height=200' );

        return false;
    } );
} );