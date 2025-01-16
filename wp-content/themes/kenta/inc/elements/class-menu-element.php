<?php
/**
 * Menu element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Spacing;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Menu_Element' ) ) {

	class Kenta_Menu_Element extends Element {

		/**
		 * After element register
		 */
		public function after_register() {
			// Register nav menu
			add_action( 'after_setup_theme', function () {
				register_nav_menu( $this->slug, $this->getLabel() );
			} );
		}

		protected function getTopLevelControls() {
			$controls = [
				( new Select( $this->getSlug( 'top_level_preset' ) ) )
					->setLabel( __( 'Top Level Preset', 'kenta' ) )
					->setDefaultValue( 'ghost' )
					->bindSelectiveRefresh( $this->getDefaultSetting( 'selective-refresh' ) )
					->setChoices( apply_filters( 'kenta_menu_top_level_preset_options', [
						'ghost'         => __( 'Ghost', 'kenta' ),
						'border-bottom' => __( 'Bottom Border', 'kenta' ),
						'border-top'    => __( 'Top Border', 'kenta' ),
						'border-both'   => __( 'Both Border', 'kenta' ),
						'pill'          => __( 'Pill', 'kenta' ),
						'custom'        => __( 'Custom (Premium)', 'kenta' ),
					] ) )
				,
				( new Slider( $this->getSlug( 'top_level_height' ) ) )
					->setLabel( __( 'Items Height', 'kenta' ) )
					->asyncCss( ".{$this->slug}", [ '--menu-items-height' => 'value' ] )
					->setDefaultValue( $this->getDefaultSetting( 'top-level-height', '50%' ) )
					->setDefaultUnit( $this->getDefaultSetting( 'top-level-height-unit', '%' ) )
					->setMin( 5 )
					->setMax( 100 )
				,
			];

			return apply_filters( 'kenta_menu_top_level_controls', $controls, $this->slug, $this->defaults );
		}

		/**
		 * @return array
		 */
		protected function getDropdownGeneralControls() {
			return [
				( new Slider( $this->getSlug( 'dropdown_width' ) ) )
					->setLabel( __( 'Min Width', 'kenta' ) )
					->asyncCss( ".{$this->slug}", [ '--dropdown-width' => 'value' ] )
					->setDefaultValue( $this->getDefaultSetting( 'dropdown-width', '200px' ) )
					->setMin( 100 )
					->setMax( 300 )
				,
				( new Radio( $this->getSlug( 'dropdown_direction' ) ) )
					->setLabel( __( 'Direction', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->buttonsGroupView()
					->setDefaultValue( $this->getDefaultSetting( 'dropdown-direction', 'right' ) )
					->setChoices( [
						'left'  => __( 'Left', 'kenta' ),
						'right' => __( 'Right', 'kenta' ),
					] )
				,
				( new Separator() ),
				( new Spacing( $this->getSlug( 'dropdown_item_padding' ) ) )
					->setLabel( __( 'Items Padding', 'kenta' ) )
					->asyncCss( ".{$this->slug}", AsyncCss::dimensions( '--dropdown-item-padding' ) )
					->setDefaultValue( $this->getDefaultSetting( 'dropdown-item-padding', [
						'top'    => '12px',
						'bottom' => '12px',
						'left'   => '12px',
						'right'  => '12px',
						'linked' => true,
					] ) )
				,
				( new Spacing( $this->getSlug( 'dropdown_radius' ) ) )
					->setLabel( __( 'Dropdown Border Radius', 'kenta' ) )
					->asyncCss( ".{$this->slug}", AsyncCss::dimensions( '--dropdown-radius' ) )
					->setDefaultValue( $this->getDefaultSetting( 'dropdown-radius', [
						'top'    => '3px',
						'bottom' => '3px',
						'left'   => '3px',
						'right'  => '3px',
						'linked' => true,
					] ) )
			];
		}

		/**
		 * @return array
		 */
		protected function getDropdownStyleControls() {
			$controls = [
				( new Select( $this->getSlug( 'dropdown_preset' ) ) )
					->setLabel( __( 'Dropdown Preset', 'kenta' ) )
					->setDefaultValue( 'ghost' )
					->bindSelectiveRefresh( $this->getDefaultSetting( 'selective-refresh' ) )
					->setChoices( [
						'ghost'      => __( 'Ghost', 'kenta' ),
						'ghost-dark' => __( 'Ghost Dark', 'kenta' ),
						'solid'      => __( 'Solid', 'kenta' ),
						'solid-dark' => __( 'Solid Dark', 'kenta' ),
						'custom'     => __( 'Custom (Premium)', 'kenta' ),
					] )
				,
			];

			return apply_filters( 'kenta_menu_dropdown_style_controls', $controls, $this->slug, $this->defaults );
		}

		/**
		 * Get all controls
		 *
		 * @return array
		 */
		public function getControls(): array {
			return [
				( new CallToAction( $this->getSlug( 'edit_locations' ) ) )
					->setLabel( __( 'Edit Menu Locations', 'kenta' ) )
					->expandCustomize( 'menu_locations' )
				,
				( new Separator() ),
				( new Slider( $this->getSlug( 'depth' ) ) )
					->setLabel( __( 'Menu Depth', 'kenta' ) )
					->setDescription( __( '"0" meas no limit.', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->setMin( 0 )
					->setMax( 10 )
					->setDefaultUnit( false )
					->setDefaultValue( $this->getDefaultSetting( 'depth', 0 ) )
				,

				( new Condition() )
					->setCondition( [ $this->getSlug( 'depth' ) => '!1' ] )
					->setControls( [
						( new Separator() ),
						( new Toggle( $this->getSlug( 'arrow' ) ) )
							->setLabel( __( 'Sub Menu Toggle Icon', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( $this->getDefaultSetting( 'arrow', 'yes' ) )
						,
						( new Condition() )
							->setCondition( [ $this->getSlug( 'arrow' ) => 'yes' ] )
							->setControls( [
								( new Icons( $this->getSlug( 'arrow-icon' ) ) )
									->setLabel( __( 'Toggle Icon', 'kenta' ) )
									->selectiveRefresh( ...$this->selectiveRefresh() )
									->setDefaultValue( [
										'value'   => 'fas fa-angle-down',
										'library' => 'fa-solid',
									] )
								,
							] )
						,
					] )
				,
				( new Section() )
					->setLabel( __( 'Top Level Options', 'kenta' ) )
					->keepMarginBelow()
					->setControls( $this->getTopLevelControls() )
				,
				( new Condition() )
					->setCondition( [ $this->getSlug( 'depth' ) => '!1' ] )
					->setControls( [
						( new Section() )
							->keepMarginBelow()
							->setLabel( __( 'Dropdown Options', 'kenta' ) )
							->setControls( [
								( new Tabs() )
									->setActiveTab( 'general' )
									->addTab( 'general', __( 'General', 'kenta' ), $this->getDropdownGeneralControls() )
									->addTab( 'style', __( 'Style', 'kenta' ), $this->getDropdownStyleControls() )
								,
							] )
						,
					] )
			];
		}

		/**
		 * @param $preset
		 *
		 * @return array
		 */
		protected function getTopLevelPreset( $preset ) {
			$presets = [
				'border-bottom' => [
					$this->getSlug( 'top_level_border_bottom' )        => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-transparent)'
					],
					$this->getSlug( 'top_level_border_bottom_active' ) => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-primary-color)'
					],
				],
				'border-top'    => [
					$this->getSlug( 'top_level_border_top' )        => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-transparent)'
					],
					$this->getSlug( 'top_level_border_top_active' ) => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-primary-color)'
					],
				],
				'border-both'   => [
					$this->getSlug( 'top_level_border_top' )           => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-transparent)'
					],
					$this->getSlug( 'top_level_border_top_active' )    => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-primary-color)'
					],
					$this->getSlug( 'top_level_border_bottom' )        => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-transparent)'
					],
					$this->getSlug( 'top_level_border_bottom_active' ) => [
						'width' => 2,
						'style' => 'solid',
						'color' => 'var(--kenta-primary-color)'
					],
				],
				'pill'          => [
					$this->getSlug( 'top_level_text_color' )       => [
						'initial' => 'var(--kenta-accent-color)',
						'hover'   => 'var(--kenta-base-color)',
						'active'  => 'var(--kenta-base-color)',
					],
					$this->getSlug( 'top_level_background_color' ) => [
						'initial' => 'var(--kenta-transparent)',
						'hover'   => 'var(--kenta-primary-color)',
						'active'  => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'top_level_padding' )          => [
						'top'    => '0.5em',
						'bottom' => '0.5em',
						'left'   => '1.5em',
						'right'  => '1.5em',
					],
				],
			];

			return apply_filters( $this->getSlug( 'top_level_preset_args' ), $presets[ $preset ] ?? [], $this->getSlug(''), $preset );
		}

		/**
		 * @param $preset
		 *
		 * @return array
		 */
		protected function getDropdownPreset( $preset ) {
			$shadow = [
				'enable'     => 'yes',
				'horizontal' => '0px',
				'vertical'   => '0px',
				'blur'       => '4px',
				'spread'     => '0px',
				'color'      => 'rgba(44, 62, 80, 0.2)',
			];

			$presets = [
				'ghost'      => [
					$this->getSlug( 'dropdown_text_color' )       => [
						'initial' => 'var(--kenta-accent-color)',
						'hover'   => 'var(--kenta-primary-color)',
						'active'  => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'dropdown_background_color' ) => [
						'initial' => 'var(--kenta-base-color)',
						'active'  => 'var(--kenta-base-color)',
					],
					$this->getSlug( 'dropdown_border' )          => [
						'width' => 1,
						'style' => 'none',
						'color' => 'var(--kenta-base-300)'
					],
					$this->getSlug( 'dropdown_divider' )          => [
						'width' => 1,
						'style' => 'solid',
						'color' => 'var(--kenta-base-300)'
					],
					$this->getSlug( 'dropdown_shadow' )           => $shadow,
				],
				'ghost-dark' => [
					$this->getSlug( 'dropdown_text_color' )       => [
						'initial' => 'var(--kenta-base-color)',
						'hover'   => 'var(--kenta-primary-color)',
						'active'  => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'dropdown_background_color' ) => [
						'initial' => 'var(--kenta-accent-color)',
						'active'  => 'var(--kenta-accent-color)',
					],
					$this->getSlug( 'dropdown_border' )          => [
						'width' => 1,
						'style' => 'none',
						'color' => 'var(--kenta-accent-active)'
					],
					$this->getSlug( 'dropdown_divider' )          => [
						'width' => 1,
						'style' => 'solid',
						'color' => 'var(--kenta-accent-active)'
					],
					$this->getSlug( 'dropdown_shadow' )           => $shadow,
				],
				'solid'      => [
					$this->getSlug( 'dropdown_text_color' )       => [
						'initial' => 'var(--kenta-accent-color)',
						'hover'   => 'var(--kenta-base-color)',
						'active'  => 'var(--kenta-base-color)',
					],
					$this->getSlug( 'dropdown_background_color' ) => [
						'initial' => 'var(--kenta-base-color)',
						'active'  => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'dropdown_border' )          => [
						'width' => 1,
						'style' => 'none',
						'color' => 'var(--kenta-base-300)'
					],
					$this->getSlug( 'dropdown_divider' )          => [
						'width' => 1,
						'style' => 'solid',
						'color' => 'var(--kenta-base-300)'
					],
					$this->getSlug( 'dropdown_shadow' )           => $shadow,
				],
				'solid-dark' => [
					$this->getSlug( 'dropdown_text_color' )       => [
						'initial' => 'var(--kenta-base-color)',
						'hover'   => 'var(--kenta-base-color)',
						'active'  => 'var(--kenta-base-color)',
					],
					$this->getSlug( 'dropdown_background_color' ) => [
						'initial' => 'var(--kenta-accent-color)',
						'active'  => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'dropdown_border' )          => [
						'width' => 1,
						'style' => 'none',
						'color' => 'var(--kenta-accent-active)'
					],
					$this->getSlug( 'dropdown_divider' )          => [
						'width' => 1,
						'style' => 'solid',
						'color' => 'var(--kenta-accent-active)'
					],
					$this->getSlug( 'dropdown_shadow' )           => $shadow,
				],
			];

			return apply_filters( $this->getSlug( 'dropdown_preset_args' ), $presets[ $preset ] ?? [], $this->getSlug(''), $preset );
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {

			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {

				// top level typography
				$css[".{$this->slug} > li"] = Css::typography( CZ::get( $this->getSlug( 'top_level_typography' ) ) );
				// dropdown typography
				$css[".{$this->slug} > li ul"] = Css::typography( CZ::get( $this->getSlug( 'dropdown_typography' ) ) );

				$top_level_preset = $this->getTopLevelPreset( CZ::get( $this->getSlug( 'top_level_preset' ) ) );
				$dropdown_preset  = $this->getDropdownPreset( CZ::get( $this->getSlug( 'dropdown_preset' ) ) );

				$css[".{$this->slug}"] = array_merge(
					[
						'--menu-items-height' => CZ::get( $this->getSlug( 'top_level_height' ), $top_level_preset ),
						'--dropdown-width'    => CZ::get( $this->getSlug( 'dropdown_width' ) ),
					],
					Css::colors( CZ::get( $this->getSlug( 'top_level_text_color' ), $top_level_preset ), [
						'initial' => '--menu-text-initial-color',
						'hover'   => '--menu-text-hover-color',
						'active'  => '--menu-text-active-color',
					] ),
					Css::colors( CZ::get( $this->getSlug( 'top_level_background_color' ), $top_level_preset ), [
						'initial' => '--menu-background-initial-color',
						'hover'   => '--menu-background-hover-color',
						'active'  => '--menu-background-active-color',
					] ),
					Css::dimensions( CZ::get( $this->getSlug( 'top_level_margin' ), $top_level_preset ), '--menu-items-margin' ),
					Css::dimensions( CZ::get( $this->getSlug( 'top_level_padding' ), $top_level_preset ), '--menu-items-padding' ),
					Css::dimensions( CZ::get( $this->getSlug( 'top_level_radius' ), $top_level_preset ), '--menu-items-radius' ),
					Css::border( CZ::get( $this->getSlug( 'top_level_border_top' ), $top_level_preset ), '--menu-items-border-top' ),
					Css::border( CZ::get( $this->getSlug( 'top_level_border_top_active' ), $top_level_preset ), '--menu-items-border-top-active' ),
					Css::border( CZ::get( $this->getSlug( 'top_level_border_bottom' ), $top_level_preset ), '--menu-items-border-bottom' ),
					Css::border( CZ::get( $this->getSlug( 'top_level_border_bottom_active' ), $top_level_preset ), '--menu-items-border-bottom-active' ),
					// dropdown css
					Css::colors( CZ::get( $this->getSlug( 'dropdown_text_color' ), $dropdown_preset ), [
						'initial' => '--dropdown-text-initial-color',
						'hover'   => '--dropdown-text-hover-color',
						'active'  => '--dropdown-text-active-color',
					] ),
					Css::colors( CZ::get( $this->getSlug( 'dropdown_background_color' ), $dropdown_preset ), [
						'initial' => '--dropdown-background-initial-color',
						'active'  => '--dropdown-background-active-color',
					] ),
					Css::dimensions( CZ::get( $this->getSlug( 'dropdown_item_padding' ), $dropdown_preset ), '--dropdown-item-padding' ),
					Css::dimensions( CZ::get( $this->getSlug( 'dropdown_radius' ), $dropdown_preset ), '--dropdown-radius' ),
					Css::shadow( CZ::get( $this->getSlug( 'dropdown_shadow' ), $dropdown_preset ), '--dropdown-box-shadow' ),
					Css::border( CZ::get( $this->getSlug( 'dropdown_border' ), $dropdown_preset ), '--dropdown-border' ),
					Css::border( CZ::get( $this->getSlug( 'dropdown_divider' ), $dropdown_preset ), '--dropdown-divider' )
				);

				return $css;
			} );
		}

		/**
		 * Seletive refresh args
		 *
		 * @return array
		 */
		protected function selectiveRefresh() {
			return [
				".{$this->getSlug( 'wrap' )}",
				[ $this, 'build' ],
				[ 'container_inclusive' => true ]
			];
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {

			$attrs['class'] = Utils::clsx(
				'kenta-menu-wrap h-full',
				$this->getSlug( 'wrap' ),
				$attrs['class'] ?? []
			);

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( $this->slug, $attr, $value );
			}

			$depth    = absint( CZ::get( $this->getSlug( 'depth' ) ) );
			$hasArrow = ( $depth !== 1 ) && CZ::checked( $this->getSlug( 'arrow' ) );

			echo '<div ' . $this->render_attribute_string( $this->slug ) . '>';
			wp_nav_menu( [
				'theme_location' => $this->slug,
				'container'      => false,
				'depth'          => $depth,
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'link_after'     => '<span class="kenta-menu-icon">' . wp_kses_post( IconsManager::render( CZ::get( $this->getSlug( 'arrow-icon' ) ) ) ) . '</span>',
				'menu_class'     => Utils::clsx( 'sf-menu clearfix kenta-menu', $this->slug, [
					'kenta-menu-has-arrow' => $hasArrow,
					'sf-dropdown-left'     => CZ::get( $this->getSlug( 'dropdown_direction' ) ) === 'left',
				], $menu_attrs['class'] ?? [] ),
				'fallback_cb'    => function ( $args ) {
					// for customize menu style, the default one not work.
					wp_page_menu( array_merge( $args, [
						'container' => 'ul'
					] ) );
				},
			] );
			echo '</div>';
		}
	}
}
