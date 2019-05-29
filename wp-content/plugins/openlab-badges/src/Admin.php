<?php

namespace OpenLab\Badges;

class Admin {
	public static function init() {
		if ( ! current_user_can( 'manage_badges' ) ) {
			return;
		}

		add_action( 'network_admin_menu', array( __CLASS__, 'admin_menu' ) );
	}

	public static function admin_menu() {
		if ( ! empty( $_POST['openlab_badges_manage_nonce'] ) ) {
			self::process_save();
		}

		if ( ! empty( $_POST['openlab_badges_create_nonce'] ) ) {
			self::process_create();
		}

		if ( ! empty( $_GET['delete-badge'] ) ) {
			self::process_delete();
		}

		if ( ! empty( $_GET['badge-action'] ) ) {
			self::add_notice( wp_unslash( $_GET['badge-action'] ) );
		}

		add_menu_page(
			__( 'Badges', 'openlab-badges' ),
			__( 'Badges', 'openlab-badges' ),
			'manage_badges',
			'openlab-badges',
			array( __CLASS__, 'admin_panel' ),
			'dashicons-carrot'
		);
	}

	public static function admin_panel() {
		wp_enqueue_style( 'openlab-badges-admin', OLBADGES_PLUGIN_URL . '/assets/css/admin.css' );
		wp_enqueue_script( 'openlab-badges-admin', OLBADGES_PLUGIN_URL . '/assets/js/admin.js' );
		wp_localize_script(
			'openlab-badges-admin',
			'OpenLabBadgesAdmin',
			array(
				'deleteConfirm' => esc_html__( 'Are you sure you want to delete this badge?', 'openlab-badges' ),
			)
		);

		$badges      = Badge::get();
		$empty_badge = new Badge;
		?>
		<div class="wrap">
			<h1><i class="dashicons dashicons-carrot"></i> <?php esc_html_e( 'Badges', 'openlab-badges' ); ?></h1>

			<h2><?php esc_html_e( 'Manage existing badges', 'openlab-badges' ); ?></h2>
			<form method="post" action="<?php echo esc_attr( self::admin_url() ); ?>">
				<ol class="badge-admin">
				<?php foreach ( $badges as $badge ) : ?>
					<li>
						<?php $badge->edit_html(); ?>
						<?php
						$delete_url = add_query_arg( 'delete-badge', $badge->get_id(), self::admin_url() );
						$delete_url = wp_nonce_url( $delete_url, 'openlab-badges-delete' );
						?>
						<a class="badge-delete" href="<?php echo esc_attr( $delete_url ); ?>"><?php esc_html_e( 'Delete Badge', 'openlab-badges' ); ?></a>
					</li>
				<?php endforeach; ?>
				</ol>

				<?php wp_nonce_field( 'openlab-badges-manage', 'openlab_badges_manage_nonce' ); ?>
				<?php submit_button(); ?>
			</form>

			<h2><?php esc_html_e( 'Create new badge', 'openlab-badges' ); ?></h2>
			<form method="post" action="<?php echo esc_attr( self::admin_url() ); ?>">
				<div class="badge-admin">
					<?php $empty_badge->edit_html(); ?>
				</div>

				<?php wp_nonce_field( 'openlab-badges-create', 'openlab_badges_create_nonce' ); ?>
				<?php submit_button( __( 'Create Badge', 'openlab-badges' ) ); ?>
			</form>
		</div>
		<?php
	}

	protected static function add_notice( $status ) {
		$type = 'success';
		$text = '';

		switch ( $status ) {
			case 'updated' :
				$text = __( 'Badges successfully saved.', 'openlab-badges' );
			break;

			case 'created' :
				$text = __( 'Badge successfully created.', 'openlab-badges' );
			break;

			case 'deleted' :
				$text = __( 'Badge successfully deleted.', 'openlab-badges' );
			break;
		}

		if ( $text ) {
			add_action( 'network_admin_notices', function() use ( $type, $text ) {
				printf(
					'<div class="notice notice-%s"><p>%s</p></div>',
					esc_attr( $type ),
					esc_html( $text )
				);
			} );
		}
	}

	protected static function admin_url() {
		return network_admin_url( 'admin.php?page=openlab-badges' );
	}

	protected static function process_save() {
		check_admin_referer( 'openlab-badges-manage', 'openlab_badges_manage_nonce' );

		if ( ! current_user_can( 'manage_badges' ) ) {
			return;
		}

		if ( empty( $_POST['badges'] ) ) {
			return;
		}

		$saved_badges = wp_unslash( $_POST['badges'] );

		foreach ( $saved_badges as $saved_badge_id => $saved_badge ) {
			$badge = new Badge( $saved_badge_id );
			$badge->set_name( $saved_badge['name'] );
			$badge->set_short_name( $saved_badge['short_name'] );
			$badge->set_image( $saved_badge['image'] );
			$badge->set_link( $saved_badge['link'] );
			$badge->save();
		}

		$redirect_to = add_query_arg( 'badge-action', 'updated', self::admin_url() );
		wp_safe_redirect( $redirect_to );
	}

	protected static function process_create() {
		check_admin_referer( 'openlab-badges-create', 'openlab_badges_create_nonce' );

		if ( ! current_user_can( 'manage_badges' ) ) {
			return;
		}

		if ( empty( $_POST['badges'] ) ) {
			return;
		}

		$saved_badge = wp_unslash( $_POST['badges']['_new'] );

		$badge = new Badge();
		$badge->set_name( $saved_badge['name'] );
		$badge->set_image( $saved_badge['image'] );
		$badge->set_link( $saved_badge['link'] );
		$badge->save();

		$redirect_to = add_query_arg( 'badge-action', 'created', self::admin_url() );
		wp_safe_redirect( $redirect_to );
	}

	protected static function process_delete() {
		check_admin_referer( 'openlab-badges-delete' );

		if ( ! current_user_can( 'manage_badges' ) ) {
			return;
		}

		$badge_id = intval( $_GET['delete-badge'] );

		$badge = new Badge( $badge_id );
		$badge->delete();

		$redirect_to = add_query_arg( 'badge-action', 'deleted', self::admin_url() );
		wp_safe_redirect( $redirect_to );
	}
}
