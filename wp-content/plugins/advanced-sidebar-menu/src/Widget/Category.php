<?php

namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\Menus\Menu_Abstract;

/**
 * Creates a Widget of parent Child Categories
 *
 * @author  OnPoint Plugins
 * @since   7.0.0
 *
 * @package Advanced Sidebar Menu
 */
class Category extends Widget_Abstract {
	const NAME = 'advanced_sidebar_menu_category';

	const TITLE                    = Menu_Abstract::TITLE;
	const INCLUDE_PARENT           = Menu_Abstract::INCLUDE_PARENT;
	const INCLUDE_CHILDLESS_PARENT = Menu_Abstract::INCLUDE_CHILDLESS_PARENT;
	const ORDER_BY                 = Menu_Abstract::ORDER_BY;
	const EXCLUDE                  = Menu_Abstract::EXCLUDE;
	const DISPLAY_ALL              = Menu_Abstract::DISPLAY_ALL;
	const LEVELS                   = Menu_Abstract::LEVELS;

	const DISPLAY_ON_SINGLE     = \Advanced_Sidebar_Menu\Menus\Category::DISPLAY_ON_SINGLE;
	const EACH_CATEGORY_DISPLAY = \Advanced_Sidebar_Menu\Menus\Category::EACH_CATEGORY_DISPLAY;

	/**
	 * Default widget values.
	 *
	 * @var array
	 */
	protected static $defaults = [
		self::TITLE                    => '',
		self::INCLUDE_PARENT           => false,
		self::INCLUDE_CHILDLESS_PARENT => false,
		self::DISPLAY_ON_SINGLE        => false,
		self::EACH_CATEGORY_DISPLAY    => 'widget',
		self::EXCLUDE                  => '',
		self::DISPLAY_ALL              => false,
		self::LEVELS                   => 1,
	];


	/**
	 * Register the widget.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'             => 'advanced-sidebar-menu advanced-sidebar-category',
			'description'           => __( 'Creates a menu of all the categories using the parent/child relationship', 'advanced-sidebar-menu' ),
			'show_instance_in_rest' => true,
		];
		$control_ops = [
			'width' => wp_is_mobile() ? false : 620,
		];

		parent::__construct( static::NAME, __( 'Advanced Sidebar - Categories', 'advanced-sidebar-menu' ), $widget_ops, $control_ops );

		$this->hook();
	}


	/**
	 * Add the sections to the widget via actions.
	 *
	 * @notice Anything using the column actions must use the $widget class passed
	 *         via do_action instead of $this
	 *
	 * @return void
	 */
	protected function hook() {
		static $hooked;
		if ( null !== $hooked ) {
			return;
		}
		$hooked = true;

		add_action( 'advanced-sidebar-menu/widget/category/left-column', [ $this, 'box_display' ], 5, 2 );
		add_action( 'advanced-sidebar-menu/widget/category/left-column', [
			$this,
			'box_display_on_single_posts',
		], 15, 2 );
		add_action( 'advanced-sidebar-menu/widget/category/left-column', [ $this, 'box_exclude' ], 20, 2 );
	}


	/**
	 * Get the label for set taxonomy.
	 *
	 * For adjusting widget option labels.
	 *
	 * @param array $instance - Widget settings.
	 * @param bool  $single   - Singular label or plural.
	 *
	 * @since 8.2.0
	 *
	 * @return mixed
	 */
	public function get_taxonomy_label( $instance, $single = true ) {
		$taxonomy = get_taxonomy( apply_filters( 'advanced-sidebar-menu/widget/category/taxonomy-for-label', 'category', $this->control_options, $instance ) );
		if ( empty( $taxonomy ) ) {
			$taxonomy = get_taxonomy( 'category' ); // Sensible fallback.
		}

		return $single ? $taxonomy->labels->singular_name : $taxonomy->labels->name;
	}


	/**
	 * Get list of display each single post's category options.
	 *
	 * @return array
	 */
	public static function get_display_each_options() {
		return [
			\Advanced_Sidebar_Menu\Menus\Category::EACH_WIDGET => __( 'In a new widget', 'advanced-sidebar-menu' ),
			\Advanced_Sidebar_Menu\Menus\Category::EACH_LIST   => __( 'In another list in the same widget', 'advanced-sidebar-menu' ),
		];
	}


	/**
	 * Display options.
	 *
	 * @param array           $instance - Widget settings.
	 * @param Widget_Abstract $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_display( array $instance, $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<?php $widget->checkbox( static::INCLUDE_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy single label */
					printf( esc_html__( 'Display the highest level parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance ) ) ) );
					?>
				</label>
			</p>
			<p>
				<?php $widget->checkbox( static::INCLUDE_CHILDLESS_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy single label */
					printf( esc_html__( 'Display menu when there is only the parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance ) ) ) );
					?>
				</label>
			</p>
			<p>
				<?php $widget->checkbox( static::DISPLAY_ALL, static::LEVELS ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy plural label */
					printf( esc_html__( 'Always display child %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance, false ) ) ) );
					?>
				</label>
			</p>
			<div <?php $widget->hide_element( static::DISPLAY_ALL, static::LEVELS ); ?>>
				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( static::LEVELS ) ); ?>">
						<?php
						ob_start();
						?>
						<select
							id="<?php echo esc_attr( $widget->get_field_id( static::LEVELS ) ); ?>"
							name="<?php echo esc_attr( $widget->get_field_name( static::LEVELS ) ); ?>">
							<option value="100">
								<?php esc_html_e( '- All -', 'advanced-sidebar-menu' ); ?>
							</option>
							<?php
							for ( $i = 1; $i < 6; $i ++ ) {
								?>
								<option
									value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $i, (int) $instance[ static::LEVELS ] ); ?>>
									<?php echo (int) $i; ?>
								</option>
								<?php
							}
							?>
						</select>
						<?php
						/* translators: {select html input}, {Selected post type plural label} */
						printf( esc_html__( 'Display %1$s levels of child %2$s', 'advanced-sidebar-menu' ), ob_get_clean(), esc_html( strtolower( $this->get_taxonomy_label( $instance, false ) ) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</label>
				</p>
			</div>

			<?php do_action( 'advanced-sidebar-menu/widget/category/display-box', $instance, $widget ); ?>

		</div>
		<?php
	}


	/**
	 * Display categories on single post settings.
	 *
	 * @param array           $instance - Widget settings.
	 * @param Widget_Abstract $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_display_on_single_posts( array $instance, $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>

				<?php $widget->checkbox( static::DISPLAY_ON_SINGLE, static::EACH_CATEGORY_DISPLAY ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy plural label */
					printf( esc_html__( 'Display %s on single posts', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance, false ) ) ) );
					?>
				</label>
			</p>

			<div <?php $widget->hide_element( static::DISPLAY_ON_SINGLE, static::EACH_CATEGORY_DISPLAY ); ?>>
				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( static::EACH_CATEGORY_DISPLAY ) ); ?>">
						<?php
						/* translators: Selected taxonomy single label */
						printf( esc_html__( "Display each single post's %s", 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance ) ) ) );
						?>
					</label>
					<select
						id="<?php echo esc_attr( $widget->get_field_id( static::EACH_CATEGORY_DISPLAY ) ); ?>"
						name="<?php echo esc_attr( $widget->get_field_name( static::EACH_CATEGORY_DISPLAY ) ); ?>"
						class="advanced-sidebar-menu-block-field"
					>
						<?php
						foreach ( static::get_display_each_options() as $value => $label ) {
							?>
							<option
								value="<?php echo esc_attr( $value ); ?>"
								<?php selected( $value, $instance[ static::EACH_CATEGORY_DISPLAY ] ); ?>
							>
								<?php echo esc_html( $label ); ?>
							</option>
							<?php
						}
						?>
					</select>
				</p>
			</div>

			<?php do_action( 'advanced-sidebar-menu/widget/category/singles-box', $instance, $widget ); ?>

		</div>
		<?php
	}


	/**
	 * Categories to exclude settings.
	 *
	 * @param array           $instance - Widget settings.
	 * @param Widget_Abstract $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_exclude( array $instance, $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<label for="<?php echo esc_attr( $widget->get_field_id( static::EXCLUDE ) ); ?>">
					<?php
					/* translators: Selected taxonomy plural label */
					printf( esc_html__( '%s to exclude (ids, comma separated)', 'advanced-sidebar-menu' ), esc_html( $this->get_taxonomy_label( $instance, false ) ) );
					?>
				</label>
				<input
					id="<?php echo esc_attr( $widget->get_field_id( static::EXCLUDE ) ); ?>"
					name="<?php echo esc_attr( $widget->get_field_name( static::EXCLUDE ) ); ?>"
					type="text"
					class="widefat advanced-sidebar-menu-block-field"
					value="<?php echo esc_attr( $instance[ static::EXCLUDE ] ); ?>" />
			</p>

			<?php
			do_action( 'advanced-sidebar-menu/widget/category/exclude-box', $instance, $widget );
			?>
		</div>
		<?php
	}


	/**
	 * Widget form.
	 *
	 * @param array $instance - Widget settings.
	 *
	 * @since 7.2.1
	 */
	public function form( $instance ) {
		$instance = $this->set_instance( $instance, static::$defaults );
		do_action( 'advanced-sidebar-menu/widget/category/before-form', $instance, $this );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( static::TITLE ) ); ?>">
				<?php esc_html_e( 'Title', 'advanced-sidebar-menu' ); ?>:
			</label>
			<input
				id="<?php echo esc_attr( $this->get_field_id( static::TITLE ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( static::TITLE ) ); ?>"
				class="widefat"
				type="text"
				value="<?php echo esc_attr( $instance[ static::TITLE ] ); ?>" />
		</p>
		<?php do_action( 'advanced-sidebar-menu/widget/category/before-columns', $instance, $this ); ?>
		<div class="advanced-sidebar-menu-column">
			<?php do_action( 'advanced-sidebar-menu/widget/category/left-column', $instance, $this ); ?>
		</div>

		<div class="advanced-sidebar-menu-column advanced-sidebar-menu-column-right">
			<?php do_action( 'advanced-sidebar-menu/widget/category/right-column', $instance, $this ); ?>
		</div>
		<div class="advanced-sidebar-menu-full-width"><!-- clear --></div>

		<?php
		do_action( 'advanced-sidebar-menu/widget/category/after-form', $instance, $this );

		return '';
	}


	/**
	 * Save the widget settings.
	 *
	 * @param array $new_instance - New widget settings.
	 * @param array $old_instance - Old widget settings.
	 *
	 * @return array|mixed
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['exclude'] = wp_strip_all_tags( $new_instance['exclude'] );

		return apply_filters( 'advanced-sidebar-menu/widget/category/update', $new_instance, $old_instance );
	}


	/**
	 * Widget Output
	 *
	 * @param array $args     - Widget registration args.
	 * @param array $instance - Widget settings.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance = $this->set_instance( $instance, static::$defaults );
		$menu = \Advanced_Sidebar_Menu\Menus\Category::factory( $instance, $args );

		do_action( 'advanced-sidebar-menu/widget/before-render', $menu, $this );

		$menu->render();

		do_action( 'advanced-sidebar-menu/widget/after-render', $menu, $this );
	}

}
