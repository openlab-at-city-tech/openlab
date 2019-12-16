<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * select posts create PDF
 */
function dkpdfg_select_posts_button() {

	// check if dk-pdf plugin is active
	if ( is_plugin_active( 'dk-pdf/dk-pdf.php' ) ) {
		// check post action
		if ( ! empty( $_POST['dkpdfg_action_create'] ) && $_POST['dkpdfg_action_create'] == 'dkpdfg_action_create' ) {
			// check nonce
			if ( ! wp_verify_nonce( $_POST['dkpdfg_create_pdf_nonce_field'], 'dkpdfg_create_pdf_action' ) ) {
				print 'Cheatin&#8217; huh?';
				exit;
			} else {
				dkpdfg_output_pdf();
			}
		}
	}
}

add_action( 'admin_init', 'dkpdfg_select_posts_button' );

/**
 * select categories create PDF
 */
function dkpdfg_select_categories_button() {

	// check if dk-pdf plugin is active
	if ( is_plugin_active( 'dk-pdf/dk-pdf.php' ) ) {
		// check post action
		if ( ! empty( $_POST['dkpdfg_action_create_categories'] ) && $_POST['dkpdfg_action_create_categories'] == 'dkpdfg_action_create_categories' ) {
			// check nonce
			if ( ! wp_verify_nonce( $_POST['dkpdfg_create_categories_pdf_nonce_field'],
				'dkpdfg_create_categories_pdf_action' ) ) {
				print 'Cheatin&#8217; huh?';
				exit;
			} else {
				dkpdfg_output_pdf();
			}
		}
	}
}

add_action( 'admin_init', 'dkpdfg_select_categories_button' );

/**
 * Creates the PDF
 */
function dkpdfg_output_pdf() {

	set_query_var( 'pdf', 'pdf' );

	// include mPDF library from DK PDF
	if ( file_exists( ABSPATH . '/wp-content/plugins/dk-pdf/includes/mpdf60/mpdf.php' ) ) {

		include( ABSPATH . '/wp-content/plugins/dk-pdf/includes/mpdf60/mpdf.php' );
	} else {

		require_once ABSPATH . '/wp-content/plugins/dk-pdf/vendor/autoload.php';
		define( '_MPDF_TTFONTDATAPATH', sys_get_temp_dir() . "/" );
	}

	// page orientation
	$dkpdf_page_orientation = get_option( 'dkpdf_page_orientation', '' );

	if ( $dkpdf_page_orientation == 'horizontal' ) {
		$format = 'A4-L';
	} else {
		$format = 'A4';
	}

	// font size
	$dkpdf_font_size   = get_option( 'dkpdf_font_size', '12' );
	$dkpdf_font_family = '';

	// margins
	$dkpdf_margin_left   = get_option( 'dkpdf_margin_left', '15' );
	$dkpdf_margin_right  = get_option( 'dkpdf_margin_right', '15' );
	$dkpdf_margin_top    = get_option( 'dkpdf_margin_top', '50' );
	$dkpdf_margin_bottom = get_option( 'dkpdf_margin_bottom', '30' );
	$dkpdf_margin_header = get_option( 'dkpdf_margin_header', '15' );

	// Instance mPDF 6.0 and below
	if ( file_exists( ABSPATH . '/wp-content/plugins/dk-pdf/includes/mpdf60/mpdf.php' ) ) {

		$mpdf = new mPDF( 'utf-8', $format, $dkpdf_font_size, $dkpdf_font_family,
			$dkpdf_margin_left, $dkpdf_margin_right, $dkpdf_margin_top, $dkpdf_margin_bottom, $dkpdf_margin_header
		);
	} else {

		// fonts
		$mpdf_default_config = (new Mpdf\Config\ConfigVariables())->getDefaults();
		$dkpdf_mpdf_font_dir = apply_filters('dkpdf_mpdf_font_dir',$mpdf_default_config['fontDir']);

		$mpdf_default_font_config = (new Mpdf\Config\FontVariables())->getDefaults();
		$dkpdf_mpdf_font_data = apply_filters('dkpdf_mpdf_font_data',$mpdf_default_font_config['fontdata']);

		// temp directory
		$dkpdf_mpdf_temp_dir = apply_filters('dkpdf_mpdf_temp_dir',realpath( __DIR__ . '/..' ) . '/tmp');

		$mpdf = new \Mpdf\Mpdf( [
			'tempDir'           => $dkpdf_mpdf_temp_dir,
			'default_font_size' => $dkpdf_font_size,
			'format'            => $format,
			'margin_left'       => $dkpdf_margin_left,
			'margin_right'      => $dkpdf_margin_right,
			'margin_top'        => $dkpdf_margin_top,
			'margin_bottom'     => $dkpdf_margin_bottom,
			'margin_header'     => $dkpdf_margin_header,
			'fontDir'           => $dkpdf_mpdf_font_dir,
			'fontdata'          => $dkpdf_mpdf_font_data,
		] );
	}

	// encrypts and sets the PDF document permissions
	// https://mpdf.github.io/reference/mpdf-functions/setprotection.html
	$enable_protection = get_option( 'dkpdf_enable_protection' );
	if( $enable_protection == 'on' ) {
		$grant_permissions = get_option( 'dkpdf_grant_permissions' );
		$mpdf->SetProtection( $grant_permissions );
	}

	// keep columns
	$keep_columns = get_option( 'dkpdf_keep_columns' );
	if( $keep_columns == 'on' ) {
		$mpdf->keepColumns = true;
	}

	$dkpdf_show_cover = get_option( 'dkpdfg_show_cover', 'on' );
	$dkpdf_show_toc = get_option( 'dkpdfg_show_toc', 'on' );

	// write cover
	if ( $dkpdf_show_cover == 'on' ) {
		$pdf_cover = dkpdfg_get_template( 'dkpdfg-cover' );
		$mpdf->WriteHTML( $pdf_cover );
	}

	// write TOC
	if ( $dkpdf_show_toc == 'on' ) {

		$toc_title = get_option( 'dkpdfg_toc_title', 'Table of contents' );

		$mpdf->WriteHTML( '<tocpagebreak toc-preHTML="&lt;h2&gt;' . $toc_title . '&lt;/h2&gt;"  paging="on" links="on" resetpagenum="1" toc-odd-footer-value="-1" toc-odd-header-value="-1" />' );
		$mpdf->h2toc       = array( 'H1' => 0 );
		$mpdf->h2bookmarks = array( 'H1' => 0 );

	} else {
		$mpdf->AddPage();
    }

	// header
	$pdf_header_html = dkpdf_get_template( 'dkpdf-header' );
	$mpdf->SetHTMLHeader( $pdf_header_html, 'O', true );

	// footer
	$pdf_footer_html = dkpdf_get_template( 'dkpdf-footer' );
	$mpdf->SetHTMLFooter( $pdf_footer_html );

	// write content
	$mpdf->WriteHTML( dkpdfg_get_template( 'dkpdfg-index' ) );

	// output PDF
	//$mpdf->Output();
	//$mpdf->Output( 'dk-pdf-generator.pdf', 'D' );

	$cover_title = get_option( 'dkpdfg_cover_title', '' );

	if ( $cover_title != '' ) {

		$mpdf->Output( $cover_title . '.pdf', 'D' );

	} else {

		$mpdf->Output( 'dk-pdf-generator.pdf', 'D' );

	}
}

/**
 * Search posts and send json data back to selectize
 */
function dkpdfg_search_posts() {

	if ( isset( $_POST['name'] ) ) {

		$args = array(
			'post_type'      => 'any',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			's'              => $_POST['name'],
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				global $post;

				$title = get_the_title();
				$id    = $post->ID;

				$rows[] = "{ \"name\": \"$title\", \"id\": \"$id\" }";

			}

		}

		wp_reset_postdata();

		// output to the browser
		header( 'Content-Type: text/javascript; charset=UTF-8' );
		echo "[\n" . join( ",\n", $rows ) . "\n]";
		die();

	}

}

add_action( 'wp_ajax_dkpdfg_search_posts', 'dkpdfg_search_posts' );
//add_action('wp_ajax_nopriv_dkpdfg_search_posts', 'dkpdfg_search_posts' );

/**
 * Update dkpdfg_selected_posts option
 * $ids array of post ids
 */
function dkpdfg_update_selected_posts() {

	if ( isset( $_POST['ids'] ) ) {

		$ids = $_POST['ids'];
		update_option( 'dkpdfg_selected_posts', array() );
		update_option( 'dkpdfg_selected_posts', $ids );

		$result['type'] = 'success';
		$result['msg']  = $ids;

	} else {

		update_option( 'dkpdfg_selected_posts', array() );

		$result['type'] = 'no ids';
		$result['msg']  = 'dkpdfg_selected_posts empty';

	}

	$result = json_encode( $result );
	echo $result;
	wp_die();

}

add_action( 'wp_ajax_update_selected_posts', 'dkpdfg_update_selected_posts' );

/**
 * get dkpdfg_selected_posts option values
 */
function dkpdfg_get_selected_posts() {

	$post_types = dkpdf_get_post_types();

	$dkpdfg_selected_posts = get_option( 'dkpdfg_selected_posts', array() );
	$rows                  = array();

	if ( ! empty( $dkpdfg_selected_posts ) ) {

		foreach ( $post_types as $post_type ) {

			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'post__in'       => $dkpdfg_selected_posts,
				'orderby'        => 'post__in',
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					global $post;

					array_push( $rows, array( $post->ID, get_the_title() ) );

				}

			}

			wp_reset_postdata();

		}

		$result['type'] = 'success';
		$result['msg']  = $rows;

	} else {

		$result['type'] = 'no data';
		$result['msg']  = 'no data';

	}

	$result = json_encode( $result );
	echo $result;
	wp_die();

}

add_action( 'wp_ajax_get_selected_posts', 'dkpdfg_get_selected_posts' );

/**
 * Search categories and send json data back to selectize
 */
function dkpdfg_search_categories() {

	if ( isset( $_POST['name'] ) ) {

		$categories = get_categories();
		foreach ( $categories as $category ) {
			$title  = $category->cat_name;
			$id     = $category->term_id;
			$rows[] = "{ \"name\": \"$title\", \"id\": \"$id\" }";
		}

		$tags = get_tags();
		foreach ( $tags as $tag ) {
			$title = preg_replace("/[^ \w]+/", "", $tag->name);
			$id     = $tag->term_id;
			$rows[] = "{ \"name\": \"$title\", \"id\": \"$id\" }";
		}

		$args       = array(
			'public'   => true,
			'_builtin' => false,
		);
		$post_types = get_post_types( $args );

		foreach ( $post_types as $post_type ) {
			$taxonomy_names = get_object_taxonomies( $post_type );
			foreach ( $taxonomy_names as $taxonomy_name ) {
				$terms = get_terms( $taxonomy_name );
				foreach ( $terms as $term ) {
					$title  = $term->name;
					$id     = $term->term_id;
					$rows[] = "{ \"name\": \"$title\", \"id\": \"$id\" }";
				}
			}
		}

		// output to the browser
		header( 'Content-Type: text/javascript; charset=UTF-8' );
		echo "[\n" . join( ",\n", $rows ) . "\n]";
		die();

	}

}

add_action( 'wp_ajax_dkpdfg_search_categories', 'dkpdfg_search_categories' );

/**
 * Update dkpdfg_selected_categories option
 * $ids array of categories ids
 */
function dkpdfg_update_selected_categories() {

	if ( isset( $_POST['ids'] ) ) {

		$ids = $_POST['ids'];
		update_option( 'dkpdfg_selected_categories', array() );
		update_option( 'dkpdfg_selected_categories', $ids );
		$result['type'] = 'success';
		$result['msg']  = $ids;

	} else {

		update_option( 'dkpdfg_selected_categories', array() );
		$result['type'] = 'no ids';
		$result['msg']  = 'dkpdfg_selected_categories empty';

	}

	$result = json_encode( $result );
	echo $result;
	wp_die();

}

add_action( 'wp_ajax_update_selected_categories', 'dkpdfg_update_selected_categories' );

/**
 * get dkpdfg_selected_categories option values
 */
function dkpdfg_get_selected_categories() {

	$dkpdfg_selected_categories = get_option( 'dkpdfg_selected_categories', array() );
	$rows                       = array();

	if ( ! empty( $dkpdfg_selected_categories ) ) {

		foreach ( $dkpdfg_selected_categories as $key ) {
			$category = get_category( $key );
			array_push( $rows, array( $category->term_id, $category->name ) );
		}

		foreach ( $dkpdfg_selected_categories as $key ) {
			$term = get_term( $key );
			array_push( $rows, array( $term->term_id, $term->name ) );
		}

		$result['type'] = 'success';
		$result['msg']  = $rows;

	} else {

		$result['type'] = 'no data';
		$result['msg']  = 'no data';

	}

	$result = json_encode( $result );
	echo $result;
	wp_die();

}

add_action( 'wp_ajax_get_selected_categories', 'dkpdfg_get_selected_categories' );

/**
 * updates dkpdfg_date_from and dkpdfg_date_to options
 */
function dkpdfg_update_date_ranges() {

	if ( isset( $_POST['date_from'] ) && isset( $_POST['date_to'] ) ) {
		// TODO sanitize
		update_option( 'dkpdfg_date_from', $_POST['date_from'] );
		update_option( 'dkpdfg_date_to', $_POST['date_to'] );
		$result['type'] = 'success';
		$result['msg']  = $_POST['date_from'] . ' ' . $_POST['date_to'];
	} else {
		update_option( 'dkpdfg_selected_categories', array() );
		$result['type'] = 'no dates';
		$result['msg']  = 'no dates';
	}
	$result = json_encode( $result );
	echo $result;
	wp_die();

}

add_action( 'wp_ajax_update_date_ranges', 'dkpdfg_update_date_ranges' );

/**
 * returs a template
 *
 * @param string template name
 *
 * @return string
 */
function dkpdfg_get_template( $template_name ) {

	$template = new DKPDFG_Template_Loader;

	ob_start();
	$template->get_template_part( $template_name );

	return ob_get_clean();

}

/**
 * Hide PDF button in both select posts and select categories
 * DK PDF filters: dkpdf_hide_button_isset, dkpdf_hide_button_equal
 */
function dkpdfg_hide_button_isset() {

	return isset( $_POST['dkpdfg_action_create'] ) || isset( $_POST['dkpdfg_action_create_categories'] ) || isset( $_GET['pdfg'] );
}

add_filter( 'dkpdf_hide_button_isset', 'dkpdfg_hide_button_isset' );

function dkpdfg_hide_button_equal() {

	return $_POST['dkpdfg_action_create'] == 'dkpdfg_action_create' || $_POST['dkpdfg_action_create_categories'] == 'dkpdfg_action_create_categories' || $_GET['pdfg'] == 'frontend';
}

add_filter( 'dkpdf_hide_button_equal', 'dkpdfg_hide_button_equal' );

/**
 * adds DK PDF Generator support info
 */
function dkpdfg_after_support() { ?>
	<div class="wrap">
		<h2 style="margin-top:20px;float:left;width:100%;">DK PDF Generator Support</h2>

		<div class="dkpdf-item">
			<h3>Documentation</h3>
			<p>Everything you need to know for getting DK PDF Generator up and running.</p>
			<p><a href="http://wp.dinamiko.com/demos/dkpdf-generator/documentation/"
					target="_blank">Go to Documentation</a></p>
		</div>

		<div class="dkpdf-item">
			<h3>Support</h3>
			<p>Having trouble? don't worry, create a new commnet in CodeCanyon item section.</p>
			<p><a href="http://codecanyon.net/item/dk-pdf-generator/13530581/comments" target="_blank">Go to Support</a>
			</p>
		</div>
	</div>
<?php }

add_action( 'dkpdf_after_support', 'dkpdfg_after_support' );

/**
 * set query_vars
 */
function dkpdfg_set_query_vars( $query_vars ) {
	$query_vars[] = 'pdfg';
	return $query_vars;
}
add_filter( 'query_vars', 'dkpdfg_set_query_vars' );

/**
 * output the pdf in frontend
 */
function dkpdfg_frontend_pdf( $query ) {

	$pdfg = sanitize_text_field( get_query_var( 'pdfg' ) );

	if( !empty( $pdfg ) && $pdfg == 'frontend' ) {
		add_filter( 'the_content', 'dkpdfg_hide_pdf_button' );
		dkpdfg_output_pdf();
	}

}
add_action( 'wp', 'dkpdfg_frontend_pdf' );

/**
 * remove dkpdfg-button shortcode in PDF
 */
function dkpdfg_hide_pdf_button( $content ) {
	remove_shortcode('dkpdfg-button');
	$content = str_replace( "[dkpdfg-button]", "", $content );
	return $content;
}

/**
 * remove dkpdfg-button shortcode when using DK PDF button
 */
function dkpdfg_hide_pdf_button_dkpdf( $content ) {
	$pdf = get_query_var( 'pdf' );
	if( $pdf ) {
		remove_shortcode('dkpdfg-button');
		$content = str_replace( "[dkpdfg-button]", "", $content );

	}
	return $content;
}
add_filter( 'the_content', 'dkpdfg_hide_pdf_button_dkpdf' );
