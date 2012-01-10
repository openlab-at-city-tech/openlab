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
<div id="post-excerpt-<?php the_ID() ?>" <?php post_class('excerpt'); ?>>
	<strong class="entry-title"><a href="<?php the_permalink() ?>" title="Permanent link to <?php the_title_attribute() ?>" rel="bookmark" rev="post-<?php the_ID(); ?>"><?php the_title(); ?></a></strong>
	
	<span class="date small"><abbr class="published" title="<?php the_time('Y-m-d\TH:i'); ?>"><?php the_time('F j, Y'); ?></abbr></span>
	
	<p class="categories filed alt-font"><?php _e('Posted in ', 'carrington-blog'); the_category(', ') ?>.</p>
	
	<span class="comments-link"><?php if (function_exists('akac_comments_link')) { akac_comments_link(); } else { comments_popup_link(__('No comments', 'carrington-blog'), __('1 comment', 'carrington-blog'), __('% comments', 'carrington-blog')); } ?></span>
</div><!-- .excerpt -->