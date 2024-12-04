<?php


namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Components\Header\HeroStyle;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class Hero extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.hero.';

	/**
	 * @param bool $include_content_settings
	 *
	 * @return array
	 */
	protected static function getOptions( $include_content_settings = true ) {

		$prefix = static::$settings_prefix;
		$result = array(
			'settings' => array(
				"{$prefix}.pen"               => array(
					'control' => array(
						'type'    => 'pen',
						'section' => 'hero',
					),
				),
				"{$prefix}props.useWhiteText" => array(
					'default'    => Defaults::get( "{$prefix}props.useWhiteText" ),
					'transport'  => 'refresh',
					'control'    => array(
						'label'       => '',
						'type'        => 'hidden',
						'section'     => 'hero',
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector'   => '#hero [data-kubio-partial-refresh=title] .wp-block-kubio-heading',
							'property'   => 'color',
							'true_value' => 'rgb(255, 255, 255)',
						),
						array(
							'selector'   => '#hero [data-kubio-partial-refresh=subtitle] .wp-block-kubio-text',
							'property'   => 'color',
							'true_value' => 'rgba(255, 255, 255, .95)',
						),
					),
				),
			),
		);

		$background_settings = static::getStyleComponent()->getOptions();

		$result = array_merge_recursive( $result, $background_settings );

		if ( $include_content_settings ) {
			$content = array(
				'settings' => static::getGeneralContentSettings( $prefix ),
			);
			$result  = array_merge_recursive( $content, $result );
		}

		return $result;

	}

	public static function getStyleComponent() {
		return HeroStyle::getInstance(
			static::$settings_prefix,
			static::selectiveRefreshSelector()
		);
	}

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::$settings_prefix . 'selective_selector', false );
	}

	protected static function getGeneralContentSettings( $prefix ) {

		$selector = static::selectiveRefreshSelector();

		return array(
			"{$prefix}props.heroSection.layout" => array(
				'default'    => Defaults::get( "{$prefix}props.heroSection.layout" ),
				'control'    => array(
					'label'       => Translations::get( 'hero_layout' ),
					'focus_alias' => 'hero_layout',
					'type'        => 'select-icon',
					'section'     => 'hero',
					'colibri_tab' => 'content',
					'choices'     => array(
						'textOnly'             =>
							array(
								'tooltip' => Translations::get( 'text_only' ),
								'label'   => Translations::get( 'text_only' ),
								'value'   => 'textOnly',
								'icon'    => Defaults::get( 'icons.textOnly.content' ),
							),

						'textWithMediaOnRight' =>
							array(
								'tooltip' => Translations::get( 'text_with_media_on_right' ),
								'label'   => Translations::get( 'text_with_media_on_right' ),
								'value'   => 'textWithMediaOnRight',
								'icon'    => Defaults::get( 'icons.textWithMediaOnRight.content' ),
							),

						'textWithMediaOnLeft'  =>
							array(
								'tooltip' => Translations::get( 'text_with_media_on_left' ),
								'label'   => Translations::get( 'text_with_media_on_left' ),
								'value'   => 'textWithMediaOnLeft',
								'icon'    => Defaults::get( 'icons.textWithMediaOnLeft.content' ),
							),
					),
				),
				'css_output' => array(
					array(
						'selector' => "{$selector} .h-column-container",
						'media'    => CSSOutput::desktopMedia(),
						'property' => 'width',
						'value'    => array(
							'textOnly'             => '80%',
							'textWithMediaOnRight' => '50%',
							'textWithMediaOnLeft'  => '50%',
						),
					),
					array(
						'selector' => "{$selector} .h-column-container:nth-child(1)",
						'media'    => CSSOutput::desktopMedia(),
						'property' => 'order',
						'value'    => array(
							'textOnly'             => '0',
							'textWithMediaOnRight' => '0',
							'textWithMediaOnLeft'  => '1',
						),
					),
					array(
						'selector' => "{$selector} .h-column-container:nth-child(2)",
						'media'    => CSSOutput::desktopMedia(),
						'property' => 'display',
						'value'    => array(
							'textOnly'             => 'none',
							'textWithMediaOnRight' => 'flex',
							'textWithMediaOnLeft'  => 'flex',
						),
					),
				),
			),

			"{$prefix}separator1"               => array(
				'default' => '',
				'control' => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'hero',
					'colibri_tab' => 'content',
				),
			),

			"{$prefix}hero_column_width"        => array(
				'default'    => Defaults::get( "{$prefix}hero_column_width" ),
				'control'    => array(
					'label'       => Translations::get( 'hero_column_width' ),
					'type'        => 'slider',
					'section'     => 'hero',
					'colibri_tab' => 'content',
					'min'         => 0,
					'max'         => 100,
				),
				'css_output' => array(
					array(
						'selector'      => "{$selector} .h-section-grid-container .h-column-container:first-child",
						'property'      => 'width',
						'media'         => CSSOutput::desktopMedia(),
						'value_pattern' => '%s%% !important',
					),
					array(
						'selector'      => "{$selector} .h-section-grid-container .h-column-container:nth-child(2)",
						'property'      => 'width',
						'media'         => CSSOutput::desktopMedia(),
						'value_pattern' => 'calc(100%% - %s%%) !important',
					),
				),
			),

			"{$prefix}separator2"               => array(
				'default' => '',
				'control' => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'hero',
					'colibri_tab' => 'content',
				),
			),

		);
	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'front-header',
			'hero',
			array(
				'component' => $this,
			)
		);

	}

	public function printBackground() {
		static::getStyleComponent()->render();
	}

	public function printSeparator() {
		$prefix         = static::$settings_prefix;
		$divider_prefix = "{$prefix}style.separatorBottom.";
		$enabled        = $this->mod( "{$divider_prefix}enabled", false );
		$negative       = $this->mod( "{$divider_prefix}negative", false );

		if ( $enabled ) {

			$style = $this->mod( "{$divider_prefix}type", 'mountains' );

			$divider_style = Defaults::get( 'divider_style' );

			if ( $negative && ! isset( $divider_style[ $style . '-negative' ] ) ) {
				$negative = false;
			}

			if ( $negative ) {
				$style .= '-negative';
			}

			$svg = '';
			if ( isset( $divider_style[ $style ] ) ) {
				$svg = $divider_style[ $style ];
			}

			// set color
			$svg = str_replace( '<path', '<path class="svg-white-bg"', $svg );

			if ( $negative ) {
				$transform = '';
			} else {
				$transform = 'transform:rotateX(180deg);';
			}

			// flip for bottom
			$svg       = str_replace( '<svg ', '<svg style="' . $transform . '" ', $svg );
			$separator = "<div class='h-separator' style='bottom: -1px;'>$svg</div>";

			echo $separator;
		}
	}
}
