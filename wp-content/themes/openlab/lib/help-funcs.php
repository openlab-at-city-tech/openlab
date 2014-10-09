<?php
/**
 * 	Help post type functions
 *
 */

/**
 * This function catches any URL that returns a 404 *and* includes the word help in the URL string
 * See http://openlab.citytech.cuny.edu/redmine/issues/964 for more details
 * @param type $redirect_url
 * @param type $requested_url
 * @return type
 */

function openlab_help_404_handler($redirect_url,$requested_url){
    if (is_404() && strpos($requested_url,'help')){
        $redirect_url = site_url('blog/help/openlab-help');
        return $redirect_url;
    }
}

add_filter('redirect_canonical','openlab_help_404_handler',10,2);

/**
 * 	Loop for single help pages
 *
 */
function openlab_help_loop() {

    global $paged, $post;
    $args = array('post_type' => 'help',
        'p' => get_the_ID());
    $temp = $wp_query;
    $wp_query = null;
    $wp_query = new WP_Query($args);

    while (have_posts()) : the_post();
        ?>

        <?php
        $help_cats = get_the_terms($post_id, 'help_category');
        sort($help_cats);
        if ($help_cats[0]->parent == 0) {
            $parent_cat_name = $help_cats[0]->name;
            $parent_cat = $help_cats[0];
        } else {
            $parent_cat = get_term($help_cats[0]->parent, 'help_category');
            $parent_cat_name = $parent_cat->name;
        }
        ?>

        <?php if ($help_cats): ?>
<h1 class="entry-title"><a class="no-deco" href="<?php echo get_term_link($parent_cat); ?>"><?php echo $parent_cat_name; ?></a><span class="print-link pull-right"><a class="print-page" href="#"><span class="fa fa-print"></span> Print this page</a></span></h1>
            <?php $this_term = openlab_get_primary_help_term_name(); ?>
            <div id="help-title"><h2 class="page-title">
                    <?php if ($this_term->parent != 0): ?>
                    <a class="regular" href="<?php echo get_term_link($this_term) ?>"><?php echo $this_term->name; ?></a> |
                    <?php endif; ?>
                    <span><?php the_title(); ?></span></h2></div>
        <?php elseif ($post->post_name == "openlab-help"): ?>
            <h1 class="entry-title"><?php echo the_title(); ?></h1>
            <div id="help-title"><h2 class="page-title"><?php _e('Do you have a question? You\'re in the right place!', 'buddypress') ?></h2></div>
        <?php else: ?>
            <h1 class="entry-title"><?php echo the_title(); ?></h1>
        <?php endif; ?>

        <?php echo ($post->post_name == 'openlab-help' || $post->post_name == 'contact-us' ? '' : openlab_help_navigation('top')); ?>

        <div class="entry-content">
            <?php the_content(); ?>
            <?php echo ($post->post_name == 'openlab-help' || $post->post_name == 'contact-us' ? '' : openlab_get_help_tag_list($post_id)); ?>
        </div>

        <?php echo ($post->post_name == 'openlab-help' || $post->post_name == 'contact-us' ? '' : openlab_help_navigation()); ?>

    <?php endwhile; // end of the loop.  ?>

    <?php
}

//end openlab_help_loop()

function openlab_get_help_tag_list($id) {

    $terms = get_the_term_list($id, 'help_tags', '', ', ', '');
    $term_list = '<div id="help-identity">'
            . '<div class="help-tags">Tags: '.($terms ? $terms : 'None assigned').'</div>'
            . '</div>';

    return $term_list;
}

function openlab_help_tags_loop() {
    ?>

    <div id="help-top"></div>

    <?php
    //first display the parent category
    global $post;
    $parent_cat_name = single_term_title('', false);
    $term = get_query_var('term');
    $parent_term = get_term_by('slug', $term, 'help_tags');

    $args = array('tax_query' => array(
            array(
                'taxonomy' => 'help_tags',
                'field' => 'slug',
                'terms' => array($parent_term->slug),
                'operator' => 'IN'
            )
        ),
        'post_type' => 'help',
        'order' => 'ASC',
        'posts_per_page' => '-1',
    );


    $tags_query = new WP_Query( $args );
    ?>

    <h1 class="parent-cat entry-title">Tag Archive for: "<?php echo $parent_cat_name; ?>"</h1>

    <?php
    while ($tags_query->have_posts()) : $tags_query->the_post();

        $post_id = get_the_ID();
        ?>

        <h2 class="help-title cat-title no-margin no-margin-bottom"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="cat-list">Category: <?php echo get_the_term_list($post_id, 'help_category', '', ', ', ''); ?></div>
        <div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>

        <?php
    endwhile; // end of the loop.
    wp_reset_query();
    ?>

        <a class="pull-right" href="#help-top">Go To Top <span class="fa fa-angle-up"></span></a>

    <?php
}

//end openlab_help_loop()

/**
 * 	Loop for help caregory
 *
 */
function openlab_help_cats_loop() {
    ?>

    <div id="help-top"></div>

    <?php
    //first display the parent category
    global $post;
    $parent_cat_name = single_term_title('', false);
    $term = get_query_var('term');
    $parent_term = get_term_by('slug', $term, 'help_category');

    $args = array('tax_query' => array(
            array(
                'taxonomy' => 'help_category',
                'field' => 'slug',
                'include_children' => false,
                'terms' => array($parent_term->slug),
                'operator' => 'IN'
            )
        ),
        'post_type' => 'help',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'posts_per_page' => '-1',
    );


    $help_query = new WP_Query( $args );
    ?>

    <?php if ($parent_term->parent == 0): ?>
    <h1 class="parent-cat entry-title"><?php echo $parent_cat_name; ?></h1>
    <?php else:
        $head_term = get_term_by('id',$parent_term->parent,'help_category');
    $child_title = '<h1 class="parent-cat entry-title"><a href="'.get_term_link($head_term).'">'.$head_term->name.'</a></h1>';
    $child_title .= '<h2 class="child-cat child-cat-num-0">'. $parent_cat_name .'</h2>';
    echo $child_title;
    endif; ?>

    <?php
    while ( $help_query->have_posts()) : $help_query->the_post();

        $post_id = get_the_ID();
        ?>

        <h2 class="help-title no-margin no-margin-bottom"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>

        <?php
    endwhile; // end of the loop.
    wp_reset_query();
    ?>

    <?php
    //now iterate through each child category
    $child_cats = get_categories(array('child_of' => $parent_term->term_id, 'taxonomy' => 'help_category'));
    $count = 0;

    foreach ($child_cats as $child) {
        $child_cat_id = $child->cat_ID;
        echo '<h2 class="child-cat child-cat-num-' . $count . '"><a href="'.get_term_link($child).'">' . $child->name . '</a></h2>';

        $args = array('tax_query' => array(
                array(
                    'taxonomy' => 'help_category',
                    'field' => 'slug',
                    'include_children' => false,
                    'terms' => array($child->slug),
                    'operator' => 'IN'
                )
            ),
            'post_type' => 'help',
            'orderby' => 'menu_order',
            'order' => 'ASC',
	    'posts_per_page' => '-1',
        );
        $child_query = null;
        $child_query = new WP_Query( $args ); //new WP_Query($args);

        while ($child_query->have_posts()) : $child_query->the_post();
            ?>

            <h2 class="help-title no-margin no-margin-bottom"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>

            <?php
        endwhile; // end of the loop.
        wp_reset_query();
        ?>

        <?php
        $count++;
    }//ecnd child_cats for each
    ?>

    <a class="pull-right" href="#help-top">Go To Top <span class="fa fa-angle-up"></span></a>

    <?php
}

//end openlab_help_loop()

/**
 * 	Loop for glossary caregory
 *
 */
function openlab_glossary_cats_loop() {
    ?>

    <div id="help-top"></div>

    <?php
    //first display the parent category
    global $post;
    $term = get_query_var('term');
    $parent_term = get_term_by('slug', $term, 'help_category');

    $args = array(
        'post_type' => 'help_glossary',
        'orderby' => 'menu_order',
        'order' => 'ASC',
	'posts_per_page' => '-1',
    );


    $cat_query = new WP_Query( $args );
    ?>

    <h1 class="parent-cat entry-title">Glossary</h1>
    <div class="glossary-description"><p><?php echo $parent_term->description; ?></p></div>

    <?php
    while ($cat_query->have_posts()) : $cat_query->the_post();

        $post_id = get_the_ID();
        ?>

        <div class="glossary-wrapper">
            <h2 class="help-title glossary-title no-margin no-margin-bottom"><?php the_title(); ?></h2>
            <div class="glossary-entry"><?php the_content(); ?></div>
            <div class="clearfloat"></div>
        </div><!--glossary-wrapper-->

        <?php
    endwhile; // end of the loop.
    wp_reset_query();
    ?>

    <a class="pull-right" href="#help-top">Go To Top <span class="fa fa-angle-up"></span></a>

    <?php
}

//end openlab_help_loop()
