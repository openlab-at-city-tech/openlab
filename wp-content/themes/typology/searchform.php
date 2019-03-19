<form class="typology-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
	<input name="s" type="text" value="" placeholder="<?php echo esc_attr( __typology('search_placeholder') ); ?>" />
	<button type="submit" class="typology-button typology-button-search typology-icon-button"><?php echo __typology('search_button'); ?></button> 
	<?php if(defined('ICL_LANGUAGE_CODE')): ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr(ICL_LANGUAGE_CODE); ?>">
	<?php endif; ?>
</form>