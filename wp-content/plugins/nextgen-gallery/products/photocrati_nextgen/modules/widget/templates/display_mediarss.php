<?php echo $before_widget . $before_title . $title . $after_title; ?>
<ul class='ngg-media-rss-widget'>
    <?php if ($instance['show_global_mrss']) { ?>
        <li>
            <?php echo $self->get_mrss_link(nggMediaRss::get_mrss_url(),
                                            $instance['show_icon'],
                                            strip_tags(stripslashes($instance['mrss_title'])),
                                            stripslashes($instance['mrss_text'])); ?>
        </li>
    <?php } ?>
</ul>
<?php echo $after_widget; ?>
