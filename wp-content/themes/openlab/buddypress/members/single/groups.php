<?php if ('invites' == bp_current_action()) : ?>
    <?php echo openlab_submenu_markup('invitations'); ?>
    <?php bp_get_template_part('members/single/groups/invites'); ?>

<?php else : ?>

    <?php do_action('bp_before_member_groups_content') ?>

    <?php bp_get_template_part('groups/groups-loop'); ?>

    <?php do_action('bp_after_member_groups_content') ?>

<?php endif; ?>
