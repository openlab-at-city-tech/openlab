<?php do_action('bp_before_member_friend_requests_content') ?>

<?php if (bp_has_members('include=' . bp_get_friendship_requests() . '&per_page=0')) : ?>


    <div id="friend-list" class="item-list group-list">
        <?php while (bp_members()) : bp_the_member(); ?>

            <div class="group-item col-sm-8 col-xs-12">
                <div class="group-item-wrapper">
                    <div class="row info-row">
                        <div class="item-avatar col-sm-9 col-xs-7">
                            <a href="<?php bp_member_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'member', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo esc_attr( sprintf( 'Profile photo of %s', bp_get_member_name() ) ); ?>"/></a>
                        </div>

                        <div class="item col-sm-15 col-xs-17">
                            <h5 class="item-title"><a class="no-deco" href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a></h5>

                            <?php if (bp_get_member_latest_update()) : ?>

                                <span class="update"> - <?php bp_member_latest_update('length=10') ?></span>

                            <?php endif; ?>

                            <div class="timestamp"><span class="fa fa-undo"></span> <?php bp_member_last_active() ?></div>
                        </div>
                        <?php do_action('bp_friend_requests_item') ?>

                        <div class="action">
                            <a class="button accept btn btn-primary link-btn btn-xs" href="<?php bp_friend_accept_request_link() ?>"><?php _e('Accept', 'buddypress'); ?></a> &nbsp;
                            <a class="button reject btn btn-primary link-btn btn-xs" href="<?php bp_friend_reject_request_link() ?>"><?php _e('Reject', 'buddypress'); ?></a>

                            <?php do_action('bp_friend_requests_item_action') ?>
                        </div>
                    </div>

                </div>
            </div>

        <?php endwhile; ?>
    </div>

    <?php do_action('bp_friend_requests_content') ?>

<?php else: ?>

    <div id="message" class="info group-list row">
        <div class="col-md-24">
            <p class="bold"><?php _e('You have no pending friendship requests.', 'buddypress'); ?></p>
        </div>
    </div>

<?php endif; ?>

<?php do_action('bp_after_member_friend_requests_content') ?>
