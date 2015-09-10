<?php /**
 * The Help Sidebar
 *
 */ ?>

<h2 class="sidebar-title hidden-xs">Help</h2>
<div class="sidebar-block hidden-xs">
<?php
$args = array(
    'theme_location' => 'helpmenu',
    'container' => 'div',
    'container_id' => 'help-menu',
    'menu_class' => 'sidebar-nav',
);
wp_nav_menu($args);
?>
</div>
<h2 class="sidebar-help-title support-team-title">Our Support Team</h2>
<div class="sidebar-block padded-block">
<div id="support-team">
    <div id="help-tags-copy"><p>The Support Team is here to answer all your OpenLab questions.</p></div>
    <?php
    $args = array(
        'name' => 'contact-us',
        'post_type' => 'help',
        'post_status' => 'publish',
        'numberposts' => 1
    );
    $my_posts = get_posts($args);

    if ($my_posts) {
        $post_id = $my_posts[0]->ID;
    }
    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => 'any',
        'post_parent' => $post_id
    );
    $attachments = get_posts($args);

    if ($attachments) {
        $i = 0;

        echo '<div id="team-thumbs" class="row">';
        foreach ($attachments as $attachment) {
            $thumb_class = "col-sm-12 col-xs-12 thumb-wrapper";

            echo '<div class="' . $thumb_class . '">';
            echo '<div class="team-thumb">';
            //use wordpress native thumbnail size for hard crop, then resize to fit container requirements
            $src = wp_get_attachment_image_src($attachment->ID, 'full');

            echo '<a href="' . bp_get_root_domain() . '/blog/help/contact-us">';
            echo '<img class="img-responsive" src="' . $src[0] . '" />';
            echo '</a>';
            echo '</div>';

            echo '<div class="team-name">';
            echo '<a href="' . bp_get_root_domain() . '/blog/help/contact-us">';
            echo $attachment->post_excerpt;
            echo '</a>';
            echo '</div>';
            echo '</div>';
            $i++;
        }//end for each
        echo '</div>';
    } //end if
    ?>
    <a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo bp_get_root_domain() . '/blog/help/contact-us'; ?>"><i class="fa fa-paper-plane-o"></i> Contact Us</a>
</div><!--support team-->
</div>

<h2 class="sidebar-help-title help-tags-title">Find a Help Topic With Tags</h2>
<div class="sidebar-block padded-block">
<div id="help-tags-copy"><p>Find answers throughout Help that correspond to the tags below:</p></div>
<div id="help-tags">
    <?php
    $args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hierarchical' => false,
    );


    $terms = get_terms(array('help_tags'),$args);

    $count = count($terms);
    if ($count > 0) {
        foreach ($terms as $term) {
            echo '<a href="' . get_term_link($term) . '" class="btn btn-default btn-primary link-btn tag-btn tag-count-' . $term->count . '">' . $term->name . '</a> ';
        }
    }
    ?>
    <div class="clearfloat"></div>
</div>
</div>

<h2 class="sidebar-title">Creative Commons</h2>
<div class="sidebar-block padded-block">
<p>Our help is licensed under Creative Commons copyright.<br />
<span class="italics cc-gloss">Attribution &mdash; NonCommercial &mdash; ShareAlike</span></p>
<div id="creative-commons">
    <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">Creative Commons</a>
</div>
</div>
