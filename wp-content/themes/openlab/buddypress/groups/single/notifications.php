<?php
/**
 * Custom template for notifications page
 * Derived from BuddyPress Group Email Subscription
 */
?>

<div id="single-course-body" class="plugins">
    <div class="row">
		<div class="col-md-24">
            <div class="submenu">
				<div class="submenu-text pull-left bold"><h2>Membership<span aria-hidden="true">:</span></h2></div>
                <ul class="nav nav-inline">
                    <?php openlab_group_membership_tabs(); ?>
                </ul>
            </div>
        </div>
    </div>

	<div id="item-body">
		<?php do_action( 'template_notices' ); ?>

		<?php openlab_ass_group_subscribe_settings(); ?>
	</div>
</div>
