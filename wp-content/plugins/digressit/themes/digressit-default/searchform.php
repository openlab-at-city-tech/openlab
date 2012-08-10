<p style-"margin-top: 100px">
<form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
	<input type="text" id="s" name="s" style="max-width: 200px" value="<?php the_search_query(); ?>" accesskey="4" />
	<input type="submit" id="searchsubmit" value="<?php echo attribute_escape(__('Search','k2_domain')); ?>" />
</form>
</p>