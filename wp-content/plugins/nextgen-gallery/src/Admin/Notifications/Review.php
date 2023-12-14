<?php

namespace Imagely\NGG\Admin\Notifications;

use Imagely\NGG\Display\View;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\URL;

class Review {

	public $_data;

	public function __construct( $params = [] ) {
		$this->_data['name']    = $params['name'];
		$this->_data['range']   = $params['range'];
		$this->_data['follows'] = $params['follows'];
	}

	public function get_name() {
		return $this->_data['name'];
	}

	public function get_gallery_count() {
		// Get the total # of galleries if we don't have them
		$settings = Settings::get_instance();
		$count    = $settings->get( 'gallery_count', false );
		if ( ! $count ) {
			$count = \M_NextGen_Admin::update_gallery_count_setting();
		}
		return $count;
	}

	public function get_range() {
		return $this->_data['range'];
	}

	public function is_renderable() {
		return ( $this->is_on_dashboard() || $this->is_on_ngg_admin_page() )
			&& $this->is_at_gallery_count()
			&& $this->is_previous_notice_dismissed()
			&& $this->gallery_created_flag_check();
	}

	public function gallery_created_flag_check() {
		$settings = Settings::get_instance();
		return $settings->get( 'gallery_created_after_reviews_introduced' );
	}

	public function is_at_gallery_count() {
		$retval  = false;
		$range   = $this->_data['range'];
		$count   = $this->get_gallery_count();
		$manager = Manager::get_instance();

		// Determine if we match the current range
		if ( $count >= $range['min'] && $count <= $range['max'] ) {
			$retval = true;
		}

		// If the current number of galleries exceeds the parent notice's maximum we should dismiss the parent
		if ( ! empty( $this->_data['follows'] ) ) {
			$follows      = $this->_data['follows'];
			$parent_range = $follows->get_range();
			if ( $count > $parent_range['max'] && ! $manager->is_dismissed( $follows->get_name() ) ) {
				$manager->dismiss( $follows->get_name(), 2 );
			}
		}

		return $retval;
	}

	public function is_previous_notice_dismissed( $level = false ) {
		$retval  = false;
		$manager = Manager::get_instance();

		if ( empty( $level ) ) {
			$level = $this;
		}

		if ( ! empty( $level->_data['follows'] ) ) {
			$parent = $level->_data['follows'];
			$retval = $manager->is_dismissed( $parent->get_name() );
			if ( ! $retval && ! empty( $parent->_data['follows'] ) ) {
				$retval = $this->is_previous_notice_dismissed( $parent );
			}
		} else {
			$retval = true;
		}

		return $retval;
	}

	public function is_on_dashboard(): bool {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}
		return preg_match( '#/wp-admin/?(index\.php)?$#', $_SERVER['REQUEST_URI'] ) == true;
	}

	public function is_on_ngg_admin_page(): bool {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		// Do not show this notification inside of the ATP popup.
		//
		// Nonce verification is not necessary here, and should be performed by methods invoking this method.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return ( preg_match( '/wp-admin.*(ngg|nextgen).*/', $_SERVER['REQUEST_URI'] )
				|| (
					isset( $_REQUEST['page'] )
					&& preg_match( '/ngg|nextgen/', sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) )
				) )
				&& ( false === strpos( strtolower( $_SERVER['REQUEST_URI'] ), '&attach_to_post' ) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	public function render() {
		$view = new View(
			'Admin/ReviewNotice',
			[ 'number' => $this->get_gallery_count() ],
			'photocrati-nextgen_admin#review_notice'
		);
		return $view->render( true );
	}

	public function dismiss( $code ) {
		$retval = [
			'dismiss'      => true,
			'persist'      => true,
			'success'      => true,
			'code'         => $code,
			'dismiss_code' => $code,
		];

		$manager = Manager::get_instance();

		if ( $code == 1 || $code == 3 ) {
			$retval['review_level_1'] = $manager->dismiss( 'review_level_1', 2 );
			$retval['review_level_2'] = $manager->dismiss( 'review_level_2', 2 );
			$retval['review_level_3'] = $manager->dismiss( 'review_level_3', 2 );
		}

		return $retval;
	}
}
