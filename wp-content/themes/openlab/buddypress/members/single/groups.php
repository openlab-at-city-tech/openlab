<?php if ('invites' == bp_current_action()) : ?>
    <div class="submenu"><div class="submenu-text">My Invitations: </div><?php echo openlab_my_invitations_submenu(); ?></div>
    <?php bp_get_template_part('members/single/groups/invites.php'); ?>

<?php else : ?>

    <?php do_action('bp_before_member_groups_content') ?>

    <?php bp_get_template_part('groups/groups-loop.php'); ?>

    <?php do_action('bp_after_member_groups_content') ?>

<?php endif; ?>
