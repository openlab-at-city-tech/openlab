<?php


namespace ColibriWP\Theme\Components\Header;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Core\PartialComponent;
use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Theme;
use ColibriWP\Theme\Translations;

class HeroStyle extends PartialComponent {

	protected static $instances = array();
	protected $prefix           = '';
	protected $selector         = '';

	public function __construct( $prefix, $selector ) {
		$this->prefix   = $prefix;
		$this->selector = $selector;
	}

	public static function getInstance( $prefix, $selector ) {
		$key = Utils::slugify( "{$prefix}-{$selector}" );
		if ( ! isset( static::$instances[ $key ] ) ) {
			static::$instances[ $key ] = new static( $prefix, $selector );
		}

		return static::$instances[ $key ];
	}

	public function renderContent( $parameters = array() ) {
		?>
		<div class="background-wrapper" data-colibri-hide-pen="true">
			<div class="background-layer">
				<div class="background-layer">
					<?php $this->doSlideshow(); ?>
					<?php $this->doVideoBackground(); ?>
				</div>
				<div class="overlay-layer"></div>
				<div class="shape-layer"></div>
			</div>
		</div>
		<?php
	}

	public function doSlideshow() {
		$slideshow_prefix = 'style.background.slideshow.';
		$component_id     = "{$this->getPrefix()}slideshow";
		$slides           = $this->prefixedMod( "{$slideshow_prefix}slides" );

		$slide_duration = $this->prefixedMod( "{$slideshow_prefix}duration.value" );
		$slide_speed    = $this->prefixedMod( "{$slideshow_prefix}duration.value" );

		$this->printSlideshow( $component_id, $slides, $slide_duration, $slide_speed );
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 *
	 * @return HeroStyle
	 */
	public function setPrefix( $prefix ) {
		$this->prefix = $prefix;

		return $this;
	}

	public function prefixedMod( $name ) {
		$name = $this->getPrefix() . $name;

		return $this->mod( $name );
	}

	public function printSlideshow( $component_id, $slides, $slide_duration, $slide_speed ) {

		if ( $this->prefixedMod( 'style.background.type' ) !== 'slideshow' ) {
			return;
		}

		$settings = json_encode(
			array(
				'speed'    => $slide_speed,
				'duration' => $slide_duration,
			)
		);

		?>
		<div class="colibri-slideshow background-layer"
			 data-kubio-settings="<?php echo esc_attr( $settings ); ?>"
			 data-colibri-id="<?php echo esc_attr( $component_id ); ?>"
			 data-kubio-component="slideshow">
			<?php foreach ( $slides as $slide ) : ?>
				<?php $this->printSlide( $slide ); ?>
			<?php endforeach; ?>
		</div>
		<?php
	}

	private function printSlide( $slide ) {
		?>
		<div class="slideshow-image"
			 style="background-image:url(<?php echo esc_attr( $slide['url'] ); ?>)"></div>
		<?php
	}

	public function doVideoBackground() {

		$component_id = "{$this->getPrefix()}video";

		$this->printVideoBackground( $component_id );
	}

	public function printVideoBackground( $component_id ) {

		if ( $this->prefixedMod( 'style.background.type' ) !== 'video' ) {
			return;
		}
		$video_prefix = 'style.background.video.';

		$mime_type = 'video/mp4';
		$poster    = $this->prefixedMod( "{$video_prefix}poster.url" );
		$video     = '';

		if ( $this->prefixedMod( "{$video_prefix}videoType" ) === 'external' ) {
			$mime_type = 'video/x-youtube';
			$video     = $this->prefixedMod( "{$video_prefix}externalUrl" );
		} else {
			$id = absint( $this->prefixedMod( "{$video_prefix}internalUrl" ) );
			if ( $id ) {
				$video     = wp_get_attachment_url( $id );
				$type      = wp_check_filetype( $video, wp_get_mime_types() );
				$mime_type = $type['type'];
			}
		}

		?>
		<div
				class="kubio-video-background background-layer"
				data-kubio-component="video-background"
				data-mime-type="<?php echo esc_attr( $mime_type ); ?>"
				data-video="<?php echo esc_attr( $video ); ?>"
				data-poster="<?php echo esc_attr( $poster ); ?>"
				style="<?php echo ( $poster ? "background-image:url({$poster});" : '' ); ?>"
				data-colibri-id="<?php echo esc_attr( $component_id ); ?>">
		</div>
		<?php
	}

	public function whenCustomizerPreview() {
		$component_id     = "{$this->getPrefix()}slideshow";
		$slideshow_prefix = 'style.background.slideshow.';
		$this->addFrontendJSData(
			$component_id,
			array(
				'wpSettings' => array(
					'slideDuration' => "{$this->getPrefix()}{$slideshow_prefix}duration.value",
					'slideSpeed'    => "{$this->getPrefix()}{$slideshow_prefix}speed.value",
				),
			),
			true
		);

		$video_prefix       = 'style.background.video.';
		$video_component_id = "{$this->getPrefix()}video";

		$this->addFrontendJSData(
			$video_component_id,
			array(

				'wpSettings' => array(
					'videoType'   => "{$this->getPrefix()}{$video_prefix}videoType",
					'externalUrl' => "{$this->getPrefix()}{$video_prefix}externalUrl",
					'internalUrl' => "{$this->getPrefix()}{$video_prefix}internalUrl",
					'posterUrl'   => "{$this->getPrefix()}{$video_prefix}poster.url",
				),
			)
		);

	}

	public function getOptions() {
		$prefix = $this->getPrefix();

		$wrapper_settings = array_merge(
			$this->getSlideshowSettings( $prefix ),
			$this->getVideoSettings( $prefix ),
			$this->getOverlaySettings( $prefix ),
			$this->getDividerSettings( $prefix ),
			array()
		);

		foreach ( $wrapper_settings as $key => $setting_data ) {
			if ( ! isset( $setting_data['control']['selective_refresh'] ) ) {
				$wrapper_settings[ $key ]['control']['selective_refresh'] = array(
					'selector' => $this->getSelector() . ' .background-wrapper',
					'function' => array( $this, 'renderContent' ),
				);
			}
		}

		return array(
			'settings' => array_merge(
				array(),
				$this->getGeneralBackgroundSettings( $prefix ),
				$this->getImageSettings( $prefix ),
				$this->getGradientSettings( $prefix ),
				$wrapper_settings,
				$this->getSpacingSettings( $prefix )
			),
		);
	}

	protected function getSlideshowSettings( $prefix ) {

		$slideshow_prefix = "{$prefix}style.background.slideshow.";

		$self = $this;

		return array(
			"{$slideshow_prefix}slides"         => array(
				'default' => Defaults::get( "{$slideshow_prefix}slides" ),
				'control' => array(
					'colibri_tab'    => 'style',
					'label'          => Translations::escHtml( 'slideshow' ),
					'type'           => 'repeater',
					'section'        => 'hero',
					'item_label'     => Translations::get( 'slide_n' ),
					'item_add_label' => Translations::get( 'add_slide' ),
					'fields'         => array(
						'url' => array(
							'type'    => 'image',
							'label'   => Translations::get( 'image' ),
							'default' => Defaults::get( "{$slideshow_prefix}slides.0.url" ),
						),
					),
					'active_rules'   => array(
						array(
							'setting'  => "{$prefix}style.background.type",
							'operator' => '=',
							'value'    => 'slideshow',
						),
					),
				),
			),

			"{$slideshow_prefix}duration.value" => array(
				'default'   => Defaults::get( "{$slideshow_prefix}duration.value", 100 ),
				'transport' => 'postMessage',
				'control'   => array(
					'label'        => Translations::escHtml( 'slide_duration' ),
					'colibri_tab'  => 'style',
					'type'         => 'slider',
					'section'      => 'hero',
					'min'          => 0,
					'max'          => 10000,
					'step'         => 100,
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}style.background.type",
							'operator' => '=',
							'value'    => 'slideshow',
						),
					),
				),
			),

			"{$slideshow_prefix}speed.value"    => array(
				'default'   => Defaults::get( "{$slideshow_prefix}speed.value" ),
				'transport' => 'postMessage',
				'control'   => array(
					'label'        => Translations::escHtml( 'effect_speed' ),
					'colibri_tab'  => 'style',
					'type'         => 'slider',
					'section'      => 'hero',
					'min'          => 100,
					'max'          => 10000,
					'step'         => 100,
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}style.background.type",
							'operator' => '=',
							'value'    => 'slideshow',
						),
					),
				),
			),

		);
	}

	protected static function getVideoSettings( $prefix ) {

		$base_active_rule
			= array(
				'setting'  => "{$prefix}style.background.type",
				'operator' => '=',
				'value'    => 'video',
			);

		$video_prefix = "{$prefix}style.background.video.";

		// $self = $this;

		return array(
			"{$video_prefix}videoType"   => array(
				'default' => Defaults::get( "{$video_prefix}videoType" ),
				'control' => array(
					'label'        => Translations::get( 'video_type' ),
					'type'         => 'button-group',
					'button_size'  => 'medium',
					'choices'      => array(
						'internal' => Translations::escHtml( 'self_hosted' ),
						'external' => Translations::escHtml( 'external_video' ),
					),
					'section'      => 'hero',
					'colibri_tab'  => 'style',
					'active_rules' => array( $base_active_rule ),
				),

			),

			"{$video_prefix}externalUrl" => array(
				'default' => Defaults::get( "{$video_prefix}externalUrl" ),
				'control' => array(
					'label'        => Translations::get( 'youtube_url' ),
					'type'         => 'input',
					'section'      => 'hero',
					'colibri_tab'  => 'style',
					'active_rules' => array(
						$base_active_rule,
						array(
							'setting'  => "{$video_prefix}videoType",
							'operator' => '=',
							'value'    => 'external',
						),
					),
				),

			),

			"{$video_prefix}internalUrl" => array(
				'default' => Defaults::get( "{$video_prefix}internalUrl" ),
				'control' => array(
					'label'        => Translations::get( 'self_hosted_video' ),
					'type'         => 'video',
					'section'      => 'hero',
					'colibri_tab'  => 'style',
					'active_rules' => array(
						$base_active_rule,
						array(
							'setting'  => "{$video_prefix}videoType",
							'operator' => '=',
							'value'    => 'internal',
						),
					),
				),

			),

			"{$video_prefix}poster.url"  => array(
				'default'   => Defaults::get( "{$video_prefix}poster.url" ),
				'transport' => 'postMessage',
				'control'   => array(
					'label'        => Translations::get( 'video_poster' ),
					'type'         => 'image',
					'section'      => 'hero',
					'colibri_tab'  => 'style',

					'active_rules' => array(
						$base_active_rule,
						array(
							'setting'  => "{$prefix}style.background.type",
							'operator' => '=',
							'value'    => 'video',
						),
					),

				),

			),

		);
	}


	public function getOverlaySettings( $prefix ) {

		$overlay_prefix = "{$prefix}style.background.overlay.";

		$active_rule_base = array(
			'setting'  => "{$overlay_prefix}enabled",
			'operator' => '=',
			'value'    => true,
		);

		$shape_base_url = Theme::rootDirectoryUri() . '/resources/images/header-shapes';

		return array(

			"{$prefix}hero.separator5"          => array(
				'default' => '',
				'control' => array(
					'label'        => '',
					'type'         => 'separator',
					'section'      => 'hero',
					'colibri_tab'  => 'style',
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}style.background.type",
							'operator' => '!=',
							'value'    => 'color',
						),
					),
				),
			),

			"{$overlay_prefix}type"             => array(
				'default'   => Defaults::get( "{$overlay_prefix}type" ),
				'transport' => 'postMessage',
				'control'   => array(
					'label'        => Translations::get( 'overlay_type' ),
					'type'         => 'button-group',
					'button_size'  => 'medium',
					'choices'      => array(
						'shapeOnly' => Translations::escHtml( 'shape_only' ),
						'color'     => Translations::escHtml( 'color' ),
						'gradient'  => Translations::escHtml( 'gradient' ),
					),
					'section'      => 'hero',
					'colibri_tab'  => 'style',
					'active_rules' => array( $active_rule_base ),
				),
			),

			"{$overlay_prefix}shape.value"      => array(
				'default'      => Defaults::get( "{$overlay_prefix}shape.value", 'none' ),
				'transport'    => 'postMessage',
				'control'      => array(
					'label'       => Translations::escHtml( 'overlay_shape' ),
					'type'        => 'select',
					'section'     => 'hero',
					'colibri_tab' => 'style',
					'size'        => 'small',
					'choices'     => array(
						'none'                      => Translations::get( 'none' ),
						'circles'                   => Translations::get( 'circles' ),
						'10degree-stripes'          => Translations::get( '10degree_stripes' ),
						'rounded-squares-blue'      => Translations::get( 'rounded_squares_blue' ),
						'many-rounded-squares-blue' => Translations::get( 'many_rounded_squares_blue' ),
						'two-circles'               => Translations::get( 'two_circles' ),
						'circles-2'                 => Translations::get( 'circles_2' ),
						'circles-3'                 => Translations::get( 'circles_3' ),
						'circles-gradient'          => Translations::get( 'circles_gradient' ),
						'circles-white-gradient'    => Translations::get( 'circles_white_gradient' ),
						'waves'                     => Translations::get( 'waves' ),
						'waves-inverted'            => Translations::get( 'waves_inverted' ),
						'dots'                      => Translations::get( 'dots' ),
						'left-tilted-lines'         => Translations::get( 'left_tilted_lines' ),
						'right-tilted-lines'        => Translations::get( 'right_tilted_lines' ),
						'right-tilted-strips'       => Translations::get( 'right_tilted_strips' ),
					),
				),
				'css_output'   => array(
					array(
						'selector'      => "{$this->selector} .background-layer .shape-layer",
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'background-image',
						'value_pattern' => 'url(' . $shape_base_url . '/%s.png)',
					),
				),
				'active_rules' => array( $active_rule_base ),
			),

			"{$overlay_prefix}light"            => array(
				'default'      => Defaults::get( "{$overlay_prefix}light" ),
				'transport'    => 'postMessage',
				'control'      => array(
					'label'       => Translations::escHtml( 'shape_light' ),
					'type'        => 'slider',
					'section'     => 'hero',
					'colibri_tab' => 'style',

				),
				'css_output'   => array(
					array(
						'selector'      => "{$this->selector} .background-layer .shape-layer",
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'filter',
						'value_pattern' => 'invert(%s%%)',
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$overlay_prefix}shape.value",
						'operator' => '!=',
						'value'    => 'none',
					),
				),
			),

			"{$overlay_prefix}color.value"      => array(
				'default'      => Defaults::get( "{$overlay_prefix}color.value" ),
				'control'      => array(
					'label'       => Translations::get( 'color' ),
					'type'        => 'color',
					'alpha'       => false,
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
				'css_output'   => array(
					array(
						'selector' => "{$this->selector} .background-layer .overlay-layer",
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-color',
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$overlay_prefix}type",
						'operator' => '=',
						'value'    => 'color',
					),
				),
			),

			"{$overlay_prefix}gradient"         => array(
				'default'      => Defaults::get( "{$overlay_prefix}gradient" ),
				'control'      => array(
					'label'       => Translations::escHtml( 'gradient' ),
					'type'        => 'gradient',
					'section'     => 'hero',
					'colibri_tab' => 'style',
					'choices'     => Defaults::get( 'gradients', '' ),
				),
				'css_output'   => array(
					array(
						'selector'      => "{$this->selector} .background-layer .overlay-layer",
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'background-image',
						'value_pattern' => CSSOutput::GRADIENT_VALUE_PATTERN,
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$overlay_prefix}type",
						'operator' => '=',
						'value'    => 'gradient',
					),
				),
			),

			"{$overlay_prefix}gradient_opacity" => array(
				'default'      => Defaults::get( "{$overlay_prefix}gradient_opacity" ),
				'control'      => array(
					'label'       => Translations::get( 'opacity' ),
					'type'        => 'slider',
					'section'     => 'hero',
					'colibri_tab' => 'style',
					'min'         => 1,
					'max'         => 100,
				),
				'css_output'   => array(
					array(
						'selector'      => "{$this->selector} .background-layer .overlay-layer",
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'opacity',
						'value_pattern' => 'calc( %s / 100 )',
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$overlay_prefix}type",
						'operator' => '=',
						'value'    => 'gradient',
					),
				),
			),

			"{$overlay_prefix}enabled"          => array(
				'default'    => Defaults::get( "{$overlay_prefix}enabled" ),
				'transport'  => 'postMessage',
				'control'    => array(
					'label'       => Translations::get( 'show_background_overlay' ),
					'type'        => 'group',
					'section'     => 'hero',
					'show_toggle' => true,
					'controls'    => array(
						"{$overlay_prefix}type",
						"{$overlay_prefix}color.value",
						"{$overlay_prefix}gradient",
						"{$overlay_prefix}gradient_opacity",
						"{$overlay_prefix}shape.value",
						"{$overlay_prefix}light",
					),
					'colibri_tab' => 'style',
				),
				'css_output' => array(
					array(
						'selector'    => "{$this->selector} .background-layer .overlay-layer",
						'media'       => CSSOutput::NO_MEDIA,
						'property'    => 'display',
						'false_value' => 'none',
					),
				),
			),
		);
	}

	public function getDividerSettings( $prefix ) {

		$divider_prefix   = "{$prefix}style.separatorBottom.";
		$section          = 'hero';
		$active_rule_base = array(
			'setting'  => "{$divider_prefix}enabled",
			'operator' => '=',
			'value'    => true,
		);

		return array(

			"{$prefix}hero.separator6"      => array(
				'default' => '',
				'control' => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
			),

			"{$divider_prefix}enabled"      => array(
				'default' => Defaults::get( "{$divider_prefix}enabled" ),
				'control' => array(
					'label'             => Translations::get( 'show_bottom_divider' ),
					'type'              => 'group',
					'section'           => $section,
					'show_toggle'       => true,
					'colibri_tab'       => 'style',
					'controls'          => array(
						"{$divider_prefix}type",
						"{$divider_prefix}negative",
						"{$divider_prefix}color",
						"{$divider_prefix}height.value",
					),
					'selective_refresh' => array(
						'selector' => $this->getSelector(),
						'function' => array( $this, 'renderContent' ),
					),

				),
			),

			"{$divider_prefix}type"         => array(
				'default'      => Defaults::get( "{$divider_prefix}type", 'none' ),
				'control'      => array(
					'label'             => Translations::escHtml( 'divider_style' ),
					'type'              => 'select',
					'section'           => 'hero',
					'colibri_tab'       => 'style',
					'size'              => 'small',
					'choices'           => array(
						'tilt'                  => Translations::get( 'tilt' ),
						'tilt-flipped'          => Translations::get( 'tilt-flipped' ),
						'triangle'              => Translations::get( 'triangle' ),
						'triangle-asymmetrical' => Translations::get( 'triangle-asymmetrical' ),
						'opacity-fan'           => Translations::get( 'opacity-fan' ),
						'opacity-tilt'          => Translations::get( 'opacity-tilt' ),
						'mountains'             => Translations::get( 'mountains' ),
						'pyramids'              => Translations::get( 'pyramids' ),
						'waves'                 => Translations::get( 'waves' ),
						'wave-brush'            => Translations::get( 'wave-brush' ),
						'waves-pattern'         => Translations::get( 'waves-pattern' ),
						'clouds'                => Translations::get( 'clouds' ),
						'curve'                 => Translations::get( 'curve' ),
						'curve-asymmetrical'    => Translations::get( 'curve-asymmetrical' ),
						'drops'                 => Translations::get( 'drops' ),
						'arrow'                 => Translations::get( 'arrow' ),
						'book'                  => Translations::get( 'book' ),
						'split'                 => Translations::get( 'split' ),
						'zigzag'                => Translations::get( 'zigzag' ),

					),
					'selective_refresh' => array(
						'selector' => $this->getSelector(),
						'function' => array( $this, 'renderContent' ),
					),
				),
				'active_rules' => array( $active_rule_base ),
			),

			"{$divider_prefix}color"        => array(
				'default'      => Defaults::get( "{$divider_prefix}color" ),
				'control'      => array(
					'label'       => Translations::escHtml( 'color' ),
					'type'        => 'color',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
				'css_output'   => array(
					array(
						'selector' => "{$this->selector} .h-separator svg path",
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-color',
					),

					array(
						'selector' => "{$this->selector} .h-separator svg path",
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'fill',
					),
				),
				'active_rules' => array( $active_rule_base ),
			),

			"{$divider_prefix}height.value" => array(
				'default'    => Defaults::get( "{$divider_prefix}height.value", 100 ),
				'control'    => array(
					'label'        => Translations::escHtml( 'divider_height' ),
					'colibri_tab'  => 'style',
					'type'         => 'slider',
					'section'      => 'hero',
					'min'          => 0,
					'max'          => 300,
					'step'         => 1,
					'active_rules' => array( $active_rule_base ),
				),
				'css_output' => array(
					array(
						'selector'      => "{$this->selector} .h-separator",
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'height',
						'value_pattern' => '%spx !important',
					),
				),
			),

			"{$divider_prefix}negative"     => array(
				'default' => Defaults::get( "{$divider_prefix}negative", 100 ),
				'control' => array(
					'label'             => Translations::escHtml( 'divider_negative' ),
					'type'              => 'switch',
					'section'           => 'hero',
					'colibri_tab'       => 'style',
					'active_rules'      => array( $active_rule_base ),
					'selective_refresh' => array(
						'selector' => $this->getSelector(),
						'function' => array( $this, 'renderContent' ),
					),
				),
			),
		);
	}

	/**
	 * @return string
	 */
	public function getSelector() {
		return $this->selector;
	}

	/**
	 * @param string $selector
	 */
	public function setSelector( $selector ) {
		$this->selector = $selector;
	}

	protected function getGeneralBackgroundSettings( $prefix ) {

		return array(

			"{$prefix}full_height"            => array(
				'default'    => Defaults::get( "{$prefix}full_height" ),
				'control'    => array(
					'label'       => Translations::get( 'full_height' ),
					'type'        => 'switch',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
				'css_output' => array(
					array(
						'selector'    => $this->selector,
						'media'       => CSSOutput::NO_MEDIA,
						'property'    => 'min-height',
						'true_value'  => '100vh',
						'false_value' => 'auto',
					),
				),
			),

			"{$prefix}hero.separator2"        => array(
				'default' => '',
				'control' => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
			),

			"{$prefix}style.background.color" => array(
				'default'    => Defaults::get( "{$prefix}style.background.color" ),
				'control'    => array(
					'label'       => Translations::escHtml( 'background_color' ),
					'type'        => 'color',
					'section'     => 'hero',
					'colibri_tab' => 'style',

				),
				'css_output' => array(
					array(
						'selector' => $this->selector,
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-color',
					),
				),
			),

			"{$prefix}style.background.type"  => array(
				'default' => Defaults::get( "{$prefix}style.background.type" ),
				'control' => array(
					'label'             => Translations::escHtml( 'background_type' ),
					'focus_alias'       => 'hero_background',
					'type'              => 'button-group',
					'button_size'       => 'medium',
					'choices'           => array(
						'image'     => Translations::escHtml( 'image' ),
						'gradient'  => Translations::escHtml( 'gradient' ),
						'slideshow' => Translations::escHtml( 'slideshow' ),
						'video'     => Translations::escHtml( 'video' ),
					),
					'colibri_tab'       => 'style',
					'none_value'        => 'color',
					'section'           => 'hero',
					'selective_refresh' => array(
						'selector' => $this->getSelector() . ' .background-wrapper',
						'function' => array( $this, 'renderContent' ),
					),
				),
			),

		);
	}

	protected function getImageSettings( $prefix ) {

		$image_prefix = "{$prefix}style.background.image.0.";

		return array(
			"{$image_prefix}source.url" => array(
				'default'      => Defaults::get( "{$image_prefix}source.url", '' ),
				'control'      => array(
					'label'       => Translations::escHtml( 'image' ),
					'type'        => 'image',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
				'css_output'   => array(
					array(
						'selector'      => static::getSelector(),
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'background-image',
						'value_pattern' => 'url("%s")',
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}style.background.type",
						'operator' => '=',
						'value'    => 'image',
					),
				),
			),

			"{$image_prefix}position"   => array(
				'default'      => Defaults::get( "{$image_prefix}position", 'top center' ),
				'control'      => array(
					'label'                   => Translations::escHtml( 'position' ),
					'type'                    => 'select',
					'section'                 => 'hero',
					'colibri_tab'             => 'style',
					'inline_content_template' => true,
					'choices'                 => array(
						'top left'      => Translations::escHtml( 'top_left' ),
						'top center'    => Translations::escHtml( 'top_center' ),
						'top right'     => Translations::escHtml( 'top_right' ),
						'center left'   => Translations::escHtml( 'center_left' ),
						'center center' => Translations::escHtml( 'center_center' ),
						'center right'  => Translations::escHtml( 'center_right' ),
						'bottom left'   => Translations::escHtml( 'bottom_left' ),
						'bottom center' => Translations::escHtml( 'bottom_center' ),
						'bottom right'  => Translations::escHtml( 'bottom_right' ),
					),
				),
				'css_output'   => array(
					array(
						'selector' => $this->selector,
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-position',
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}style.background.type",
						'operator' => '=',
						'value'    => 'image',
					),
				),
			),

			"{$image_prefix}attachment" => array(
				'default'      => Defaults::get( "{$image_prefix}attachment", '' ),
				'control'      => array(
					'label'                   => Translations::escHtml( 'attachment' ),
					'inline_content_template' => true,
					'type'                    => 'select',
					'section'                 => 'hero',
					'colibri_tab'             => 'style',
					'choices'                 => array(
						'scroll' => Translations::escHtml( 'scroll' ),
						'fixed'  => Translations::escHtml( 'fixed' ),
					),
				),
				'css_output'   => array(
					array(
						'selector' => $this->selector,
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-attachment',
					),

					array(
						'selector'      => static::getSelector(),
						'media'         => CSSOutput::mobileMedia(),
						'property'      => 'background-attachment',
						'value_pattern' => 'none',
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}style.background.type",
						'operator' => '=',
						'value'    => 'image',
					),
				),
			),

			"{$image_prefix}repeat"     => array(
				'default'      => Defaults::get( "{$image_prefix}repeat", 'no-repeat' ),
				'control'      => array(
					'label'                   => Translations::escHtml( 'repeat', '' ),
					'inline_content_template' => true,
					'type'                    => 'select',
					'section'                 => 'hero',
					'colibri_tab'             => 'style',
					'choices'                 => array(
						'no-repeat' => Translations::escHtml( 'no-repeat' ),
						'repeat'    => Translations::escHtml( 'repeat', '' ),
						'repeat-x'  => Translations::escHtml( 'repeat', 'X' ),
						'repeat-y'  => Translations::escHtml( 'repeat', 'Y' ),

					),
				),
				'css_output'   => array(
					array(
						'selector' => $this->selector,
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-repeat',
					),

				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}style.background.type",
						'operator' => '=',
						'value'    => 'image',
					),
				),
			),

			"{$image_prefix}size"       => array(
				'default'      => Defaults::get( "{$image_prefix}size", 'cover' ),
				'control'      => array(
					'label'                   => Translations::escHtml( 'size' ),
					'inline_content_template' => true,
					'type'                    => 'select',
					'section'                 => 'hero',
					'colibri_tab'             => 'style',
					'choices'                 => array(
						'auto'    => Translations::escHtml( 'auto' ),
						'cover'   => Translations::escHtml( 'cover' ),
						'contain' => Translations::escHtml( 'contain' ),
					),
				),
				'css_output'   => array(
					array(
						'selector' => $this->selector,
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-size',
					),

				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}style.background.type",
						'operator' => '=',
						'value'    => 'image',
					),
				),
			),
		);
	}

	protected function getGradientSettings( $prefix ) {

		$gradient_prefix = "{$prefix}style.background.image.0.";

		return array(
			"{$gradient_prefix}source.gradient" => array(
				'default'      => Defaults::get( "{$gradient_prefix}source.gradient" ),
				'control'      => array(
					'label'       => Translations::escHtml( 'gradient' ),
					'type'        => 'gradient',
					'section'     => 'hero',
					'colibri_tab' => 'style',
					'choices'     => Defaults::get( 'gradients', '' ),
				),
				'css_output'   => array(
					array(
						'selector'      => static::getSelector(),
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'background-image',
						'value_pattern' => CSSOutput::GRADIENT_VALUE_PATTERN,
					),
				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}style.background.type",
						'operator' => '=',
						'value'    => 'gradient',
					),
				),
			),
		);
	}

	protected function getSpacingSettings( $prefix ) {
		return array(
			"{$prefix}hero.separator1"            => array(
				'default'      => '',
				'control'      => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
				'active_rules' => array(
					array(
						'setting'  => "{$prefix}full_height",
						'operator' => '=',
						'value'    => false,
					),
				),
			),

			"{$prefix}style.padding.top.value"    => array(
				'default'    => Defaults::get( "{$prefix}style.padding.top.value", 150 ),
				'transport'  => 'postMessage',
				'control'    => array(
					'label'        => Translations::escHtml( 'spacing_top' ),
					'colibri_tab'  => 'style',
					'type'         => 'slider',
					'section'      => 'hero',
					'min'          => 0,
					'max'          => 300,
					'step'         => 1,
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}full_height",
							'operator' => '=',
							'value'    => false,
						),
					),
				),
				'css_output' => array(
					array(
						'selector'      => $this->selector,
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'padding-top',
						'value_pattern' => '%spx',
					),
				),
			),

			"{$prefix}style.padding.bottom.value" => array(
				'default'    => Defaults::get( "{$prefix}style.padding.bottom.value", 150 ),
				'transport'  => 'postMessage',
				'control'    => array(
					'label'        => Translations::escHtml( 'spacing_bottom' ),
					'colibri_tab'  => 'style',
					'type'         => 'slider',
					'section'      => 'hero',
					'min'          => 0,
					'max'          => 300,
					'step'         => 1,
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}full_height",
							'operator' => '=',
							'value'    => false,
						),
					),
				),
				'css_output' => array(
					array(
						'selector'      => $this->selector,
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'padding-bottom',
						'value_pattern' => '%spx',
					),
				),
			),
		);
	}
}
