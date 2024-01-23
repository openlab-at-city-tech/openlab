<?php
/**
 * BP Classic Members Widget class.
 *
 * @package bp-classic\inc\members\classes
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Members Widget.
 *
 * @since 1.0.3
 */
class BP_Classic_Members_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Setup widget name & description.
		$name        = _x( '(BuddyPress) Members', 'widget name', 'bp-classic' );
		$description = __( 'A dynamic list of recently active, popular, and newest members', 'bp-classic' );

		// Call WP_Widget constructor.
		parent::__construct(
			false,
			$name,
			array(
				'description'                 => $description,
				'classname'                   => 'widget_bp_core_members_widget buddypress widget',
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
			)
		);

		if ( is_customize_preview() || bp_is_widget_block_active( '', $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'bp-classic-widget-styles' );

		wp_enqueue_script(
			'bp-widget-members',
			trailingslashit( bp_classic()->inc_url ) . 'members/js/widget-members.js',
			array( 'jquery' ),
			bp_classic_version(),
			true
		);
	}

	/**
	 * Display the Members widget.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		global $members_template;

		// Get widget settings.
		$settings = $this->parse_settings( $instance );

		/**
		 * Filters the title of the Members widget.
		 *
		 * @since 1.8.0
		 * @since 2.3.0 Added 'instance' and 'id_base' to arguments passed to filter.
		 *
		 * @param string $title    The widget title.
		 * @param array  $settings The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $settings['title'], $settings, $this->id_base );
		$title = $settings['link_title'] ? '<a href="' . bp_get_members_directory_permalink() . '">' . $title . '</a>' : $title;

		/**
		 * Filters the separator of the member widget links.
		 *
		 * @since 2.4.0
		 *
		 * @param string $separator Separator string. Default '|'.
		 */
		$separator = apply_filters( 'bp_members_widget_separator', '|' );

		// Output before widget HTMl, title (and maybe content before & after it).
		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$max_limit   = bp_get_widget_max_count_limit( __CLASS__ );
		$max_members = $settings['max_members'] > $max_limit ? $max_limit : (int) $settings['max_members'];

		// Setup args for querying members.
		$members_args = array(
			'user_id'         => 0,
			'type'            => $settings['member_default'],
			'per_page'        => $max_members,
			'max'             => $max_members,
			'populate_extras' => true,
			'search_terms'    => false,
		);

		// Back up the global.
		$old_members_template = $members_template;
		?>

		<?php if ( bp_has_members( $members_args ) ) : ?>

			<div class="item-options" id="members-list-options">
				<a href="<?php bp_members_directory_permalink(); ?>" id="newest-members" class="<?php ( 'newest' === $settings['member_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Newest', 'bp-classic' ); ?></a>
				<span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php bp_members_directory_permalink(); ?>" id="recently-active-members" class="<?php ( 'active' === $settings['member_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Active', 'bp-classic' ); ?></a>

				<?php if ( bp_is_active( 'friends' ) ) : ?>
					<span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
					<a href="<?php bp_members_directory_permalink(); ?>" id="popular-members" class="<?php ( 'popular' === $settings['member_default'] ) ? 'selected' : ''; ?>"><?php esc_html_e( 'Popular', 'bp-classic' ); ?></a>
				<?php endif; ?>

			</div>

			<ul id="members-list" class="item-list" aria-live="polite" aria-relevant="all" aria-atomic="true">

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
								<?php if ( 'newest' === $settings['member_default'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_registered( array( 'relative' => false ) ) ); ?>"><?php bp_member_registered(); ?></span>
								<?php elseif ( 'active' === $settings['member_default'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>
								<?php else : ?>
									<span class="activity"><?php bp_member_total_friend_count(); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</li>

				<?php endwhile; ?>

			</ul>

			<?php wp_nonce_field( 'bp_core_widget_members', '_wpnonce-members', false ); ?>

			<input type="hidden" name="members_widget_max" id="members_widget_max" value="<?php echo esc_attr( $settings['max_members'] ); ?>" />

		<?php else : ?>

			<div class="widget-error">
				<?php esc_html_e( 'No one has signed up yet!', 'bp-classic' ); ?>
			</div>

			<?php
		endif;

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Restore the global.
		$members_template = $old_members_template;
	}

	/**
	 * Update the Members widget options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 * @return array $instance The parsed options to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$max_limit = bp_get_widget_max_count_limit( __CLASS__ );

		$instance['title']          = wp_strip_all_tags( $new_instance['title'] );
		$instance['max_members']    = $new_instance['max_members'] > $max_limit ? $max_limit : intval( $new_instance['max_members'] );
		$instance['member_default'] = wp_strip_all_tags( $new_instance['member_default'] );
		$instance['link_title']     = ! empty( $new_instance['link_title'] );

		return $instance;
	}

	/**
	 * Output the Members widget options form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Widget instance settings.
	 */
	public function form( $instance ) {
		$max_limit = bp_get_widget_max_count_limit( __CLASS__ );

		// Get widget settings.
		$settings       = $this->parse_settings( $instance );
		$title          = wp_strip_all_tags( $settings['title'] );
		$max_members    = $settings['max_members'] > $max_limit ? $max_limit : intval( $settings['max_members'] );
		$member_default = wp_strip_all_tags( $settings['member_default'] );
		$link_title     = (bool) $settings['link_title'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'bp-classic' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'link_title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>" value="1" <?php checked( $link_title ); ?> />
				<?php esc_html_e( 'Link widget title to Members directory', 'bp-classic' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'max_members' ) ); ?>">
				<?php esc_html_e( 'Max members to show:', 'bp-classic' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_members' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_members' ) ); ?>" type="number" min="1" max="<?php echo esc_attr( $max_limit ); ?>" value="<?php echo esc_attr( $max_members ); ?>" style="width: 30%" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'member_default' ) ); ?>"><?php esc_html_e( 'Default members to show:', 'bp-classic' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'member_default' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'member_default' ) ); ?>">
				<option value="newest" <?php selected( $member_default, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bp-classic' ); ?></option>
				<option value="active" <?php selected( $member_default, 'active' ); ?>><?php esc_html_e( 'Active', 'bp-classic' ); ?></option>
				<option value="popular" <?php selected( $member_default, 'popular' ); ?>><?php esc_html_e( 'Popular', 'bp-classic' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Widget instance settings.
	 * @return array The widget settings into defaults array.
	 */
	public function parse_settings( $instance = array() ) {
		return bp_parse_args(
			$instance,
			array(
				'title'          => __( 'Members', 'bp-classic' ),
				'max_members'    => 5,
				'member_default' => 'active',
				'link_title'     => false,
			),
			'members_widget_settings'
		);
	}
}
