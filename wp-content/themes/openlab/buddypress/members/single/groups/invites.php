
<?php do_action( 'bp_before_group_invites_content' ) ?>

<?php if ( bp_has_groups( 'type=invites&user_id=' . bp_loggedin_user_id() ) ) : ?>

            <div id="group-invites" class="invites group-list item-list row">

		<?php while ( bp_groups() ) : bp_the_group(); ?>

                <div class="group-item col-xs-12">
                            <div class="group-item-wrapper">
                                <div class="row info-row">
                                    <div class="item-avatar alignleft col-xs-7">
                                        <a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_group_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo esc_html( bp_get_group_name() ); ?>"/></a>
                                    </div>
                                    <div class="item col-xs-16">
                                        <h2 class="item-title"><a class="no-deco" href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
                                        <?php
                                        $group_type = openlab_get_group_type();
                                        $group_id = bp_get_group_id();
                                        //course group type
                                        if ($group_type == 'course'):
                                            ?>

                                            <?php
                                            $admins = groups_get_group_admins($group_id);
                                            $faculty_id = $admins[0]->user_id;
                                            $first_name = ucfirst(xprofile_get_field_data('First Name', $faculty_id));
                                            $last_name = ucfirst(xprofile_get_field_data('Last Name', $faculty_id));
                                            $wds_faculty = $first_name . " " . $last_name;
                                            $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
                                            $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
                                            $wds_year = groups_get_groupmeta($group_id, 'wds_year');
                                            $wds_departments = groups_get_groupmeta($group_id, 'wds_departments');
                                            ?>
                                            <div class="info-line uppercase"><?php echo $wds_faculty; ?> | <?php echo openlab_shortened_text($wds_departments, 20); ?> | <?php echo $wds_course_code; ?> | <span class="bold"><?php echo $wds_semester; ?> <?php echo $wds_year; ?></span></div>
                                        <?php elseif ($group_type == 'portfolio'): ?>

                                            <div class="info-line"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></div>

                                        <?php endif; ?>

                                        <?php
                                        $len = strlen(bp_get_group_description());
                                        if ($len > 135) {
                                            $this_description = substr(bp_get_group_description(), 0, 135);
                                            $this_description = str_replace("</p>", "", $this_description);
                                            echo $this_description . '&hellip; <a href="' . bp_get_group_permalink() . '">See&nbsp;More</a></p>';
                                        } else {
                                            bp_group_description();
                                        }
                                        ?>

                                        <?php do_action('bp_group_invites_item') ?>

                                        <div class="action invite-member-actions">
                                            <a class="button accept btn btn-primary link-btn" href="<?php bp_group_accept_invite_link() ?>"><?php _e('Accept', 'buddypress') ?></a> &nbsp;
                                            <a class="button reject confirm btn btn-primary link-btn" href="<?php bp_group_reject_invite_link() ?>"><?php _e('Reject', 'buddypress') ?></a>

                                            <?php do_action('bp_group_invites_item_action') ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

		<?php endwhile; ?>
	</div>

<?php else: ?>

	<div id="message" class="info group-list row">
            <div class="col-md-24">
		<p class="bold"><?php _e( 'You have no outstanding group invites.', 'buddypress' ) ?></p>
            </div>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_group_invites_content' ) ?>
