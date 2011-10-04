<?php
/**
 * Functions used within Genesis admin.
 *
 * @package Genesis
 * @todo document functions within functions/admin.php
 */

//	create a page checklist
function genesis_page_checklist($name = '', $selected = array()) {
	$pages = get_pages();
	$name = esc_attr( $name );

	//	home link
	if ( in_array('home', (array)$selected) ) $checked = 'checked'; else $checked = '';

	$checkboxes = '<li><label class="selectit"><input type="checkbox" name="'.$name.'[]" value="home" '.$checked.' /> Home</label></li>'."\n";

	//	other pages
	foreach ( (array)$pages as $page ) {
		if(in_array($page->ID, (array)$selected)) $checked = 'checked'; else $checked = '';

		$ancestors = get_post_ancestors($page->ID);
		$indent = count((array)$ancestors); $indent = 'style="padding-left: '.($indent * 15).'px;"';

		$checkboxes .= '<li '.$indent.'><label><input type="checkbox" name="'.$name.'[]" value="'.$page->ID.'" '.$checked.' /> ';
		$checkboxes .= esc_html( get_the_title( $page->ID ) ).'</label></li>'."\n";
	}

	echo $checkboxes;
}

//	create a category checklist
function genesis_category_checklist($name = '', $selected = array()) {
	$name = esc_attr( $name );

	//	home link
	if ( in_array('home', (array)$selected) ) $checked = 'checked'; else $checked = '';

	$checkboxes = '<li><label class="selectit"><input type="checkbox" name="'.$name.'[]" value="home" '.$checked.' /> Home</label></li>'."\n";

	//	categories
	ob_start();
	wp_category_checklist(0,0, $selected, false, '', $checked_on_top = false);
	$checkboxes .= str_replace('name="post_category[]"', 'name="'.$name.'[]"', ob_get_clean());

	echo $checkboxes;
}