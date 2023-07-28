<?php
// @todo Pagination.
$connections = \OpenLab\Connections\Connection::get( [ 'group_id' => bp_get_current_group_id() ] );

$group_site_id  = openlab_get_site_id_by_group_id( bp_get_current_group_id() );
$group_site_url = get_site_url( $group_site_id );

$site_categories = OpenLab\Connections\Util::fetch_taxonomy_terms_for_site( $group_site_url, 'category' );
$site_tags       = OpenLab\Connections\Util::fetch_taxonomy_terms_for_site( $group_site_url, 'post_tag' );

$current_group_type_label = openlab_get_group_type_label( [ 'case' => 'upper' ] );

$current_group_status = groups_get_current_group()->status;

switch ( $current_group_status ) {
	case 'private' :
		$group_status_text = sprintf( 'Because your %s is private, no content can be shared with connected groups.', $current_group_type_label );
	break;

	case 'hidden' :
		$group_status_text = sprintf( 'Because your %s is hidden, no content can be shared with connected groups.', $current_group_type_label );
	break;

	default :
		$group_status_text = '';
	break;
}

?>

<?php do_action( 'template_notices' ); ?>

<div class="openlab-connections">
	<?php if ( $connections ) : ?>
		<div class="connections-settings" data-group-id="<?php echo esc_attr( bp_get_current_group_id() ); ?>">
			<input type="hidden" id="current-group-status" value="<?php echo esc_attr( groups_get_current_group()->status ); ?>" />

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

				$selected_categories = [];
				if ( isset( $connection_settings['categories'] ) ) {
					if ( 'all' === $connection_settings['categories'] ) {
						$selected_categories = 'all';
					} else {
						$selected_categories = array_map( 'intval', $connection_settings['categories'] );
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
								<span class="accordion-caret"></span>
								<span class="sr-only">Expand</span>

								<?php echo wp_kses_post( __( '<span class="connection-settings-gloss-title">Sharing Preferences:</span>&nbsp;Manage the content shared with this connection.', 'openlab-connections' ) ); ?>
							</button>

							<div class="accordion-content">
								<div class="connection-setting">
									<label for="connection-<?php echo esc_attr( $connection->get_connection_id() ); ?>-categories"><?php esc_html_e( 'Include posts and comments from the following categories:', 'openlab-connections' ); ?></label>
									<select multiple id="connection-<?php echo esc_attr( $connection->get_connection_id() ); ?>-categories" class="connection-tax-term-selector">
										<option value="_all" <?php selected( 'all' === $selected_categories ); ?>><?php esc_html_e( 'All categories', 'openlab-connections' ); ?></option>

										<?php foreach ( $site_categories as $site_category ) : ?>
											<option value="<?php echo esc_attr( $site_category['id'] ); ?>" <?php selected( is_array( $selected_categories ) && in_array( $site_category['id'], $selected_categories, true ) ); ?>><?php echo esc_html( $site_category['name'] ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>

								<div class="connection-setting connection-setting-checkbox">
									<input type="checkbox" <?php checked( $connection_settings['exclude_comments'] ); ?> class="connection-setting-exclude-comments" id="connection-<?php echo esc_attr( $connection->get_connection_id() ); ?>-exclude-comments" name="connection-settings[content-type][comment]" value="1" /> <label for="connection-<?php echo esc_attr( $connection->get_connection_id() ); ?>-exclude-comments"><?php esc_html_e( 'Do not include comments', 'openlab-connections' ); ?>
								</div>

								<div class="connection-setting connection-setting-checkbox">
									<input type="checkbox" <?php checked( empty( $selected_categories ) ); ?> class="connection-setting-none" id="connection-<?php echo esc_attr( $connection->get_connection_id() ); ?>-none" /> <label for="connection-<?php echo esc_attr( $connection->get_connection_id() ); ?>-none"><?php esc_html_e( 'Do not share any content with this connection', 'openlab-connections' ); ?>
								</div>

								<?php if ( $group_status_text ) : ?>
									<p class="connection-private-group-notice is-hidden">
										<?php echo esc_html( $group_status_text ); ?>
									</p>
								<?php endif; ?>
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
</div>
