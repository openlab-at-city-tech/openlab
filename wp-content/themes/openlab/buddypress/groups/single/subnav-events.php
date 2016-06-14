<div class="submenu no-ajax" role="navigation">
    <div class="submenu-text pull-left bold">Calendar:</div>
    <ul class="nav nav-inline">
        <?php bp_get_options_nav(buddypress()->groups->current_group->slug . '_events'); ?>
        <?php if (openlab_eo_is_event_detail_screen()): ?>

            <?php $event_obj = openlab_eo_get_single_event_query_obj(); ?>

            <?php if (isset($event_obj->post_title)): ?>
                <li id="single-event-name" class="current-menu-item"><span><?php echo $event_obj->post_title ?></span></li>
                    <?php endif; ?>

        <?php endif; ?>
    </ul>
</div>