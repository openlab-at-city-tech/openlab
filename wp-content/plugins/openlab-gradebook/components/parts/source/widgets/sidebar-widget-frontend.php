<?php echo $before_widget; ?>

<?php
if ($title) {
    echo $before_title . $title . $after_title;
}
?>

<p class="link-wrapper"><a href="<?php echo $url; ?>"><?php echo $message; ?></a></p>

<?php echo $after_widget; ?>
