<?php
/**
 * Kadence\Style_Guide\Component class
 *
 * @package kadence
 */

namespace Kadence\Style_Guide;

use Kadence\Component_Interface;
use function Kadence\kadence;

/**
 * Class for managing style guide.
 */
class Component implements Component_Interface {
	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'style_guide';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'customize_preview_init', array( $this, 'preview_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'controls_scripts' ) );
		add_action( 'wp_footer', array( $this, 'style_guide_template' ) );
	}


	/**
	 * Customizer Preview css
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function preview_scripts() {
		wp_enqueue_style( 'kadence-style-guide-styles', get_template_directory_uri() . '/assets/css/style-guide.min.css', array(), KADENCE_VERSION );
		wp_enqueue_script( 'kadence-style-guide-previewer', get_template_directory_uri() . '/assets/js/style-guide-previewer.min.js', array( 'jquery', 'customize-preview' ), KADENCE_VERSION, false );
	}

	/**
	 * Customizer Controls css
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function controls_scripts() {
		wp_enqueue_script( 'kadence-style-guide-controls', get_template_directory_uri() . '/assets/js/style-guide-controls.min.js', array(), KADENCE_VERSION );
		wp_enqueue_style( 'kadence-style-guide-controls', get_template_directory_uri() . '/assets/css/style-guide-controls.min.css', array(), KADENCE_VERSION );
	}

	/**
	 * Get Option Type
	 *
	 * @access public
	 * @return string
	 */
	public function style_guide_template() {
		if ( ! is_customize_preview() ) {
			return;
		}

		?>
			<div class="kt-style-guide-wrapper">
				<?php echo do_shortcode( $this->render_style_guide_markup() ); ?>
			</div>
		<?php
	}

	/**
	 * Customizer Easy Navigation Tour Markup.
	 *
	 * @return mixed HTML Markup.
	 * @since 4.8.0
	 */
	public function render_style_guide_markup() {
		$settings = apply_filters(
			'kadence_style_guide_color_palette',
			array(
				'color_groups' => array(
					'accent' => array(
						'title' => __( 'Accents', 'kadence' ),
						'colors' => array(
							'palette1' => array(
								'title' => __( 'Accent', 'kadence' ),
								'code'  => 'var(--global-palette1)',
							),
							'palette2' => array(
								'title' => __( 'Accent - alt', 'kadence' ),
								'code'  => 'var(--global-palette2)',
							),
							'palette10' => array(
								'title' => __( 'Accent - complement', 'kadence' ),
								'code'  => 'var(--global-palette10)',
							),
						),
					),
					'contrast' => array(
						'title' => __( 'Contrast', 'kadence' ),
						'colors' => array(
							'palette3' => array(
								'title' => __( 'Strongest text', 'kadence' ),
								'code'  => 'var(--global-palette3)',
							),
							'palette4' => array(
								'title' => __( 'Strong text', 'kadence' ),
								'code'  => 'var(--global-palette4)',
							),
							'palette5' => array(
								'title' => __( 'Medium text', 'kadence' ),
								'code'  => 'var(--global-palette5)',
							),
							'palette6' => array(
								'title' => __( 'Subtle text', 'kadence' ),
								'code'  => 'var(--global-palette6)',
							),
						),
					),
					'base' => array(
						'title' => __( 'Base', 'kadence' ),
						'colors' => array(
							'palette7' => array(
								'title' => __( 'Subtle background', 'kadence' ),
								'code'  => 'var(--global-palette7)',
							),
							'palette8' => array(
								'title' => __( 'Lighter background', 'kadence' ),
								'code'  => 'var(--global-palette8)',
							),
							'palette9' => array(
								'title' => __( 'White or offwhite', 'kadence' ),
								'code'  => 'var(--global-palette9)',
							),
						),
					),
					'notice' => array(
						'title' => __( 'Notices', 'kadence' ),
						'colors' => array(
							'palette11' => array(
								'title' => __( 'Success', 'kadence' ),
								'code'  => 'var(--global-palette11)',
							),
							'palette12' => array(
								'title' => __( 'Info', 'kadence' ),
								'code'  => 'var(--global-palette12)',
							),
							'palette13' => array(
								'title' => __( 'Alert', 'kadence' ),
								'code'  => 'var(--global-palette13)',
							),
							'palette14' => array(
								'title' => __( 'Warning', 'kadence' ),
								'code'  => 'var(--global-palette14)',
							),
							'palette15' => array(
								'title' => __( 'Rating', 'kadence' ),
								'code'  => 'var(--global-palette15)',
							),
						),
					),
				),
			)
		);

		ob_start();
		?>
			<button class="kt-close-tour" type="button">
				<span class="screen-reader-text"><?php esc_html_e( 'Close', 'kadence' ); ?></span>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg>
			</button>
			<div class="kt-tour-inner-wrap">
				<div class="kt-quick-tour-body">
					<div class="kt-sg-2-col-grid">
						<div class="kt-styler-card">
							<p class="kt-sg-card-title"> <?php esc_html_e( 'Site Title & Logo', 'kadence' ); ?>
							<div class="kt-sg-element-wrap kt-sg-logo-section <?php echo esc_attr( kadence()->option('logo-title-inline' ) ? 'kt-logo-title-inline' : '' ); ?>">
								<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'section', 'title_tagline' ) ); ?>
								<?php do_action( 'kadence_site_branding' ); ?>
							</div>
						</div>
						<div class="kt-sg-1-col-grid">
							<div class="kt-styler-card">
								<p class="kt-sg-card-title"> <?php esc_html_e( 'Site Icon', 'kadence' ); ?>
								<div class="kt-sg-element-wrap">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'section', 'kadence_customizer_site_identity' ) ); ?>
									<?php $this->site_icon_update(); ?>
								</div>
							</div>

							<div class="kt-styler-card">
								<p class="kt-sg-card-title"> <?php esc_html_e( 'Buttons', 'kadence' ); ?>
								<div class="kt-sg-button-element-wrap">
									<div class="kt-sg-element-wrap">
										<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'section', 'kadence_customizer_general_buttons', 'general', '', true ) ); ?>
										<button class="button kt-quick-tour-item-trigger" <?php echo $this->get_style_guide_shortcut_trigger_attributes( 'section', 'kadence_customizer_general_buttons'); ?>>
											<?php esc_html_e( 'Base', 'kadence' ); ?> 
										</button>
									</div>
									<div class="kt-sg-element-wrap">
										<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'section', 'kadence_customizer_secondary_button', 'general', '', true ) ); ?>
										<button class="button button-style-secondary kt-quick-tour-item-trigger" <?php echo $this->get_style_guide_shortcut_trigger_attributes( 'section', 'kadence_customizer_secondary_button'); ?>>
											<?php esc_html_e( 'Secondary', 'kadence' ); ?> 
										</button>
									</div>
									<div class="kt-sg-element-wrap">
										<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'section', 'kadence_customizer_outline_button', 'general', '', true ) ); ?>
										<button class="button button-style-outline kt-quick-tour-item-trigger" <?php echo $this->get_style_guide_shortcut_trigger_attributes( 'section', 'kadence_customizer_outline_button'); ?>>
											<?php esc_html_e( 'Outline', 'kadence' ); ?> 
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="kt-sg-colors-section kt-styler-card">
						<p class="kt-sg-card-title"> <?php esc_html_e( 'Colors', 'kadence' ); ?>
						<div class="kt-sg-colors-section-wrap">
							<?php
							foreach ( $settings['color_groups'] as $key => $group ) {
								?>
								<div class="kt-sg-color-group-wrap">
									<p class="kt-sg-color-group-title"> <?php echo esc_html( $group['title'] ); ?></p>
									<div class="kt-sg-color-items-wrap">
									<?php
									foreach ( $group['colors'] as $key => $data_attrs ) {
										?>
										<div class="kt-sg-color-item-wrap">
											<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'kadence_color_palette', 'general', 'data-reference="kt-' . esc_attr( $key ) . '"' ) ); ?>
											<span class="kt-sg-color-picker" style="background:<?php echo esc_attr( $data_attrs['code'] ); ?>"> </span>
											<span class="kt-sg-field-title"> <?php echo esc_html( $data_attrs['title'] ); ?>
										</div>
										<?php
									}
									?>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>

					<div class="kt-sg-content-section-wrap kt-styler-card">
						<p class="kt-sg-card-title"> <?php esc_html_e( 'Typography', 'kadence' ); ?>
						<div class="kt-sg-content-inner-wrap">

							<div class="kt-sg-heading-more-section">
								<div class="kt-sg-heading-card">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'h1_font', 'general' ) ); ?>
									
									<h1 class="kt-sg-heading"> <?php esc_html_e( 'H1 Heading', 'kadence' ); ?> </h1>
								</div>

								<div class="kt-sg-heading-card">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'h2_font', 'general' ) ); ?>
									
									<h2 class="kt-sg-heading"> <?php esc_html_e( 'H2 Heading', 'kadence' ); ?> </h2>
								</div>

								<div class="kt-sg-heading-card">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'h3_font', 'general' ) ); ?>
									
									<h3 class="kt-sg-heading"> <?php esc_html_e( 'H3 Heading', 'kadence' ); ?> </h3>
								</div>

								<div class="kt-sg-heading-card">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'h4_font', 'general' ) ); ?>
									
									<h4 class="kt-sg-heading"> <?php esc_html_e( 'H4 Heading', 'kadence' ); ?> </h4>
								</div>

								<div class="kt-sg-heading-card">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'h5_font', 'general' ) ); ?>
									
									<h5 class="kt-sg-heading"> <?php esc_html_e( 'H5 Heading', 'kadence' ); ?> </h5>
								</div>

								<div class="kt-sg-heading-card">
									<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'h6_font', 'general' ) ); ?>
									
									<h6 class="kt-sg-heading"> <?php esc_html_e( 'H6 Heading', 'kadence' ); ?> </h6>
								</div>
							</div>

							<div class="kt-sg-content-section">
								<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'base_font', 'general' ) ); ?>
								<p> <?php esc_html_e( 'This is your website\'s main body text; it represents the standard paragraph style used across pages and posts. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur cursus pellentesque sem, ac maximus tortor venenatis ac. Aenean convallis metus libero, ut sagittis nisi commodo nec. Sed tempor tempor erat, blandit pellentesque tortor. Proin ipsum velit, dictum ac luctus a, eleifend et sapien.', 'kadence' ); ?> </p>

								<?php echo do_shortcode( $this->get_style_guide_shortcut_trigger( 'control', 'base_font', 'general' ) ); ?>
								<p> <?php esc_html_e( 'Experiment with various font families, sizes, weights, and styles to establish a clear hierarchy and visual rhythm across your website. Each level of text, from bold, attention-grabbing headings to clean, readable body copy, works together to create balance, enhance readability, and guide users smoothly through your content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur cursus pellentesque sem, ac maximus tortor venenatis ac. Aenean convallis metus libero, ut sagittis nisi commodo nec. Sed tempor tempor erat, blandit pellentesque tortor.', 'kadence' ); ?> </p>

								<p class="kt-sg-card-title"> <?php esc_html_e( 'Quote', 'kadence' ); ?>
								<blockquote>
									<p> <?php esc_html_e( 'Good typography is invisible. Bad typography is everywhere.', 'kadence' ); ?> </p> <br/>
									<footer> <?php esc_html_e( 'Anonymous', 'kadence' ); ?> </footer>
								</blockquote>

								<p class="kt-sg-content-divider"></p>

								<p class="kt-sg-card-title"> <?php esc_html_e( 'Unordered List', 'kadence' ); ?>
								<ul>
									<li> <?php esc_html_e( 'List Item 1', 'kadence' ); ?> </li>
									<li> <?php esc_html_e( 'List Item 2', 'kadence' ); ?> </li>
									<li> <?php esc_html_e( 'List Item 3', 'kadence' ); ?> </li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render customizer style guide shortcut pencil.
	 *
	 * @param string $type Section|Control.
	 * @param string $name Section name|Control name.
	 * @param string $context General|Design name.
	 * @param string $extras if any other parameter to pass.
	 *
	 * @return string Trigger for style guide shortcut.
	 * @since 4.8.0
	 */
	public function get_style_guide_shortcut_trigger( $type, $name, $context = 'general', $extras = '', $is_small = false ) {
		$small_class_string = $is_small ? 'kt-quick-tour-item-small' : '';
		return '<span class="kt-quick-tour-item kt-quick-tour-item-trigger ' . $small_class_string . '" data-type="' . $type . '" data-name="' . $name . '" data-context="' . $context . '" ' . $extras . '> <span class="kt-sg-customizer-shortcut"> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M0.5 6C0.5 2.96243 2.96243 0.5 6 0.5H18C21.0376 0.5 23.5 2.96243 23.5 6V18C23.5 21.0376 21.0376 23.5 18 23.5H6C2.96243 23.5 0.5 21.0376 0.5 18V6Z" fill="white" fill-opacity="0.8"/> <path d="M0.5 6C0.5 2.96243 2.96243 0.5 6 0.5H18C21.0376 0.5 23.5 2.96243 23.5 6V18C23.5 21.0376 21.0376 23.5 18 23.5H6C2.96243 23.5 0.5 21.0376 0.5 18V6Z" stroke="#E2E8F0"/> <g clip-path="url(#clip0_8460_9362)"> <path d="M14.5 7.50081C14.6273 7.35032 14.7849 7.22784 14.9625 7.14115C15.1402 7.05446 15.334 7.00547 15.5318 6.99731C15.7296 6.98915 15.9269 7.022 16.1112 7.09375C16.2955 7.1655 16.4627 7.27459 16.6022 7.41407C16.7416 7.55354 16.8503 7.72034 16.9213 7.90383C16.9922 8.08732 17.0239 8.28347 17.0143 8.4798C17.0047 8.67612 16.954 8.8683 16.8654 9.04409C16.7769 9.21988 16.6524 9.37542 16.5 9.50081L9.75 16.2508L7 17.0008L7.75 14.2508L14.5 7.50081Z" stroke="#020617" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M13.5 8.5L15.5 10.5" stroke="#020617" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_8460_9362"> <rect width="12" height="12" fill="white" transform="translate(6 6)"/> </clipPath> </defs> </svg> </span> </span>';
	}

	/**
	 * Render customizer style guide shortcut pencil.
	 *
	 * @param string $type Section|Control.
	 * @param string $name Section name|Control name.
	 * @param string $context General|Design name.
	 * @param string $extras if any other parameter to pass.
	 *
	 * @return string Trigger for style guide shortcut.
	 * @since 4.8.0
	 */
	public function get_style_guide_shortcut_trigger_attributes( $type, $name, $context = 'general', $extras = '' ) {
		return 'data-type="' . $type . '" data-name="' . $name . '" data-context="' . $context . '" ' . $extras . '';
	}
	/**
	 * Render site icon.
	 *
	 * @since 4.8.0
	 */
	public function site_icon_update() {
		$uploaded_icon_url = get_site_icon_url( 32 );
		$site_icon_url     = empty( $uploaded_icon_url ) ? admin_url() . 'images/wordpress-logo.svg' : $uploaded_icon_url;
		?>
			<p class="kt-sg-site-icon-wrap">
				<span class="kt-sg-site-icon-aside-divider"></span>
				<span class="kt-sg-site-icon-inner-wrap">
					<img class="kt-sg-site-icon" alt="<?php esc_attr_e( 'Site Icon', 'kadence' ); ?>" src="<?php echo esc_url( $site_icon_url ); ?>" />
					<span class="kt-sg-site-title"> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> </span>
					<span class="kt-sg-site-blogdescription"> <?php echo esc_attr( ! empty( get_bloginfo( 'description' ) ) ? ' - ' . get_bloginfo( 'description' ) : '' ); ?> </span>
				</span>
				<span class="kt-sg-site-icon-aside-divider"></span>
			</p>
		<?php
	}
}
