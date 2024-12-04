<?php

namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class TopBarListIcons extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.icon_list.';

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::$settings_prefix . 'selective_selector', false );
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix = static::$settings_prefix;

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'information_fields' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(

				"{$prefix}pen"                 => array(
					'control' => array(
						'type'    => 'pen',
						'section' => "{$prefix}section",
					),

				),

				"{$prefix}localProps.iconList" => array(
					'default' => Defaults::get( "{$prefix}localProps.iconList" ),
					'control' => array(
						'label'          => Translations::get( 'icons' ),
						'type'           => 'repeater',
						'section'        => "{$prefix}section",
						'colibri_tab'    => 'content',
						'item_add_label' => Translations::get( 'add_item' ),
						'max'            => 10,
						'min'            => 0,
						'fields'         => array(
							'text'       => array(
								'type'    => 'text',
								'label'   => Translations::get( 'text' ),
								'default' => Translations::get( 'text' ),
							),

							'icon'       => array(
								'type'    => 'icon',
								'label'   => Translations::get( 'icon' ),
								'default' => Defaults::get( 'icons.facebook' ),
							),

							'link_value' => array(
								'type'    => 'text',
								'label'   => Translations::get( 'link' ),
								'default' => '#',
							),

						),
					),
				),
			),
		);
	}

	public function getPenPosition() {
		return static::PEN_ON_RIGHT;
	}

	public function renderContent( $parameters = array() ) {
		/*
		 this prevents the pen to show after adding a new item
		if (\is_customize_preview() ): ?>
		  <style type="text/css">
			  <?php echo static::selectiveRefreshSelector(); ?>
			  .customize-partial-edit-shortcut {
				  left: auto !important;
				  top: -6px !important;
			  }
		  </style>
		<?php endif;
		*/
		View::partial(
			'front-header',
			'top-bar/list-icons',
			array(
				'component' => $this,
			)
		);

	}

	public function printIcons() {
		$icons = $this->mod( static::$settings_prefix . 'localProps.iconList', array() );
		if ( $icons ) {
			$count = count( $icons );

			for ( $i = 0; $i < $count; $i ++ ) {
				$icon = $icons[ $i ];
				$name = 'middle';

				if ( $i === 0 ) {
					$name = 'first';
				}
				if ( $i + 1 === $count ) {
					$name = 'last';
				}
				View::partial( 'front-header', "top-bar/list-icon-$name", $icon );
			}
		}
	}

}
