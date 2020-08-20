<?php
$search_value = wp_unslash( openlab_get_current_filter( 'search' ) );
?>

<div class="sidebar-filter sidebar-filter-search">
	<div class="form-group">
		<input id="search-terms" class="form-control" value="<?php echo esc_attr( $search_value ); ?>" type="text" name="search" placeholder="<?php esc_attr_e( 'Enter keyword', 'openlab-theme' ); ?>" /><label class="sr-only" for="search-terms"><?php esc_html_e( 'Enter keyword', 'openlab-theme' ); ?></label>
	</div>
	<div class="clearfloat"></div>
</div><!-- sidebar-filter-search -->
