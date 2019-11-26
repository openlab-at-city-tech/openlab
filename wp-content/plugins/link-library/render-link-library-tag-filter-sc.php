<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function RenderLinkLibraryFilterBox( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $include_tags, $exclude_tags, $show_tag_filters, $tag_label, $show_price_filters, $price_label ) {

	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
	extract( $generaloptions );

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	$output = '<div class="linklibrary-filters">';
	$output .= '<fieldset>';
	$output .= '<legend>' . __( 'Filters', 'link-library' ) . '</legend>';
	
	if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
		$prev_link_price = $_GET['link_price'];
	} else {
		$prev_link_price = '';
	}
	
	if ( isset( $_GET['searchll'] ) && !empty( $_GET['searchll'] ) ) {
		$searchstring = $_GET['searchll'];
	} else {
		$searchstring = '';
	}

	if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {

		$output .= '<div class="tag-filters">';
		$output .= '<div class="tag-filters-title">' . $tag_label . '</div>';
		$link_terms = get_terms( array( 'taxonomy' => 'link_library_tags', 'include' => $include_tags, 'exclude' => $exclude_tags ) );

		if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
			$prev_link_tags = $_GET['link_tags'];
		} else {
			$prev_link_tags = '';
		}

		$prev_link_tags_array = explode( '.', $prev_link_tags );

		$output .= '<div class="tag-filters-choices">';

		foreach ( $link_terms as $link_term ) {
			$output .= '<input type="checkbox" name="link_tag_list[]" class="link_tag_list" ' . checked( in_array( $link_term->slug, $prev_link_tags_array ), true, false ) . ' value="' . $link_term->slug . '"/> ' . $link_term->name . '<br/>';
		}

		$output .= '</div>';

		$output .= '<input type="hidden" name="link_tags" class="link_tags" value="' . $prev_link_tags . '">';

		$output .= '<script type="text/javascript">';

		$output .= "function isInArray(days, day) {\n";
		$output .= "\treturn days.indexOf(day.toLowerCase()) > -1;\n";
		$output .= "}\n";

		$output .= "jQuery('.link_tag_list').click( function() {\n";
		$output .= "\tcurrent_link_tags = jQuery('.link_tags').val();\n";
		$output .= "\tif (typeof current_link_tags == 'undefined') current_link_tags = '';\n";
		$output .= "\tif ( current_link_tags ) { current_link_tags_array = current_link_tags.split('.'); } else { current_link_tags_array = new Array(); }\n";
		$output .= "\tif ( jQuery(this).is(':checked') && !isInArray( current_link_tags_array, jQuery(this).val() ) ) {\n";
		$output .= "\t\tcurrent_link_tags_array.push( jQuery(this).val() );\n";
		$output .= "\t} else if ( jQuery(this).prop('checked', false) && isInArray( current_link_tags_array, jQuery(this).val() ) ) {\n";
		$output .= "\t\tcurrent_link_tags_array.splice( current_link_tags_array.indexOf(jQuery(this).val()));\n";
		$output .= "\t}\n";
		$output .= "\tvar link_tags_string = current_link_tags_array.join('.');\n";
		$output .= "\twindow.location.href = '//' + location.host + location.pathname + '?' + 'link_tags=' + link_tags_string";
				
		if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {
			$output .= " + '&'";
			
			if ( 'free' == $prev_link_price ) {
				$output .= " + 'link_price=free'";
			} else {
				$output .= " + 'link_price='";
			}
		}
		
		if ( !empty( $searchstring ) ) {
			$output .= " + '&searchll=" . $searchstring . "'";
		}
		
		$output .= "});\n";

		$output .= '</script>';

		$output .= '</div>';
	}

	if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {
		$output .= '<div class="tag-filters">';
		$output .= '<div class="tag-filters-title">' . $price_label . '</div>';

		$output .= '<div class="price-filters-choices">';
		$output .= '<input type="checkbox" name="link_price" class="link_price" ' . checked( $prev_link_price, 'free', false ) . ' value="free"/> ' . __( 'Free', 'link-library' ) . '<br/>';
		$output .= '</div>';
		
		$output .= '<script type="text/javascript">';

		$output .= "jQuery('.link_price').click( function() {\n";
		
		if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
			$output .= "\tcurrent_link_tags = jQuery('.link_tags').val();\n";
			$output .= "\tif (typeof current_link_tags == 'undefined') current_link_tags = '';\n";
		}
		
		$output .= "\twindow.location.href = '//' + location.host + location.pathname + '?'";
		
		if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
			$output .= " + 'link_tags=' + current_link_tags";
			$output .= " + '&'";
		}
		
		if ( 'free' == $prev_link_price ) {
			$output .= " + 'link_price='";
		} else {
			$output .= " + 'link_price=free'";
		}
		
		if ( !empty( $searchstring ) ) {
			$output .= " + '&searchll='" . $searchstring . "'";
		}

		$output .= ";\n";
		
		$output .= "});\n";

		$output .= '</script>';

		$output .= '</div>';
	}

	//$output .= '<input type="submit" value="' . __( 'Apply filter', 'link-library' ) . '" />';

	$output .= '</fieldset>';

	$output .= '</div>';

	return $output;
}