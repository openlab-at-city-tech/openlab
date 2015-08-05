<?php
do_action('bp_before_group_forum_content');
if (bp_is_group_forum_topic_edit()) {
    bp_get_template_part('groups/single/forum/edit');
} elseif (bp_is_group_forum_topic()) {
    bp_get_template_part('groups/single/forum/topic');
} else {
    ?>
    <div class="row"><div class="col-md-17">
        <div class="submenu">
            <?php if (is_user_logged_in()) : ?>
                <div class="submenu-text pull-left bold">Discussion:</div>
                <ul class="nav nav-inline">
                <li class="new-topic"><a href="#post-new" class="show-hide-new"><?php _e('New Topic', 'buddypress') ?></a></li>
                </ul>
            <?php endif ?>
        </div></div>

            <?php do_action('bp_forums_directory_group_sub_types'); ?>

            <div class="col-sm-7 pull-right align-right">
                <form class="standard-form form-inline">
                <div class="form-group">
                <label for="forums-order-by"><?php _e('Order By:', 'buddypress'); ?></label>
                <select id="forums-order-by" class="form-control">
                    <option value="active"><?php _e('Last Active', 'buddypress'); ?></option>
                    <option value="popular"><?php _e('Most Posts', 'buddypress'); ?></option>
                    <option value="unreplied"><?php _e('Unreplied', 'buddypress'); ?></option>

                    <?php do_action('bp_forums_directory_order_options'); ?>
                </select>
                </div>
                </form>
            </div>
        </ul>
    </div>
    <div class="forums single-forum" role="main">
        <?php bp_get_template_part('forums/forums-loop'); ?>
    </div><!-- .forums.single-forum -->

    <?php
}

do_action('bp_after_group_forum_content');

if (!bp_is_group_forum_topic_edit() && !bp_is_group_forum_topic() && !bp_group_is_user_banned() && ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() )) :
    ?>
    <div id="new-topic-post">
        <form action="" method="post" id="forum-topic-form" class="standard-form form-panel">


            <div class="panel panel-default">
                <div class="panel-heading semibold"><?php _e('Post a New Topic:', 'buddypress') ?></div>
                <div class="panel-body">
            <?php do_action('bp_before_group_forum_post_new') ?>

            <?php if (bp_groups_auto_join() && !bp_group_is_member()) : ?>
                <p><?php _e('You will auto join this group when you start a new topic.', 'buddypress') ?></p>
            <?php endif; ?>

            <p id="post-new"></p>

            <label><?php _e('Title:', 'buddypress') ?></label>
            <input class="form-control" type="text" name="topic_title" id="topic_title" value="" />

            <label><?php _e('Content:', 'buddypress') ?></label>
            <textarea class="form-control" name="topic_text" id="topic_text"></textarea>

            <label><?php _e('Tags (comma separated):', 'buddypress') ?></label>
            <input class="form-control" type="text" name="topic_tags" id="topic_tags" value="" />

            <?php do_action('bp_after_group_forum_post_new') ?>
            </div>
    </div>

            <div class="submit">
                <input class="btn btn-primary" type="submit" name="submit_topic" id="submit" value="<?php _e('Post Topic', 'buddypress') ?>" />
            </div>

            <?php wp_nonce_field('bp_forums_new_topic') ?>

        </form><!-- #forum-topic-form -->
    </div><!-- #new-topic-post -->

<?php endif; ?>
