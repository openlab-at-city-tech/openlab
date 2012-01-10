<?php

// This file is part of the Carrington Blog Theme for WordPress
// http://carringtontheme.com
//
// Copyright (c) 2008-2009 Crowd Favorite, Ltd. All rights reserved.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }

?>
			<div class="clear"></div>
			</div><!-- .wrapper -->
		</div><!-- #main -->
		<hr class="lofi" />
		<div id="footer" class="section">
			<div class="wrapper">		
				<p id="generator-link"><?php _e('Proudly powered by <a href="http://wordpress.org/" rel="generator">WordPress</a> and <a href="http://carringtontheme.com" title="Carrington theme for WordPress">Carrington</a>.', 'carrington-blog'); ?></p>
<?php
if (cfct_get_option('cfct_credit') == 'yes') {
?>
				<p id="developer-link"><?php printf(__('<a href="http://crowdfavorite.com" title="Custom WordPress development, design and backup services." rel="developer designer">%s</a>', 'carrington-blog'), 'Carrington Theme by Crowd Favorite'); ?></p>
<?php
}
?>
			</div><!--.wrapper-->
		</div><!--#footer -->
	</div><!--#page-->
	<?php wp_footer() ?>
</body>
</html>