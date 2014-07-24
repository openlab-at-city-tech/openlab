<div class="submenu"><div class="submenu-text">My Messages: </div><?php echo openlab_my_messages_submenu(); ?></div>
<?php if ('compose' == bp_current_action()) : ?>
    <?php bp_get_template_part('members/single/messages/compose.php'); ?>

<?php elseif ('view' == bp_current_action()) : ?>
    <?php bp_get_template_part('members/single/messages/single.php'); ?>

<?php else : ?>

    <?php do_action('bp_before_member_messages_content') ?>

    <div class="messages">
        <?php if ('notices' == bp_current_action()) : ?>
            <?php bp_get_template_part('members/single/messages/notices-loop.php'); ?>

        <?php else : ?>
            <?php bp_get_template_part('members/single/messages/messages-loop.php'); ?>

        <?php endif; ?>
    </div><!-- .messages -->

    <?php do_action('bp_after_member_messages_content') ?>

<?php endif; ?>
