<?php do_action( 'bp_before_member_home_content' ); ?>

    <?php openlab_bp_mobile_sidebar('members'); ?>
    <div class="col-sm-18 col-xs-24 members-single-home">
        <div id="openlab-main-content" class="content-wrapper">

<?php do_action( 'bp_before_member_body' );

		if ( bp_is_user_activity() || !bp_current_component() ) :
			cuny_student_profile();

		elseif ( bp_is_current_component( 'my-activity' ) ) :
			bp_get_template_part( 'members/single/my-activity' );

		elseif ( bp_is_user_blogs() ) :
			bp_get_template_part( 'members/single/blogs'    );

		elseif ( bp_is_user_friends() ) :
			bp_get_template_part( 'members/single/friends'  );

		elseif ( bp_is_user_groups() ) :
			bp_get_template_part( 'members/single/groups'   );

		elseif ( bp_is_user_messages() ) :
			bp_get_template_part( 'members/single/messages' );

		elseif ( bp_is_user_profile() ) :
			bp_get_template_part( 'members/single/profile'  );

		elseif ( bp_is_user_forums() ) :
			bp_get_template_part( 'members/single/forums'   );

		elseif ( bp_is_user_notifications() ) :
			bp_get_template_part( 'members/single/notifications' );

		elseif ( bp_is_user_settings() ) :
			bp_get_template_part( 'members/single/settings' );
                
                elseif (bp_current_action() == 'invite-new-members' || bp_current_action() == 'sent-invites') :
                    bp_get_template_part( 'members/single/invite-anyone' );
                
		// If nothing sticks, load a generic template
		else :
			bp_get_template_part( 'members/single/plugins'  );
                
		endif;

		do_action( 'bp_after_member_body' ); ?>
                        </div>
    </div>

<?php do_action( 'bp_after_member_home_content' ); ?>
    
<?php openlab_bp_sidebar('members'); ?>