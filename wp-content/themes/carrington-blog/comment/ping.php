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
<div id="div-ping-<?php comment_ID(); ?>" <?php comment_class('ping hentry'); ?>>
<?php

printf(__('<cite class="vcard author fn entry-title">%s</cite> <span class="linked-to-this-post alt-font">linked to this post</span>', 'carrington-blog'), get_comment_author_link());

?> 
	<span class="date alt-font">
		<span class="on"><?php _e('on'); ?></span> <abbr class="published" title="<?php comment_date('Y-m-d\TH:i:sO'); ?>"><?php comment_date(); ?></abbr>
	</span>
	<blockquote class="entry-summary" cite="<?php comment_author_url(); ?>"><?php comment_text() ?></blockquote> 
	<?php edit_comment_link(__('Edit', 'carrington-blog'), '<div class="edit edit-comment">', '</div>'); ?>
</div><!--.ping-->