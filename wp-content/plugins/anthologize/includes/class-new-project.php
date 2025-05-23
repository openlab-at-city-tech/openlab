<?php

if ( ! class_exists( 'Anthologize_New_Project' ) ) :

	class Anthologize_New_Project {

		/**
		 * Singleton bootstrap
		 *
		 * @since 0.7
		 * @return obj Anthologize instance
		 */
		public static function init() {
			static $instance;
			if ( empty( $instance ) ) {
				$instance = new Anthologize_New_Project();
			}
			return $instance;
		}

		function __construct() {
			if ( $_GET['page'] === 'anthologize/includes/class-new-project.php' ) {
				$this->display();
			}
		}

		function save_project() {

			$post_data = array(
				'post_title'    => 'Default Title',
				'post_type'     => 'anth_project',
				'post_status'   => '',
				'post_date'     => date( 'Y-m-d G:H:i' ),
				'post_date_gmt' => gmdate( 'Y-m-d G:H:i' ),
			);

			if ( ! empty( $_POST['post_title'] ) ) {
				$post_data['post_title'] = sanitize_text_field( $_POST['post_title'] );
			}

			if ( ! empty( $_POST['post_status'] ) ) {
				$post_data['post_status'] = sanitize_text_field( $_POST['post_status'] );
			}

			// If we're editing an existing project.
			if ( ! empty( $_POST['project_id'] ) ) {
				$project_id = (int) $_POST['project_id'];

				if ( ! $new_anthologize_meta = get_post_meta( $project_id, 'anthologize_meta', true ) ) {
					$new_anthologize_meta = $_POST['anthologize_meta'];
				} else {
					foreach ( $_POST['anthologize_meta'] as $key => $value ) {
						$new_anthologize_meta[ $key ] = $value;
					}
				}

				$the_project = get_post( $project_id );
				if ( ! empty( $_POST['post_status'] ) && ( $the_project->post_status != $_POST['post_status'] ) ) {
					$this->change_project_status( $project_id, sanitize_text_field( $_POST['post_status'] ) );
				}

				$post_data['ID'] = $project_id;
				wp_update_post( $post_data );

				if ( is_null( $new_anthologize_meta ) ) {
					delete_post_meta( $post_data['ID'], 'anthologize_meta' );
				} else {
					update_post_meta( $post_data['ID'], 'anthologize_meta', $new_anthologize_meta );
				}
			} else { // Otherwise, we're creating a new project

				$new_post = wp_insert_post( $post_data );
				// Nothing to save if we are creating a new project
				// update_post_meta($new_post, 'anthologize_meta', $new_anthologize_meta );

			}

			wp_redirect( get_admin_url() . 'admin.php?page=anthologize&project_saved=1' );
		}

		function change_project_status( $project_id, $status ) {
			if ( $status != 'publish' && $status != 'draft' ) {
				return;
			}

			$args = array(
				'post_status' => array( 'draft', 'publish' ),
				'post_parent' => $project_id,
				'nopaging'    => true,
				'post_type'   => 'anth_part',
			);

			$parts = get_posts( $args );

			foreach ( $parts as $part ) {
				if ( $part->post_status != $status ) {
					$update_part = array(
						'ID'          => $part->ID,
						'post_status' => $status,
					);
					wp_update_post( $update_part );
				}

				$args = array(
					'post_status' => array( 'draft', 'publish' ),
					'post_parent' => $part->ID,
					'nopaging'    => true,
					'post_type'   => 'anth_library_item',
				);

				$library_items = get_posts( $args );

				foreach ( $library_items as $item ) {
					if ( $item->post_status != $status ) {
						$update_item = array(
							'ID'          => $item->ID,
							'post_status' => $status,
						);
						wp_update_post( $update_item );
					}
				}
			}
		}

		function display() {

			if ( isset( $_POST['save_project'] ) ) {
				check_admin_referer( 'anthologize_new_project' );
				$this->save_project();
				return;
			}

			if ( ! empty( $_GET['project_id'] ) ) {
				// We are editing a project

				$project_id = $_GET['project_id'];
				$project    = get_post( $project_id );
				if ( empty( $project ) ) {
					_e( 'Unknown project ID', 'anthologize' );
					return;
				}
				$meta = get_post_meta( $project->ID, 'anthologize_meta', true );

			} else {
				$project = null;
			}

			?>
		<div class="wrap anthologize">

			<div id="anthologize-logo"><img src="<?php echo esc_url( plugins_url() . '/anthologize/images/anthologize-logo.gif' ); ?>" alt="<?php esc_attr_e( 'Anthologize logo', 'anthologize' ); ?>" /></div>

			<?php if ( $project ) : ?>
			<h2><?php _e( 'Edit Project', 'anthologize' ); ?></h2>
			<?php else : ?>
			<h2><?php _e( 'Add New Project', 'anthologize' ); ?></h2>
			<?php endif; ?>
			<form action="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/admin.php?page=anthologize_new_project&noheader=true" method="post">
				<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="project-title"><?php _e( 'Project Title', 'anthologize' ); ?></label></th>
					<?php
					$existing_project_title = $project ? $project->post_title : '';
					?>
					<td><input type="text" name="post_title" id="project-title" value="<?php echo esc_attr( $existing_project_title ); ?>"></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="project-subtitle"><?php _e( 'Subtitle', 'anthologize' ); ?></label>
					<?php
					$existing_subtitle = $project ? $meta['subtitle'] : '';
					?>
					<td><input type="text" name="anthologize_meta[subtitle]" id="project-subtitle" value="<?php echo esc_attr( $existing_subtitle ); ?>" /></td>
				</tr>

				<?php /* Hidden until there is a more straightforward way to display projects on the front end of WP */ ?>
				<?php
				/*
				<tr valign="top">
					<th scope="row"><label for="post_status"><?php _e( 'Project Status', 'anthologize' ) ?></label></th>
					<td>
						<input type="radio" name="post_status" value="publish" <?php if ( $project->post_status == 'publish' ) : ?>checked="checked"<?php endif; ?> > Published<br />
						<input type="radio" name="post_status" value="draft" <?php if ( $project->post_status != 'publish' ) : ?>checked="checked"<?php endif; ?>> Draft<br />
						<p><small><?php _e( 'Published projects are available via the web. Remember that you can change the status of your project later.', 'anthologize' ) ?></small></p>
					</td>
				</tr>
				*/
				?>

			</table>


				<div class="anthologize-button"><input type="submit" name="save_project" value="<?php _e( 'Save Project', 'anthologize' ); ?>"></div>
			<?php $existing_project_id = $project ? $project->ID : ''; ?>
			<input type="hidden" name="project_id" value="<?php echo esc_attr( $existing_project_id ); ?>">

			<?php wp_nonce_field( 'anthologize_new_project' ); ?>

			</form>

		</div>
			<?php
		}
	}

endif;

function item_meta_redirect( $location ) {
	$location = get_admin_url() . 'admin.php?page=anthologize';
	echo $location;
	exit;
	return $location;
}

add_filter( 'redirect_post_location', 'item_meta_redirect' );
