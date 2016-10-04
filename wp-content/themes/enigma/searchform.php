<div class="input-group">
	 <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>"> 	
		<input type="text" class="form-control"  name="s" id="s" placeholder="<?php _e( "What do you want to find?", 'enigma' ); ?>" />
		<span class="input-group-btn">
		<button class="btn btn-search" type="submit"><i class="fa fa-search"></i></button>
		</span>
	 </form> 
</div>