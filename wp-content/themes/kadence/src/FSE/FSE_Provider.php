<?php
/**
 * Kadence\FSE\FSE_Provider class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use Kadence\Service_Provider;

/**
 * Wires the Full Site Editing mode.
 *
 * Registers while functions.php loads, before core's block theme checks on
 * `after_setup_theme`.
 *
 * @since 1.6.0
 */
final class FSE_Provider extends Service_Provider {

	/**
	 * Binds the services and adds the hooks.
	 *
	 * Hooks get container callbacks, so each service is built the first time one of its hooks runs.
	 *
	 * It also does two things right away, not on a hook: it reads the mode to choose the hooks, and
	 * refreshes core's block theme flag. Core caches that flag when it builds the theme object, before
	 * functions.php loads, and reads it on `after_setup_theme` at priority 1. A callback of ours on that
	 * hook could run after another callback that already read the stale flag.
	 *
	 * @since 1.6.0
	 */
	public function register(): void {
		$this->bind_services();
		$this->add_fse_mode_settings_hooks();

		if ( ! $this->container->get( Mode::class )->is_enabled() ) {
			$this->container->get( Block_Theme_Cache::class )->refresh();
			$this->add_classic_shield_hooks();
			return;
		}

		add_filter( 'theme_file_path', $this->container->callback( Block_Theme_Index::class, 'filter_path' ), 10, 2 );
		$this->container->get( Block_Theme_Cache::class )->refresh();

		$this->add_template_hooks();
		$this->add_layout_hooks();
		$this->add_palette_hooks();
		$this->add_font_hooks();
		$this->add_button_hooks();
		$this->add_content_hooks();
		$this->add_customizer_hooks();
	}

	/**
	 * Binds the FSE services as singletons.
	 *
	 * @since 1.6.0
	 */
	private function bind_services(): void {
		$this->container->singleton( Mode::class );
		$this->container->singleton( Rest::class );
		$this->container->singleton( Block_Theme_Cache::class );
		$this->container->singleton( Dashboard::class );
		$this->container->singleton( Block_Theme_Index::class );
		$this->container->singleton( Templates::class );
		$this->container->singleton( Content::class );
		$this->container->singleton( Classic_Shield::class );
		$this->container->singleton( Customizer_Sections::class );
		$this->container->singleton( Navigation::class );
		$this->container->singleton( Repair::class );
		$this->container->singleton( Repair_Dismissal::class );
		$this->container->singleton( Accent_Complement::class );
		$this->container->singleton( Slot_Map_Builder::class );
		$this->container->singleton( Palette_Shim::class );
		$this->container->singleton( Layout_Widths::class );
		$this->container->singleton( Font_Families::class );
		$this->container->singleton( Typography_Feed::class );
		$this->container->singleton( Customizer_Swatches::class );
		$this->container->singleton( Derived_Tokens::class );
		$this->container->singleton( Palette_Literal::class );
		$this->container->singleton( Style_Feed::class );
		$this->container->singleton( Button_Styles::class );
		$this->container->singleton( Body_Colors::class );
		$this->container->singleton( Global_Stylesheet::class );

		// Built once per request, the first time the palette shim needs it.
		$this->container->singleton(
			Slot_Map::class,
			function (): Slot_Map {
				return $this->container->get( Slot_Map_Builder::class )->build();
			}
		);
	}

	/**
	 * Adds the hooks behind the FSE mode setting: its REST routes, its dashboard card,
	 * the cache reset when it changes, and the menu conversion and a fresh repair checklist
	 * when it turns on. They run whether the mode is on or off.
	 *
	 * @since 1.6.0
	 */
	private function add_fse_mode_settings_hooks(): void {
		add_action( 'rest_api_init', $this->container->callback( Rest::class, 'register_routes' ) );
		add_action( 'admin_print_styles-appearance_page_kadence', $this->container->callback( Dashboard::class, 'add_dashboard_data' ), 20 );

		// Priority 0, so the cache is cleared before listeners at the default priority run.
		add_action( 'kadence_fse_mode_enabled', $this->container->callback( Block_Theme_Cache::class, 'clear' ), 0 );
		add_action( 'kadence_fse_mode_disabled', $this->container->callback( Block_Theme_Cache::class, 'clear' ), 0 );
		add_action( 'kadence_fse_mode_enabled', $this->container->callback( Navigation::class, 'convert_menus' ) );
		add_action( 'kadence_fse_mode_enabled', $this->container->callback( Repair::class, 'build_checklist' ) );
		add_action( 'kadence_fse_mode_enabled', $this->container->callback( Repair_Dismissal::class, 'reset' ) );

		// The opt-in dialog lists a transparent header before the mode is on, so this runs in both modes.
		add_action( 'added_post_meta', $this->container->callback( Repair::class, 'track_transparent_post' ), 10, 4 );
		add_action( 'updated_post_meta', $this->container->callback( Repair::class, 'track_transparent_post' ), 10, 4 );
		add_action( 'transition_post_status', $this->container->callback( Repair::class, 'track_published_post' ), 10, 3 );
	}

	/**
	 * Keeps templates saved in Full Site Editing mode from replacing the classic templates once the mode is off.
	 *
	 * Only sites that opted in can have templates saved in the mode.
	 *
	 * @since 1.6.0
	 */
	private function add_classic_shield_hooks(): void {
		if ( $this->container->get( Mode::class )->was_enabled() ) {
			add_filter( 'get_block_templates', $this->container->callback( Classic_Shield::class, 'hide_templates' ), 10, 3 );
		}
	}

	/**
	 * Adds the hooks that serve the theme's block templates and patterns.
	 *
	 * @since 1.6.0
	 */
	private function add_template_hooks(): void {
		add_filter( 'get_block_templates', $this->container->callback( Templates::class, 'add_templates' ), 10, 3 );
		add_filter( 'get_block_file_template', $this->container->callback( Templates::class, 'resolve_template' ), 10, 3 );
		add_filter( 'pre_get_block_template', $this->container->callback( Templates::class, 'resolve_saved_template' ), 10, 3 );
		add_action( 'init', $this->container->callback( Templates::class, 'register_patterns' ) );
		add_action( 'init', $this->container->callback( Templates::class, 'register_block_styles' ) );

		// The front end and the editor canvas both run this.
		add_action( 'enqueue_block_assets', $this->container->callback( Templates::class, 'enqueue_block_css' ) );

		// The block templates never call print_styles(), so the stylesheets are enqueued up front.
		add_filter( 'kadence_preloading_styles_enabled', '__return_false' );

		add_filter( 'kadence_css_files', $this->container->callback( Global_Stylesheet::class, 'filter_css_files' ) );
	}

	/**
	 * Adds the hooks that let the Site Editor own the palette colors.
	 *
	 * @since 1.6.0
	 */
	private function add_palette_hooks(): void {
		// Nothing else in the theme hooks this in FSE mode. 20 keeps clear of plugins and
		// child themes at the default priority, and stays below Kadence Blocks at 999,
		// which replaces the theme palette in its override mode.
		add_filter( 'wp_theme_json_data_theme', $this->container->callback( Palette_Shim::class, 'fill_theme_origin' ), 20 );
		add_filter( 'wp_theme_json_data_theme', $this->container->callback( Style_Feed::class, 'fill_theme_origin' ), 20 );

		// After the generator's own `:root` block, so the redefinitions win on source order.
		add_filter( 'kadence_dynamic_css', $this->container->callback( Palette_Shim::class, 'append_properties' ), 99 );
		add_filter( 'kadence_editor_dynamic_css', $this->container->callback( Palette_Shim::class, 'append_properties' ), 99 );
		add_filter( 'kadence_dynamic_css', $this->container->callback( Style_Feed::class, 'append_properties' ), 99 );
		add_filter( 'kadence_editor_dynamic_css', $this->container->callback( Style_Feed::class, 'append_editor_properties' ), 99 );

		// Same priority and place as the shim's own, so a set token wins on source order.
		add_filter( 'kadence_dynamic_css', $this->container->callback( Derived_Tokens::class, 'append_properties' ), 99 );
		add_filter( 'kadence_editor_dynamic_css', $this->container->callback( Derived_Tokens::class, 'append_properties' ), 99 );
		add_filter( 'kadence_dynamic_css', $this->container->callback( Body_Colors::class, 'append_properties' ), 99 );
		add_filter( 'kadence_editor_dynamic_css', $this->container->callback( Body_Colors::class, 'append_editor_properties' ), 99 );
		add_filter( 'kadence_editor_body_colors', $this->container->callback( Body_Colors::class, 'filter_editor_body_colors' ) );

		add_filter( 'kadence_site_palette_option', $this->container->callback( Palette_Literal::class, 'filter_palette_option' ), 10, 2 );

		add_filter( 'customize_sanitize_js_kadence_global_palette', $this->container->callback( Customizer_Swatches::class, 'filter_js_value' ) );

		// After the theme's own callback at 10, so these declarations win on source order.
		add_action( 'customize_controls_enqueue_scripts', $this->container->callback( Customizer_Swatches::class, 'add_controls_properties' ), 11 );
	}

	/**
	 * Adds the hooks that let the Site Editor own the content width and the wide width.
	 *
	 * @since 1.6.0
	 */
	private function add_layout_hooks(): void {
		// Same priority as the palette shim, so a plugin or child theme at 10 is merged first.
		add_filter( 'wp_theme_json_data_theme', $this->container->callback( Layout_Widths::class, 'fill_theme_origin' ), 20 );

		// After the generator's own `:root` block, so the redefinitions win on source order. Registered before the
		// palette hooks, so at priority 99 the palette and style blocks still come last and nothing reads these.
		add_filter( 'kadence_dynamic_css', $this->container->callback( Layout_Widths::class, 'append_properties' ), 99 );
		add_filter( 'kadence_editor_dynamic_css', $this->container->callback( Layout_Widths::class, 'append_properties' ), 99 );
	}

	/**
	 * Adds the hooks that list the Customizer's fonts in the Site Editor's font picker, feed them
	 * into Styles → Typography, and let a change made there reach Kadence's CSS and fonts.
	 *
	 * @since 1.6.0
	 */
	private function add_font_hooks(): void {
		// Same priority as the palette shim, for the same reason.
		add_filter( 'wp_theme_json_data_theme', $this->container->callback( Font_Families::class, 'fill_theme_origin' ), 20 );

		// Keeps a theme list the Site Editor saved from outranking the one above.
		add_filter( 'wp_theme_json_data_user', $this->container->callback( Font_Families::class, 'strip_user_theme_copy' ) );

		// The Site Editor loads the saved record over REST, which skips the filter above.
		add_filter( 'rest_request_after_callbacks', $this->container->callback( Font_Families::class, 'strip_theme_copy_from_response' ), 10, 3 );

		// After plugins and child themes at 10, so the Customizer's fonts win.
		add_filter( 'wp_theme_json_data_theme', $this->container->callback( Typography_Feed::class, 'fill_theme_origin' ), 20 );

		// Same priority as the palette shim's, so these land after the generator's own rules and win on source order.
		add_filter( 'kadence_dynamic_css', $this->container->callback( Typography_Feed::class, 'append_properties' ), 99 );
		add_filter( 'kadence_editor_dynamic_css', $this->container->callback( Typography_Feed::class, 'append_editor_properties' ), 99 );

		// Installed families are dropped after the chosen weights are added, so none comes back.
		add_filter( 'kadence_theme_google_fonts_array', $this->container->callback( Typography_Feed::class, 'add_chosen_variants' ), 10, 2 );
		add_filter( 'kadence_theme_google_fonts_array', $this->container->callback( Font_Families::class, 'skip_installed_google_fonts' ), 20 );
	}

	/**
	 * Adds the hooks that give the Site Editor the Customizer's buttons.
	 *
	 * @since 1.6.0
	 */
	private function add_button_hooks(): void {
		// Before core registers its blocks at 10, so every theme origin built with the Button
		// block keeps Secondary's styles. Core caches the origin until another block registers.
		add_action( 'init', $this->container->callback( Button_Styles::class, 'register_block_style' ), 9 );

		// Beside the style feed, at its priority.
		add_filter( 'wp_theme_json_data_theme', $this->container->callback( Button_Styles::class, 'fill_theme_origin' ), 20 );
	}

	/**
	 * Adds the hooks that adjust post content and layout rendered by the block templates.
	 *
	 * @since 1.6.0
	 */
	private function add_content_hooks(): void {
		add_filter( 'render_block_core/group', $this->container->callback( Content::class, 'hide_title_and_feature' ), 10, 2 );
		add_filter( 'render_block_core/post-featured-image', $this->container->callback( Content::class, 'hide_title_and_feature' ), 10, 2 );
		add_filter( 'render_block_core/post-template', $this->container->callback( Content::class, 'add_list_item_class' ), 10, 2 );
		add_filter( 'kadence_post_layout', $this->container->callback( Content::class, 'disable_sidebar' ) );
		add_filter( 'kadence_theme_enable_comment_css', $this->container->callback( Content::class, 'enable_comment_styles' ) );
	}

	/**
	 * Adds the hooks that hide the Customizer panels the Site Editor replaces.
	 *
	 * The filters run at priority 30, after the theme's own section filter at 20, which forces
	 * the footer widget sections active. The re-homing runs at 20 on customize_register, after
	 * the theme registers Site Identity inside the Header panel.
	 *
	 * @since 1.6.0
	 */
	private function add_customizer_hooks(): void {
		add_action( 'customize_register', $this->container->callback( Customizer_Sections::class, 'rehome_site_identity' ), 20 );
		add_action( 'customize_register', $this->container->callback( Customizer_Sections::class, 'describe_typography' ), 20 );
		add_action( 'customize_register', $this->container->callback( Customizer_Sections::class, 'add_layout_notice' ), 20 );
		add_filter( 'customize_panel_active', $this->container->callback( Customizer_Sections::class, 'filter_panel_active' ), 30, 2 );
		add_filter( 'customize_section_active', $this->container->callback( Customizer_Sections::class, 'filter_section_active' ), 30, 2 );
		add_filter( 'customize_control_active', $this->container->callback( Customizer_Sections::class, 'filter_control_active' ), 30, 2 );
		add_filter( 'kadence_theme_customizer_control_contexts', $this->container->callback( Customizer_Sections::class, 'filter_control_contexts' ), 30 );
	}
}
