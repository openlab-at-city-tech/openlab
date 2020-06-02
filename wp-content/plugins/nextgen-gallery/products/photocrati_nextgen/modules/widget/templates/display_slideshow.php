<?php
echo $before_widget;
if ($title)
    echo $before_title . $title . $after_title; ?>
<div class="ngg_slideshow widget">
    <?php echo $out; ?>
</div>
<?php echo $after_widget; ?>
