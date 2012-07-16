<?php
/* This file has most of the code for handling the Main Options, plus some utility functions */

function weaver_allow_multisite() {
    // return true if it is allowed to use on MultiSite
    return (!is_multisite() || current_user_can('install-themes') || WEAVER_MULTISITE_ALLOPTIONS);
}

function weaver_setup_post_args($args) {
   /* setup WP_Query arg list */

    $cats = weaver_get_page_categories();
    if (!empty($cats)) $args['cat'] = $cats;

    $tags = weaver_get_page_tags();
    if (!empty($tags)) $args['tag'] = $tags;

    $onepost = weaver_get_page_onepost();
    if (!empty($onepost)) $args['name'] = $onepost;

    $orderby = weaver_get_page_orderby();
    if (!empty($orderby)) $args['orderby'] = $orderby;

    $order = weaver_get_page_order();
    if (!empty($order)) $args['order'] = $order;

    $author_name = weaver_get_page_author();
    if (!empty($author_name)) $args['author_name'] = $author_name;

    $posts_per_page = weaver_get_page_posts_per();
    if (!empty($posts_per_page)) $args['posts_per_page'] = $posts_per_page;

    // if (weaver_is_checked_page_opt('ttw_hide_sticky')) $args['caller_get_posts'] = false;	 /* doesn't seem to work... */

    return $args;
}

function weaver_page_content() {
    // display page content with featured image thumbnail
    /* Check if it has a thumbnail,  and if it's a small one */
    global $post;
    global $weaverii_header;
    if (has_post_thumbnail() && !weaver_getopt_checked('ttw_hide_page_featured')
	&& (  $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ) )  /* $src, $width, $height */
	&& $image[1] < $weaverii_header['width']) {
	the_post_thumbnail( 'thumbnail' );
    }
    weaver_the_content();
}

function weaver_get_page() {
    /* get the current posts display number
      needed for when Page with Posts is front page
    */
    $paged = get_query_var('paged');
	if (!isset($paged) || empty($paged)) {
		$paged = 1;
	}
    $page = get_query_var( 'page' );
    if ( $page > 1)
	$paged = $page;
    return $paged;
}

function weaver_get_page_categories() {
    $cats = weaver_get_per_page_value('ttw_category');
    if (empty($cats)) return '';
    // now convert slugs to ids
    return weaver_cat_slugs_to_ids($cats);
}

function weaver_cat_slugs_to_ids($cats) {
    if (empty($cats)) return '';
    // now convert slugs to numbers
    $clist = explode(',',$cats);	// break into a list
    $cat_list = '';
    foreach ($clist as $slug) {
	$neg = 1;	// not negative
	if ($slug[0] == '-') {
	    $slug = substr($slug,1);	// zap the -
	    $neg = -1;
	}
	if (strlen($slug) > 0 && is_numeric($slug)) { // allow both slug and id
	    $cat_id = $neg * (int)$slug;
	    if ($cat_list == '') $cat_list = strval($cat_id);
	    else $cat_list .= ','.strval($cat_id);
	} else {
	    $cur_cat = get_category_by_slug($slug);
	    if ($cur_cat) {
		$cat_id = $neg * (int)$cur_cat->cat_ID;
		if ($cat_list == '') $cat_list = strval($cat_id);
		else $cat_list .= ','.strval($cat_id);
	    }
	}
    }
    return $cat_list;
}

function weaver_get_page_tags() {
    $tags = weaver_get_per_page_value('ttw_tag');
    if (empty($tags)) return '';
    return $tags;
}
function weaver_get_page_onepost() {
    $the_post = weaver_get_per_page_value('ttw_onepost');
    if (empty($the_post)) return '';
    return $the_post;
}
function weaver_get_page_orderby() {
    $orderby = weaver_get_per_page_value('ttw_orderby');
    if (empty($orderby)) return '';

    if ($orderby == 'author' || $orderby == 'date' || $orderby == 'title' || $orderby == 'rand')
        return $orderby;
    weaver_page_posts_error('orderby must be author, date, title, or rand. You used: '. $orderby);
    return '';
}
function weaver_get_page_order() {
    $order = weaver_get_per_page_value('ttw_order');
    if (empty($order)) return '';
    if ($order == 'ASC' || $order == 'DESC')
        return $order;
    weaver_page_posts_error('order value must be ASC or DESC. You used: '. $order);
    return '';
}
function weaver_get_page_posts_per() {
    $ppp = weaver_get_per_page_value('ttw_posts_per_page');
    if (empty($ppp)) return '';
    // now convert slugs to numbers
    return $ppp;
}
function weaver_get_page_author() {
    $author = weaver_get_per_page_value('ttw_author');
    if (empty($author)) return '';
    return $author;
}

function weaver_get_per_page_value($name) {
    global $weaver_cur_page_ID;
    return get_post_meta($weaver_cur_page_ID,$name,true);
}

function weaver_is_checked_page_opt($meta_name) {
    // the standard is to check options to hide things
    global $weaver_cur_page_ID;

    $val = get_post_meta($weaver_cur_page_ID,$meta_name,true);  // retrieve meta value
    if (!empty($val)) return true;		// value exists - 'on'
    return false;
}

function weaver_html_br() {
    echo (' <br /> ');
}

function weaver_help_link($link, $info) {
    	$t_dir = get_template_directory_uri();
	$pp_help =  '<a href="' . $t_dir . '/' . $link . '" target="_blank" title="' . $info . '">'
		. '<img class="entry-cat-img" src="' . $t_dir . '/images/icons/help-1.png" style="position:relative; top:4px; padding-left:4px;" /></a>';
	echo($pp_help);
}

function weaver_get_per_post_value($meta_name) {
    global $weaver_cur_post_id;

    return get_post_meta($weaver_cur_post_id,$meta_name,true);  // retrieve meta value
}

function weaver_is_checked_post_opt($meta_name) {
    // the standard is to check options to hide things
    global $weaver_cur_post_id;

    $val = get_post_meta($weaver_cur_post_id,$meta_name,true);  // retrieve meta value
    if (!empty($val)) return true;		// value exists - 'on'
    return false;
}


function weaver_page_posts_error($info='') {
    echo('<h2 style="color:red;">WARNING: error defining Custom Field on Page with Posts.</h2>');
    if (strlen($info) > 0) echo('More info: '.$info.'<br />');
}
function weaver_add_q($q, $item, $tag='') {
    if ($item == '') return $q;

    if (!empty($q))
        return $q . '&' . $tag . $item;
    else
	return $tag . $item;
}

function weaver_multi_col($content){
	// layout content into two colums, multiple rows using <!--more--> to split for 2 col template
	// derived from: http://www.robsearles.com/2009/07/05/wordpress-multiple-content-columns/

	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);

	// the first "more" is converted to a span with ID
	$columns = preg_split('/(<span id="more-\d+"><\/span>)|(<!--more-->)<\/p>/', $content);
	$col_count = count($columns);


	if($col_count > 1) {
	    for($i=0; $i < $col_count; $i++) {
		// check to see if there is a final </p>, if not add it
		if(!preg_match('/<\/p>\s?$/', $columns[$i]) )  {
		    $columns[$i] .= '</p>';
		}
		// check to see if there is an appending </p>, if there is, remove
		$columns[$i] = preg_replace('/^\s?<\/p>/', '', $columns[$i]);
		// now add the div wrapper
		if ((int)($i % 2) == 0) $coldiv = 'left'; else $coldiv = 'right';
		if ($coldiv == 'right' && ($i+1) < $col_count) {
		    $break_cols ='<hr /><div class="clear-cols"></div>';
		} else {
		    $break_cols = '';
		}
		$columns[$i] = '<div class="multi-content-col-'.$coldiv.'">'.$columns[$i] .'</div>' . $break_cols ;
	    }
	    $content = join($columns, "\n").'<div class="clear-cols"></div>';
	}
	else {
	    // this page does not have dynamic columns
	    $content = wpautop($content);
	}
	// remove any left over empty <p> tags
	$content = str_replace('<p></p>', '', $content);
	return $content;
}

function weaver_put_area($name) {
    // for the extra code areas between major divs

    $area_name = 'ttw_' . $name . '_insert';
    $hide_front = 'ttw_hide_front_' . $name;
    $hide_rest = 'ttw_hide_rest_' . $name;

    if (weaver_getopt($area_name)) {	/* area insert defined? */
	if (is_front_page()) {
	    if (!weaver_getopt($hide_front)) echo (do_shortcode(weaver_getopt($area_name)));
	} else if (!weaver_getopt($hide_rest)) {
	    echo (do_shortcode(weaver_getopt($area_name)));
	}
    }

    $per_page_code = weaver_get_per_page_value('page-' . $name . '-code');	/* or on a per page basis! */
    if (!empty($per_page_code)) {
	echo(do_shortcode($per_page_code));
    }
}

function weaver_put_page_title($tag, $style=null) {
    if (weaver_is_checked_page_opt('ttw-hide-page-title')) return;
    echo ("<$tag class=\"entry-title\" $style >");
    the_title();
    echo("</$tag>\n");
}

function weaver_put_perpage_widgetarea() {
    global $weaver_cur_page_ID;

    $extra = trim(get_post_meta($weaver_cur_page_ID,'ttw_show_extra_areas',true));
    $area = 'per-page-' . $extra;  // retrieve meta value
    if (strlen($extra) > 0) {		// want to display some areas
	if (!weaver_check_perpage_exists($area,'per-page-widget'))
	    return;
	if ( !is_active_sidebar($area)) return;
	ob_start(); /* let's use output buffering to allow use of Dynamic Widgets plugin and not have empty sidebar */
	$success = dynamic_sidebar($area);
	$content = ob_get_clean();
	if ($success) {
	?>
	    <div id="per-page-widget" class="widget-area <?php echo $area; ?>" role="complementary" ><ul class="xoxo">
	    <?php echo($content) ; ?>
	    </ul></div>
	<?php
	}
    }
}

function weaver_replace_primary() {
    global $weaver_cur_page_ID;

    $extra = trim(get_post_meta($weaver_cur_page_ID,'ttw_show_replace_primary',true));
    $area = 'per-page-' . $extra;  // retrieve meta value
    if (strlen($extra) > 0) {		// want to display some areas
	if (!weaver_check_perpage_exists($area,'primary'))
	    return true;	// must be true so we don't get double area ids
	if ( !is_active_sidebar($area)) return false;
	ob_start(); /* let's use output buffering to allow use of Dynamic Widgets plugin and not have empty sidebar */
	$success = dynamic_sidebar($area);
	$content = ob_get_clean();
	if ($success) {
	?>
	    <div id="primary" class="widget-area <?php echo $area; ?>" role="complementary" ><ul class="xoxo">
	    <?php echo($content) ; ?>
	    </ul></div>
	<?php
	    return true;
	}
	return false;
    }
    return false;
}

function weaver_replace_secondary() {
    global $weaver_cur_page_ID;

    $extra = trim(get_post_meta($weaver_cur_page_ID,'ttw_show_replace_secondary',true));
    $area = 'per-page-' . $extra;  // retrieve meta value
    if (strlen($extra) > 0) {		// want to display some areas
        if (!weaver_check_perpage_exists($area,'secondary'))
	    return true;	// must be true so we don't get double area ids
	if ( !is_active_sidebar($area)) return false;
	ob_start(); /* let's use output buffering to allow use of Dynamic Widgets plugin and not have empty sidebar */
	$success = dynamic_sidebar($area);
	$content = ob_get_clean();
	if ($success) {
	?>
	    <div id="secondary" class="widget-area <?php echo $area; ?>" role="complementary" ><ul class="xoxo">
	    <?php echo($content) ; ?>
	    </ul></div>
	<?php
	    return true;
	}
	return false;
    }
    return false;
}

function weaver_replace_alternative($id) {
    global $weaver_cur_page_ID;

    $extra = trim(get_post_meta($weaver_cur_page_ID,'ttw_show_replace_alternative',true));
    $area = 'per-page-' . $extra;  // retrieve meta value
    if (strlen($extra) > 0) {		// want to display some areas
        if (!weaver_check_perpage_exists($area,$id))
	    return true;	// must be true so we don't get double area ids
	if ( !is_active_sidebar($area)) return false;
	ob_start(); /* let's use output buffering to allow use of Dynamic Widgets plugin and not have empty sidebar */
	$success = dynamic_sidebar($area);
	$content = ob_get_clean();
	if ($success) {
	?>
	    <div id="<?php echo($id); ?>" class="widget-area <?php echo $area; ?>" role="complementary" ><ul class="xoxo">
	    <?php echo($content) ; ?>
	    </ul></div>
	<?php
	    return true;
	}
	return false;
    }
    return false;
}

function weaver_check_perpage_exists($area,$styleid) {
    $sidebars_widgets = wp_get_sidebars_widgets();
    if ( empty($sidebars_widgets[$area]) ) { ?>
	<div id="<?php echo $styleid; ?>" class="widget-area <?php echo $area; ?>" role="complementary" ><ul class="xoxo">
	    <?php echo(__("<strong>Note: Per Page widget area: $area not found.</strong> You've likely mistyped the name, haven't defined the area yet, or haven't added a widget.",WEAVER_TRANSADMIN)) ; ?>
	    </ul>
	</div>
	<?php
	return false;
    }
    return true;
}

function weaver_relative_url($subpath){
    // generate a relative URL from the site's root
    return parse_url(trailingslashit(get_template_directory_uri()) . $subpath,PHP_URL_PATH);
}

function weaver_use_inline_css($css_file) {
     return weaver_getopt('ttw_force_inline_css') || !weaver_f_exists($css_file) || !weaver_getopt('ttw_subtheme');
}

function weaver_hide_site_title() {
    if (weaver_getopt('ttw_hide_site_title') || weaver_is_checked_page_opt('ttw-hide-site-title')) {
	return 'style="display:none;"';
    }
    return '';
}


define('WEAVER_DEBUG',false);
function weaver_debug($msg) {
    if (WEAVER_DEBUG) {
        echo("\n*******************>$msg<*******************<br />\n");
    }
}
?>
