<?php
/**
 * BP Classic Friends Widget class.
 *
 * @package bp-classic\inc\friends\classes
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The User Friends widget class.
 *
 * @since 1.0.0
 */
class BP_Classic_Friends_Widget extends WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'A dynamic list of recently active, popular, and newest Friends of the displayed member. Widget is only shown when viewing a member profile.', 'bp-classic' ),
			'classname'                   => 'widget_bp_core_friends_widget buddypress widget',
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( false, $name = _x( '(BuddyPress) Friends', 'widget name', 'bp-classic' ), $widget_ops );

		if ( is_customize_preview() || bp_is_widget_block_active( '', $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'bp-classic-widget-styles' );

		wp_enqueue_script(
			'bp_core_widget_friends-js',
			trailingslashit( bp_classic()->inc_url ) . 'friends/js/widget-friends.js',
			array( 'jquery' ),
			bp_classic_version(),
			true
		);
	}

	/**
	 * Display the widget.
	 *
	 * @since 1.0.0
	 *
	 * @global BP_Core_Members_Template $members_template The main member template loop class.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance The widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		global $members_template;

		if ( ! bp_displayed_user_id() ) {
			return;
		}

		$user_id      = bp_displayed_user_id();
		$friends_slug = bp_get_friends_slug();
		$link         = bp_displayed_user_url(
			array(
				'single_item_component' => bp_rewrites_get_slug( 'members', 'member_' . $friends_slug, $friends_slug ),
			)
		);

		/* Translators: %s is the User's Full name */
		$instance['title'] = sprintf( __( "%s's Friends", 'bp-classic' ), bp_get_displayed_user_fullname() );

		if ( empty( $instance['friend_default'] ) ) {
			$instance['friend_default'] = 'active';
		}

		/**
		 * Filters the Friends widget title.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$title = $instance['link_title'] ? '<a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>' : esc_html( $title );

		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$members_args = array(
			'user_id'         => absint( $user_id ),
			'type'            => sanitize_text_field( $instance['friend_default'] ),
			'max'             => absint( $instance['max_friends'] ),
			'populate_extras' => 1,
		);

		// Back up the global.
		$old_members_template = $members_template;
		?>

		<?php if ( bp_has_members( $members_args ) ) : ?>
			<div class="item-options" id="friends-list-options">
				<a href="<?php bp_members_directory_permalink(); ?>" id="newest-friends" class="<?php ( 'newest' === $instance['friend_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Newest', 'bp-classic' ); ?></a>
				| <a href="<?php bp_members_directory_permalink(); ?>" id="recently-active-friends" class="<?php ( 'active' === $instance['friend_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Active', 'bp-classic' ); ?></a>
				| <a href="<?php bp_members_directory_permalink(); ?>" id="popular-friends" class="<?php ( 'popular' === $instance['friend_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Popular', 'bp-classic' ); ?></a>
			</div>

			<ul id="friends-list" class="item-list">
				<?php
				while ( bp_members() ) :
					bp_the_member();
					?>
					<li class="vcard">
						<div class="item-avatar">
							<a href="<?php bp_member_permalink(); ?>" class="bp-tooltip" data-bp-tooltip="<?php bp_member_name(); ?>"><?php bp_member_avatar(); ?></a>
						</div>

						<div class="item">
							<div class="item-title fn"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></div>
							<div class="item-meta">
								<?php if ( 'newest' === $instance['friend_default'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_registered( array( 'relative' => false ) ) ); ?>"><?php bp_member_registered(); ?></span>
								<?php elseif ( 'active' === $instance['friend_default'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>
								<?php else : ?>
									<span class="activity"><?php bp_member_total_friend_count(); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'bp_core_widget_friends', '_wpnonce-friends' ); ?>
			<input type="hidden" name="friends_widget_max" id="friends_widget_max" value="<?php echo absint( $instance['max_friends'] ); ?>" />

		<?php else : ?>

			<div class="widget-error">
				<?php esc_html_e( 'Sorry, no members were found.', 'bp-classic' ); ?>
			</div>

			<?php
		endif;

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Restore the global.
		$members_template = $old_members_template;
	}

	/**
	 * Process a widget save.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance The parameters saved by the user.
	 * @param array $old_instance The parameters as previously saved to the database.
	 * @return array $instance The processed settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['max_friends']    = absint( $new_instance['max_friends'] );
		$instance['friend_default'] = sanitize_text_field( $new_instance['friend_default'] );
		$instance['link_title']     = ! empty( $new_instance['link_title'] );

		return $instance;
	}

	/**
	 * Render the widget edit form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The saved widget settings.
	 */
	public function form( $instance ) {
		$defaults = array(
			'max_friends'    => 5,
			'friend_default' => 'active',
			'link_title'     => false,
		);

		$instance = bp_parse_args(
			(array) $instance,
			$defaults
		);

		$max_friends    = $instance['max_friends'];
		$friend_default = $instance['friend_default'];
		$link_title     = (bool) $instance['link_title'];
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'link_title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>" value="1" <?php checked( $link_title ); ?> /> <?php esc_html_e( 'Link widget title to Members directory', 'bp-classic' ); ?></label></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'max_friends' ) ); ?>"><?php esc_html_e( 'Max friends to show:', 'bp-classic' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_friends' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_friends' ) ); ?>" type="text" value="<?php echo absint( $max_friends ); ?>" style="width: 30%" /></label></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'friend_default' ) ); ?>"><?php esc_html_e( 'Default friends to show:', 'bp-classic' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'friend_default' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'friend_default' ) ); ?>">
				<option value="newest" <?php selected( $friend_default, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bp-classic' ); ?></option>
				<option value="active" <?php selected( $friend_default, 'active' ); ?>><?php esc_html_e( 'Active', 'bp-classic' ); ?></option>
				<option value="popular"  <?php selected( $friend_default, 'popular' ); ?>><?php esc_html_e( 'Popular', 'bp-classic' ); ?></option>
			</select>
		</p>
		<?php
	}
}
