<?php
/**
 * Search element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Search_Element' ) ) {

	class Kenta_Search_Element extends Element {

		use Kenta_Icon_Button_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Radio( $this->getSlug( 'style' ) ) )
					->setLabel( __( 'Search Style', 'kenta' ) )
					->setDefaultValue( 'modal' )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->bindSelectiveRefresh( 'kenta-header-selective-css' )
					->buttonsGroupView()
					->setChoices( [
						'popup'  => __( 'Popup', 'kenta' ),
						'modal'  => __( 'Modal', 'kenta' ),
						'inline' => __( 'Inline', 'kenta' ),
					] )
				,
				( new Condition() )
					->setCondition( [ $this->getSlug( 'style' ) => 'inline' ] )
					->setControls( [
						( new Slider( $this->getSlug( 'inline_width' ) ) )
							->setLabel( __( 'Search Input Width', 'kenta' ) )
							->enableResponsive()
							->asyncCss( ".{$this->slug}", [ 'width' => 'value' ] )
							->setUnits( [
								[ 'unit' => 'px', 'min' => 10, 'max' => 500 ],
								[ 'unit' => '%', 'min' => 1, 'max' => 100 ],
							] )
							->setDefaultValue( '260px' )
						,
					] )
					->setReverseControls( [
						( new Tabs() )
							->setLabel( __( 'Search Icon', 'kenta' ) )
							->showLabel()
							->setActiveTab( 'icon' )
							->addTab( 'icon', __( 'Icon', 'kenta' ), array_merge( [
								( new Icons( $this->getSlug( 'icon_button_icon' ) ) )
									->setLabel( __( 'Icon', 'kenta' ) )
									->selectiveRefresh( ...$this->selectiveRefresh() )
									->setDefaultValue( [
										'value'   => 'fas fa-magnifying-glass',
										'library' => 'fa-solid'
									] )
								,
								( new Separator() ),
							], $this->getIconControls( [
								'selector'              => ".{$this->slug} .kenta-search-button",
								'render-callback'       => $this->selectiveRefresh(),
								'css-selective-refresh' => 'kenta-header-selective-css',
							] ) ) )
							->addTab( 'style', __( 'Style', 'kenta' ), $this->getIconStyleControls( [
								'selector'              => ".{$this->slug} .kenta-search-button",
								'render-callback'       => $this->selectiveRefresh(),
								'css-selective-refresh' => 'kenta-header-selective-css',
							] ) )
						,
					] )
				,
				( new Text( $this->getSlug( 'placeholder' ) ) )
					->setLabel( __( 'Search Placeholder Text', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->setDefaultValue( __( 'Type & Hit Enter', 'kenta' ) )
				,
				( new Condition() )
					->setCondition( [ $this->getSlug( 'style' ) => 'modal' ] )
					->setControls( apply_filters( 'kenta_search_modal_controls', [
						( new Radio( $this->getSlug( 'modal_style' ) ) )
							->setLabel( __( 'Modal Style', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-header-selective-css' )
							->setDefaultValue( 'light' )
							->buttonsGroupView()
							->setChoices( apply_filters( 'kenta_search_modal_style', [
								'light' => __( 'Light', 'kenta' ),
								'dark'  => __( 'Dark', 'kenta' ),
							] ) )
						,
					] ) )
				,
			];
		}

		/**
		 * @param $style
		 *
		 * @return array|mixed
		 */
		protected function getSearchModalPresets( $style ) {
			$presets = [
				'light' => [
					$this->getSlug( 'input_color' )        => [
						'initial'     => 'var(--kenta-accent-color)',
						'focus'       => 'var(--kenta-accent-color)',
						'placeholder' => 'var(--kenta-accent-color)',
					],
					$this->getSlug( 'input_border_color' ) => [
						'initial' => 'var(--kenta-base-300)',
						'focus'   => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'modal_close_color' )  => [
						'initial' => 'var(--kenta-accent-color)',
						'hover'   => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'modal_background' )   => [
						'type'  => 'color',
						'color' => 'var(--kenta-base-color)',
					],
				],
				'dark'  => [
					$this->getSlug( 'input_color' )        => [
						'initial'     => 'var(--kenta-base-color)',
						'focus'       => 'var(--kenta-base-color)',
						'placeholder' => 'var(--kenta-base-300)',
					],
					$this->getSlug( 'input_border_color' ) => [
						'initial' => 'var(--kenta-accent-active)',
						'focus'   => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'modal_close_color' )  => [
						'initial' => 'var(--kenta-base-color)',
						'hover'   => 'var(--kenta-primary-color)',
					],
					$this->getSlug( 'modal_background' )   => [
						'type'  => 'color',
						'color' => 'var(--kenta-accent-color)',
					],
				],
			];

			return $presets[ $style ] ?? [];
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$search_style = CZ::get( $this->getSlug( 'style' ) );

				if ( $search_style !== 'inline' ) {
					$css[".{$this->slug}"]                      = [ 'width' => 'auto' ];
					$css[".{$this->slug} .kenta-search-button"] = $this->getIconButtonCss();
				} else {
					$css[".{$this->slug}"] = [ 'width' => CZ::get( $this->getSlug( 'inline_width' ) ) ];
				}

				if ( $search_style === 'modal' ) {
					$modal_preset = $this->getSearchModalPresets( CZ::get( $this->getSlug( 'modal_style' ) ) );

					$css[".{$this->slug}_modal"] = array_merge(
						Css::background( CZ::get( $this->getSlug( 'modal_background' ), $modal_preset ) ),
						Css::colors( CZ::get( $this->getSlug( 'input_color' ), $modal_preset ), [
							'initial'     => '--kenta-search-input-initial-color',
							'focus'       => '--kenta-search-input-focus-color',
							'placeholder' => '--kenta-search-input-placeholder-color',
						] ),
						Css::colors( CZ::get( $this->getSlug( 'input_border_color' ), $modal_preset ), [
							'initial' => '--kenta-search-input-border-initial-color',
							'focus'   => '--kenta-search-input-border-focus-color',
						] ),
						Css::colors( CZ::get( $this->getSlug( 'modal_close_color' ), $modal_preset ), [
							'initial' => '--kenta-modal-action-initial',
							'hover'   => '--kenta-modal-action-hover',
						] )
					);

					$css[".{$this->slug}_modal .search-input"] = [
						'text-align' => 'center',
					];
				}

				return $css;
			} );

			if ( CZ::get( $this->getSlug( 'style' ) ) === 'modal' && true !== has_action( 'kenta_action_before', [
					$this,
					'render_search_modal'
				] ) ) {
				add_action( 'kenta_action_before', [ $this, 'render_search_modal' ] );
			}
		}

		/**
		 * Render search form
		 */
		public function render_search_form( $args = [] ) {
			$args = wp_parse_args( $args, [
				'placeholder'    => CZ::get( $this->getSlug( 'placeholder' ) ),
				'disable_submit' => true,
			] );

			get_search_form( $args );
		}

		/**
		 * Render search modal
		 */
		public function render_search_modal() {
			$css = [
				'kenta-search-modal kenta-modal',
				$this->slug . '_modal',
			];

			?>
            <div id="kenta-search-modal" data-toggle-behaviour="toggle" class="<?php Utils::the_clsx( $css ); ?>">
                <div class="kenta-modal-content">
                    <div class="max-w-screen-md mx-auto mt-60 kenta-search-modal-form form-controls form-underline"
                         data-redirect-focus="#kenta-close-search-modal-button">
						<?php $this->render_search_form(); ?>
                    </div>
                </div>

                <div class="kenta-modal-actions">
                    <button id="kenta-close-search-modal-button"
                            class="kenta-close-modal"
                            data-toggle-target="#kenta-search-modal"
                            data-toggle-hidden-focus=".kenta-search-button"
                            type="button"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
			<?php
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			$preset       = $this->getIconButtonPreset( CZ::get( $this->getSlug( 'icon_button_preset' ) ) );
			$shape        = CZ::get( $this->getSlug( 'icon_button_icon_shape' ), $preset );
			$fill         = CZ::get( $this->getSlug( 'icon_button_shape_fill_type' ), $preset );
			$search_style = CZ::get( $this->getSlug( 'style' ) );

			$attrs['class'] = ( $attrs['class'] ?? '' ) . ' kenta-search-wrap kenta-form relative ' . $this->slug;

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'search-wrap', $attr, $value );
			}

			/**
			 * Inline style
			 */
			if ( $search_style === 'inline' ) {
				echo '<div ' . $this->render_attribute_string( 'search-wrap' ) . '>';
				$this->render_search_form();
				echo '</div>';

				return;
			}

			/**
			 * Modal & popup
			 */

			$button_classes = Utils::clsx( [
				'kenta-search-button',
				'kenta-icon-button',
				'kenta-icon-button-' . $shape,
				'kenta-icon-button-' . $fill => $shape !== 'none',
			] );

			$this->add_render_attribute( 'search-button', 'class', $button_classes );

			if ( $search_style === 'modal' ) {
				$this->add_render_attribute( 'search-button', 'data-toggle-target', '#kenta-search-modal' );
				$this->add_render_attribute( 'search-button', ' data-toggle-show-focus', '#kenta-search-modal :focusable' );
			}

			if ( $search_style === 'popup' ) {
				$this->add_render_attribute( 'search-wrap', 'data-popup-target', "kenta-search-popup" );
			}

			?>
            <div <?php $this->print_attribute_string( 'search-wrap' ); ?>>
                <button type="button" <?php $this->print_attribute_string( 'search-button' ); ?>>
					<?php IconsManager::print( CZ::get( $this->getSlug( 'icon_button_icon' ) ) ); ?>
                </button>
				<?php
				if ( CZ::get( $this->getSlug( 'style' ) ) === 'popup' ) {
					?>
                    <div class="kenta-search-popup kenta-popup kenta-form absolute right-0 p-half-gutter border border-base-300 bg-base-color z-[9]">
						<?php $this->render_search_form() ?>
                    </div>
					<?php
				}
				?>
            </div>
			<?php
		}
	}
}
