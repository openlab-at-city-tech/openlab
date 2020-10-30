<?php
namespace Ari_Fancy_Lightbox\Helpers;

use Ari_Fancy_Lightbox\Forms\Settings as Settings_Form;
use Ari_Fancy_Lightbox\Helpers\Settings as Settings_Helper;
use Ari\Utils\Array_Helper as Array_Helper;

class Helper {
    private static $system_args = array(
        'action',

        'msg',

        'msg_type',

        'noheader',
    );

    public static function build_url( $add_args = array(), $remove_args = array(), $remove_system_args = true, $encode_args = true ) {
        if ( $remove_system_args ) {
            $remove_args = array_merge( $remove_args, self::$system_args );
        }

        if ( $encode_args )
            $add_args = array_map( 'rawurlencode', $add_args );

        return add_query_arg( $add_args, remove_query_arg( $remove_args ) );
    }

    public static function get_settings_form() {
        $form = new Settings_Form();
        $form->bind( Array_Helper::to_flat_array( Settings_Helper::instance()->full_options() ) );

        return $form;
    }
}
