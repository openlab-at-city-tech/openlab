<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function RenderLinkLibraryFilterBox( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $include_tags, $exclude_tags, $show_tag_filters, $tag_label, $show_price_filters, $price_label, $show_alphabetic_filters, $alphabetic_label, $showapplybutton ) {

	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
	extract( $generaloptions );

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	$output = '<div class="linklibrary-filters">';
	$output .= '<fieldset>';
	$output .= '<legend>' . __( 'Filters', 'link-library' ) . '</legend>';

	if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
		$prev_link_price = sanitize_text_field( $_GET['link_price'] );
	} else {
		$prev_link_price = '';
	}

	if ( isset( $_GET['link_letter'] ) && !empty( $_GET['link_letter'] ) ) {
		$prev_link_letter = sanitize_text_field( $_GET['link_letter'] );
	} else {
		$prev_link_letter = '';
	}

	if ( isset( $_GET['searchll'] ) && !empty( $_GET['searchll'] ) ) {
		$searchstring = sanitize_text_field( $_GET['searchll'] );
	} else {
		$searchstring = '';
	}

	if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {

		$output .= '<div class="tag-filters">';
		$output .= '<div class="tag-filters-title">' . $tag_label . '</div>';
		$link_terms = get_terms( array( 'taxonomy' => $generaloptions['tagtaxonomy'], 'include' => $include_tags, 'exclude' => $exclude_tags ) );

		if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
			$prev_link_tags = sanitize_text_field( $_GET['link_tags'] );
		} else {
			$prev_link_tags = '';
		}

		$prev_link_tags_array = explode( '.', $prev_link_tags );

		$output .= '<div class="tag-filters-choices">';

		foreach ( $link_terms as $link_term ) {
			$output .= '<div class="filter-choice"><label><input type="checkbox" name="link_tag_list[]" class="link_tag_list" ' . checked( in_array( $link_term->slug, $prev_link_tags_array ), true, false ) . ' value="' . $link_term->slug . '"/> ' . $link_term->name . '</label></div>';
		}

		$output .= '</div>';

		$output .= '<input type="hidden" name="link_tags" class="link_tags" value="' . $prev_link_tags . '">';

		if ( !$showapplybutton ) {
			$output .= '<script type="text/javascript">';

			$javascriptoutput = '';
			if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
				$javascriptoutput .= "\tcurrent_link_letter = jQuery('.link_letter').val();\n";
				$javascriptoutput .= "\tif (typeof current_link_letter == 'undefined') current_link_letter = '';\n";
			}
	
			$javascriptoutput .= "function isInArray(days, day) {\n";
			$javascriptoutput .= "\treturn days.indexOf(day.toLowerCase()) > -1;\n";
			$javascriptoutput .= "}\n";
	
			$javascriptoutput .= "jQuery('.link_tag_list').click( function() {\n";
			$javascriptoutput .= "\tcurrent_link_tags = jQuery('.link_tags').val();\n";
			$javascriptoutput .= "\tif (typeof current_link_tags == 'undefined') current_link_tags = '';\n";
			$javascriptoutput .= "\tif ( current_link_tags ) { current_link_tags_array = current_link_tags.split('.'); } else { current_link_tags_array = new Array(); }\n";
			$javascriptoutput .= "\tif ( jQuery(this).is(':checked') && !isInArray( current_link_tags_array, jQuery(this).val() ) ) {\n";
			$javascriptoutput .= "\t\tcurrent_link_tags_array.push( jQuery(this).val() );\n";
			$javascriptoutput .= "\t} else if ( jQuery(this).prop('checked', false) && isInArray( current_link_tags_array, jQuery(this).val() ) ) {\n";
			$javascriptoutput .= "\t\tcurrent_link_tags_array.splice( current_link_tags_array.indexOf(jQuery(this).val()));\n";
			$javascriptoutput .= "\t}\n";
			$javascriptoutput .= "\tvar link_tags_string = current_link_tags_array.join('.');\n";
			$javascriptoutput .= "\twindow.location.href = '//' + location.host + location.pathname + '?' + 'link_tags=' + link_tags_string";
	
			if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
				$javascriptoutput .= " + '&'";
				$javascriptoutput .= " + 'link_letter=' + current_link_letter";
			}
	
			if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {
				$javascriptoutput .= " + '&'";
	
				if ( 'free' == $prev_link_price ) {
					$javascriptoutput .= " + 'link_price=free'";
				} else {
					$javascriptoutput .= " + 'link_price='";
				}
			}
	
			if ( !empty( $searchstring ) ) {
				$javascriptoutput .= " + '&searchll=" . esc_js( $searchstring ) . "'";
			}

			$javascriptoutput .= ";\n";
	
			$javascriptoutput .= "});\n";			

			$output .= $javascriptoutput;

			$output .= '</script>';
		}

		$output .= '</div>';
	}

	if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {
		$output .= '<div class="tag-filters">';
		$output .= '<div class="tag-filters-title">' . $price_label . '</div>';

		$output .= '<div class="price-filters-choices">';
		$output .= '<input type="checkbox" name="link_price" class="link_price" ' . checked( $prev_link_price, 'free', false ) . ' value="free"/> ' . __( 'Free', 'link-library' ) . '<br/>';
		$output .= '</div>';

		if ( !$showapplybutton ) {
			$output .= '<script type="text/javascript">';

			$javascriptoutput = '';

			$javascriptoutput .= "jQuery('.link_price').click( function() {\n";

			if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
				$javascriptoutput .= "\tcurrent_link_tags = jQuery('.link_tags').val();\n";
				$javascriptoutput .= "\tif (typeof current_link_tags == 'undefined') current_link_tags = '';\n";
			}

			if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
				$javascriptoutput .= "\tcurrent_link_letter = jQuery('.link_letter').val();\n";
				$javascriptoutput .= "\tif (typeof current_link_letter == 'undefined') current_link_letter = '';\n";
			}

			$javascriptoutput .= "\twindow.location.href = '//' + location.host + location.pathname + '?'";

			if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
				$javascriptoutput .= " + 'link_tags=' + current_link_tags";
			}

			if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
				$javascriptoutput .= " + '&'";
				$javascriptoutput .= " + 'link_letter=' + current_link_letter";
			}

			if ( 'free' == $prev_link_price ) {
				$javascriptoutput .= " + '&link_price='";
			} else {
				$javascriptoutput .= " + '&link_price=free'";
			}

			if ( !empty( $searchstring ) ) {
				$javascriptoutput .= " + '&searchll=" . esc_js( $searchstring ) . "'";
			}

			$javascriptoutput .= ";\n";

			$javascriptoutput .= "});\n";			

			$output .= $javascriptoutput;

			$output .= '</script>';
		}

		$output .= '</div>';
	}

	if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
		$output .= '<div class="tag-filters">';
		$output .= '<div class="tag-filters-title">' . $alphabetic_label . '</div>';

		$output .= '<div class="alphabetic-filters-choices">';
		$output .= '<select name="link_letter" class="link_letter">';
		$output .= '<option value="">' . __( 'All', 'link-library' ) . '<br/>';
		$output .= '<option disabled>_________<br/>';

		foreach( range( 'A', 'Z' ) as $v ){
			$output .= '<option value="' . $v . '" ';
			$output .= selected( $v, $prev_link_letter, false );
			$output .= '>' . $v . '</option>';
		}

		$output .= '<option disabled>_________<br/>';

		foreach( range( '0', '9' ) as $v ){
			$output .= '<option value="' . $v . '"';
			$output .= selected( $v, $prev_link_letter, false );
			$output .= '>' . $v . '</option>';
		}

		$output .= '</select>';

		$output .= '</div>';

		if ( !$showapplybutton ) {

			$output .= '<script type="text/javascript">';

			$javascriptoutput = '';

			$javascriptoutput .= "jQuery('.link_letter').change( function() {\n";

			if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
				$javascriptoutput .= "\tcurrent_link_tags = jQuery('.link_tags').val();\n";
				$javascriptoutput .= "\tif (typeof current_link_tags == 'undefined') current_link_tags = '';\n";
			}

			$javascriptoutput .= "\tcurrent_link_letter = jQuery('.link_letter').val();\n";
			$javascriptoutput .= "\tif (typeof current_link_letter == 'undefined') current_link_letter = '';\n";

			$javascriptoutput .= "\twindow.location.href = '//' + location.host + location.pathname + '?'";

			$javascriptoutput .= " + 'link_letter=' + current_link_letter";

			if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
				$javascriptoutput .= " + '&'";
				$javascriptoutput .= " + 'link_tags=' + current_link_tags";
			}

			if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {
				$javascriptoutput .= " + '&'";

				if ( 'free' == $prev_link_price ) {
					$javascriptoutput .= " + 'link_price='";
				} else {
					$javascriptoutput .= " + 'link_price=free'";
				}
			}

			if ( !empty( $searchstring ) ) {
				$javascriptoutput .= " + '&searchll=" . esc_js( $searchstring ) . "'";
			}

			$javascriptoutput .= ";\n";

			$javascriptoutput .= "});\n";

			$output .= $javascriptoutput;

			$output .= '</script>';

		}

		$output .= '</div>';
	}

	if ( $showapplybutton ) {
		$output .= '<div class="applyfiltersbutton"><input id="applyfilters" type="button" value="' . __( 'Apply Filters', 'link-library' ) . '"></div>';

		$output .= '<script type="text/javascript">';

		$javascriptoutput = '';

		$javascriptoutput .= "function isInArray(days, day) {\n";
		$javascriptoutput .= "\treturn days.indexOf(day.toLowerCase()) > -1;\n";
		$javascriptoutput .= "}\n";

		$javascriptoutput .= "jQuery('#applyfilters').click( function() {\n";

		if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
			$javascriptoutput .= "\tvar current_link_tags_array = new Array();\n";
			$javascriptoutput .= "\tjQuery('.link_tag_list:checked').each(function(){\n";
			$javascriptoutput .= "\t\tcurrent_link_tags_array.push( jQuery( this ).val());\n";
			$javascriptoutput .= "\t});\n";
			$javascriptoutput .= "\tvar link_tags_string = current_link_tags_array.join('.');\n";
		}

		if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
			$javascriptoutput .= "\tcurrent_link_letter = jQuery('.link_letter').val();\n";
			$javascriptoutput .= "\tif (typeof current_link_letter == 'undefined') current_link_letter = '';\n";
		}

		if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {
			$javascriptoutput .= "\tcurrent_price = '';\n";
			$javascriptoutput .= "\tif ( jQuery('.link_price').is(':checked') ) current_price = 'free';\n";
		}

		$javascriptoutput .= "\twindow.location.href = '//' + location.host + location.pathname + '?'";

		if ( ( is_bool( $show_tag_filters ) && $show_tag_filters ) || ( !is_bool( $show_tag_filters ) && $show_tag_filters != 'false' ) ) {
			$javascriptoutput .= " + 'link_tags=' + link_tags_string";
			$javascriptoutput .= " + '&'";
		}

		if ( ( is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters ) || ( !is_bool( $show_alphabetic_filters ) && $show_alphabetic_filters != 'false' ) ) {
			$javascriptoutput .= " + 'link_letter=' + current_link_letter";
			$javascriptoutput .= " + '&'";
		}

		if ( ( is_bool( $show_price_filters ) && $show_price_filters ) || ( !is_bool( $show_price_filters ) && $show_price_filters != 'false' ) ) {			
			$javascriptoutput .= " + 'link_price=' + current_price";
			$javascriptoutput .= " + '&'";
		}

		if ( !empty( $searchstring ) ) {
			$javascriptoutput .= " + 'searchll=" . esc_js( $searchstring ) . "'";
		}

		$javascriptoutput .= ";\n";

		$javascriptoutput .= "});\n";

		$output .= $javascriptoutput;

		$output .= '</script>';

	}

	$output .= '</fieldset>';

	$output .= '</div>';

	return $output;
}
