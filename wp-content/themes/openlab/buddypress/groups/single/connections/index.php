<?php
// @todo Pagination.
$connections = \OpenLab\Connections\Connection::get( [ 'group_id' => bp_get_current_group_id() ] );

$group_site_id  = openlab_get_site_id_by_group_id( bp_get_current_group_id() );
$group_site_url = get_site_url( $group_site_id );

$site_categories = OpenLab\Connections\Util::fetch_taxonomy_terms_for_site( $group_site_url, 'category' );
$site_tags       = OpenLab\Connections\Util::fetch_taxonomy_terms_for_site( $group_site_url, 'post_tag' );

?>

<?php do_action( 'template_notices' ); ?>

<?php if ( $connections ) : ?>
	<div class="connections-settings" data-group-id="<?php echo esc_attr( bp_get_current_group_id() ); ?>">
		<?php foreach ( $connections as $connection ) : ?>
			<?php
			$other_group_id_array = array_filter(
				$connection->get_group_ids(),
				function( $group_id ) {
					return $group_id !== bp_get_current_group_id();
				}
			);

			$connected_group_id = reset( $other_group_id_array );

			$connected_group = groups_get_group( $connected_group_id );

			$connected_group_url = bp_get_group_permalink( $connected_group );

			$connected_group_avatar = bp_core_fetch_avatar(
				[
					'item_id' => $connected_group_id,
					'object'  => 'group',
					'type'    => 'full',
				]
			);

			$connection_settings = $connection->get_group_settings( bp_get_current_group_id() );

			$all_is_checked = !  array_diff( [ 'post', 'comment' ], $connection_settings['content_types'] ) && ! array_diff( [ 'post_tag' => 'all', 'category' => 'all' ], $connection_settings['post_taxes'] );

			$selected_categories = [];
			if ( isset( $connection_settings['post_taxes']['category'] ) ) {
				if ( 'all' === $connection_settings['post_taxes']['category'] ) {
					$selected_categories = 'all';
				} else {
					$selected_categories = array_map( 'intval', $connection_settings['post_taxes']['category'] );
				}
			}

			$selected_tags = [];
			if ( isset( $connection_settings['post_taxes']['post_tag'] ) ) {
				if ( 'all' === $connection_settings['post_taxes']['post_tag'] ) {
					$selected_tags = 'all';
				} else {
					$selected_tags = array_map( 'intval', $connection_settings['post_taxes']['post_tag'] );
				}
			}

			?>
			<div class="connection-settings" id="connection-settings-<?php echo esc_attr( $connection->get_connection_id() ); ?>" data-connection-id="<?php echo esc_attr( $connection->get_connection_id() ); ?>">
				<div class="avatar-column">
					<a href="<?php echo esc_url( $connected_group_url ); ?>"><?php echo $connected_group_avatar; ?></a>
				</div>

				<div class="primary-column">
					<div class="connected-group-link item-title h2">
						<a class="no-deco" href="<?php echo esc_url( $connected_group_url ); ?>"><?php echo esc_html( $connected_group->name ); ?></a>
					</div>

					<div class="accordion">
						<button class="accordion-toggle" aria-expanded="false" aria-controls="accordion-content">
							<span class="accordion-caret">&gt;</span>
							<span class="sr-only">Expand</span>

							<?php echo wp_kses_post( __( '<strong>Sharing:</strong>&nbsp;Manage the content shared with this connection.', 'openlab-connections' ) ); ?>
						</button>

						<div class="accordion-content">
							<div class="connection-setting-checkbox">
								<input type="checkbox" <?php checked( $all_is_checked ); ?> class="connection-setting-all" id="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-all" /> <label for="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-all"><?php esc_html_e( 'All', 'openlab-connections' ); ?>
							</div>

							<div class="connection-setting-checkbox connection-setting-checkbox-post">
								<input type="checkbox" <?php checked( in_array( 'post', $connection_settings['content_types'], true ) ); ?> class="connection-setting-post" id="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-post" name="connection-settings[content-type][post]" value="1" /> <label for="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-post"><?php esc_html_e( 'Posts', 'openlab-connections' ); ?></label>

								<div class="connection-setting-checkbox-subsettings">
									<div>
										<input type="checkbox" <?php checked( isset( $connection_settings['post_taxes']['category'] ) ); ?> class="connection-setting-post-tax" id="connection-setting-<?php esc_attr( $connection->get_connection_id() ); ?>-post-category" name="connection-settings[post-taxes][category]" value="1" /> <label for="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-post-category" class=""><?php esc_html_e( 'Category', 'openlab-connections' ); ?></label>

										<select name="connection-settings[post-tax-terms][category]" multiple id="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-category-terms" class="connection-tax-term-selector">
											<option value="_all" <?php selected( 'all' === $selected_categories ); ?>><?php esc_html_e( 'All categories', 'openlab-connections' ); ?></option>

											<?php foreach ( $site_categories as $site_category ) : ?>
												<option value="<?php echo esc_attr( $site_category['id'] ); ?>" <?php selected( is_array( $selected_categories ) && in_array( $site_category['id'], $selected_categories, true ) ); ?>><?php echo esc_html( $site_category['name'] ); ?></option>
											<?php endforeach; ?>

										</select>
									</div>

									<div>
										<input type="checkbox" <?php checked( isset( $connection_settings['post_taxes']['post_tag'] ) ); ?> class="connection-setting-post-tax" id="connection-setting-<?php esc_attr( $connection->get_connection_id() ); ?>-post-tag" name="connection-settings[post-taxes][post_tag]" value="1" /> <label for="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-post-tag" class=""><?php esc_html_e( 'Tag', 'openlab-connections' ); ?></label>

										<select name="connection-settings[post-tax-terms][post_tag]" multiple id="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-tag-terms" class="connection-tax-term-selector">
											<option value="_all" <?php selected( 'all' === $selected_tags ); ?>><?php esc_html_e( 'All tags', 'openlab-connections' ); ?></option>

											<?php foreach ( $site_tags as $site_tag ) : ?>
												<option value="<?php echo esc_attr( $site_tag['id'] ); ?>" <?php selected( is_array( $selected_tags ) && in_array( $site_tag['id'], $selected_tags, true ) ); ?>><?php echo esc_html( $site_tag['name'] ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>

							<div class="connection-setting-checkbox">
								<input type="checkbox" <?php checked( in_array( 'comment', $connection_settings['content_types'], true ) ); ?> class="connection-setting-comment" id="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-comment" name="connection-settings[content-type][comment]" value="1" /> <label for="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-comment"><?php esc_html_e( 'Comments', 'openlab-connections' ); ?>
							</div>

							<div class="connection-setting-checkbox">
								<input type="checkbox" <?php checked( empty( $connection_settings['content_types'] ) ); ?> class="connection-setting-none" id="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-none" /> <label for="connection-setting-<?php echo esc_attr( $connection->get_connection_id() ); ?>-none"><?php esc_html_e( 'None', 'openlab-connections' ); ?>
							</div>
						</div>
					</div>
				</div>

				<a href="<?php echo esc_url( wp_nonce_url( $connection->get_disconnect_url( bp_get_current_group_id() ), 'disconnect-' . $connection->get_connection_id() ) ); ?>" onclick="return confirm( '<?php echo esc_js( sprintf( __( 'Are you sure you want to disconnect from %s?', 'openlab-connections' ), $connected_group->name ) ); ?>' )" class="disconnect-button no-deco btn btn-primary" aria-label="<?php esc_attr_e( 'Disconnect', 'openlab-connections' ); ?>"><?php esc_html_e( 'Connected', 'openlab-connections' ); ?></a>

				<?php wp_nonce_field( 'connection-settings-' . $connection->get_connection_id(), 'connection-settings-' . $connection->get_connection_id() . '-nonce', false ); ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p><?php echo wp_kses_post( __( 'This feature connects related spaces on the Openlab. It is useful for sharing site activity with cohorts, collaborators, and across course sections. Visit <a href="tk">OpenLab Help</a> for more information.', 'openlab-connections' ) ); ?></p>

	<p><?php esc_html_e( 'This group does not have any connections.', 'openlab-connections' ); ?></p>
<?php endif; ?>
