<?php echo $before_widget; ?>

<?php
if ($title) {
    echo $before_title . $title . $after_title;
}
?>

<?php if ($message): ?>

    <p class="message-wrapper"><?php echo $message; ?></p>

<?php endif; ?>

<p class="link-wrapper"><a href="<?php echo $url; ?>">OpenLab Gradebook</a></p>

<?php echo $after_widget; ?>
