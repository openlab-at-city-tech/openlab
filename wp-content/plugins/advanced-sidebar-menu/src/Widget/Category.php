<?php

namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\Menus\Category as CategoryMenu;
use Advanced_Sidebar_Menu\Menus\Menu_Abstract;

/**
 * Creates a Widget of parent Child Categories
 *
 * @author   OnPoint Plugins
 *
 * @package  Advanced Sidebar Menu
 *
 * @phpstan-import-type CATEGORY_SETTINGS from CategoryMenu
 * @phpstan-import-type WIDGET_ARGS from Widget
 *
 * @formatter:off
 * @phpstan-type DEFAULTS \Required<\Pick<CATEGORY_SETTINGS,'title'|'exclude'|'display_all'|'include_childless_parent'|'include_parent'|'levels'|'new_widget'|'single'>>
 * @formatter:on
 *
 * @implements WidgetWithId<CATEGORY_SETTINGS, DEFAULTS>
 * @extends Widget_Abstract<CATEGORY_SETTINGS, DEFAULTS>
 */
class Category extends Widget_Abstract implements WidgetWithId {
	/**
	 * @use Checkbox<CATEGORY_SETTINGS>
	 */
	use Checkbox;

	/**
	 * @use Instance<CATEGORY_SETTINGS, DEFAULTS>
	 */
	use Instance;

	use WidgetIdAccess;

	public const NAME = 'advanced_sidebar_menu_category';

	public const TITLE                    = Menu_Abstract::TITLE;
	public const INCLUDE_PARENT           = Menu_Abstract::INCLUDE_PARENT;
	public const INCLUDE_CHILDLESS_PARENT = Menu_Abstract::INCLUDE_CHILDLESS_PARENT;
	public const ORDER_BY                 = Menu_Abstract::ORDER_BY;
	public const EXCLUDE                  = Menu_Abstract::EXCLUDE;
	public const DISPLAY_ALL              = Menu_Abstract::DISPLAY_ALL;
	public const LEVELS                   = Menu_Abstract::LEVELS;

	public const DISPLAY_ON_SINGLE     = CategoryMenu::DISPLAY_ON_SINGLE;
	public const EACH_CATEGORY_DISPLAY = CategoryMenu::EACH_CATEGORY_DISPLAY;

	/**
	 * Default widget values.
	 *
	 * @var DEFAULTS
	 */
	protected static $defaults = [
		self::TITLE                    => '',
		self::INCLUDE_PARENT           => '',
		self::INCLUDE_CHILDLESS_PARENT => '',
		self::DISPLAY_ON_SINGLE        => '',
		self::EACH_CATEGORY_DISPLAY    => 'widget',
		self::EXCLUDE                  => '',
		self::DISPLAY_ALL              => '',
		self::LEVELS                   => '1',
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
		$control_ops = [];
		if ( ! wp_is_mobile() ) {
			$control_ops['width'] = 620;
		}

		parent::__construct( self::NAME, __( 'Advanced Sidebar - Categories', 'advanced-sidebar-menu' ), $widget_ops, $control_ops );

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
	 * @since 8.2.0
	 *
	 * @phpstan-param CATEGORY_SETTINGS $instance
	 *
	 * @param array                     $instance - Widget settings.
	 * @param bool                      $single   - Singular label or plural.
	 *
	 * @return string
	 */
	public function get_taxonomy_label( array $instance, $single = true ): string {
		$taxonomy = get_taxonomy( apply_filters( 'advanced-sidebar-menu/widget/category/taxonomy-for-label', 'category', $this->control_options, $instance ) );
		if ( false === $taxonomy ) {
			$taxonomy = get_taxonomy( 'category' );// Sensible fallback.
			if ( false === $taxonomy ) {
				return $single ? __( 'Category', 'advanced-sidebar-menu' ) : __( 'Categories', 'advanced-sidebar-menu' );
			}
		}

		return $single ? $taxonomy->labels->singular_name : $taxonomy->labels->name;
	}


	/**
	 * Get list of display each single post's category options.
	 *
	 * @return array{widget: string, list: string}
	 */
	public static function get_display_each_options() {
		return [
			CategoryMenu::EACH_WIDGET => __( 'In a new widget', 'advanced-sidebar-menu' ),
			CategoryMenu::EACH_LIST   => __( 'In another list in the same widget', 'advanced-sidebar-menu' ),
		];
	}


	/**
	 * Display options.
	 *
	 * @phpstan-param CATEGORY_SETTINGS $instance
	 *
	 * @param array                     $instance - Widget settings.
	 * @param Category                  $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_display( array $instance, Category $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<?php $widget->checkbox( self::INCLUDE_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy single label */
					printf( esc_html__( 'Display the highest level parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance ) ) ) );
					?>
				</label>
			</p>
			<p>
				<?php $widget->checkbox( self::INCLUDE_CHILDLESS_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy single label */
					printf( esc_html__( 'Display menu when there is only the parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance ) ) ) );
					?>
				</label>
			</p>
			<p>
				<?php $widget->checkbox( self::DISPLAY_ALL, self::LEVELS ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy plural label */
					printf( esc_html__( 'Always display child %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance, false ) ) ) );
					?>
				</label>
			</p>
			<div <?php $widget->hide_element( self::DISPLAY_ALL, self::LEVELS ); ?>>
				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( self::LEVELS ) ); ?>">
						<?php
						ob_start();
						?>
						<select
							id="<?php echo esc_attr( $widget->get_field_id( self::LEVELS ) ); ?>"
							name="<?php echo esc_attr( $widget->get_field_name( self::LEVELS ) ); ?>">
							<option value="100">
								<?php esc_html_e( '- All -', 'advanced-sidebar-menu' ); ?>
							</option>
							<?php
							for ( $i = 1; $i < 6; $i ++ ) {
								?>
								<option
									value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $i, (int) $instance[ self::LEVELS ] ); ?>>
									<?php echo \absint( $i ); ?>
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
	 * @phpstan-param CATEGORY_SETTINGS $instance
	 *
	 * @param array                     $instance - Widget settings.
	 * @param Category                  $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_display_on_single_posts( array $instance, Category $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>

				<?php $widget->checkbox( self::DISPLAY_ON_SINGLE, self::EACH_CATEGORY_DISPLAY ); ?>
				<label>
					<?php
					/* translators: Selected taxonomy plural label */
					printf( esc_html__( 'Display %s on single posts', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance, false ) ) ) );
					?>
				</label>
			</p>

			<div <?php $widget->hide_element( self::DISPLAY_ON_SINGLE, self::EACH_CATEGORY_DISPLAY ); ?>>
				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( self::EACH_CATEGORY_DISPLAY ) ); ?>">
						<?php
						/* translators: Selected taxonomy single label */
						printf( esc_html__( "Display each single post's %s", 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_taxonomy_label( $instance ) ) ) );
						?>
					</label>
					<select
						id="<?php echo esc_attr( $widget->get_field_id( self::EACH_CATEGORY_DISPLAY ) ); ?>"
						name="<?php echo esc_attr( $widget->get_field_name( self::EACH_CATEGORY_DISPLAY ) ); ?>"
						class="advanced-sidebar-menu-block-field"
					>
						<?php
						foreach ( static::get_display_each_options() as $value => $label ) {
							?>
							<option
								value="<?php echo esc_attr( $value ); ?>"
								<?php selected( $value, $instance[ self::EACH_CATEGORY_DISPLAY ] ); ?>
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
	 * @phpstan-param CATEGORY_SETTINGS $instance
	 *
	 * @param array                     $instance - Widget settings.
	 * @param Category                  $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_exclude( array $instance, Category $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<label for="<?php echo esc_attr( $widget->get_field_id( self::EXCLUDE ) ); ?>">
					<?php
					/* translators: Selected taxonomy plural label */
					printf( esc_html__( '%s to exclude (ids, comma separated)', 'advanced-sidebar-menu' ), esc_html( $this->get_taxonomy_label( $instance, false ) ) );
					?>
				</label>
				<input
					id="<?php echo esc_attr( $widget->get_field_id( self::EXCLUDE ) ); ?>"
					name="<?php echo esc_attr( $widget->get_field_name( self::EXCLUDE ) ); ?>"
					type="text"
					class="widefat advanced-sidebar-menu-block-field"
					value="<?php echo esc_attr( $instance[ self::EXCLUDE ] ); ?>" />
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
	 * @phpstan-param CATEGORY_SETTINGS $instance
	 *
	 * @param array                     $instance - Widget settings.
	 *
	 * @return string
	 */
	public function form( $instance ) {
		$settings = $this->set_instance( $instance, static::$defaults );
		do_action( 'advanced-sidebar-menu/widget/category/before-form', $settings, $this );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( self::TITLE ) ); ?>">
				<?php esc_html_e( 'Title', 'advanced-sidebar-menu' ); ?>:
			</label>
			<input
				id="<?php echo esc_attr( $this->get_field_id( self::TITLE ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( self::TITLE ) ); ?>"
				class="widefat"
				type="text"
				value="<?php echo esc_attr( $settings[ self::TITLE ] ); ?>" />
		</p>
		<?php do_action( 'advanced-sidebar-menu/widget/category/before-columns', $settings, $this ); ?>
		<div class="advanced-sidebar-menu-column">
			<?php do_action( 'advanced-sidebar-menu/widget/category/left-column', $settings, $this ); ?>
		</div>

		<div class="advanced-sidebar-menu-column advanced-sidebar-menu-column-right">
			<?php do_action( 'advanced-sidebar-menu/widget/category/right-column', $settings, $this ); ?>
		</div>
		<div class="advanced-sidebar-menu-full-width"><!-- clear --></div>

		<?php
		do_action( 'advanced-sidebar-menu/widget/category/after-form', $settings, $this );

		return '';
	}


	/**
	 * Save the widget settings.
	 *
	 * @param CATEGORY_SETTINGS $new_instance - New widget settings.
	 * @param CATEGORY_SETTINGS $old_instance - Old widget settings.
	 *
	 * @return CATEGORY_SETTINGS
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['exclude'] = wp_strip_all_tags( $new_instance['exclude'] );

		return apply_filters( 'advanced-sidebar-menu/widget/category/update', $new_instance, $old_instance );
	}


	/**
	 * Widget Output
	 *
	 * @param WIDGET_ARGS       $args     - Widget registration args.
	 * @param CATEGORY_SETTINGS $instance - Widget settings.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$settings = $this->set_instance( $instance, static::$defaults );
		$menu = CategoryMenu::factory( $settings, $args );

		do_action( 'advanced-sidebar-menu/widget/before-render', $menu, $this );

		$menu->render();

		do_action( 'advanced-sidebar-menu/widget/after-render', $menu, $this );
	}
}
