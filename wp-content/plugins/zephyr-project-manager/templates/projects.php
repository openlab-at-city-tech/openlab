<?php
	/**
	* Project Page
	* Users can create, view, edit and manage projects from this page
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Core\Categories;
	use Inc\Base\BaseController;
	use Inc\ZephyrProjectManager;

	$settings = Utillities::general_settings();
	$manager = ZephyrProjectManager();
	$base_url = esc_url( admin_url( '/admin.php?page=zephyr_project_manager_projects' ) );
	$base_url = apply_filters( 'zpm_project_to_categories_url', $base_url );
	$categories = $manager::get_categories();

	$general_settings = Utillities::general_settings();
	$category_all = new stdClass();
	$category_all->id = '-1';
	$category_all->name = __( 'All', 'zephyr-project-manager' );
	$category_all->description = __( 'All projects', 'zephyr-project-manager' );
	$category_all->color = $settings['primary_color'];
	array_unshift( $categories, $category_all );
	$category_id = isset( $_GET['category_id'] ) ? $_GET['category_id'] : '';
	$show_projects = apply_filters( 'zpm_show_projects', true );
	$projects_per_page = $settings['projects_per_page'];
	$page = isset( $_GET['projects_page'] ) && !empty( $_GET['projects_page'] ) ? $_GET['projects_page'] : 1;
	$offset = ($page - 1) * $projects_per_page;
	if ($offset < 0) {
		$offset = 0;
	}
	$total_pages = Projects::get_total_pages();
	$last_view = get_user_meta( get_current_user_id(), 'project_view' );
	$list_view = $last_view == 'list' ? true : false;
?>

<div class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container" class="zpm_add_project">
		<div class="zpm_body" style="display: none;">
			<ul class="nav nav-tabs" >
				<li class="active"><a href="#tab-1"><?php _e( 'New Project', 'zephyr-project-manager' ); ?></a></li>
			</ul>
		</div>

		<?php if (isset($_GET['action']) && $_GET['action'] == 'edit_project') : ?>

			<?php include( ZPM_PLUGIN_PATH . '/templates/parts/project-single.php' ); ?>

		<?php else: ?>
			<?php if ( $show_projects ) : ?>
				<?php echo apply_filters( 'zpm_projects_header', '' ); ?>
				<?php if ( !empty( $category_id ) || $settings['enable_category_grouping'] == false ) : ?>

						<?php
							$projects = Projects::get_paginated_projects( $projects_per_page, $offset );
							$projects = apply_filters( 'zpm_project_grid_projects', $projects );
							$category = Categories::get_category( $category_id );
							if ( $category_id == '-1' || $category_id == 'all' ) {
								$category = $category_all;
							}
							$project_count = sizeof( $projects );
						?>

						<?php if ($settings['enable_category_grouping']) : ?>
							<h4 id="zpm-header-breadcrumb">
								<a class="zpm-header-back lnr lnr-chevron-left" href="<?php echo $base_url; ?>"></a>
								<?php echo $category->name; ?>
								<small id="zpm-header-description"> - <?php echo $category->description; ?></small>
							</h4>
						<?php endif; ?>
						
						<!-- Deprecated -->

						<!-- <div id="zpm-project-list__count">
							<?php _e( 'Showing', 'zephyr-project-manager' ) ?> <span id="zpm-project-count__current"><?php echo $page; ?></span> <?php _e( 'of', 'zephyr-project-manager' ) ?> <span id="zpm-project-count__total"><?php echo $total_pages; ?></span> <?php _e( 'pages', 'zephyr-project-manager' ) ?>
						</div> -->

						<div id="zpm-projects__view-options">
							<span id="zpm-project-view__archived" class="zpm-button__block zpm-fa-icon zpm-toggle-state zpm-color__hover-primary fas fa-archive" title="<?php _e( 'View Archived Projects', 'zephyr-project-manager' ); ?>"></span>
							<span class="zpm-button__block zpm-project-view__option zpm-color__hover-primary fas fa-th-large <?php echo $list_view ? '' : 'zpm-state__active'; ?>" data-view="grid" title="<?php _e( 'Grid', 'zephyr-project-manager' ); ?>"></span>
							<span class="zpm-button__block zpm-project-view__option zpm-color__hover-primary fas fa-th-list <?php echo $list_view ? 'zpm-state__active' : ''; ?>" data-view="list" title="<?php _e( 'List', 'zephyr-project-manager' ); ?>"></span>

							<span id="zpm-project-view__title"><?php _e( 'All Projects', 'zephyr-project-manager' ); ?></span>
						</div>

						<div id="zpm_projects_holder" class="zpm_body">
							<div id="zpm_project_manager_display" class="<?php echo ($project_count == '0') ? 'zpm_hide' : ''; ?>">
								<div id="zpm_project_page_options">
									<!-- <div id="zpm_filter_projects" class="zpm_custom_dropdown zpm_button" data-dropdown-id="zpm_filter_options">
										<span class="zpm_selected_option"><?php _e( 'Filter Projects', 'zephyr-project-manager' ); ?></span>
										<ul id="zpm_filter_options" class="zpm_custom_dropdown_options">

											<li class="zpm_selection_option" data-zpm-filter="-1"><?php _e( 'All Projects', 'zephyr-project-manager' ); ?></li>
											<li class="zpm_selection_option" data-zpm-filter="1"><?php _e( 'Incomplete Projects', 'zephyr-project-manager' ); ?></li>
											<li class="zpm_selection_option" data-zpm-filter="2"><?php _e( 'Completed Projects', 'zephyr-project-manager' ); ?></li>
											
											<li class="zpm-dropdown__subdropdown-container"><?php _e( 'Categories', 'zephyr-project-manager' ); ?>
												<ul class="zpm-dropdown zpm-subdropdown">
													<?php foreach ($categories as $value) : ?>
														<li class="zpm_selection_option" data-zpm-category-filter="<?php echo $value->id; ?>"><?php echo $value->name; ?></li>
													<?php endforeach; ?>
												</ul>
												<span class="zpm-submenu-indicator lnr lnr-chevron-right"></span>
											</li>
										</ul>
									</div> -->
									<!-- <h3 id="zpm-project-filter-title"><?php _e( 'All Projects', 'zephyr-project-manager' ); ?></h3> -->
									
									<?php if ( Utillities::can_create_projects() ) : ?>
										<button id="zpm_create_new_project" class="zpm_button"><?php _e( 'New Project', 'zephyr-project-manager' ); ?></button>
									<?php endif; ?>
								</div>
							</div>

							<!-- No projects yet -->
							<?php if ($project_count == '0') : ?>
								<div class="zpm_no_results_message">
									<?php printf( __( 'No projects created yet. To create a project, click on the \'Add\' button at the top right of the screen or click %s here %s', 'zephyr-project-manager' ), '<a id="zpm_first_project" class="zpm_button_link">', '</a>' ); ?>
								</div>
							<?php endif; ?>

							<!-- Project list/grid -->
							<div id="zpm_project_list" class="<?php echo $list_view ? 'zpm-project-view__list' : ''; ?>">
								<?php include( ZPM_PLUGIN_PATH . '/templates/parts/project_grid.php' ); ?>
							</div>

							<div id="zpm-project-pagination">
								<!-- Deprecated Next/Prev buttons -->
								<!-- <button class="zpm-projects-previous zpm_button zpm_button_inverted" <?php echo $page <= 1 ? 'disabled="disabled"' : ''; ?>><?php _e( 'Previous', 'zephyr-project-manager' ); ?></button>
								<button class="zpm-projects-next zpm_button zpm_button_inverted" <?php echo $page >= $total_pages ? 'disabled="disabled"' : ''; ?>><?php _e( 'Next', 'zephyr-project-manager' ); ?></button> -->

								<?php if ($total_pages > 1) : ?>
									<?php for ($i = 1; $i <= $total_pages; $i++) : ?>
										<button class="zpm-projects-pagination__page zpm_button zpm_button_inverted <?php echo $page == $i ? 'zpm-pagination__current-page' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
									<?php endfor; ?>
								<?php endif; ?>
							</div>	
						</div>
					
				<?php else: ?>
					<?php $back_url = apply_filters( 'zpm_project_categories_back_url', '' ); ?>

					<h4 id="zpm-header-breadcrumb">

						<?php if ( !empty( $back_url ) ) : ?>
							<a class="zpm-header-back lnr lnr-chevron-left" href="<?php echo $back_url; ?>"></a>
						<?php endif; ?>

						<?php _e( 'Choose a category', 'zephyr-project-manager' ); ?>
					</h4>
					<div class="zpm-grid zpm-category-grid">
						<div class="zpm-grid__row">
							<?php foreach ($categories as $category) : ?>
								<?php Categories::card_html( $category ); ?>
							<?php endforeach; ?>
						</div>
					</div> 
				<?php endif; ?>

			<?php else: ?>
				<?php echo apply_filters( 'zpm_projects_grid', '' ); ?>
			<?php endif; ?>

		<?php endif; ?>
	</div>
</div>
<?php $this->get_footer(); ?>