<?php

namespace Imagely\NGG\Util;

class Security {

	public static function get_mapped_cap( $capability_name ) {
		switch ( $capability_name ) {
			case 'nextgen_edit_display_settings':
			case 'nextgen_edit_settings': {
				$capability_name = 'NextGEN Change options';
				break;
			}
			case 'nextgen_edit_style': {
				$capability_name = 'NextGEN Change style';
				break;
			}
			case 'nextgen_edit_displayed_gallery': {
				$capability_name = 'NextGEN Attach Interface';
				break;
			}
			case 'nextgen_edit_gallery': {
				$capability_name = 'NextGEN Manage gallery';
				break;
			}
			case 'nextgen_edit_gallery_unowned': {
				$capability_name = 'NextGEN Manage others gallery';
				break;
			}
			case 'nextgen_upload_image':
			case 'nextgen_upload_images': {
				$capability_name = 'NextGEN Upload images';
				break;
			}
			case 'nextgen_edit_album_settings': {
				$capability_name = 'NextGEN Edit album settings';
				break;
			}
			case 'nextgen_edit_album': {
				$capability_name = 'NextGEN Edit album';
				break;
			}
		}

		return $capability_name;
	}

	public static function create_nonce( $cap = -1 ) {
		return \wp_create_nonce( self::get_mapped_cap( $cap ) );
	}

	public static function verify_nonce( $nonce, $cap = -1 ) {
		return \wp_verify_nonce( $nonce, self::get_mapped_cap( $cap ) );
	}

	public static function is_allowed( $capability_name, $user = false ) {
		$capability_name = self::get_mapped_cap( $capability_name );

		if ( ! $user && function_exists( 'wp_get_current_user' ) ) {
			$user = \wp_get_current_user();
		} elseif ( is_numeric( $user ) ) {
			$user = new \WP_User( $user );
		}

		return $user && $user->has_cap( $capability_name );
	}
}
