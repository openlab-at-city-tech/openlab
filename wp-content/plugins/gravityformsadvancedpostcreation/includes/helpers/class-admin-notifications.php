<?php
/**
 * Gravity Forms Advanced Post Creation Form Population Functions.
 *
 * @package     Gravity_Forms\Gravity_Forms_APC
 * @copyright   Copyright (c) 2008-2025, Rocketgenius
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

namespace Gravity_Forms\Gravity_Forms_APC\Helpers;

use GF_Advanced_Post_Creation;
use GFAddOn;
use GFAPI;

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Admin_Notifications.
 *
 * Handles admin notifications for Advanced Post Creation.
 *
 * @since 1.6.0
 */
class Admin_Notifications {

	/**
	 * Show a notice on the payment feed settings page when an APC Feed's
	 * Enable Post Editing is set to true.
	 *
	 * @since 1.6.0
	 *
	 * @param array $errors
	 * @return array[string] Array of error messages to display.
	 */
	public function maybe_show_payment_addon_message( $errors ) {
		if ( rgget( 'page' ) !== 'gf_edit_forms' || rgget( 'view' ) !== 'settings' ) {
			return $errors;
		}

		// Subview is the add-on slug whose feed settings are being shown.
		$subview = rgget( 'subview' );
		$form_id = absint( rgget( 'id' ) );
		if ( ! $subview || ! $form_id ) {
			return $errors;
		}

		if ( ! class_exists( 'GFAddOn' ) ) {
			return $errors;
		}

		$payment_slugs = [];
		if ( method_exists( 'GFAddOn', 'get_registered_addons' ) ) {
			foreach ( GFAddOn::get_registered_addons() as $class ) {
				if ( is_string( $class ) && class_exists( $class ) && is_subclass_of( $class, 'GFPaymentAddOn' ) ) {
					$instance = is_callable( array( $class, 'get_instance' ) ) ? $class::get_instance() : null;
					if ( $instance && method_exists( $instance, 'get_slug' ) ) {
						$payment_slugs[ $instance->get_slug() ] = true;
					}
				}
			}
		}

		// If current subview isn't a subclass of GFPaymentAddOn, bail.
		if ( empty( $payment_slugs[ $subview ] ) ) {
			return $errors;
		}

		if ( ! $this->form_has_apc_editing_enabled( $form_id ) ) {
			return $errors;
		}

		$errors[] = wp_kses_post(
			sprintf(
				// Translators: 1: opening anchor tag, 2: screen reader text with external link icon and closing anchor tag.
				__( 'This form has an active Post Creation feed with post editing enabled. Post editing cannot be used on forms with payment feeds. If you activate a payment feed, post editing will be disabled for this form. %1$sLearn more. %2$s', 'gravityformsadvancedpostcreation' ),
				'<a href="https://docs.gravityforms.com/editing-a-post-with-the-advanced-post-creation-add-on/" target="_blank" rel="noopener noreferrer">',
				'<span class="screen-reader-text">' . esc_html__( '(opens in a new tab)', 'gravityformsadvancedpostcreation' ) . '</span>&nbsp;<span class="gform-icon gform-icon--external-link"></span></a>'
			)
		);

		return $errors;
	}

	/**
	 * After saving a payment feed, if there are any APC feeds on the form with
	 * enable_editing turned on, we need to set them to off. Runs on the
	 * gform_post_save_feed_settings hook.
	 *
	 * @since 1.6.0
	 *
	 * @param int $feed_id
	 * @param int $form_id
	 * @param array $settings
	 * @param GFAddOn $addon
	 */
	public function turn_off_post_editing_after_save( $feed_id, $form_id, $settings, $addon ) {
		if ( ! is_subclass_of( $addon, 'GFPaymentAddOn' ) ) {
			return;
		}

		$feed = GFAPI::get_feed( $feed_id );
		if ( is_wp_error( $feed ) || rgar( $feed, 'is_active' ) !== '1' ) {
			return;
		}

		$this->turn_off_post_editing( $form_id );
	}

	/**
	 * After adding a payment feed (which sometimes happens automatically when a
	 * payment field is added), if there are any APC feeds on the form with
	 * enable_editing turned on, we need to set them to off. Runs on the
	 * gform_after_add_feed hook.
	 *
	 * @since 1.6.0
	 *
	 * @param int $feed_id
	 * @param int $form_id
	 * @param array $feed_meta
	 * @param string $addon_slug
	 */
	public function turn_off_post_editing_after_add( $feed_id, $form_id, $feed_meta, $addon_slug ) {
		$addon = GFAddon::get_addon_by_slug( $addon_slug );
		if ( ! is_subclass_of( $addon, 'GFPaymentAddOn' ) ) {
			return;
		}

		$feed = GFAPI::get_feed( $feed_id );
		if ( is_wp_error( $feed ) || rgar( $feed, 'is_active' ) !== '1' ) {
			return;
		}

		$this->turn_off_post_editing( $form_id );
	}

	/**
	 * After changing the active/inactive status of a payment feed, if there
	 * are any APC feeds on the form with enable_editing turned on, we need to
	 * set them to off. Runs on the gform_update_feed_active hook.
	 *
	 * @since 1.6.0
	 *
	 * @param int $feed_id
	 * @param bool $status
	 * @param GFAddOn $addon
	 */
	public function turn_off_post_editing_after_status_change( $feed_id, $status, $addon ) {
		if ( ! is_subclass_of( $addon, 'GFPaymentAddOn' ) ) {
			return;
		}

		$feed = GFAPI::get_feed( $feed_id );
		if ( is_wp_error( $feed ) || rgar( $feed, 'is_active' ) !== '1' ) {
			return;
		}

		$form_id = rgar( $feed, 'form_id' );
		if ( ! $form_id ) {
			return;
		}

		$this->turn_off_post_editing( $form_id );
	}

	/**
	 * Turns off post editing for all APC feeds on the given form ID.
	 *
	 * @since 1.6.0
	 *
	 * @param int $form_id
	 */
	public function turn_off_post_editing( $form_id ) {
		$apc_feeds = $this->get_apc_feeds_by_form_id( $form_id );

		foreach ( $apc_feeds as $apc_feed ) {
			$meta = rgar( $apc_feed, 'meta', [] );
			if ( rgar( $meta, 'enable_editing' ) === '1' ) {
				$meta['enable_editing'] = '0';
				GFAPI::update_feed( rgar( $apc_feed, 'id' ), $meta, $form_id );
			}
		}
	}

	/**
	 * Checks if the form passed in has any active APC feeds with
	 * enable_editing turned on.
	 *
	 * @since 1.6.0
	 *
	 * @param int $form_id
	 *
	 * @return bool
	 */
	private function form_has_apc_editing_enabled( $form_id ) {
		$feeds = $this->get_apc_feeds_by_form_id( $form_id );
		foreach ( $feeds as $feed ) {
			$meta = rgar( $feed, 'meta', [] );
			if ( rgar( $meta, 'enable_editing' ) === '1' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns an array of the APC Feeds for the given form ID.
	 *
	 * @since 1.6.0
	 *
	 * @param int $form_id
	 *
	 * @return array
	 */
	private function get_apc_feeds_by_form_id( $form_id ) {
		if ( ! class_exists( 'GF_Advanced_Post_Creation' ) && ! is_callable( [ 'GF_Advanced_Post_Creation', 'get_instance' ] ) ) {
			return [];
		}

		$apc_slug = GF_Advanced_Post_Creation::get_instance()->get_slug();
		$feeds    = GFAPI::get_feeds( null, $form_id, $apc_slug );
		if ( is_wp_error( $feeds ) || empty( $feeds ) ) {
			return [];
		}

		return $feeds;
	}
}
