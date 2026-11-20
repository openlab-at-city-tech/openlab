<?php
/**
 * Kadence\FSE\Customizer_Sections class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use Kadence\Theme_Customizer;
use Kadence_Control_Blank;
use WP_Customize_Control;
use WP_Customize_Manager;
use WP_Customize_Panel;
use WP_Customize_Section;

/**
 * Decides which Customizer panels, sections and controls Full Site Editing mode shows.
 *
 * The Site Editor replaces some of what the Customizer edits. Those panels are hidden,
 * not removed: every setting stays registered, so stored values load, export and survive
 * turning the mode off unchanged, and the Customizer never writes to a setting its user
 * could not see.
 *
 * Third-party and core sections are left alone. The theme moves core's Site Identity section
 * into the Header panel, so {@see self::rehome_site_identity()} takes it back out to the top
 * level and it stays visible when the Header panel is hidden. Only the theme's logo layout
 * controls in it go.
 *
 * @since 1.6.0
 */
final class Customizer_Sections {

	/**
	 * The Site Editor owns this, so the Customizer does not show it.
	 *
	 * @since 1.6.0
	 */
	public const HIDDEN = 'hidden';

	/**
	 * Theme behavior with no block-theme equivalent, so it stays visible.
	 *
	 * @since 1.6.0
	 */
	public const LIVE = 'live';

	/**
	 * Still reaches Full Site Editing output, so it stays visible until Global Styles owns
	 * its value and the output that follows it is gated.
	 *
	 * @since 1.6.0
	 */
	public const TRANSITIONAL = 'transitional';

	/**
	 * The theme's General → Layout section.
	 *
	 * @since 1.6.0
	 */
	private const LAYOUT = 'kadence_customizer_general_layout';

	/**
	 * Core's Site Identity section.
	 *
	 * @since 1.6.0
	 */
	private const SITE_IDENTITY = 'title_tagline';

	/**
	 * The theme's Typography section.
	 *
	 * @since 1.6.0
	 */
	private const TYPOGRAPHY = 'kadence_customizer_general_typography';

	/**
	 * Controls hidden one by one inside a section that stays visible.
	 *
	 * @since 1.6.0
	 */
	private const HIDDEN_CONTROLS = [
		// Full Site Editing forces preloading off.
		'enable_preload',
		// Classic template ordering, which the block templates do not use.
		'comment_form_before_list',
		// Styles → Typography sets the base and heading fonts, with the headings title and italics toggle that go with them.
		'base_font',
		'load_base_italic',
		'info_heading',
		'heading_font',
		'h1_font',
		'h2_font',
		'h3_font',
		'h4_font',
		'h5_font',
		'h6_font',
		// Styles → Layout sets the content and wide widths. Edge spacing, narrow width and the content style stay.
		'content_width',
	];

	/**
	 * The class of each panel, which its sections inherit unless the map names them.
	 *
	 * Panels that mix classes, such as General and Colors & Fonts, are not listed: each of
	 * their sections is named in {@see self::SECTIONS}.
	 *
	 * @since 1.6.0
	 */
	private const PANELS = [
		'kadence_customizer_header'    => self::HIDDEN,
		'kadence_customizer_footer'    => self::HIDDEN,
		'kadence_customizer_all_posts' => self::TRANSITIONAL,
	];

	/**
	 * The class of each theme section that does not inherit one from its panel.
	 *
	 * Sections the header and footer panels hold, including those that Kadence Pro and
	 * WooCommerce add, inherit {@see self::HIDDEN} from the panel. The Posts/Pages Layout
	 * sections, including custom post types, inherit {@see self::TRANSITIONAL}.
	 *
	 * @since 1.6.0
	 */
	private const SECTIONS = [
		// Colors & Fonts.
		'kadence_customizer_general_colors'      => self::TRANSITIONAL,
		self::TYPOGRAPHY                         => self::TRANSITIONAL,
		'kadence_customizer_general_buttons'     => self::TRANSITIONAL,
		'kadence_customizer_secondary_button'    => self::LIVE,
		'kadence_customizer_outline_button'      => self::LIVE,
		// General.
		'kadence_customizer_general_layout'      => self::TRANSITIONAL,
		'kadence_customizer_sidebar'             => self::HIDDEN,
		'kadence_customizer_sidebar_design'      => self::HIDDEN,
		'kadence_customizer_general_image'       => self::TRANSITIONAL,
		'kadence_customizer_scroll_up'           => self::LIVE,
		'kadence_customizer_scroll_up_design'    => self::LIVE,
		'kadence_customizer_general_social'      => self::HIDDEN,
		'kadence_customizer_general_comments'    => self::LIVE,
		'kadence_customizer_general_performance' => self::LIVE,
		'kadence_customizer_breadcrumbs'         => self::HIDDEN,
		'kadence_customizer_general_404'         => self::HIDDEN,
		// The 404 design still styles the 404 template, so it waits for the output gating.
		'kadence_customizer_general_404_design'  => self::TRANSITIONAL,
		// Top-level Posts/Pages Layout sections.
		'kadence_customizer_search'              => self::TRANSITIONAL,
		'kadence_customizer_search_design'       => self::TRANSITIONAL,
		// Site identity.
		self::SITE_IDENTITY                      => self::LIVE,
		'kadence_customizer_site_identity'       => self::LIVE,
		// The Kadence Pro upsell.
		'kadence_pro_upgrade'                    => self::LIVE,
	];

	/**
	 * Gets the class of a theme section.
	 *
	 * @since 1.6.0
	 *
	 * @param string $section_id The registered section id.
	 * @param string $panel_id   The id of the panel the section sits in, or an empty string.
	 * @return string|null One of the class constants, or null for a section the theme did not register.
	 */
	public function get_section_class( string $section_id, string $panel_id = '' ): ?string {
		return self::SECTIONS[ $section_id ] ?? self::PANELS[ $panel_id ] ?? null;
	}

	/**
	 * Gets the class of a theme panel.
	 *
	 * @since 1.6.0
	 *
	 * @param string $panel_id The registered panel id.
	 * @return string|null One of the class constants, or null for a panel with mixed sections or one the theme did not register.
	 */
	public function get_panel_class( string $panel_id ): ?string {
		return self::PANELS[ $panel_id ] ?? null;
	}

	/**
	 * Moves Site Identity out of the hidden Header panel to the top level.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Customize_Manager $wp_customize The Customizer manager.
	 * @return void
	 */
	public function rehome_site_identity( $wp_customize ): void {
		$section = $wp_customize->get_section( self::SITE_IDENTITY );

		if ( $section ) {
			$section->panel = '';
		}
	}

	/**
	 * Tells the Typography section where the hidden base and heading fonts are edited now.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Customize_Manager $wp_customize The Customizer manager.
	 * @return void
	 */
	public function describe_typography( $wp_customize ): void {
		$section = $wp_customize->get_section( self::TYPOGRAPHY );

		if ( $section ) {
			/* translators: "Styles" and "Typography" are the names WordPress shows in the Site Editor. */
			$section->description = esc_html__( 'Base and heading fonts are set in the Site Editor, under Styles → Typography.', 'kadence' );
		}
	}

	/**
	 * Takes the hidden Content Max Width control's place with a link to where the widths moved.
	 *
	 * The notice has no setting, so nothing it shows is stored or published.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Customize_Manager $wp_customize The Customizer manager.
	 * @return void
	 */
	public function add_layout_notice( $wp_customize ): void {
		$site_editor_layout_url = add_query_arg(
			[
				'p'       => '/styles',
				'section' => '/layout',
			],
			admin_url( 'site-editor.php' )
		);

		$wp_customize->add_control(
			new Kadence_Control_Blank(
				$wp_customize,
				'kadence_content_width_fse_notice',
				[
					'label'       => esc_html__( 'Content Width', 'kadence' ),
					'description' => sprintf(
						'%1$s <a href="%2$s">%3$s</a>',
						esc_html__( 'The content and wide widths have moved to the Site Editor.', 'kadence' ),
						esc_url( $site_editor_layout_url ),
						esc_html__( 'Edit the widths in the Site Editor', 'kadence' )
					),
					'section'     => self::LAYOUT,
					'settings'    => [],
					'capability'  => 'edit_theme_options',
					// Before Content Edge Spacing and the other priority 20 controls, where Content Max Width was.
					'priority'    => 19,
				]
			)
		);
	}

	/**
	 * Hides a panel the Site Editor replaces.
	 *
	 * @since 1.6.0
	 *
	 * @param bool               $active Whether the panel is active.
	 * @param WP_Customize_Panel $panel  The panel.
	 * @return bool
	 */
	public function filter_panel_active( $active, $panel ) {
		return self::HIDDEN === $this->get_panel_class( $panel->id ) ? false : (bool) $active;
	}

	/**
	 * Hides a section the Site Editor replaces.
	 *
	 * @since 1.6.0
	 *
	 * @param bool                 $active  Whether the section is active.
	 * @param WP_Customize_Section $section The section.
	 * @return bool
	 */
	public function filter_section_active( $active, $section ) {
		return self::HIDDEN === $this->get_section_class( $section->id, (string) $section->panel ) ? false : (bool) $active;
	}

	/**
	 * Hides a control the Site Editor replaces inside a section that stays visible.
	 *
	 * These are the controls with no use in Full Site Editing mode, or that the Site Editor
	 * edits instead: the single settings named in {@see self::HIDDEN_CONTROLS}, and the Kadence logo layout controls in Site
	 * Identity, which belong to the hidden header. Core's own title, tagline and logo
	 * controls stay.
	 *
	 * @since 1.6.0
	 *
	 * @param bool                 $active  Whether the control is active.
	 * @param WP_Customize_Control $control The control.
	 * @return bool
	 */
	public function filter_control_active( $active, $control ) {
		return $this->is_hidden_control( (string) $control->id, (string) $control->section ) ? false : (bool) $active;
	}

	/**
	 * Drops the display rules of the controls {@see self::filter_control_active()} hides, and of
	 * every other Site Identity control.
	 *
	 * The theme's Customizer script applies a control's display rules when it loads and
	 * whenever a setting they read changes, which would show a hidden control again. Core's
	 * logo, title and tagline only show on the General tab, and Site Identity has no tabs in
	 * the mode, so a Design tab picked in another section would hide them.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, mixed> $contexts The display rules, keyed by control id.
	 * @return array<string, mixed>
	 */
	public function filter_control_contexts( $contexts ) {
		global $wp_customize;

		$contexts = (array) $contexts;
		$settings = (array) Theme_Customizer::$settings;

		foreach ( array_keys( $contexts ) as $id ) {
			$control = $wp_customize instanceof WP_Customize_Manager ? $wp_customize->get_control( (string) $id ) : null;
			$section = $control ? $control->section : ( $settings[ $id ]['section'] ?? '' );

			if ( self::SITE_IDENTITY === $section || $this->is_hidden_control( (string) $id, (string) $section ) ) {
				unset( $contexts[ $id ] );
			}
		}

		return $contexts;
	}

	/**
	 * Whether a control is one the Site Editor replaces inside a section that stays visible.
	 *
	 * @since 1.6.0
	 *
	 * @param string $id         The control id.
	 * @param string $section_id The id of the control's section.
	 * @return bool
	 */
	private function is_hidden_control( string $id, string $section_id ): bool {
		if ( in_array( $id, self::HIDDEN_CONTROLS, true ) ) {
			return true;
		}

		return self::SITE_IDENTITY === $section_id && array_key_exists( $id, (array) Theme_Customizer::$settings );
	}
}
