<?php if (bp_is_group_admin_page() && bp_group_is_visible()) : ?>
    <?php bp_get_template_part('groups/single/admin.php'); ?>

<?php elseif (bp_is_group_members() && bp_group_is_visible()) : ?>
    <?php bp_get_template_part('groups/single/members.php'); ?>

<?php elseif (bp_is_group_invites() && bp_group_is_visible()) : ?>
    <?php bp_get_template_part('groups/single/send-invites.php'); ?>

<?php elseif (bp_is_group_forum() && bp_group_is_visible()) : ?>
    <?php bp_get_template_part('groups/single/forum.php'); ?>

<?php elseif (bp_is_group_membership_request()) : ?>
    <?php bp_get_template_part('groups/single/request-membership.php'); ?>
<?php endif; ?>
