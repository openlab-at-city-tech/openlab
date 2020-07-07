<div class='wrap wrap-media-cleaner'>

	<?php
		echo $admin->display_title( "Media Cleaner" );
		$posts_per_page = get_option( 'wpmc_results_per_page', 20 );
		$view = isset ( $_GET[ 'view' ] ) ? sanitize_text_field( $_GET[ 'view' ] ) : "issues";
		$paged = isset ( $_GET[ 'paged' ] ) ? sanitize_text_field( $_GET[ 'paged' ] ) : 1;
		$s = isset ( $_GET[ 's' ] ) ? sanitize_text_field( $_GET[ 's' ] ) : null;
		$f = isset ( $_GET[ 'f' ] ) ? sanitize_text_field( $_GET[ 'f' ] ) : null;
		$orderby = isset ( $_GET[ 'orderby' ] ) ? sanitize_text_field( $_GET[ 'orderby' ] ) : '';
		$order = isset ( $_GET[ 'order' ] ) ? sanitize_text_field( $_GET[ 'order' ] ) : 'asc';
		$table_scan = $wpdb->prefix . "mclean_scan";
		$table_refs = $wpdb->prefix . "mclean_refs";
		$filterByTypeSQL = '';
		if ( !empty( $f ) ) {
			$availableFilters = [ 'NO_CONTENT', 'ORPHAN_FILE', 'ORPHAN_RETINA', 'ORPHAN_WEBP', 'ORPHAN_MEDIA' ];
			if ( in_array( $f, $availableFilters ) )
				$filterByTypeSQL = " AND issue = '$f'";
			else
				$f = null;
		}

		// Check the DB
		// If does not exist, let's create it.
		// TODO: When PHP 7 only, let's clean this and use anonymous functions.
		$db_init = !( strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_scan'" ) ) != strtolower( $table_scan )
			|| strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_refs'" ) ) != strtolower( $table_refs ) );
		if ( !$db_init ) {
			wpmc_create_database();
			$db_init = !( strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_scan'" ) ) != strtolower( $table_scan )
				|| strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_refs'" ) ) != strtolower( $table_refs ) );
		}

		// It still doesn't exist. That's not your lucky day :(
		if ( !$db_init ) {
			echo "<div class='notice notice-error'><p>";
			_e( "<b>The database is not ready for Media Cleaner. The scan will not work.</b> Click on the <b>Reset</b> button, it re-creates the tables required by Media Cleaner. If this message still appear, contact the support.", 'media-cleaner' );
			echo "</p></div>";
		}

		// Check the access rights to the uploads directory
		$upload_folder = wp_upload_dir();
		$basedir = $upload_folder['basedir'];
		if ( !is_writable( $basedir ) ) {
			echo "<div class='notice notice-error'><p>";
			_e( 'The directory for uploads is not writable. Media Cleaner will only be able to scan.', 'media-cleaner' );
			echo "</p></div>";
		}

		$issues_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_scan WHERE ignored = 0 AND deleted = 0" . $filterByTypeSQL );
		$total_size = $wpdb->get_var( "SELECT SUM(size) FROM $table_scan WHERE ignored = 0 AND deleted = 0" . $filterByTypeSQL );
		$trash_total_size = $wpdb->get_var( "SELECT SUM(size) FROM $table_scan WHERE ignored = 0 AND deleted = 1" . $filterByTypeSQL );
		$ignored_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_scan WHERE ignored = 1" . $filterByTypeSQL );
		$deleted_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_scan WHERE deleted = 1" . $filterByTypeSQL );

		// Create the Order By
		$sqlOrderBy = "path, time";
		if ( $orderby === 'size' ) {
			$sqlOrderBy = "size " . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ( $orderby === 'path' ) {
			$sqlOrderBy = "path " . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ( $orderby === 'id' ) {
			$sqlOrderBy = "postId " . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}

		if ( $view == 'deleted' ) {
			$items_count = $deleted_count;
			$items = $wpdb->get_results( $wpdb->prepare( "SELECT id, type, postId, path, size, ignored, deleted, issue
				FROM $table_scan WHERE ignored = 0 AND deleted = 1 AND path LIKE %s $filterByTypeSQL
				ORDER BY $sqlOrderBy
				LIMIT %d, %d", '%' . $s . '%', ( $paged - 1 ) * $posts_per_page, $posts_per_page ), OBJECT );
		}
		else if ( $view == 'ignored' ) {
			$items_count = $ignored_count;
			$items = $wpdb->get_results( $wpdb->prepare( "SELECT id, type, postId, path, size, ignored, deleted, issue
				FROM $table_scan
				WHERE ignored = 1 AND deleted = 0 AND path LIKE %s $filterByTypeSQL
				ORDER BY $sqlOrderBy
				LIMIT %d, %d", '%' . $s . '%', ( $paged - 1 ) * $posts_per_page, $posts_per_page ), OBJECT );
		}
		else {
			$items_count = $issues_count;
			$items = $wpdb->get_results( $wpdb->prepare( "SELECT id, type, postId, path, size, ignored, deleted, issue
				FROM $table_scan
				WHERE ignored = 0 AND deleted = 0  AND path LIKE %s $filterByTypeSQL
				ORDER BY $sqlOrderBy
				LIMIT %d, %d", '%' . $s . '%', ( $paged - 1 ) * $posts_per_page, $posts_per_page ), OBJECT );
		}
	?>

	<div id="wpmc_actions">

		<!-- SCAN -->
		<?php if ( $view != 'deleted' ): ?>
		<a id='wpmc_scan' class='button-primary' style='float: left;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-search"></span><?php _e("Start Scan", 'media-cleaner'); ?></a>
		<?php endif; ?>

		<!-- PAUSE -->
		<?php if ( $view != 'deleted' ): ?>
		<a id='wpmc_pause' onclick='wpmc_pause()' class='button' style='float: left; margin-left: 5px; display: none;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-pause"></span><?php _e("Pause", 'media-cleaner'); ?></a>
		<?php endif; ?>

		<!-- DELETE SELECTED -->
		<a id='wpmc_delete' class='button exclusive' style='float: left; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-no"></span><?php _e("Delete", 'media-cleaner'); ?></a>
		<?php if ( $view == 'deleted' ): ?>
		<a id='wpmc_recover' onclick='wpmc_recover()' class='button-secondary' style='float: left; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-repeat"></span><?php _e( "Recover", 'media-cleaner' ); ?></a>
		<?php endif; ?>

		<!-- IGNORE SELECTED -->
		<a id='wpmc_ignore' class='button exclusive' style='float: left; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-yes"></span><?php
			if ( $view == 'ignored' )
				_e( "Mark as Issue", 'media-cleaner' );
			else
				_e( "Ignore", 'media-cleaner' );
		?>
		</a>

		<!-- DELETE ALL -->
		<?php if ( $view == 'deleted' ): // i ?>
		<a id='wpmc_empty_trash' class='button button-red exclusive' style='float: right; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-trash"></span><?php _e("Empty trash", 'media-cleaner'); ?></a>
		<a id='wpmc_recover_all' class='button-primary exclusive' style='float: right; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-repeat"></span><?php _e("Recover all", 'media-cleaner'); ?></a>
		<?php else: // i ?>
		<?php if ( $f || $s ): // ii ?>
		<a id='wpmc_delete_all' class='button button-red exclusive' data-filter='<?php echo esc_attr( $f ) ?>' data-search='<?php echo esc_attr( $s ) ?>' style='float: right; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-trash"></span><?php _e("Delete these results", 'media-cleaner'); ?></a>
		<?php else: // ii ?>
		<a id='wpmc_delete_all' class='button button-red exclusive' style='float: right; margin-left: 5px;'><span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-trash"></span><?php _e("Delete all", 'media-cleaner'); ?></a>
		<?php endif; // ii ?>
		<?php endif; // i ?>

		<form id="posts-filter" action="upload.php" method="get" style='float: right;'>
			<p class="search-box" style='margin-left: 5px; float: left;'>
				<input type="search" name="s" placeholder="<?php _e('Search', 'media-cleaner' ); ?>" style="width: 120px;" value="<?php echo $s ? esc_attr( $s ) : ""; ?>">
				<select name="f" id="filter-by-type" style="margin-top: -3px; font-size: 13px;">
					<option <?php echo !$f ? 'selected="selected"' : ''; ?> value="0"><?php
						_e('All Issues', 'media-cleaner' ); ?></option>
					<option <?php echo $f === 'NO_CONTENT' ? 'selected="selected"' : ''; ?> value="NO_CONTENT"><?php
						_e( 'Seems not use', 'media-cleaner' ); ?></option>
					<option <?php echo $f === 'ORPHAN_MEDIA' ? 'selected="selected"' : ''; ?>value="ORPHAN_MEDIA"><?php
						_e( 'No attached file', 'media-cleaner' ); ?></option>
					<option <?php echo $f === 'ORPHAN_FILE' ? 'selected="selected"' : ''; ?>value="ORPHAN_FILE"><?php
						_e( 'Not in Library', 'media-cleaner' ); ?></option>
					<option <?php echo $f === 'ORPHAN_RETINA' ? 'selected="selected"' : ''; ?>value="ORPHAN_RETINA"><?php
						_e( 'Orphan Retina', 'media-cleaner' ); ?></option>
					<option <?php echo $f === 'ORPHAN_WEBP' ? 'selected="selected"' : ''; ?>value="ORPHAN_WEBP"><?php
						_e( 'Orphan WebP', 'media-cleaner' ); ?></option>
				</select>
				<input type="hidden" name="page" value="media-cleaner">
				<input type="hidden" name="view" value="<?php echo $view; ?>">
				<input type="hidden" name="paged" value="1">
				<input type="submit" class="button exclusive" value="<?php _e( 'Search', 'media-cleaner' ); ?>"><span style='border-right: #A2A2A2 solid 1px; margin-left: 5px; margin-right: 3px;'>&nbsp;</span>
			</p>
		</form>

		<!-- PROGRESS -->
		<span style='margin-left: 12px; font-size: 15px; top: 5px; position: relative; color: #747474;' id='wpmc_progression'></span>

	</div>

	<p>
		<?php
			$method = get_option( 'wpmc_method', 'media' );
			if ( $db_init ) {
				if ( !$admin->is_registered() )
					$method = 'media';

				$hide_warning = get_option( 'wpmc_hide_warning', false );
				if ( !$hide_warning ) {
					echo "<div class='notice notice-warning'><p>";
					_e( "<b style='color: red;'>Important.</b> <b>Backup your DB and your /uploads directory before using Media Cleaner. </b> The deleted files will be temporarily moved to the <b>uploads/wpmc-trash</b> directory. After testing your website, you can check the <a href='?page=media-cleaner&s&view=deleted'>trash</a> to either empty it or recover the media and files. The Media Cleaner does its best to be safe to use. However, WordPress being a very dynamic and pluggable system, it is impossible to predict all the situations in which your files are used. <b style='color: red;'>Again, please backup!</b> If you don't know how, give a try to this: <a href='https://meow.click/blogvault' target='_blank'>BlogVault</a>. <br /><br /><b style='color: red;'>Be thoughtful.</b> Don't blame Media Cleaner if it deleted too many or not enough of your files. It makes cleaning possible and this task is only possible this way; don't post a bad review because it broke your install. <b>If you have a proper backup, there is no risk</b>. Sorry for the lengthy message, but better be safe than sorry. You can disable this big warning in the options if you have a Pro license. Make sure you read this warning twice. Media Cleaner is awesome and always getting better so I hope you will enjoy it. Thank you :)", 'media-cleaner' );
					echo "</p></div>";
				}

				if ( !MEDIA_TRASH ) {
					echo "<div class='notice notice-warning columned'>";
					_e(  "<p>The trash for the Media Library is disabled. Any media removed by the plugin will be <b>permanently deleted</b>. To enable it, modify your wp-config.php file and add this line (preferably at the top): <b>define( 'MEDIA_TRASH', true );</b>", 'media-cleaner' );
					echo '</p>';
					echo '<div><a href="#" id="wpmc_enable_media_trash" class="button-primary">'. __( 'Add it automatically', 'media-cleaner' ) .'</a></div>';
					echo '</div>';
				}
			}

			if ( !$admin->is_registered() ) {
				echo "<div class='notice notice-warning'><p>";
				_e( "This plugin is a lot of work so please consider <a target='_blank' href='//meowapps.com/plugin/media-cleaner'>Media Cleaner Pro</a> in order to receive support and to contribute in the evolution of it. Also, <a target='_blank' href='//meowapps.com/plugin/media-cleaner'>Media Cleaner Pro</a> version will also give you the option <b>to scan the physical files in your /uploads folder</b> and extra checks for the common Page Builders.", 'media-cleaner' );
				echo "</p></div>";

				$unsupported = array();

				if ( class_exists( 'ACF' ) || function_exists( 'acfw_globals' ) )
					array_push( $unsupported, 'ACF' );

				if ( function_exists( '_et_core_find_latest' ) )
					array_push( $unsupported, 'Divi' );

				if ( class_exists( 'Vc_Manager' ) )
					array_push( $unsupported, 'Visual Composer' );

				if ( function_exists( 'fusion_builder_map' ) )
					array_push( $unsupported, 'Fusion Builder' );

				if ( function_exists( 'elementor_load_plugin_textdomain' ) )
					array_push( $unsupported, 'Elementor' );

				if ( class_exists( 'FLBuilderModel' ) )
					array_push( $unsupported, 'Beaver Builder' );

				if ( class_exists( 'Oxygen_VSB_Dynamic_Shortcodes' ) )
					array_push( $unsupported, 'Oxygen Builder' );

				if ( class_exists( 'Brizy_Editor_Post' ) )
					array_push( $unsupported, 'Brizy Editor' );

				if ( function_exists( 'amd_zlrecipe_convert_to_recipe' ) )
					array_push( $unsupported, 'ZipList Recipe' );

				if ( class_exists( 'UberMenu' ) )
					array_push( $unsupported, 'UberMenu' );

				if ( class_exists( 'X_Bootstrap' ) )
					array_push( $unsupported, 'Theme X' );

				if ( class_exists( 'SiteOrigin_Panels' ) )
					array_push( $unsupported, 'SiteOrigin PageBuilder' );

				if ( defined( 'TASTY_PINS_PLUGIN_FILE' ) )
					array_push( $unsupported, 'Tasty Pins' );

				if ( class_exists( 'WCFMmp' ) )
					array_push( $unsupported, 'WCFM Marketplace' );

				if ( class_exists( 'RevSliderFront' ) )
					array_push( $unsupported, 'Revolution Slider' );

				if ( defined( 'WPESTATE_PLUGIN_URL' ) )
					array_push( $unsupported, 'WP Residence' );

				if ( defined( 'AV_FRAMEWORK_VERSION' ) )
					array_push( $unsupported, 'Avia Framework' );

				if ( class_exists( 'FAT_Portfolio' ) )
					array_push( $unsupported, 'FAT Portfolio' );

				if ( class_exists( 'YIKES_Custom_Product_Tabs' ) )
					array_push( $unsupported, 'Yikes Custom Product Tabs' );

				if ( function_exists( 'drts' ) )
					array_push( $unsupported, 'Directories' );

				if ( class_exists( 'ImageMapPro' ) )
					array_push( $unsupported, 'Image Map Pro' );

				if ( class_exists( 'YOOtheme\Builder\Wordpress\BuilderListener' ) ) {
					array_push( $unsupported, 'YooTheme Builder' );
				}

				if ( !empty( $unsupported ) ) {
					echo "<div class='notice notice-error'><p>";
					_e( "<b>Important note about the following plugin(s): </b>", 'media-cleaner' );
					echo '<b>' . join( ', ', $unsupported ) . '</b>. ';
					_e( "They require additional checks which are implemented in the <a target='_blank' href='//meowapps.com/plugin/media-cleaner'>Media Cleaner Pro</a>.", 'media-cleaner' );
					echo "</p></div>";
				}
			}

			$check_content = get_option( 'wpmc_content', true );
			$check_library = get_option(' wpmc_media_library', true );
			$live_content = get_option(' wpmc_live_content', true );

			$access_settings = ' ' . sprintf(
				// translators: %s is URL leading to the plugin settings page
				__( '<a href="%s">Click here</a> to modify the settings.', 'media-cleaner' ),
				'admin.php?page=wpmc_settings-menu' );

			if ( $method == 'media' && ( $check_content || $live_content ) ) {
				echo "<div class='notice notice-success'><p>";
				_e( "Media Cleaner will analyze the Media Library for entries which aren't used in the content.", 'media-cleaner' );
				echo $access_settings;
				echo "</p></div>";
			}
			else if ( $method == 'media' ) {
				echo "<div class='notice notice-error'><p>";
				_e( "Media Cleaner will analyze the Media Library. Since <i>Content</i> has not be checked, a special scan will be ran: <u>only broken media entries</u> will be detected.", 'media-cleaner' );
				echo $access_settings;
				echo "</p></div>";
			}
			else if ( $method == 'files' && ( $check_content || $live_content ) && $check_library ) {
				echo "<div class='notice notice-success'><p>";
				_e( "Media Cleaner will analyze the filesystem for files which aren't registered in the Media Library and aren't used in the content.", 'media-cleaner' );
				echo $access_settings;
				echo "</p></div>";
			}
			else if ( $method == 'files' && $check_library ) {
				echo "<div class='notice notice-success'><p>";
				_e( "Media Cleaner will analyze the filesystem for files which aren't registered in the Media Library.", 'media-cleaner' );
				echo $access_settings;
				echo "</p></div>";
			}
			else if ( $method == 'files' && ( $check_content || $live_content ) ) {
				echo "<div class='notice notice-success'><p>";
				_e( "Media Cleaner will analyze the filesystem for files which aren't used in the content.", 'media-cleaner' );
				echo $access_settings;
				echo "</p></div>";
			}
			else if ( $method == 'files' ) {
				echo "<div class='notice notice-error'><p>";
				_e( "Media Cleaner will analyze the filesystem. <b>Neither <i>Content</i> or <i>Media Library</i> has been checked.</b> <u>Therefore, all the files will be listed as issues</u>.", 'media-cleaner' );
				echo $access_settings;
				echo "</p></div>";
			}
			else {
				echo "<div class='notice notice-error'><p>";
				_e( "This type of scan hasn't been set.", 'media-cleaner' );
				echo "</p></div>";
			}

			echo sprintf(
				// translators: %1$s is a number of found issues, %2$s is a size of detected files, %3$s is a total size of files in trash
				__( 'There are <b>%1$s issue(s)</b> with your files, accounting for <b>%2$s MB</b>. Your trash contains <b>%3$s MB.</b>', 'media-cleaner' ),
				number_format( $issues_count, 0 ),
				number_format( $total_size / 1000000, 2 ),
				number_format( $trash_total_size / 1000000, 2 )
			);
		?>
	</p>

	<div id='wpmc-paging'>
	<?php

	function create_link( $s, $f, $orderby, $order, $view ) {
		return '?page=media-cleaner&s=' . urlencode( $s ) . '&f=' . urlencode( $f ) . 
		'&orderby=' . urlencode( $orderby ) . '&order=' . urlencode( $order ) . '&view=' . $view;
	}

	echo paginate_links(array(
		'base' => create_link( $s, $f, $orderby, $order, $view ) . '%_%',
		'current' => $paged,
		'format' => '&paged=%#%',
		'total' => ceil( $items_count / $posts_per_page ),
		'prev_next' => false
	));
	?>
	</div>

	<ul class="subsubsub">
		<li class="all"><a <?php if ( $view == 'issues' ) echo "class='current'"; ?> href='?page=media-cleaner&s=<?php echo $s; ?>&view=issues'><?php _e( "Issues", 'media-cleaner' ); ?></a><span class="count">(<?php echo $issues_count; ?>)</span></li> |
		<li class="all"><a <?php if ( $view == 'ignored' ) echo "class='current'"; ?> href='?page=media-cleaner&s=<?php echo $s; ?>&view=ignored'><?php _e( "Ignored", 'media-cleaner' ); ?></a><span class="count">(<?php echo $ignored_count; ?>)</span></li> |
		<li class="all"><a <?php if ( $view == 'deleted' ) echo "class='current'"; ?> href='?page=media-cleaner&s=<?php echo $s; ?>&view=deleted'><?php _e( "Trash", 'media-cleaner' ); ?></a><span class="count">(<?php echo $deleted_count; ?>)</span></li>
	</ul>

	<table id='wpmc-table' class='wp-list-table widefat fixed striped media'>

		<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style="padding: 8px 2px;">
					<input id="wpmc-cb-select-all" type="checkbox">
				</th>
				<?php if ( !get_option( 'wpmc_hide_thumbnails', false ) ): ?>
				<th style='width: 64px;'><?php _e( 'Thumb', 'media-cleaner' ) ?></th>
				<?php endif; ?>
				<th style='width: 50px;'><?php _e( 'Type', 'media-cleaner' ) ?></th>
				<th style='width: 80px;'><?php _e( 'Origin', 'media-cleaner' ) ?></th>

				<?php if ( !empty( $wplr ) ):  ?>
					<th style='width: 70px;'><?php _e( 'LR ID', 'media-cleaner' ) ?></th>
				<?php endif; ?>

				<th class='manage-column sortable <?= $order === "asc" ? "desc" : "asc" ?>'>
					<a href='<?= create_link( $s, $f, 'path', ( $order === "asc" ? "desc" : "asc" ), $view ) ?>'>
						<span><?php _e( 'Path', 'media-cleaner' ) ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th style='width: 220px;'><?php _e( 'Issue', 'media-cleaner' ) ?></th>
				<th class='manage-column sortable <?= $order === "asc" ? "desc" : "asc" ?>' style='width: 120px; text-align: right;'>
					<a href='<?= create_link( $s, $f, 'size', ( $order === "asc" ? "desc" : "asc" ), $view ) ?>'>
						<span><?php _e( 'Size', 'media-cleaner' ) ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php
				foreach ( $items as $issue ) {
					if ( $view == 'deleted' ) {
						$regex = "^(.*)(\\s\\(\\+.*)$";
						$issue->path = preg_replace( '/' .$regex . '/i', '$1', $issue->path );
					}
			?>
			<tr>
				<td><input type="checkbox" name="id" value="<?php echo $issue->id ?>"></td>
				<?php if ( !get_option( 'wpmc_hide_thumbnails', false ) ): ?>
				<td>
					<?php
						if ( $issue->deleted == 0 ) {
							if ( $issue	->type == 0 ) {
								// FILE
								$upload_dir = wp_upload_dir();
								$url = htmlspecialchars( $upload_dir['baseurl'] . '/' . $issue->path, ENT_QUOTES );
								echo "<a target='_blank' href='" . $url .
									"'><img style='max-width: 48px; max-height: 48px;' src='" . $url . "' /></a>";
							}
							else {
								// MEDIA
								$file = get_attached_file( $issue->postId );
								if ( file_exists( $file ) ) {
									$attachmentsrc = wp_get_attachment_image_src( $issue->postId, 'thumbnail' );
									if ( empty( $attachmentsrc ) )
										echo '<span class="dashicons dashicons-no-alt"></span>';
									else {
										$attachmentsrc_clean = htmlspecialchars( $attachmentsrc[0], ENT_QUOTES );
										echo "<a target='_blank' href='" . $attachmentsrc_clean .
											"'><img style='max-width: 48px; max-height: 48px;' src='" .
											$attachmentsrc_clean . "' />";
									}
								}
								else {
									echo '<span class="dashicons dashicons-no-alt"></span>';
								}
							}
						}
						if ( $issue->deleted == 1 ) {
							$upload_dir = wp_upload_dir();
							$url = htmlspecialchars( $upload_dir['baseurl'] . '/wpmc-trash/' . $issue->path, ENT_QUOTES );
							echo "<a target='_blank' href='" . $url .
								"'><img style='max-width: 48px; max-height: 48px;' src='" . $url . "' /></a>";
						}
					?>
				</td>
				<?php endif; ?>
				<td><?php echo $issue->type == 0 ? 'FILE' : 'MEDIA'; ?></td>
				<td><?php echo $issue->type == 0 ? 'Filesystem' : ("<a href='post.php?post=" .
					$issue->postId . "&action=edit'>ID " . $issue->postId . "</a>"); ?></td>
				<?php if ( !empty( $wplr ) ) { $info = $wplr->get_sync_info( $issue->postId ); ?>
					<td style='width: 70px;'><?php echo ( !empty( $info ) && $info->lr_id ? $info->lr_id : "" ); ?></td>
				<?php } ?>
				<td><?php echo stripslashes( $issue->path ); ?></td>
				<td><?php $core->echo_issue( $issue->issue ); ?></td>
				<td style='text-align: right;'><?php echo number_format( $issue->size / 1000, 2 ); ?> KB</td>
			</tr>
			<?php } ?>
		</tbody>

		<tfoot>
			<tr><th></th>
			<?php if ( !get_option( 'hide_thumbnails', false ) ): ?>
			<th></th>
			<?php endif; ?>
			<th><?php _e( 'Type', 'media-cleaner' ) ?></th><th><?php _e( 'Origin', 'media-cleaner' ) ?></th>
			<?php if ( !empty( $wplr ) ):  ?>
				<th style='width: 70px;'><?php _e( 'LR ID', 'media-cleaner' ) ?></th>
			<?php endif; ?>
			<th><?php _e( 'Path', 'media-cleaner' ) ?></th><th><?php _e( 'Issue', 'media-cleaner' ) ?></th><th style='width: 80px; text-align: right;'><?php _e( 'Size', 'media-cleaner' ) ?></th></tr>
		</tfoot>

	</table>

	<div id='wpmc-paging'>
	<?php
	echo paginate_links(array(
		'base' => '?page=media-cleaner&s=' . urlencode($s) . '&view=' . $view . '%_%',
		'current' => $paged,
		'format' => '&paged=%#%',
		'total' => ceil( $items_count / $posts_per_page ),
		'prev_next' => false
	));
	?>
	</div>
</div>

<div id="wpmc-dialog" class="hidden" style="max-width:800px"></div>

<div id="wpmc-error-dialog" class="hidden" style="max-width:800px">
	<h3><!-- The content will be inserted by JS --></h3>
	<p>Please check your logs.<br>Do you want to <a href="#" class="retry">try again</a>, or <a href="#" class="skip">skip this entry</a>?</p>
	<div class="options">
		<a href="#" class="always-retry"><?php _e( 'Always Retry', 'media-cleaner' ) ?></a>
		<a href="#" class="skip-all"><?php _e( 'Skip All', 'media-cleaner' ) ?></a>
	</div>
</div>
