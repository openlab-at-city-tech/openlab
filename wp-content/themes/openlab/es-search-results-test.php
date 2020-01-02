<?php

/**
 * Template Name: es-search-results-test
 */

$search_term = isset( $_GET['search'] ) ? wp_unslash( $_GET['search'] ) : '';

$results = '';
if ( $search_term ) {
	$results = groups_get_groups( [
		'search_terms' => $search_term,
	] );
}

?>

<?php get_header(); ?>

<form method="get" action="">
	<input type="text" name="search" value="<?php echo esc_attr( $search_term ); ?>" />
	<input type="submit" value="Search" />
</form>

<ul>
<?php foreach ( $results['groups'] as $group ) : ?>
	<li>
		<div><?php printf( '<a href="%s">%s</a>', bp_get_group_permalink( $group ), $group->name ); ?></div>
		<div>Description: <?php echo $group->description; ?></div>
		<div>Group type: <?php echo openlab_get_group_type( $group->id ); ?></div>
	</li>
<?php endforeach; ?>
</ul>

<?php get_footer(); ?>

