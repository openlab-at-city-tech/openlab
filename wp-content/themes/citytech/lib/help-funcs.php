<?php
/**
 * 	Help post type functions
 *
 */

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
        } else {
            $parent_cat = get_term($help_cats[0]->parent, 'help_category');
            $parent_cat_name = $parent_cat->name;
        }
        ?>

        <?php if ($help_cats): ?>
            <h1 class="entry-title"><a href="<?php echo get_term_link($parent_cat); ?>"><?php echo $parent_cat_name; ?></a></h1>
            <?php $this_term = openlab_get_primary_help_term_name(); ?>
            <div id="help-title"><h2 class="page-title"><a href="<?php echo get_term_link($this_term) ?>"><?php echo $this_term->name; ?></a> | <?php the_title(); ?></h2></div>
        <?php elseif ($post->post_name == "openlab-help"): ?>
            <h1 class="entry-title"><?php echo the_title(); ?></h1>
            <div id="help-title"><h2 class="page-title"><?php _e('Do you have a question? You\'re in the right place!', 'buddypress') ?></h2></div>
        <?php else: ?>
            <h1 class="entry-title"><?php echo the_title(); ?></h1>       
        <?php endif; ?>

        <?php //print this page button - this is going to be absolutely positioned for now ?>
        <div class="print-page"><input type="button" value="Print this page"
                                       onclick="window.print();
                                               return false;" /></div>

        <?php openlab_help_navigation('top'); ?>

        <div class="entry-content">
            <?php the_content(); ?>
            <?php echo openlab_get_help_tag_list($post_id); ?>
        </div>

        <?php openlab_help_navigation(); ?>

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
    );


    $temp = $wp_query;
    $wp_query = null;
    $wp_query = query_posts($args); //new WP_Query($args); 
    ?> 	

    <h1 class="parent-cat">Tag Archive for: "<?php echo $parent_cat_name; ?>"</h1>

    <?php
    while (have_posts()) : the_post();

        $post_id = get_the_ID();
        ?>

        <h3 class="entry-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="cat-list">category: <?php echo get_the_term_list($post_id, 'help_category', '', ', ', ''); ?></div>
        <div class="help-tags">tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>

        <?php
    endwhile; // end of the loop. 
    wp_reset_query();
    ?>

    <a href="#help-top">Go To Top</a>

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
    );


    $temp = $wp_query;
    $wp_query = null;
    $wp_query = query_posts($args); //new WP_Query($args); 
    ?> 	

    <?php if ($parent_term->parent == 0): ?>
    <h1 class="parent-cat"><?php echo $parent_cat_name; ?></h1>
    <?php else: 
        $head_term = get_term_by('id',$parent_term->parent,'help_category');
    $child_title = '<h1 class="parent-cat"><a href="'.get_term_link($head_term).'">'.$head_term->name.'</a></h1>';
    $child_title .= '<h2 class="child-cat child-cat-num-0">'. $parent_cat_name .'</h2>';
    echo $child_title;
    endif; ?>

    <?php
    while (have_posts()) : the_post();

        $post_id = get_the_ID();
        ?>

        <h3 class="entry-title help-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
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
        echo '<h2 class="child-cat child-cat-num-' . $count . '">' . $child->name . '</h2>';

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
        );
        $temp = $wp_query;
        $wp_query = null;
        $wp_query = query_posts($args); //new WP_Query($args);

        while (have_posts()) : the_post();
            ?>

            <h3 class="entry-title help-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>

            <?php
        endwhile; // end of the loop. 
        wp_reset_query();
        ?>

        <?php
        $count++;
    }//ecnd child_cats for each 
    ?>

    <a href="#help-top">Go To Top</a>

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
    );


    $temp = $wp_query;
    $wp_query = null;
    $wp_query = query_posts($args); //new WP_Query($args); 
    ?> 	

    <h1 class="parent-cat">Glossary</h1>
    <div class="glossary-description"><p><?php echo $parent_term->description; ?></p></div>

    <?php
    while (have_posts()) : the_post();

        $post_id = get_the_ID();
        ?>

        <div class="glossary-wrapper">
            <h3 class="glossary-title"><?php the_title(); ?></h3>
            <div class="glossary-entry"><?php the_content(); ?></div>
            <div class="clearfloat"></div>
        </div><!--glossary-wrapper-->

        <?php
    endwhile; // end of the loop. 
    wp_reset_query();
    ?>

    <a href="#help-top">Go To Top</a>

    <?php
}

//end openlab_help_loop()