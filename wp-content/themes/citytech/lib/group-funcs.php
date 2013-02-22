<?php 
/**
* Library of group-related functions
*
*/

/**
* This function consolidates the group privacy settings in one spot for easier updating
*
*/

function openlab_group_privacy_settings($group_type)
{ 	
	global $bp;
	?>
	<h4><?php _e( 'Privacy Settings', 'buddypress' ); ?></h4>
	<?php if ($bp->current_action == 'admin' || openlab_is_portfolio()): ?>
	<h5><?php _e( ucfirst($group_type).' Profile')?></h5>
    <?php endif; ?>
    
    <?php if ($bp->current_action == 'create'): ?>
    <p id="privacy-intro"><?php _e('To change these settings later, use the '.$group_type.' Profile Settings page.','buddypress'); ?></p>
    <?php else: ?>
	<p><?php _e('These settings affect how others view your '.ucfirst($group_type).' Profile.') ?></p>
    <?php endif; ?>
    
	<div class="radio">

		<?php /* Portfolios get different copy */ ?>
		<?php if ( openlab_is_portfolio() ) : ?>
			<label>
				<input type="radio" name="group-status" value="public" <?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked" <?php } else { bp_group_show_status_setting('public'); } ?>/>
				<strong><?php _e( 'This is a public '.ucfirst($group_type), 'buddypress' ) ?></strong>
				<ul>
					<li><?php _e( 'This '.ucfirst($group_type).' Profile and related content and activity will be visible to the public.', 'buddypress' ) ?></li>
                                    <li><?php _e( 'This '.ucfirst($group_type).' will be listed in the '.ucfirst($group_type).'s directory, search results, or OpenLab home page.', 'buddypress' ) ?></li>
				</ul>
			</label>

			<label>
				<input type="radio" name="group-status" value="hidden"<?php bp_group_show_status_setting('hidden') ?> />
				<strong><?php _e( 'This is a hidden ' .ucfirst($group_type).'.', 'buddypress' ) ?></strong>
				<ul>
					<li><?php _e( 'This '.ucfirst($group_type).' Profile will only be visible to members of your Access List.', 'buddypress' ) ?></li>
                                        <li><p id="privacy-intro"><?php _e('Note: Use the '.ucfirst($group_type).' Profile Settings to add members to your Access List.','buddypress'); ?></p></li>
                                        <li><?php _e( 'This '.ucfirst($group_type).' Profile will NOT be listed in the '.ucfirst($group_type).'s directory, search results, or OpenLab home page.', 'buddypress' ) ?></li>
                                        <li><?php _e( 'The link to this '.ucfirst($group_type).' Profile and Site will not be publicly visible on your OpenLab Profile.', 'buddypress' ) ?></li>
				</ul>
			</label>
		<?php else : /* All other group types */ ?>
			<label>
				<input type="radio" name="group-status" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked" <?php } else { bp_group_show_status_setting('public'); } ?> />
				<strong><?php _e( 'This is a public '.ucfirst($group_type), 'buddypress' ) ?></strong>
				<ul>
					<li><?php _e( 'This '.ucfirst($group_type).' Profile and related content and activity will be visible to the public.', 'buddypress' ) ?></li>
                                    <li><?php _e( 'This '.ucfirst($group_type).' will be listed in the '.ucfirst($group_type).'s directory, search results, and may be displayed on the OpenLab home page.', 'buddypress' ) ?></li>
                                    <li><?php _e( 'Any OpenLab member may join this '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
				</ul>
			</label>

			<label>
				<input type="radio" name="group-status" value="private"<?php bp_group_show_status_setting('private') ?> />
				<strong><?php _e( 'This is a private '.ucfirst($group_type), 'buddypress' ) ?></strong>
				<ul>
					<li><?php _e( 'This '.ucfirst($group_type).' Profile and related content and activity will only be visible to members of the group.', 'buddypress' ) ?></li>
                                    <li><?php _e( 'This '.ucfirst($group_type).' will be listed in the ' .ucfirst($group_type).' directory, search results, and may be displayed on the OpenLab home page.', 'buddypress' ) ?></li>
                                    <li><?php _e( 'Only OpenLab members who request membership and are accepted may join this '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
				</ul>
			</label>

			<label>
				<input type="radio" name="group-status" value="hidden"<?php bp_group_show_status_setting('hidden') ?> />
				<strong><?php _e( 'This is a hidden ' .ucfirst($group_type).'.', 'buddypress' ) ?></strong>
				<ul>
					<li><?php _e( 'This '.ucfirst($group_type).' Profile, related content and activity will only be visible only to members of the '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
                                        <li><?php _e( 'This '.ucfirst($group_type).' Profile will NOT be listed in the '.ucfirst($group_type).' directory, search results, or OpenLab home page.', 'buddypress' ) ?></li>
                                        <li><?php _e( 'Only OpenLab members who are invited may join this '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
				</ul>
			</label>
		<?php endif ?>
	</div>

	<?php /* Site privacy markup */ ?>


	<?php if ( $site_id = openlab_get_site_id_by_group_id() ) : ?>
		<h4><?php _e( ucfirst($group_type).' Site')?></h4>
		<p><?php _e('These settings affect how others view your '.ucfirst($group_type).' Site.') ?></p>
		<?php openlab_site_privacy_settings_markup( $site_id ) ?>
	<?php endif ?>

	<?php if ($bp->current_action == 'admin'): ?>
    	<?php do_action( 'bp_after_group_settings_admin' ); ?>
    	<p><input type="submit" value="<?php _e( 'Save Changes', 'buddypress' ) ?> &rarr;" id="save" name="save" /></p>
		<?php wp_nonce_field( 'groups_edit_group_settings' ); ?>
	<?php elseif ($bp->current_action == 'create'): ?>
    	<?php wp_nonce_field( 'groups_create_save_group-settings' ) ?>
    <?php endif; 
}