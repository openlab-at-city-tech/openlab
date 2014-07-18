<?php if ('invites' == bp_current_action()) : ?>
    <div class="submenu"><div class="submenu-text">My Invitations: </div><?php echo openlab_my_invitations_submenu(); ?></div>
    <?php locate_template(array('buddypress/members/single/groups/invites.php'), true) ?>

<?php else : ?>

    <?php do_action('bp_before_member_groups_content') ?>

    <?php locate_template(array('buddypress/groups/groups-loop.php'), true) ?>

    <?php do_action('bp_after_member_groups_content') ?>

<?php endif; ?>
