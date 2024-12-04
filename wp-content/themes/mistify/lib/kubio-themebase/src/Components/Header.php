<?php



namespace Kubio\Theme\Components;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\View;


class Header extends \ColibriWP\Theme\Components\Header {


	public function renderContent( $parameters = array() ) {

		Hooks::prefixed_do_action( 'before_header' );
		$header_class = View::isFrontPage() ? 'header-front-page' : 'header-inner-page';
		View::printSkipToContent();
		?>
		<div class="header <?php echo $header_class; ?>">
			<?php View::isFrontPage() ? $this->renderFrontPageFragment() : $this->renderInnerPageFragment(); ?>
		</div>
		<script type='text/javascript'>
			(function () {
				// forEach polyfill
				if (!NodeList.prototype.forEach) {
					NodeList.prototype.forEach = function (callback) {
						for (var i = 0; i < this.length; i++) {
							callback.call(this, this.item(i));
						}
					}
				}
				var navigation = document.querySelector('[data-colibri-navigation-overlap="true"], .h-navigation_overlap');
				if (navigation) {
					var els = document
						.querySelectorAll('.h-navigation-padding');
					if (els.length) {
						els.forEach(function (item) {
							item.style.paddingTop = navigation.offsetHeight + "px";
						});
					}
				}
			})();
		</script>
		<?php
	}

}
