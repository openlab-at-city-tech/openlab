<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" value="<?php _e( 'Type and press enter', 'lingonberry' ); ?>" onfocus=" if ( this.value == '<?php _e( 'Type and press enter', 'lingonberry' ); ?>' ) this.value = '';" onblur="if ( this.value == '' ) this.value = '<?php _e( 'Type and press enter', 'lingonberry' ); ?>';" name="s" id="s" /> 
	<input type="submit" id="searchsubmit" value="<?php _e( 'Search', 'lingonberry' ); ?>" class="button hidden">
</form>