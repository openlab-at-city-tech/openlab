<?php


namespace Kubio\Theme\Components\Common;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Components\Header\HeroStyle as BaseHeroStyle;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Theme;
use ColibriWP\Theme\Translations;

class HeroStyle extends BaseHeroStyle {

	const STYLE_PREFIX = 'style.descendants.outer';


	protected static function getVideoSettings( $prefix ) {

		$style_prefix = self::STYLE_PREFIX;

		$base_active_rule
			= array(
				'setting'  => "{$prefix}{$style_prefix}.background.type",
				'operator' => '=',
				'value'    => 'video',
			);

		$video_prefix = "{$prefix}{$style_prefix}.background.video.";

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
				'default' => Defaults::get( "{$video_prefix}poster.url" ),
				'control' => array(
					'label'        => Translations::get( 'video_poster' ),
					'type'         => 'image',
					'section'      => 'hero',
					'colibri_tab'  => 'style',

					'active_rules' => array(
						$base_active_rule,
						array(
							'setting'  => "{$prefix}{$style_prefix}.background.type",
							'operator' => '=',
							'value'    => 'video',
						),
					),

				),

			),

		);
	}

	public function getOverlaySettings( $prefix ) {

		$style_prefix = self::STYLE_PREFIX;

		$overlay_prefix = "{$prefix}{$style_prefix}.background.overlay.";

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
							'setting'  => "{$prefix}{$style_prefix}.background.type",
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
				'transport'    => 'refresh',
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

					array(
						'selector' => "{$this->selector} .background-layer .shape-layer",
						'media'    => CSSOutput::NO_MEDIA,
						'property' => 'background-size',
						'value'    => array(
							'dots'                      => 'auto',
							'left-tilted-lines'         => 'auto',
							'right-tilted-lines'        => 'auto',

							'circles'                   => 'cover',
							'10degree-stripes'          => 'cover',
							'rounded-squares-blue'      => 'cover',
							'many-rounded-squares-blue' => 'cover',
							'two-circles'               => 'cover',
							'circles-2'                 => 'cover',
							'circles-3'                 => 'cover',
							'circles-gradient'          => 'cover',
							'circles-white-gradient'    => 'cover',
							'waves'                     => 'cover',
							'waves-inverted'            => 'cover',
							'right-tilted-strips'       => 'cover',
						),
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
				'transport'    => 'postMessage',
				'control'      => array(
					'label'       => Translations::get( 'color' ),
					'type'        => 'color',
					'alpha'       => true,
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
				'transport'  => 'selective_refresh',
				'control'    => array(
					'label'             => Translations::get( 'show_background_overlay' ),
					'type'              => 'group',
					'section'           => 'hero',
					'show_toggle'       => true,
					'controls'          => array(
						"{$overlay_prefix}type",
						"{$overlay_prefix}color.value",
						"{$overlay_prefix}gradient",
						"{$overlay_prefix}gradient_opacity",
						"{$overlay_prefix}shape.value",
						"{$overlay_prefix}light",
					),
					'colibri_tab'       => 'style',
					'selective_refresh' => array(
						'selector' => $this->getSelector(),
						'function' => array( $this, 'renderContent' ),
					),
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

		$style_prefix = self::STYLE_PREFIX;

		$divider_prefix   = "{$prefix}{$style_prefix}.separators.separatorBottom.";
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
						'tilt'         => Translations::get( 'tilt' ),
						'tilt-flipped' => Translations::get( 'tilt-flipped' ),

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

		);
	}

	public function printVideoBackground( $component_id ) {

		if ( $this->prefixedMod( static::STYLE_PREFIX . '.background.type' ) !== 'video' ) {
			return;
		}
		$video_prefix = static::STYLE_PREFIX . '.background.video.';

		$mime_type = 'video/mp4';
		$poster    = $this->prefixedMod( "{$video_prefix}poster.url" );
		$video     = '';

		if ( $this->prefixedMod( "{$video_prefix}videoType" ) === 'external' ) {
			$mime_type = 'video/x-youtube';
			$video     = $this->prefixedMod( "{$video_prefix}externalUrl" );
		} else {
			$id = absint( $this->prefixedMod( "{$video_prefix}internalUrl" ) );
			// $poster = $this->prefixedMod( 'video_background_self_hosted_poster' );

			if ( $id ) {
				$video     = wp_get_attachment_url( $id );
				$type      = wp_check_filetype( $video, wp_get_mime_types() );
				$mime_type = $type['type'];
			} else {
				$video     = $this->prefixedMod( "{$video_prefix}internalUrl" );
				$mime_type = 'video/mp4';
			}
		}

		?>
		<div
				class="colibri-video-background background-layer"
				style="<?php echo( $poster ? "background-image:url({$poster}); background-size:cover;" : '' ); ?>"
				data-kubio-component="video-background"
				data-mime-type="<?php echo esc_attr( $mime_type ); ?>"
				data-video="<?php echo esc_attr( $video ); ?>"
				data-poster="<?php echo esc_attr( $poster ); ?>"
				data-kubio-id="<?php echo esc_attr( $component_id ); ?>">
		</div>
		<?php
	}

	public function whenCustomizerPreview() {
		$component_id     = "{$this->getPrefix()}slideshow";
		$slideshow_prefix = static::STYLE_PREFIX . '.background.slideshow.';
		$this->addFrontendJSData(
			$component_id,
			array(
				'wpSettings' => array(
					'duration' => "{$this->getPrefix()}{$slideshow_prefix}duration.value",
					'speed'    => "{$this->getPrefix()}{$slideshow_prefix}speed.value",
				),
			),
			true
		);

		$video_prefix       = static::STYLE_PREFIX . '.background.video.';
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

	public function renderContent( $parameters = array() ) {

		$overlay_style_prefix = self::STYLE_PREFIX;
		$overlay_prefix       = "{$this->getPrefix()}{$overlay_style_prefix}.background.overlay.";

		$display_overlay = $this->mod( $overlay_prefix . 'enabled', false );
		?>
		<div class="background-wrapper" data-colibri-hide-pen="true">
			<div class="background-layer">
				<div class="background-layer">
					<?php $this->doSlideshow(); ?>
					<?php $this->doVideoBackground(); ?>
				</div>
				<?php if ( $display_overlay ) : ?>
					<div class="overlay-layer"></div>
				<?php endif; ?>
				<div class="shape-layer <?php echo esc_attr( $this->mod( $overlay_prefix . 'shape.value' ) ); ?>"></div>
			</div>
		</div>
		<?php
	}

	public function doSlideshow() {
		$slideshow_prefix = static::STYLE_PREFIX . '.background.slideshow.';
		$component_id     = "{$this->getPrefix()}slideshow";
		$slides           = $this->prefixedMod( "{$slideshow_prefix}slides" );

		$slide_duration = $this->prefixedMod( "{$slideshow_prefix}duration.value" );
		$slide_speed    = $this->prefixedMod( "{$slideshow_prefix}duration.value" );

		$this->printSlideshow( $component_id, $slides, $slide_duration, $slide_speed );
	}

	public function printSlideshow( $component_id, $slides, $slide_duration, $slide_speed ) {

		if ( $this->prefixedMod( static::STYLE_PREFIX . '.background.type' ) !== 'slideshow' ) {
			return;
		}

		?>
		<div class="kubio-slideshow background-layer"
			 data-kubio-id="<?php echo esc_attr( $component_id ); ?>"
			 data-duration="<?php echo esc_attr( $slide_duration ); ?>ms"
			 data-speed="<?php echo esc_attr( $slide_speed ); ?>ms"
			 data-kubio-component="slideshow">
			<?php foreach ( $slides as $slide ) : ?>
				<?php $this->printSlide( $slide ); ?>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function printSlide( $slide ) {
		?>
		<div class="slideshow-image"
			 style="background-image:url(<?php echo esc_attr( $slide['url'] ); ?>)"></div>
		<?php
	}

	protected function getSlideshowSettings( $prefix ) {

		$stylePrefix       = static::STYLE_PREFIX;
		$background_prefix = "{$prefix}{$stylePrefix}.background";
		$slideshow_prefix  = "{$background_prefix}.slideshow.";

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
					'min'            => 2,
					'fields'         => array(
						'url' => array(
							'type'    => 'image',
							'label'   => Translations::get( 'image' ),
							'default' => Defaults::get( "{$slideshow_prefix}slides.0.url" ),
						),
					),
					'active_rules'   => array(
						array(
							'setting'  => "{$background_prefix}.type",
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
							'setting'  => "{$background_prefix}.type",
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
							'setting'  => "{$background_prefix}.type",
							'operator' => '=',
							'value'    => 'slideshow',
						),
					),
				),
			),

		);
	}

	protected function getGeneralBackgroundSettings( $prefix ) {

		$style_prefix = self::STYLE_PREFIX;

		return array(

			"{$prefix}full_height"                      => array(
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

			"{$prefix}hero.separator2"                  => array(
				'default' => '',
				'control' => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'hero',
					'colibri_tab' => 'style',
				),
			),

			"{$prefix}{$style_prefix}.background.color" => array(
				'default'    => Defaults::get( "{$prefix}{$style_prefix}.background.color" ),
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

			"{$prefix}{$style_prefix}.background.type"  => array(
				'default'    => Defaults::get( "{$prefix}{$style_prefix}.background.type" ),
				'transport'  => 'selective_refresh',
				'control'    => array(
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
						'selector' => static::getSelector() . ' .background-wrapper',
						'function' => array( $this, 'renderContent' ),
					),
				),
				'css_output' => array(
					array(
						'selector' => static::getSelector(),
						'property' => 'background-image',
						'value'    => array(
							'color' => 'none',
						),
					),
				),
			),

		);
	}

	protected function getImageSettings( $prefix ) {

		$style_prefix = self::STYLE_PREFIX;

		$image_prefix = "{$prefix}{$style_prefix}.background.image.0.";

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
						'setting'  => "{$prefix}{$style_prefix}.background.type",
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
						'setting'  => "{$prefix}{$style_prefix}.background.type",
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
						'setting'  => "{$prefix}{$style_prefix}.background.type",
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
						'setting'  => "{$prefix}{$style_prefix}.background.type",
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
						'setting'  => "{$prefix}{$style_prefix}.background.type",
						'operator' => '=',
						'value'    => 'image',
					),
				),
			),
		);
	}

	protected function getGradientSettings( $prefix ) {
		$style_prefix = self::STYLE_PREFIX;

		$gradient_prefix = "{$prefix}{$style_prefix}.background.image.0.";

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
						'setting'  => "{$prefix}{$style_prefix}.background.type",
						'operator' => '=',
						'value'    => 'gradient',
					),
				),
			),
		);
	}

	protected function getSpacingSettings( $prefix ) {
		$style_prefix = self::STYLE_PREFIX;

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
				'default'    => Defaults::get( "{$prefix}{$style_prefix}.padding.top.value", 90 ),
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
				'default'    => Defaults::get( "{$prefix}{$style_prefix}.padding.bottom.value", 90 ),
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
