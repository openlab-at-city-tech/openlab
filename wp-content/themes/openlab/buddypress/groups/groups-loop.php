<?php
/**
 * Groups loop.
 */

$group_sort = openlab_get_current_filter( 'sort' );
$group_args = array(
	'search_terms' => urldecode( openlab_get_current_filter( 'search' ) ),
	'per_page'     => 12,
	'type'         => $group_sort,
);

$user_id = bp_displayed_user_id();

$filters = array();
if ( bp_is_user_groups() ) {
	if ( isset( $_GET['type'] ) && in_array( $_GET['type'], openlab_group_types(), true ) ) {
		$group_type = wp_unslash( $_GET['type'] );
	} else {
		$group_type = 'course';
	}

	$group_args['user_id'] = $user_id;
} elseif ( openlab_is_search_results_page() ) {
	$group_type = openlab_get_current_filter( 'group-types' );
	if ( ! $group_type ) {
		$group_type = openlab_group_types();
	}
} else {
	$group_type = openlab_page_slug_to_grouptype();

	if ( openlab_is_my_groups_directory() ) {
		$group_args['user_id'] = bp_loggedin_user_id();
	}
}

$meta_query = array(
	array(
		'key'   => 'wds_group_type',
		'value' => $group_type,
	),
);

$school = openlab_get_current_filter( 'school' );
if ( $school && 'all' !== strtolower( $school )) {
	$meta_query[] = array(
		'key'   => 'openlab_school',
		'value' => $school,
	);
}

$office = openlab_get_current_filter( 'office' );
if ( $office && 'all' !== strtolower( $office )) {
	$meta_query[] = array(
		'key'   => 'openlab_office',
		'value' => $office,
	);
}

$department = openlab_get_current_filter( 'department' );
if ( $department && 'all' !== strtolower( $department ) ) {
	$meta_query[] = array(
		'key'   => 'openlab_department',
		'value' => $department,
	);
}

$semester = openlab_get_current_filter( 'term' );
if ( $semester && 'term_all' != strtolower( $semester ) ) {
	$semester_parts  = explode('-', $semester);
	$semester_season = ucwords( $semester_parts[0] );
	$semester_year   = ucwords( $semester_parts[1] );

	$meta_query[] = array(
		'key'   => 'wds_semester',
		'value' => $semester_season,
	);
	$meta_query[] = array(
		'key'   => 'wds_year',
		'value' => $semester_year,
	);
}

$member_type = openlab_get_current_filter( 'member_type' );
if ( $member_type && 'user_type_all' !== $member_type ) {
	$meta_query[] = array(
		'key'   => 'portfolio_user_type',
		'value' => ucwords( $member_type ),
	);
}

$is_cloneable = openlab_get_current_filter( 'is_cloneable' );
if ( $is_cloneable ) {
	$meta_query['cloneable'] = array(
		'key'     => 'enable_sharing',
		'compare' => 'EXISTS',
	);
}

$is_open = openlab_get_current_filter( 'is_open' );
if ( $is_open ) {
	$meta_query['blog_public'] = array(
		'key'      => 'blog_public',
		'value'    => [ '1', '0' ],
		'operator' => 'IN',
	);

	$group_args['status'] = 'public';
}

$group_args['meta_query'] = $meta_query;

$categories   = openlab_get_current_filter( 'cat' );
if ( ! empty( $categories ) ) {
	if ( 'cat_all' === strtolower( $categories ) ) {

		$terms    = get_terms('bp_group_categories');
		$term_ids = wp_list_pluck($terms, 'term_id');
	} else {
		$term_obj = get_term_by('slug', $categories, 'bp_group_categories');
		$term_ids = $term_obj->term_id;
	}

	$group_args['tax_query'] = array(
		array(
			'taxonomy' => 'bp_group_categories',
			'terms'    => $term_ids,
			'field'    => 'term_id',
		)
	);
}

$descendant_of = openlab_get_current_filter( 'descendant-of' );
if ( $descendant_of ) {
	$descendant_of_group = groups_get_group( $descendant_of );

	$descendant_of_admin_ids   = openlab_get_all_group_contact_ids( $descendant_of );
	$descendant_of_admin_links = array_map( 'bp_core_get_userlink', $descendant_of_admin_ids );

	$descendant_of_string = sprintf(
		'Displaying clones of <a href="%s">%s</a> by %s.',
		esc_attr( bp_get_group_permalink( $descendant_of_group ) ),
		esc_html( $descendant_of_group->name ),
		implode( ', ', $descendant_of_admin_links )
	);
}

$ancestor_of = openlab_get_current_filter( 'ancestor-of' );
if ( $ancestor_of ) {
	$ancestor_of_group = groups_get_group( $ancestor_of );

	$ancestor_of_admin_ids   = openlab_get_all_group_contact_ids( $ancestor_of );
	$ancestor_of_admin_links = array_map( 'bp_core_get_userlink', $ancestor_of_admin_ids );

	$ancestor_of_string = sprintf(
		'Displaying ancestors of <a href="%s">%s</a> by %s.',
		esc_attr( bp_get_group_permalink( $ancestor_of_group ) ),
		esc_html( $ancestor_of_group->name ),
		implode( ', ', $ancestor_of_admin_links )
	);
}

// Get private groups of the user
$private_groups = openlab_get_user_private_membership( $user_id );

// Exclude private groups if not current user's profile or don't have moderate access.
if( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) ) {
	$group_args['exclude'] = $private_groups;
}
?>

<?php if ( bp_has_groups( $group_args ) ) : ?>

<div class="row group-archive-header-row">
	<?php if ( openlab_is_my_profile() ) :
		echo openlab_submenu_markup( 'groups', $group_type, false );
	else : ?>
		<div class="col-lg-19 col-md-18 col-sm-16">
			<?php if ( openlab_is_search_results_page() ) : ?>
				Narrow down your results using the search filters.
			<?php elseif ( $descendant_of ) : ?>
				<?php echo $descendant_of_string; ?>
			<?php elseif ( $ancestor_of ) : ?>
				<?php echo $ancestor_of_string; ?>
			<?php else : ?>
				Use the search and filters to find a <?php echo esc_html( ucwords( $group_type ) ); ?>.
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="group-count col-lg-5 col-md-6 col-sm-8"><?php cuny_groups_pagination_count(); ?></div>
</div>

	<div id="group-list" class="item-list group-list row">
		<?php if ( openlab_is_my_groups_directory() ) : ?>
			<div class="my-group-archive-sort form-inline">
				<label for="openlab-sort-my-groups" class="sr-only">Select sort order</label>
				<select id="openlab-sort-my-groups" class="form-control">
					<option value="alphabetical" <?php selected( $group_sort, 'alphabetical' ); ?>>Alphabetical</option>
					<option value="newest" <?php selected( $group_sort, 'newest' ); ?>>Newest</option>
					<option value="active" <?php selected( $group_sort, 'active' ); ?>>Last Active</option>
				</select>
			</div>
		<?php endif; ?>

		<?php
		while ( bp_groups() ) : bp_the_group();
			$group_id        = bp_get_group_id();
			$group_site_url  = openlab_get_group_site_url( $group_id );
			$this_group_type = openlab_get_group_type( $group_id );

			$classes = 'group-item col-xs-12';
			if ( openlab_group_has_badges( $group_id ) || openlab_group_can_be_cloned( $group_id ) || openlab_group_is_open( $group_id ) ) {
				$classes .= ' group-has-badges';
			}

			$group_avatar = bp_core_fetch_avatar(
				[
					'item_id' => $group_id,
					'object'  => 'group',
					'type'    => 'full',
					'html'    => false,
				]
			);

			?>
			<div class="<?php echo esc_attr( $classes ); ?>">
				<div class="group-item-wrapper">
					<div class="row">
						<div class="item-avatar alignleft col-xs-6">
							<?php if ( openlab_is_search_results_page() ) : ?>
								<div class="group-type-flag"><?php echo openlab_get_group_type_label( [ 'case' => 'upper' ] ); ?></div>
							<?php endif; ?>

							<a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo $group_avatar; ?>" alt="<?php echo esc_attr( bp_get_group_name() ); ?>"/></a>

							<?php if ( $group_site_url && wds_site_can_be_viewed() ) : ?>
								<a class="group-site-link" href="<?php echo esc_attr( $group_site_url ); ?>"><?php esc_html_e( 'Visit Site', 'openlab-theme' ); ?><span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a>
							<?php endif; ?>
						</div>

						<div class="item col-xs-18">
							<p class="item-title h2">
								<a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_group_permalink() ?>" data-basevalue="55" data-minvalue="20" data-basewidth="290"><?php bp_group_name() ?></a>
								<span class="original-copy hidden"><?php bp_group_name() ?></span>
								<?php do_action( 'openlab_group_directory_after_group_title' ); ?>
							</p>

							<?php if ( 'course' === $this_group_type ) : ?>

								<div class="info-line uppercase">
									<?php echo openlab_output_group_contact_line( $group_id ); ?>
								</div>

								<div class="info-line uppercase">
									<?php echo openlab_output_course_info_line($group_id); ?>
								</div>

							<?php elseif ( 'portfolio' === $this_group_type ) : ?>

								<div class="info-line uppercase"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></div>

							<?php else : ?>

								<div class="info-line uppercase">
									<?php echo openlab_output_group_contact_line( $group_id ); ?>
								</div>

							<?php endif; ?>

							<div class="description-line">
								<p class="truncate-on-the-fly" data-basevalue="105" data-basewidth="250"><?php echo bp_get_group_description_excerpt() ?></p>
							</div>

							<?php if( current_user_can( 'bp_moderate' ) && in_array( $group_id, $private_groups ) ) { ?>
							<p class="private-membership-indicator"><span class="fa fa-eye-slash"></span> Membership hidden</p>
							<?php } ?>
						</div>
					</div><!--item-->

					<?php do_action( 'openlab_theme_after_group_group_directory' ); ?>

				</div>
			</div>
			<?php endwhile; ?>
        </div>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php echo openlab_groups_pagination_links() ?>
		</div>
<?php else: ?>

	<div class="row group-archive-header-row">
		<?php if ( openlab_is_my_profile() ) : ?>
			<?php echo openlab_submenu_markup( 'groups', $group_type, false ); ?>
		<?php else : ?>
			<div class="current-group-filters current-portfolio-filters col-sm-19">&nbsp;</div>
		<?php endif; ?>
	</div>

	<div id="group-list" class="item-list row">
		<div class="widget-error query-no-results col-sm-24">
			<p class="bold">There are no items to display.</p>
		</div>
	</div>
<?php endif; ?>
