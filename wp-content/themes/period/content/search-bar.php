<?php
if ( get_theme_mod( 'search_bar' ) != 'show' ) {
	return;
}
?>
<div class='search-form-container'>
	<button id="search-icon" class="search-icon">
		<i class="fas fa-search"></i>
	</button>
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text"><?php esc_html_e( 'Search', 'period' ); ?></label>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search...', 'period' ); ?>" value="" name="s"
		       title="<?php esc_attr_e( 'Search for:', 'period' ); ?>" tabindex="-1"/>
	</form>
</div>