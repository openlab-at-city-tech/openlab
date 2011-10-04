<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<div id="search">
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
		<div>
	        <input type="text" class="field" name="s" id="s"  value="<?php _e('Search', 'modularity') ?>" onfocus="if (this.value == '<?php _e('Search', 'modularity') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search', 'modularity') ?>';}" />
		</div>
	</form>
</div>
