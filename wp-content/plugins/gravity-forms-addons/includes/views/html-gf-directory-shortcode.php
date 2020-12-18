<?php
/**
 * The template that contains [directory] shortcode content.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */

if ( ! isset( $directory_shown ) ) {
	$directory_shown = true;
	?>

<script>
	<?php if ( ! empty( $lightboxsettings['images'] ) || ! empty( $lightboxsettings['entry'] ) ) { ?>

	var tb_pathToImage = "<?php echo esc_js( site_url( '/wp-includes/js/thickbox/loadingAnimation.gif' ) ); ?>";
	var tb_closeImage = "<?php echo esc_js( site_url( '/wp-includes/js/thickbox/tb-close.png' ) ); ?>";
	var tb_height = 600;
	<?php } ?>
	function not_empty( variable ) {
		if ( variable == '' || variable == null || variable == 'undefined' || typeof(variable) == 'undefined' ) {
			return false;
		} else {
			return true;
		}
	}

	<?php if ( ! empty( $jstable ) ) { ?>
	jQuery( document ).ready( function ( $ ) {
		$( '.tablesorter' ).each( function () {
			$( this ).tablesorter(<?php echo apply_filters( 'kws_gf_directory_tablesorter_options', '' ); ?>);
		} );
	} );
	<?php } else if ( isset( $jssearch ) && $jssearch ) { ?>
	function Search( search, sort_field_id, sort_direction, search_criteria ) {
		if ( not_empty( search ) ) {
			var search = "&gf_search=" + encodeURIComponent( search );
		} else {
			var search = '';
		}

		var search_filters = '';
		if ( not_empty( search_criteria ) ) {
			$.each( search_criteria, function ( index, value ) {
				search_filters += "&filter_" + index + "=" + encodeURIComponent( value );
			} );
		}

		if ( not_empty( sort_field_id ) ) {
			var sort = "&sort=" + sort_field_id;
		} else {
			var sort = '';
		}
		if ( not_empty( sort_direction ) ) {
			var dir = "&dir=" + sort_direction;
		} else {
			var dir = '';
		}
		var page = '<?php if ( $wp_rewrite->using_permalinks() ) { echo '?'; } else { echo '&'; } ?> page=' +<?php echo isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : '"1"'; ?>;
		var location = "<?php echo esc_js( get_permalink( $post->ID ) ); ?>" + page + search + sort + dir + search_filters;
		document.location = location;
	}
	<?php } ?>
</script>
<?php } ?>


<div class="wrap">
	<?php if ( $titleshow ) : ?>
		<h2><?php echo $titleprefix . $title; ?></h2>
	<?php endif; ?>

	<?php
	// --- Render Search Box ---

	if ( $search || ! empty( $show_search_filters ) ) :
		?>

		<form id="lead_form" method="get" action="<?php echo $formaction; ?>">
			<?php
			//New logic for search criterias (since 3.5)

			if ( ! empty( $show_search_filters ) ) {

				foreach ( $show_search_filters as $key ) {
					$properties = self::get_field_properties( $form, $key );
					if ( in_array(
						$properties['type'],
						array(
							'select',
							'checkbox',
							'radio',
							'post_category',
						)
					) ) {
						echo self::render_search_dropdown( $properties['label'], 'filter_' . $properties['id'], $properties['choices'] ); //Label, name attr, choices
					} else {
						echo self::render_search_input( $properties['label'], 'filter_' . $properties['id'] ); //label, attr name
					}
				}
			}

			?>
			<p class="search-box">
				<?php if ( $search ) : ?>
					<label class="hidden" for="lead_search"><?php esc_html_e( 'Search Entries:', 'gravity-forms-addons' ); ?></label>
					<input type="text" name="gf_search" id="lead_search" value="<?php echo $search_query; ?>" <?php if ( $searchtabindex ) { echo ' tabindex="' . intval( $searchtabindex ) . '"'; }?> />
				<?php endif; ?>
				<?php
				// If not using permalinks, let's make the form work!
				echo ! empty( $_GET['p'] ) ? '<input name="p" type="hidden" value="' . esc_html( $_GET['p'] ) . '" />' : '';
				echo ! empty( $_GET['page_id'] ) ? '<input name="page_id" type="hidden" value="' . esc_html( $_GET['page_id'] ) . '" />' : '';
				?>
				<input type="submit" class="button" id="lead_search_button" value="<?php esc_attr_e( 'Search', 'gravity-forms-addons' ); ?>" <?php if ( $searchtabindex ) { echo ' tabindex="' . intval( $searchtabindex ++ ) . '"'; }?> />
			</p>
		</form>

		<?php
	endif;


	//Displaying paging links if appropriate

	if ( $lead_count > 0 && $showcount || $page_links ) {
		if ( $lead_count == 0 ) {
			$first_item_index --;
		}
		?>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php
				if ( $showcount ) {
					if ( ( $first_item_index + $page_size ) > $lead_count || $page_size <= 0 ) {
						$second_part = $lead_count;
					} else {
						$second_part = $first_item_index + $page_size;
					}
					?>
					<span class="displaying-num"><?php printf( __( 'Displaying %1$d - %2$d of %3$d', 'gravity-forms-addons' ), $first_item_index + 1, $second_part, $lead_count ); ?></span>
					<?php
				}
				if ( $page_links ) {
					echo $page_links;
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}

	do_action( 'kws_gf_before_directory_after_nav', do_action( 'kws_gf_before_directory_after_nav_form_' . $form_id, $form, $leads, compact( 'approved', 'sort_field', 'sort_direction', 'search_query', 'first_item_index', 'page_size', 'star', 'read', 'is_numeric', 'start_date', 'end_date' ) ) );
	?>

	<table class="<?php echo $tableclass; ?>" cellspacing="0"
							 <?php
								if ( ! empty( $tablewidth ) ) {
									echo ' width="' . $tablewidth . '"';
								}
								echo $tablestyle ? ' style="' . $tablestyle . '"' : '';
								?>
	>
		<?php if ( $thead ) { ?>
			<thead>
			<tr>
				<?php

				$addressesExist = false;
				foreach ( $columns as $field_id => $field_info ) {
					$dir = $field_id == 0 ? 'DESC' : 'ASC'; //default every field so ascending sorting except date_created (id=0)
					if ( $field_id == $sort_field ) { //reverting direction if clicking on the currently sorted field
						$dir = $sort_direction == 'ASC' ? 'DESC' : 'ASC';
					}
					if ( is_array( $adminonlycolumns ) && ! in_array( $field_id, $adminonlycolumns ) || ( is_array( $adminonlycolumns ) && in_array( $field_id, $adminonlycolumns ) && $showadminonly ) || ! $showadminonly ) {
						if ( $field_info['type'] == 'address' && $appendaddress && $hideaddresspieces ) {
							$addressesExist = true;
							continue;
						}
						?>
						<?php
						$_showlink = false;
						if ( isset( $jssearch ) && $jssearch && ! isset( $jstable ) ) {
							?>
							<th scope="col" id="gf-col-<?php echo $form_id . '-' . $field_id; ?>" class="manage-column" onclick="Search('<?php echo $search_query; ?>', '<?php echo $field_id; ?>', '<?php echo $dir; ?>', '' );" style="cursor:pointer;">
																  <?php
						} elseif ( isset( $jstable ) && $jstable || $field_info['type'] === 'id' ) {
							?>
							<th scope="col" id="gf-col-<?php echo $form_id . '-' . $field_id; ?>" class="manage-column">
							<?php
						} else {
							$_showlink = true;
							$searchpage     = isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
							$new_query_args = array(
								'gf_search' => $search_query,
								'sort'      => $field_id,
								'dir'       => $dir,
								'pagenum'   => $searchpage,
							);
							foreach ( $search_criteria as $key => $value ) {
								$new_query_args[ 'filter_' . $key ] = $value;
							}
							?>
							<th scope="col" id="gf-col-<?php echo $form_id . '-' . $field_id; ?>" class="manage-column">
							<a href="<?php echo esc_url_raw( add_query_arg( $new_query_args, get_permalink( $post->ID ) ) );?>">
							<?php
						}
						if ( $field_info['type'] == 'id' && $entry ) {
							$label = $entryth;
						} else {
							$label = $field_info['label'];
						}

						$label = apply_filters( 'kws_gf_directory_th', apply_filters( 'kws_gf_directory_th_' . $field_id, apply_filters( 'kws_gf_directory_th_' . sanitize_title( $label ), $label ) ) );
						echo esc_html( $label );

						if ( $_showlink ) {

							?>
					</a><?php } ?>
						</th>
						<?php
					}
				}

				if ( $appendaddress && $addressesExist ) {
					?>
					<th scope="col" id="gf-col-<?php echo $form_id . '-' . $field_id; ?>" class="manage-column" onclick="Search('<?php echo $search_query; ?>', '<?php echo $field_id; ?>', '<?php echo $dir; ?>');" style="cursor:pointer;">
						<?php
						$label = apply_filters( 'kws_gf_directory_th', apply_filters( 'kws_gf_directory_th_address', 'Address' ) );
						echo esc_html( $label )

						?>
						</th>
					<?php
				}
				?>
			</tr>
			</thead>
		<?php } ?>
		<tbody class="list:user user-list">
		<?php
		include_once( GF_DIRECTORY_PATH . 'includes/views/template-row.php' );
		?>
		</tbody>
		<?php
		if ( $tfoot ) {
			if ( isset( $jssearch ) && $jssearch && ! isset( $jstable ) ) {
				$th = '<th scope="col" id="gf-col-' . $form_id . '-' . $field_id . '" class="manage-column" onclick="Search(\'' . $search_query . '\', \'' . $field_id . '\', \'' . $dir . '\');" style="cursor:pointer;">';
			} else {
				$th = '<th scope="col" id="gf-col-' . $form_id . '-' . $field_id . '" class="manage-column">';
			}
			?>
			<tfoot>
			<tr>
				<?php
				$addressesExist = false;
				foreach ( $columns as $field_id => $field_info ) {
					$dir = $field_id == 0 ? 'DESC' : 'ASC'; //default every field so ascending sorting except date_created (id=0)
					if ( $field_id == $sort_field ) { //reverting direction if clicking on the currently sorted field
						$dir = $sort_direction == 'ASC' ? 'DESC' : 'ASC';
					}
					if ( is_array( $adminonlycolumns ) && ! in_array( $field_id, $adminonlycolumns ) || ( is_array( $adminonlycolumns ) && in_array( $field_id, $adminonlycolumns ) && $showadminonly ) || ! $showadminonly ) {
						if ( $field_info['type'] == 'address' && $appendaddress && $hideaddresspieces ) {
							$addressesExist = true;
							continue;
						}

						echo $th;

						if ( $field_info['type'] == 'id' && $entry ) {
							$label = $entryth;
						} else {
							$label = $field_info['label'];
						}

						$label = apply_filters( 'kws_gf_directory_th', apply_filters( 'kws_gf_directory_th_' . $field_id, apply_filters( 'kws_gf_directory_th_' . sanitize_title( $label ), $label ) ) );
						echo esc_html( $label )

						?>
				</th>
						<?php
					}
				}
				if ( $appendaddress && $addressesExist ) {
					?>
					<th scope="col" id="gf-col-<?php echo $form_id . '-' . $field_id; ?>" class="manage-column" onclick="Search('<?php echo $search_query; ?>', '<?php echo $field_id; ?>', '<?php echo $dir; ?>');" style="cursor:pointer;">
						<?php
						$label = apply_filters( 'kws_gf_directory_th', apply_filters( 'kws_gf_directory_th_address', 'Address' ) );
						echo esc_html( $label );
						?>
						</th>
					<?php
				}
				?>
			</tr>
			<?php
			if ( ! empty( $credit ) ) {
				self::get_credit_link( count( $columns ), $options );
			}
			?>
			</tfoot>
		<?php } ?>
	</table>
	<?php

	do_action( 'kws_gf_after_directory_before_nav', do_action( 'kws_gf_after_directory_before_nav_form_' . $form_id, $form, $leads, compact( 'approved', 'sort_field', 'sort_direction', 'search_query', 'first_item_index', 'page_size', 'star', 'read', 'is_numeric', 'start_date', 'end_date' ) ) );


	//Displaying paging links if appropriate

	if ( $lead_count > 0 && $showcount || $page_links ) {
		if ( $lead_count == 0 ) {
			$first_item_index --;
		}
		?>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php
				if ( $showcount ) {
					if ( ( $first_item_index + $page_size ) > $lead_count || $page_size <= 0 ) {
						$second_part = $lead_count;
					} else {
						$second_part = $first_item_index + $page_size;
					}
					?>
					<span class="displaying-num"><?php printf( __( 'Displaying %1$d - %2$d of %3$d', 'gravity-forms-addons' ), $first_item_index + 1, $second_part, $lead_count ); ?></span>
					<?php
				}
				if ( $page_links ) {
					echo $page_links;
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}

	?>
</div>

<?php

if ( empty( $credit ) ) {
	echo "\n" . '<!-- Directory generated by Gravity Forms Directory : http://wordpress.org/extend/plugins/gravity-forms-addons/ -->' . "\n";
}

do_action( 'kws_gf_after_directory', do_action( 'kws_gf_after_directory_form_' . $form_id, $form, $leads, compact( 'approved', 'sort_field', 'sort_direction', 'search_query', 'first_item_index', 'page_size', 'star', 'read', 'is_numeric', 'start_date', 'end_date' ) ) );

