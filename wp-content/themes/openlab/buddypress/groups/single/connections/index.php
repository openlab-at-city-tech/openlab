<?php
// @todo Pagination.
$connections = \OpenLab\Connections\Connection::get( [ 'group_id' => bp_get_current_group_id() ] );
?>

<?php do_action( 'template_notices' ); ?>

<p>This feature connects related spaces on the Openlab. It is useful for sharing site activity with cohorts, collaborators, and across course sections. Visit <a href="tk">OpenLab Help</a> for more information.</p>

<?php if ( $connections ) : ?>
	<div class="connections-settings">
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
			?>
			<div class="connection-settings">
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
							Settings will go here.
						</div>
					</div>
				</div>

				<button class="disconnect-button" aria-label="<?php esc_attr_e( 'Disconnect', 'openlab-connections' ); ?>"><?php esc_html_e( 'Connected', 'openlab-connections' ); ?></button>
			</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p><?php esc_html_e( 'This group does not have any connections.', 'openlab-connections' ); ?></p>
<?php endif; ?>

