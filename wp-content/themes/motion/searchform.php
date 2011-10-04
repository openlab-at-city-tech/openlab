<div id="search">
	<form method="get" id="searchform" action="<?php home_url(); ?>/">
		<p>
			<input type="text" value="<?php _e("Search this site...", 'motion_theme'); ?>" onfocus="if (this.value == '<?php _e("Search this site...", 'motion_theme'); ?>' ) { this.value = ''; }" onblur="if (this.value == '' ) { this.value = '<?php _e("Search this site...", 'motion_theme'); ?>'; }" name="s" id="searchbox" />
			<input type="submit" class="submitbutton" value="GO" />
		</p>
	</form>
</div>