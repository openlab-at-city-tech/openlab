<?php
/**
 * Custom template for notifications page
 * Derived from BuddyPress Group Email Subscription
 */
?>

<div id="single-course-body" class="plugins">
    <div class="row"><div class="col-md-24">
            <div class="submenu">
                <ul class="nav nav-inline">
                    <?php openlab_group_membership_tabs(); ?>
                </ul>
            </div>

			<?php do_action( 'template_notices' ); ?>

            <?php openlab_ass_group_subscribe_settings(); ?>
        </div>
    </div>
</div>
