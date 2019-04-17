<?php $cart_elements = johannes_woocommerce_cart_elements(); ?>
<ul class="johannes-menu-action johannes-cart">
	<li>
		<a href="<?php echo esc_url( $cart_elements['cart_url'] ); ?>"><span class="header-el-label"><?php echo __johannes( 'cart_label' ); ?></span><i class="jf jf-cart"></i></a>
		<?php if ( $cart_elements['products_count'] > 0 ) : ?>
			<span class="johannes-cart-count"><?php echo absint( $cart_elements['products_count'] ); ?></span>
		<?php endif; ?>
	</li>
</ul>