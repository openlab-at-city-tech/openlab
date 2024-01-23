<?php
/**
 * BP Classic Groups Widget Functions.
 *
 * @package bp-classic\inc\groups
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Groups Legacy Widget.
 *
 * @since 1.0.0
 */
function bp_classic_groups_register_groups_widget() {
	register_widget( 'BP_Classic_Groups_Widget' );
}

/**
 * Register widgets for groups component.
 *
 * @since 1.0.0
 */
function bp_classic_groups_register_widgets() {
	add_action( 'widgets_init', 'bp_classic_groups_register_groups_widget' );
}
add_action( 'bp_register_widgets', 'bp_classic_groups_register_widgets' );

/**
 * AJAX callback for the Groups List widget.
 *
 * @since 1.0.0
 */
function bp_classic_groups_ajax_widget() {
	check_ajax_referer( 'groups_widget_groups_list' );

	$filter = 'recently-active-groups';
	if ( isset( $_POST['filter'] ) ) {
		$filter = sanitize_text_field( wp_unslash( $_POST['filter'] ) );
	}

	switch ( $filter ) {
		case 'newest-groups':
			$type = 'newest';
			break;
		case 'recently-active-groups':
		default:
			$type = 'active';
			break;
		case 'popular-groups':
			$type = 'popular';
			break;
		case 'alphabetical-groups':
			$type = 'alphabetical';
			break;
	}

	$per_page = isset( $_POST['max_groups'] ) ? intval( $_POST['max_groups'] ) : 5;

	$groups_args = array(
		'user_id'  => 0,
		'type'     => $type,
		'per_page' => $per_page,
		'max'      => $per_page,
	);

	if ( bp_has_groups( $groups_args ) ) : ?>
		<?php echo '0[[SPLIT]]'; ?>
		<?php
		while ( bp_groups() ) :
			bp_the_group();
			?>
			<li <?php bp_group_class(); ?>>
				<div class="item-avatar">
					<a href="<?php bp_group_url(); ?>"><?php bp_group_avatar_thumb(); ?></a>
				</div>

				<div class="item">
					<div class="item-title"><?php bp_group_link(); ?></div>
					<div class="item-meta">
						<?php if ( 'newest-groups' === $filter ) : ?>
							<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_date_created( 0, array( 'relative' => false ) ) ); ?>">
								<?php
								/* Translators: %s is the date the group was created on. */
								printf( esc_html__( 'created %s', 'bp-classic' ), esc_html( bp_get_group_date_created() ) );
								?>
							</span>
						<?php elseif ( 'popular-groups' === $filter ) : ?>
							<span class="activity"><?php bp_group_member_count(); ?></span>
						<?php else : ?>
							<span class="activity" data-livestamp="<?php esc_attr( bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ) ); ?>">
								<?php
								/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
								printf( esc_html_x( 'Active %s', 'last time the group was active', 'bp-classic' ), esc_html( bp_get_group_last_active() ) );
								?>
							</span>
						<?php endif; ?>
					</div>
				</div>
			</li>
		<?php endwhile; ?>

		<?php wp_nonce_field( 'groups_widget_groups_list', '_wpnonce-groups' ); ?>
		<input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $per_page ); ?>" />

		<?php
		else :
			printf(
				'-1[[SPLIT]]<li>%s</li>',
				esc_html__( 'No groups matched the current filter.', 'bp-classic' )
			);
		endif;

		exit();
}
add_action( 'wp_ajax_widget_groups_list', 'bp_classic_groups_ajax_widget' );
add_action( 'wp_ajax_nopriv_widget_groups_list', 'bp_classic_groups_ajax_widget' );
