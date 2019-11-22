<?php
/**
 * dkpdfg-index.php
 * This template is used to display the content in the PDF
 *
 * Do not edit this template directly,
 * copy this template and paste in your theme (or child theme) inside a directory named dkpdfg
 */
?>

<html>
<head>

	<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo( 'stylesheet_url' ); ?>" media="all" />

	<style type="text/css">

		body {
			background: #FFF;
			font-size: 100%;
		}

		h1 {
			font-size: 140%;
		}

		h2 {
			font-size: 120%;
		}

		h3 {
			font-size: 110%;
		}

		h4 {
			font-size: 100%;
		}

		h5 {
			font-size: 90%;
		}

		h6 {
			font-size: 80%;
		}

		<?php
	if ( function_exists( 'dkpdf_get_post_types' ) ) {
	  $css = get_option( 'dkpdf_pdf_custom_css', '' );
	  echo $css;
	}
  ?>

	</style>

</head>

<body>

<?php

// get selected posts
$dkpdfg_selected_posts = get_option( 'dkpdfg_selected_posts', array() );

if ( $dkpdfg_selected_posts ) {

	// get posts types
	$post_types = dkpdf_get_post_types();

	// count posts
	$count          = 0;
	$count_selected = count( $dkpdfg_selected_posts ) - 2;

	foreach ( $post_types as $post_type ) {

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'post__in'       => $dkpdfg_selected_posts,
			'orderby'        => 'post__in',
		);

		$the_query = new WP_Query( apply_filters( 'dkpdfg_index_args', $args ) );

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post(); ?>

				<h1><?php the_title(); ?></h1>

				<?php //the_content();?>
				<?php
				// removes more tag and shows all post content in the PDF
				global $post;
				$unfiltered_content = str_replace( '<!--more-->', '', $post->post_content );
				$filtered_content   = apply_filters( 'the_content', $unfiltered_content );
				echo $filtered_content;
				?>

				<?php
				// adds a pagebreak for getting post titles at the begining of each page
				// removes the last blank page in the PDF.
				if ( $count <= $count_selected ) { ?>

					<pagebreak />

				<?php }

				$count ++;

				?>

			<?php }

		}

		wp_reset_postdata();

	}

}

// get categories - since 1.3
$dkpdfg_selected_categories = get_option( 'dkpdfg_selected_categories', array() );

if ( $dkpdfg_selected_categories ) {

	if ( $dkpdfg_selected_posts ) { ?>
		<pagebreak />
	<?php }

	$date_from = get_option( 'dkpdfg_date_from', date( 'Y-m-d', strtotime( "-1 month" ) ) );
	$date_to   = get_option( 'dkpdfg_date_to', date( 'Y-m-d', current_time( 'timestamp', 1 ) ) );

	$args       = array(
		'public' => true,
	);
	$post_types = get_post_types( $args );

	$tax_query = array( 'relation' => 'OR' );

	foreach ( $post_types as $post_type ) {
		$taxonomy_names = get_object_taxonomies( $post_type );
		foreach ( $taxonomy_names as $taxonomy_name ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy_name,
				'field'    => 'term_id',
				'terms'    => $dkpdfg_selected_categories,
			);
		}
	}
	$tax_query[] = array(
		'taxonomy' => 'category',
		'field'    => 'term_id',
		'terms'    => $dkpdfg_selected_categories,
	);

	$args = array(
		'post_type'      => $post_types,
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'tax_query'      => $tax_query,
		'date_query' => array(
			array(
				'after'     => $date_from,
				'before'    => $date_to,
				'inclusive' => true,
			),
		),
	);

	$the_query = new WP_Query( $args );

	// count posts
	$count2          = 0;
	$count_selected2 = $the_query->post_count - 2;

	if ( $the_query->have_posts() ) {

		while ( $the_query->have_posts() ) {
			$the_query->the_post(); ?>

			<h1><?php the_title(); ?></h1>

			<?php //the_content();?>
			<?php
			// removes more tag and shows all post content in the PDF
			global $post;
			$unfiltered_content = str_replace( '<!--more-->', '', $post->post_content );
			$filtered_content   = apply_filters( 'the_content', $unfiltered_content );
			echo $filtered_content;
			?>

			<?php
			// adds a pagebreak for getting post titles at the begining of each page
			// removes the last blank page in the PDF.
			if ( $count2 <= $count_selected2 ) { ?>

				<pagebreak />

			<?php }

			$count2 ++;

			?>

		<?php }

	}

	wp_reset_postdata();

}

?>

</body>

</html>
