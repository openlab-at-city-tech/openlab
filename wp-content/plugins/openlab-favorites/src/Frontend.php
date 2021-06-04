<?php

namespace OpenLab\Favorites;

use OpenLab\Favorites\Favorite\Favorite;
use OpenLab\Favorites\Favorite\Query;

use OpenLab\Favorites\remove_favorite;
use OpenLab\Favorites\user_has_favorited_group;

use OpenLab\Favorites\PLUGIN_VER;
use OpenLab\Favorites\ROOT_FILE;

class Frontend {
	public static function init() {
		add_action( 'bp_group_header_actions', [ __CLASS__, 'add_button' ], 30 );
		add_action( 'bp_actions', [ __CLASS__, 'catch_action_request' ] );

		add_action( 'openlab_group_directory_after_group_title', [ __CLASS__, 'add_icon_to_group_title' ] );

		add_action( 'admin_bar_menu', [ __CLASS__, 'add_toolbar_item' ], 28 );

		self::register_assets();
	}

	/**
	 * Adds 'Save to Favorites' button to group profile.
	 */
	public static function add_button() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		wp_enqueue_style( 'openlab-favorites' );
		wp_enqueue_script( 'openlab-favorites' );

		$user_id  = bp_loggedin_user_id();
		$group_id = bp_get_current_group_id();
		$group    = groups_get_group( $group_id );

		if ( user_has_favorited_group( $user_id, $group_id ) ) {
			$url = add_query_arg(
				array(
					'ol-fav-action'   => 'unfavorite',
					'_wpnonce' => wp_create_nonce( 'openlab_favorites_unfavorite_' . $group_id ),
				),
				bp_get_group_permalink( $group )
			);

			$text       = 'Favorite';
			$hover_text = 'Remove from Favorites';
			$icon_class = 'fa-bookmark';
		} else {
			$url = add_query_arg(
				array(
					'ol-fav-action'   => 'favorite',
					'_wpnonce' => wp_create_nonce( 'openlab_favorites_favorite_' . $group_id ),
				),
				bp_get_group_permalink( $group )
			);

			$text       = 'Add to Favorites';
			$hover_text = 'Add to Favorites';
			$icon_class = 'fa-bookmark-o';
		}

		?>
		<a class="btn btn-has-hover-text btn-default btn-block btn-primary link-btn" href="<?php echo esc_attr( $url ); ?>"><i class="fa <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i> <span class="non-hover-text"><?php echo esc_html( $text ); ?></span><span class="hover-text"><?php echo esc_html( $hover_text ); ?></a>
		<?php
	}

	/**
	 * Catches action request.
	 */
	public static function catch_action_request() {
		if ( ! bp_is_group() ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( empty( $_GET['ol-fav-action'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_GET['ol-fav-action'] ) );
		if ( ! in_array( $action, [ 'favorite', 'unfavorite' ], true ) ) {
			return;
		}

		$user_id  = bp_loggedin_user_id();
		$group_id = bp_get_current_group_id();

		$nonce_action = 'favorite' === $action ? 'openlab_favorites_favorite_' . $group_id : 'openlab_favorites_unfavorite_' . $group_id;
		check_admin_referer( $nonce_action );

		$saved   = false;
		$message = '';

		$already_favorited = user_has_favorited_group( $user_id, $group_id );

		if ( 'favorite' === $action ) {
			if ( $already_favorited ) {
				$message = 'Already saved to Favorites';
			} else {
				$favorite = new Favorite();
				$favorite->set_user_id( $user_id );
				$favorite->set_group_id( $group_id );

				$saved = $favorite->save();

				if ( $saved ) {
					$message = 'Successfully added to Favorites.';
				} else {
					$message = 'Could not add to Favorites.';
				}
			}
		} else {
			$message = 'Could not remove from Favorites';
			if ( $already_favorited ) {
				$saved = remove_favorite( $user_id, $group_id );

				if ( $saved ) {
					$message = 'Successfully removed from Favorites.';
				}
			}
		}

		if ( $saved ) {
			bp_core_add_message( $message, 'success' );
		} else {
			bp_core_add_message( $message, 'error' );
		}

		bp_core_redirect( bp_get_group_permalink( groups_get_group( $group_id ) ) );
	}

	public static function add_toolbar_item( $wp_admin_bar ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$favorites = Query::get_results(
			[
				'user_id' => bp_loggedin_user_id(),
			]
		);

		if ( ! $favorites ) {
			return;
		}

		$wp_admin_bar->add_node(
			[
				'id'    => 'openlab-favorites',
				'title' => '<span class="toolbar-item-icon fa fa-bookmark" aria-hidden="true"></span><span class="sr-only">Favorites</span>',
				'meta'  => [
					'class' => 'openlab-favorites-admin-bar-menu admin-bar-menu hidden-xs icon-group-1',
				],
			]
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'openlab-favorites',
				'id'     => 'openlab-favorites-ts-title',
				'title'  => 'My Favorites',
				'meta'   => array(
					'class' => 'submenu-title bold',
				),
			)
		);

		foreach ( $favorites as $favorite ) {
			$group = groups_get_group( $favorite->get_group_id() );

			$wp_admin_bar->add_node(
				[
					'id'     => 'openlab-favorites-' . $favorite->get_id(),
					'parent' => 'openlab-favorites',
					'title'  => $group->name,
					'href'   => bp_get_group_permalink( $group ),
					'meta'   => [
						'class' => 'admin-bar-menu-item mobile-no-hover',
					],
				]
			);
		}
	}

	public static function add_icon_to_group_title() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id  = bp_loggedin_user_id();
		$group_id = bp_get_group_id();

		if ( ! user_has_favorited_group( $user_id, $group_id ) ) {
			return;
		}

		?>
		<span class="favorite-marker"><span class="sr-only">This group is in your Favorites</span><span class="fa fa-icon fa-bookmark-o"></span></span>
		<?php
	}

	public static function register_assets() {
		$plugin_dir = plugin_dir_url( ROOT_FILE );

		wp_register_style( 'openlab-favorites', $plugin_dir . '/assets/css/openlab-favorites.css', [], PLUGIN_VER );
		wp_register_script( 'openlab-favorites', $plugin_dir . '/assets/js/openlab-favorites.js', [ 'jquery' ], PLUGIN_VER, true );
	}
}
