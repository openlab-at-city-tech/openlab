
<?php do_action('bp_before_group_invites_content') ?>

<?php if (bp_has_groups('type=invites&user_id=' . bp_loggedin_user_id())) : ?>

    <div id="group-invites" class="invites group-list item-list row">

        <?php while (bp_groups()) : bp_the_group(); ?>

            <div class="group-item col-xs-12">
                <div class="group-item-wrapper">
                    <div class="row info-row">
                        <div class="item-avatar alignleft col-xs-7">
                            <a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_group_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo esc_html(bp_get_group_name()); ?>"/></a>
                        </div>
                        <div class="item col-xs-17">
                            <p class="item-title h2"><a class="no-deco truncate-on-the-fly" href="<?php bp_group_permalink() ?>" data-basevalue="65" data-minvalue="20" data-basewidth="280"><?php bp_group_name() ?></a></p>

                            <div class="description-line">
                                <p class="truncate-on-the-fly" data-link="<?php echo bp_get_group_permalink() ?>" data-basevalue="100" data-basewidth="280"><?php echo bp_get_group_description_excerpt() ?></p>
                                <p class="original-copy hidden"><?php echo bp_get_group_description_excerpt() ?></p>
                            </div>

                            <?php do_action('bp_group_invites_item') ?>

                            <div class="action invite-member-actions">
                                <a class="button accept btn btn-primary link-btn no-margin no-margin-top" href="<?php bp_group_accept_invite_link() ?>"><?php _e('Accept', 'buddypress') ?><span class="sr-only"> invitation to <?php bp_group_name() ?></span></a> &nbsp;
                                <a class="button reject confirm btn btn-primary link-btn no-margin no-margin-top" href="<?php bp_group_reject_invite_link() ?>"><?php _e('Reject', 'buddypress') ?><span class="sr-only"> invitation to <?php bp_group_name() ?></span></a>

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
            <p class="bold"><?php _e('You have no outstanding group invites.', 'buddypress') ?></p>
        </div>
    </div>

<?php endif; ?>

<?php do_action('bp_after_group_invites_content') ?>
