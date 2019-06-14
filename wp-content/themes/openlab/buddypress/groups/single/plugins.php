<?php
/**
 * Group plugins - includes files
 *
 */
global $bp, $wp_query;
?>

<div id="single-course-body" class="plugins action-<?php echo $bp->current_action ?> component-<?php echo $bp->current_component ?><?php echo (openlab_eo_is_event_detail_screen() ? ' event-detail' : '') ?>">
    <div class="row submenu-row"><div class="col-md-24">
            <div class="submenu">
                <?php if ($bp->current_action == 'invite-anyone' || $bp->current_action == 'notifications') : ?>

                    <ul class="nav nav-inline">
                        <?php openlab_group_membership_tabs(); ?>
                    </ul>
                <?php elseif ($bp->current_action == 'docs'): ?>

                    <ul class="nav nav-inline">
                        <?php openlab_docs_tabs(); ?>
                    </ul>

                <?php elseif ($bp->current_action == 'files'): ?>

                    <div class="row">
                        <div class="submenu col-sm-17">
                            <ul class="nav nav-inline">
                                <li class="current-menu-item"><a href=""><?php _e('Files', 'bp-group-documents'); ?></a></li>
                            </ul>
                        </div>
                    </div>

                <?php elseif ($bp->current_component === 'events' || $bp->current_action === 'events'): ?>

                    <?php //do nothing - event sub nav is handled via template override in buddypress/groups/single/subnav-events.php ?>

                <?php else: ?>

                    <ul class="nav nav-inline">
                        <?php do_action('bp_group_plugin_options_nav'); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div></div>

    <div id="item-body">

        <?php do_action('bp_before_group_plugin_template'); ?>

        <div class="entry-content">
            <?php do_action('bp_template_content'); ?>
        </div>

        <?php do_action('bp_after_group_plugin_template'); ?>
    </div><!-- #item-body -->
</div>
