<?php
/**
 * BP Classic Groups Widget class.
 *
 * @package bp-classic\inc\groups\classes
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Groups widget.
 *
 * @since 1.0.0
 */
class BP_Classic_Groups_Widget extends WP_Widget {

	/**
	 * Working as a group, we get things done better.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'A dynamic list of recently active, popular, newest, or alphabetical groups', 'bp-classic' ),
			'classname'                   => 'widget_bp_groups_widget buddypress widget',
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( false, _x( '(BuddyPress) Groups', 'widget name', 'bp-classic' ), $widget_ops );

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
			'groups_widget_groups_list-js',
			trailingslashit( bp_classic()->inc_url ) . 'groups/js/widget-groups.js',
			array( 'jquery' ),
			bp_classic_version(),
			true
		);
	}

	/**
	 * Extends our front-end output method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Array of arguments for the widget.
	 * @param array $instance Widget instance data.
	 */
	public function widget( $args, $instance ) {
		global $groups_template;

		/**
		 * Filters the user ID to use with the widget instance.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Empty user ID.
		 */
		$user_id = apply_filters( 'bp_group_widget_user_id', '0' );

		if ( empty( $instance['group_default'] ) ) {
			$instance['group_default'] = 'popular';
		}

		if ( empty( $instance['title'] ) ) {
			$instance['title'] = __( 'Groups', 'bp-classic' );
		}

		/**
		 * Filters the title of the Groups widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$title = ! empty( $instance['link_title'] ) ? '<a href="' . esc_url( bp_get_groups_directory_url() ) . '">' . $title . '</a>' : $title;

		/**
		 * Filters the separator of the group widget links.
		 *
		 * @since 2.4.0
		 *
		 * @param string $separator Separator string. Default '|'.
		 */
		$separator = apply_filters( 'bp_groups_widget_separator', '|' );

		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$max_limit  = bp_get_widget_max_count_limit( __CLASS__ );
		$max_groups = ! empty( $instance['max_groups'] ) ? (int) $instance['max_groups'] : 5;

		if ( $max_groups > $max_limit ) {
			$max_groups = $max_limit;
		}

		$group_args = array(
			'user_id'  => $user_id,
			'type'     => $instance['group_default'],
			'per_page' => $max_groups,
			'max'      => $max_groups,
		);

		// Back up the global.
		$old_groups_template = $groups_template;
		?>

		<?php if ( bp_has_groups( $group_args ) ) : ?>
			<div class="item-options" id="groups-list-options">
				<a href="<?php bp_groups_directory_url(); ?>" id="newest-groups" class="<?php ( 'newest' === $instance['group_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Newest', 'bp-classic' ); ?></a>
				<span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php bp_groups_directory_url(); ?>" id="recently-active-groups" class="<?php ( 'active' === $instance['group_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Active', 'bp-classic' ); ?></a>
				<span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php bp_groups_directory_url(); ?>" id="popular-groups" class="<?php ( 'popular' === $instance['group_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Popular', 'bp-classic' ); ?></a>
				<span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php bp_groups_directory_url(); ?>" id="alphabetical-groups" class="<?php ( 'alphabetical' === $instance['group_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Alphabetical', 'bp-classic' ); ?></a>
			</div>

			<ul id="groups-list" class="item-list" aria-live="polite" aria-relevant="all" aria-atomic="true">
				<?php
				while ( bp_groups() ) :
					bp_the_group();
					?>
					<li <?php bp_group_class(); ?>>
						<div class="item-avatar">
							<a href="<?php bp_group_url(); ?>" class="bp-tooltip" data-bp-tooltip="<?php bp_group_name(); ?>"><?php bp_group_avatar_thumb(); ?></a>
						</div>

						<div class="item">
							<div class="item-title"><?php bp_group_link(); ?></div>
							<div class="item-meta">
								<span class="activity">
								<?php
								if ( 'newest' === $instance['group_default'] ) {
									/* Translators: %s is the date the group was created on. */
									printf( esc_html__( 'created %s', 'bp-classic' ), esc_html( bp_get_group_date_created() ) );
								} elseif ( 'popular' === $instance['group_default'] ) {
									bp_group_member_count();
								} else {
									/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
									printf( esc_html__( 'Active %s', 'bp-classic' ), esc_html( bp_get_group_last_active() ) );
								}
								?>
								</span>
							</div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'groups_widget_groups_list', '_wpnonce-groups' ); ?>
			<input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $max_groups ); ?>" />

		<?php else : ?>

			<div class="widget-error">
				<?php esc_html_e( 'There are no groups to display.', 'bp-classic' ); ?>
			</div>

			<?php
		endif;

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Restore the global.
		$groups_template = $old_groups_template;
	}

	/**
	 * Extends our update method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $new_instance New instance data.
	 * @param array $old_instance Original instance data.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$max_limit = bp_get_widget_max_count_limit( __CLASS__ );

		$instance['title']         = wp_strip_all_tags( $new_instance['title'] );
		$instance['max_groups']    = $new_instance['max_groups'] > $max_limit ? $max_limit : intval( $new_instance['max_groups'] );
		$instance['group_default'] = wp_strip_all_tags( $new_instance['group_default'] );
		$instance['link_title']    = ! empty( $new_instance['link_title'] );

		return $instance;
	}

	/**
	 * Extends our form method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $instance Current instance.
	 * @return mixed
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'         => __( 'Groups', 'bp-classic' ),
			'max_groups'    => 5,
			'group_default' => 'active',
			'link_title'    => false,
		);

		$instance = bp_parse_args(
			(array) $instance,
			$defaults,
			'groups_widget_form'
		);

		$max_limit = bp_get_widget_max_count_limit( __CLASS__ );

		$title         = wp_strip_all_tags( $instance['title'] );
		$max_groups    = $instance['max_groups'] > $max_limit ? $max_limit : intval( $instance['max_groups'] );
		$group_default = wp_strip_all_tags( $instance['group_default'] );
		$link_title    = (bool) $instance['link_title'];
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-classic' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'link_title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>" value="1" <?php checked( $link_title ); ?> /> <?php esc_html_e( 'Link widget title to Groups directory', 'bp-classic' ); ?></label></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'max_groups' ) ); ?>"><?php esc_html_e( 'Max groups to show:', 'bp-classic' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_groups' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_groups' ) ); ?>" type="number" min="1" max="<?php echo esc_attr( $max_limit ); ?>" value="<?php echo esc_attr( $max_groups ); ?>" style="width: 30%" /></label></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'group_default' ) ); ?>"><?php esc_html_e( 'Default groups to show:', 'bp-classic' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'group_default' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'group_default' ) ); ?>">
				<option value="newest" <?php selected( $group_default, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bp-classic' ); ?></option>
				<option value="active" <?php selected( $group_default, 'active' ); ?>><?php esc_html_e( 'Active', 'bp-classic' ); ?></option>
				<option value="popular"  <?php selected( $group_default, 'popular' ); ?>><?php esc_html_e( 'Popular', 'bp-classic' ); ?></option>
				<option value="alphabetical" <?php selected( $group_default, 'alphabetical' ); ?>><?php esc_html_e( 'Alphabetical', 'bp-classic' ); ?></option>
			</select>
		</p>
		<?php
	}
}
