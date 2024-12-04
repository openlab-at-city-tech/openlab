<?php


namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class Image extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.hero.image.';
	protected static $selector        = '#hero';

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::$settings_prefix . 'selective_selector', false );
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix        = static::$settings_prefix;
		$selector      = static::$selector;
		$prefix_shadow = "{$prefix}style.descendants.image.boxShadow.";
		$prefix_frame  = "{$prefix}style.descendants.frameImage.";

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'image' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(
				"{$prefix}localProps.url"            => array(
					'default' => Defaults::get( "{$prefix}localProps.url" ),
					'control' => array(
						'label'       => Translations::get( 'image' ),
						'type'        => 'image',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
				),

				"{$prefix_shadow}layers.0"           => array(
					'default'      => Defaults::get( "{$prefix_shadow}layers.0" ),
					'control'      => array(
						'type'        => 'composed',
						'input_type'  => 'switch',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'fields'      => array(
							'x'      => array(
								'type'    => 'slider',
								'label'   => Translations::get( 'horizontal' ),
								'default' => Defaults::get( "{$prefix_shadow}layers.0.x" ),
								'props'   => array(
									'min' => - 100,
									'max' => 100,
								),
							),
							'y'      => array(
								'type'    => 'slider',
								'label'   => Translations::get( 'vertical' ),
								'default' => Defaults::get( "{$prefix_shadow}layers.0.y" ),
								'props'   => array(
									'min' => - 100,
									'max' => 100,

								),
							),
							'spread' => array(
								'type'    => 'slider',
								'label'   => Translations::get( 'spread' ),
								'default' => Defaults::get( "{$prefix_shadow}layers.0.spread" ),
								'props'   => array(
									'options' => array(
										'min' => 0,
										'max' => 100,
									),
								),
							),
							'blur'   => array(
								'type'    => 'slider',
								'label'   => Translations::get( 'blur' ),
								'default' => Defaults::get( "{$prefix_shadow}layers.0.blur" ),
								'props'   => array(
									'options' => array(
										'min' => 0,
										'max' => 100,
									),
								),
							),
							'color'  => array(
								'type'    => 'color-picker',
								'label'   => Translations::get( 'color' ),
								'default' => Defaults::get( "{$prefix_shadow}layers.0.color" ),
								'props'   => array(
									'inline' => true,
								),
							),
						),
					),
					'css_output'   => array(
						array(
							'selector'      => "{$selector} img",
							'media'         => CSSOutput::NO_MEDIA,
							'property'      => 'box-shadow',
							'value_pattern' => '#x#px #y#px #blur#px #spread#px #color#',
						),
					),
					'active_rules' => array(
						array(
							'setting'  => "{$prefix_shadow}enabled",
							'operator' => '=',
							'value'    => true,
						),
					),
				),

				"{$prefix_shadow}enabled"            => array(
					'default'    => Defaults::get( "{$prefix_shadow}enabled" ),
					'control'    => array(
						'label'       => Translations::get( 'box_shadow' ),
						'input_type'  => 'switch',
						'type'        => 'group',
						'section'     => "{$prefix}section",
						'show_toggle' => true,
						'controls'    => array(
							"{$prefix_shadow}layers.0",
						),
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector'    => "{$selector} img",
							'media'       => CSSOutput::NO_MEDIA,
							'property'    => 'box-shadow',
							'false_value' => 'none',
						),
					),
				),
				// frame options
				"{$prefix}props.frame.type"          => array(
					'default'    => Defaults::get( "{$prefix}props.frame.type" ),
					'control'    => array(
						'label'       => Translations::get( 'type' ),
						'type'        => 'select',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'choices'     => array(
							'border'     => Translations::escHtml( 'border' ),
							'background' => Translations::escHtml( 'background' ),
						),
					),
					'css_output' => array(
						array(
							'selector' => "{$selector} div.wp-block-kubio-image__frameImage",
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'border-style',
							'value'    => array(
								'border'     => 'solid',
								'background' => 'none',
							),
						),
						array(
							'selector' => "{$selector} div.wp-block-kubio-image__frameImage",
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'background-color',
							'value'    => array(
								'border'     => 'transparent',
								'background' => '',
							),
						),
					),
				),

				"{$prefix_frame}backgroundColor"     => array(
					'default'    => Defaults::get( "{$prefix_frame}backgroundColor" ),
					'control'    => array(
						'label'       => Translations::escHtml( 'color' ),
						'type'        => 'color',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector' => "{$selector} .wp-block-kubio-image__frameImage",
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'background-color',
						),
						array(
							'selector' => "{$selector} .wp-block-kubio-image__frameImage",
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'border-color',
						),
					),
				),

				"{$prefix_frame}width"               => array(
					'default'    => Defaults::get( "{$prefix_frame}width" ),
					'control'    => array(
						'label'       => Translations::get( 'width' ),
						'type'        => 'slider',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'min'         => 0,
						'max'         => 300,
					),
					'css_output' => array(
						array(
							'selector'      => "{$selector} .wp-block-kubio-image__frameImage",
							'media'         => CSSOutput::NO_MEDIA,
							'property'      => 'width',
							'value_pattern' => '%s%%',
						),
					),
				),

				"{$prefix_frame}height"              => array(
					'default'    => Defaults::get( "{$prefix_frame}height" ),
					'control'    => array(
						'label'       => Translations::get( 'height' ),
						'type'        => 'slider',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'min'         => 0,
						'max'         => 300,
					),
					'css_output' => array(
						array(
							'selector'      => "{$selector} .wp-block-kubio-image__frameImage",
							'media'         => CSSOutput::NO_MEDIA,
							'property'      => 'height',
							'value_pattern' => '%s%%',
						),
					),
				),

				"{$prefix_frame}transform.translate" => array(
					'default'    => Defaults::get( "{$prefix_frame}transform.translate" ),
					'control'    => array(
						'type'        => 'composed',
						'input_type'  => 'textarea',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'fields'      => array(
							'x_value' => array(
								'type'    => 'slider',
								'label'   => Translations::get( 'offset_left' ),
								'default' => Defaults::get( "{$prefix_frame}transform.translate.x_value" ),
								'props'   => array(
									'min' => - 50,
									'max' => 50,
								),
							),

							'y_value' => array(
								'type'    => 'slider',
								'label'   => Translations::get( 'offset_top' ),
								'default' => Defaults::get( "{$prefix_frame}transform.translate.y_value" ),
								'props'   => array(
									'min' => - 50,
									'max' => 50,

								),
							),
						),
					),
					'css_output' => array(
						array(
							'selector'      => "{$selector} .wp-block-kubio-image__frameImage",
							'media'         => CSSOutput::NO_MEDIA,
							'property'      => 'transform',
							'value_pattern' => 'translateX(#x_value#%) translateY(#y_value#%)',
						),
					),
				),

				"{$prefix_frame}thickness"           => array(
					'default'      => Defaults::get( "{$prefix_frame}thickness" ),
					'control'      => array(
						'label'       => Translations::get( 'frame_thickness' ),
						'type'        => 'slider',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'min'         => 0,
						'max'         => 100,
					),
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}props.frame.type",
							'operator' => '=',
							'value'    => 'border',
						),
					),
					'css_output'   => array(
						array(
							'selector'      => "{$selector} .wp-block-kubio-image__frameImage",
							'media'         => CSSOutput::NO_MEDIA,
							'property'      => 'border-width',
							'value_pattern' => '%spx',
						),
					),
				),

				"{$prefix}props.showFrameOverImage"  => array(
					'default'    => Defaults::get( "{$prefix}props.showFrameOverImage" ),
					'control'    => array(
						'label'       => Translations::get( 'frame_over_image' ),
						'type'        => 'switch',
						'show_toggle' => true,
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector'    => "{$selector} .wp-block-kubio-image__frameImage",
							'media'       => CSSOutput::NO_MEDIA,
							'property'    => 'z-index',
							'true_value'  => '1',
							'false_value' => '-1',
						),
					),
				),

				"{$prefix}props.showFrameShadow"     => array(
					'default'        => Defaults::get( "{$prefix}props.showFrameShadow" ),
					'control'        => array(
						'label'       => Translations::get( 'frame_shadow' ),
						'type'        => 'switch',
						'show_toggle' => true,
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
					// 'js_output' => array(
					// array(
					// 'selector' => "{$selector} .wp-block-kubio-image__frameImage",
					// 'action'   => 'toggle-class',
					// 'value'    => 'h-image__frame_shadow',
					// ),
					// ),
						'css_output' => array(
							array(
								'selector'    => "{$selector}  .wp-block-kubio-image__frameImage",
								'media'       => CSSOutput::NO_MEDIA,
								'property'    => 'box-shadow',
								'false_value' => '',
								'true_value'  => '0 2px 4px -1px rgb(0 0 0 / 20%), 0 4px 5px 0 rgb(0 0 0 / 14%), 0 1px 10px 0 rgb(0 0 0 / 12%)',
							),
						),
				),

				"{$prefix}props.enabledFrameOption"  => array(
					'default' => Defaults::get( "{$prefix}props.enabledFrameOption" ),
					'control' => array(
						'label'       => Translations::get( 'frame_options' ),
						'input_type'  => 'switch',
						'type'        => 'group',
						'section'     => "{$prefix}section",
						'show_toggle' => true,
						'controls'    => array(
							// "{$prefix_frame}composed",
							"{$prefix}props.frame.type",
							"{$prefix_frame}backgroundColor",
							"{$prefix_frame}width",
							"{$prefix_frame}height",
							"{$prefix_frame}transform.translate",
							"{$prefix_frame}thickness",
							"{$prefix}props.showFrameOverImage",
							"{$prefix}props.showFrameShadow",
						),
						'colibri_tab' => 'content',
					),
				),
			),
		);
	}

	public function renderContent( $parameters = array() ) {

		// if ( $this->mod( static::$settings_prefix . 'show' ) ) {
		View::partial(
			'front-header',
			'image',
			array(
				'component' => $this,
			)
		);
		// }
	}

	public function printImage( $classes, $placeholder = null ) {
		$prefix = static::$settings_prefix;
		$image  = $this->mod( "{$prefix}localProps.url" );
		if ( ! $image ) {

			if ( $placeholder === null ) {
				$placeholder = get_template_directory_uri() . '/resources/images/placeholder.png';
			}

			$image = $placeholder;
		}
		?>
		<img src="<?php echo esc_attr( $image ); ?>" alt="" class="<?php echo esc_attr( $classes ); ?>"/>
		<?php
	}

	public function printFrame( $classes ) {
		$prefix = static::$settings_prefix;
		if ( ! $this->mod( "{$prefix}props.enabledFrameOption", false ) ) {
			return;
		}

		$classes = is_array( $classes ) ? $classes : array( $classes );

		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"></div>
		<?php
	}


	public function getPenPosition() {
		return static::PEN_ON_RIGHT;
	}
}
