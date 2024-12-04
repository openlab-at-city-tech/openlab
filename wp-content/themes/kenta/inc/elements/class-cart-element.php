<?php
/**
 * Cart element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Cart_Element' ) ) {

	class Kenta_Cart_Element extends Element {

		use Kenta_Icon_Button_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function after_register() {
			add_filter( 'add_to_cart_fragments', array( $this, 'cart_link_fragments' ) );
		}

		/**
		 * Async update cart badge
		 *
		 * @param $fragments
		 *
		 * @return mixed
		 */
		public function cart_link_fragments( $fragments ) {

			ob_start();
			$this->show_badge();
			$html = ob_get_clean();

			$fragments['.kenta-cart-badge-wrapper'] = $html;

			return $fragments;
		}

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Tabs() )
					->setActiveTab( 'icon' )
					->addTab( 'icon', __( 'Icon', 'kenta' ), array_merge( [
						( new Icons( $this->getSlug( 'icon_button_icon' ) ) )
							->setLabel( __( 'Icon', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( [
								'value'   => 'fas fa-basket-shopping',
								'library' => 'fa-solid',
							] )
						,
						( new Separator() ),
					], $this->getIconControls( [
						'selector'              => ".{$this->slug} .kenta-cart-trigger",
						'render-callback'       => $this->selectiveRefresh(),
						'css-selective-refresh' => 'kenta-header-selective-css',
					] ) ) )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getIconStyleControls( [
						'selector'              => ".{$this->slug} .kenta-cart-trigger",
						'render-callback'       => $this->selectiveRefresh(),
						'css-selective-refresh' => 'kenta-header-selective-css',
					] ) )
			];
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$css[".{$this->slug} .kenta-cart-trigger"] = $this->getIconButtonCss();

				return $css;
			} );
		}

		/**
		 * Validates whether the Woo Cart instance is available in the request
		 *
		 * @return bool
		 */
		protected function is_woo_cart_available() {
			if ( ! KENTA_WOOCOMMERCE_ACTIVE ) {
				return false;
			}

			$woo = WC();

			return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
		}

		/**
		 * Show cart count badge
		 */
		public function show_badge() {
			echo '<span class="kenta-cart-badge-wrapper">';

			$cart_count = WC()->cart->cart_contents_count;
			if ( $cart_count > 0 ) {
				echo '<span class="kenta-cart-badge absolute font-sans font-bold leading-none text-red-100 bg-red-600 rounded-full">';
				echo $cart_count;
				echo '</span>';
			}

			echo '</span>';
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			if ( ! $this->is_woo_cart_available() ) {
				return;
			}

			$preset = $this->getIconButtonPreset( CZ::get( $this->getSlug( 'icon_button_preset' ) ) );
			$shape  = CZ::get( $this->getSlug( 'icon_button_icon_shape' ), $preset );
			$fill   = CZ::get( $this->getSlug( 'icon_button_shape_fill_type' ), $preset );

			$button_classes = Utils::clsx( [
				'kenta-cart-trigger',
				'kenta-icon-button',
				'kenta-icon-button-' . $shape,
				'kenta-icon-button-' . $fill => $shape !== 'none',
			] );

			$this->add_render_attribute( 'cart', 'class', $button_classes );

			$attrs['class'] = ( $attrs['class'] ?? '' ) . ' kenta-cart-trigger-wrap relative ' . $this->slug;

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'cart-wrap', $attr, $value );
			}

			$this->add_render_attribute( 'cart-wrap', 'data-popup-target', "kenta-cart-popup" );

			$cart_count = WC()->cart->cart_contents_count;
			$cart_link  = esc_url( $cart_count ? wc_get_cart_url() : wc_get_page_permalink( 'shop' ) );

			?>
            <div <?php $this->print_attribute_string( 'cart-wrap' ); ?>>
                <a href="<?php echo esc_url( $cart_link ) ?>" <?php $this->print_attribute_string( 'cart' ); ?>>
					<?php IconsManager::print( CZ::get( $this->getSlug( 'icon_button_icon' ) ) ); ?>
					<?php $this->show_badge(); ?>
					<?php if ( ! apply_filters( 'woocommerce_widget_cart_is_hidden', is_cart() || is_checkout() ) ): ?>
                        <div class="kenta-cart-popup kenta-popup kenta-heading kenta-heading-style-1 absolute right-0 p-half-gutter border border-base-300 bg-base-color z-[9]">
							<?php the_widget( 'WC_Widget_Cart' ); ?>
                        </div>
					<?php endif; ?>
                </a>
            </div>
			<?php
		}
	}
}
