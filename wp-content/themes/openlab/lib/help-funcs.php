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
function openlab_help_404_handler($redirect_url, $requested_url) {
    if (is_404() && strpos($requested_url, 'help')) {
        $redirect_url = site_url('blog/help/openlab-help');
        return $redirect_url;
    }
}

add_filter('redirect_canonical', 'openlab_help_404_handler', 10, 2);

/**
 * 	Loop for single help pages
 *
 */
function openlab_help_loop() {
    global $paged, $post;

    $post_id = get_the_ID();
    $hp_query = new WP_Query( array(
	'post_type' => 'help',
	'p' => $post_id,
    ) );

    while ( $hp_query->have_posts() ) : $hp_query->the_post();
        ?>

        <?php
        $help_cats = get_the_terms($post_id, 'help_category');

	if ( ! empty( $help_cats ) ) {

                sort($help_cats);

		if ( $help_cats[0]->parent == 0 ) {
			$parent_cat_name = $help_cats[0]->name;
			$parent_cat = $help_cats[0];
		} else {
			$parent_cat = get_term( $help_cats[0]->parent, 'help_category' );
			$parent_cat_name = $parent_cat->name;
		}
	}

	$back_next_nav = '';

	$prev_post = get_adjacent_post( false, '', true );
	$next_post = get_adjacent_post( false, '', false );

	$back_next_nav .= '<nav id="help-title-nav">';

	if ( $prev_post ) {
		$back_next_nav .= '<span class="nav-previous">';
		$back_next_nav .=   '<span class="fa fa-chevron-circle-left"></span>';
		$back_next_nav .=   sprintf( '<a href="%s">Back</a>', esc_url( get_permalink( $prev_post ) ) );
		$back_next_nav .= '</span>';
	}

	if ( $next_post ) {
		$back_next_nav .= '<span class="nav-previous">';
		$back_next_nav .=    sprintf( '<a href="%s">Next</a>', esc_url( get_permalink( $next_post ) ) );
		$back_next_nav .=    '<span class="nav-next fa fa-chevron-circle-right"></span>';
		$back_next_nav .= '</span>';
	}

	$back_next_nav .= '</nav><!-- #nav-single -->';

        ?>

        <?php if ($help_cats): ?>
            <h1 class="entry-title help-entry-title"><a class="no-deco" href="<?php echo get_term_link($parent_cat); ?>"><?php echo $parent_cat_name; ?></a>
                <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="print-link pull-right hidden-xs"><a class="print-page" href="#"><span class="fa fa-print"></span> Print this page</a></span></h1>
            <?php $this_term = openlab_get_primary_help_term_name(); ?>
            <div id="help-title"><h2 class="page-title">
		    Topics:
                    <?php if ($this_term->parent != 0): ?>
                        <a class="regular" href="<?php echo get_term_link($this_term) ?>"><?php echo $this_term->name; ?></a> |
                    <?php endif; ?>
                    <span><?php the_title(); ?></span> |
		    <?php echo $back_next_nav; ?>
	   </h2></div>
        <?php elseif ($post->post_name == "openlab-help"): ?>
            <h1 class="entry-title"><?php echo the_title(); ?>
                <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </h1>
            <div id="help-title"><h2 class="page-title"><?php _e('Do you have a question? You\'re in the right place!', 'buddypress') ?></h2></div>
        <?php else: ?>
            <h1 class="entry-title"><?php echo the_title(); ?>
                <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </h1>
        <?php endif; ?>

        <div class="entry-content">
            <?php the_content(); ?>
            <?php echo ( $post->post_name == 'openlab-help' || $post->post_name == 'contact-us' ? '' : openlab_get_help_tag_list( $post_id ) ); ?>
        </div>

        <?php echo ($post->post_name == 'openlab-help' || $post->post_name == 'contact-us' ? '' : openlab_help_navigation()); ?>

    <?php endwhile; // end of the loop.   ?>

    <?php
}

//end openlab_help_loop()

function openlab_get_help_tag_list($id) {

    $terms = get_the_term_list($id, 'help_tags', '', ', ', '');
    $term_list = '<div id="help-identity">'
            . '<div class="help-tags">Tags: ' . ($terms ? $terms : 'None assigned') . '</div>'
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


    $tags_query = new WP_Query($args);
    ?>

    <h1 class="parent-cat entry-title">Tag Archive for: "<?php echo $parent_cat_name; ?>"
        <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button></h1>

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


    $help_query = new WP_Query($args);
    ?>

    <?php if ($parent_term->parent == 0): ?>
        <h1 class="parent-cat entry-title"><?php echo $parent_cat_name; ?>
            <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </h1>
	<div id="help-title">
	    <h2 class="page-title"><span>Topics</span></h2>
	</div>
        <?php
    else:
        $head_term = get_term_by('id', $parent_term->parent, 'help_category');
        ?>
        <h1 class="parent-cat entry-title"><a class="no-deco" href="<?php echo get_term_link($head_term) ?>"><?php echo $head_term->name ?></a>
            <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </h1>
	<div id="help-title">
	    <h2 class="page-title">
                Topics: <span><?php echo esc_html( $parent_term->name ) ?></span>
            </h2>
	</div>
    <?php
    endif;
    ?>

    <?php if ( $help_query->have_posts() ) : ?>
    <div class="col-md-12">
        <div class="child-cat-container">
            <h2 class="child-cat child-cat-num-0"><?php echo $parent_cat_name ?></h2>
	    <ul>
	    <?php
	    while ($help_query->have_posts()) : $help_query->the_post();

		$post_id = get_the_ID();
		?>
		<li>
		    <h3 class="help-title no-margin no-margin-bottom"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
		    <div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>
		</li>

		<?php
	    endwhile; // end of the loop.
	    wp_reset_query();
	    ?>
	    </ul>
	</div>
    </div>
    <?php endif; ?>

    <?php
    // Now get child cats and sort into two arrays for the two columns.
    $child_cats = get_categories(array('child_of' => $parent_term->term_id, 'taxonomy' => 'help_category'));
    $cols = array( 'left' => array(), 'right' => array() );
    $current_col = 'left';
    foreach ( $child_cats as $child_cat ) {
        $cols[ $current_col ][] = $child_cat;
	$current_col = 'left' === $current_col ? 'right' : 'left';
    }

    $count = 0;

    foreach ( $cols as $col_name => $col_cats ) {
	    echo '<div class="col-md-12">';
	    foreach ( $col_cats as $child ) {
		$child_cat_id = $child->cat_ID;
		echo '<div class="child-cat-container child-cat-container-' . intval( $child_cat_id ) . '">';
		echo '<h2 class="child-cat child-cat-num-' . $count . '"><a href="' . get_term_link($child) . '">' . $child->name . '</a></h2>';

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
		$child_query = new WP_Query($args); //new WP_Query($args);

		echo '<ul>';
		while ($child_query->have_posts()) : $child_query->the_post();
		    ?>
		    <li>
			    <h3 class="help-title no-margin no-margin-bottom"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
			    <div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ', ''); ?></div>
		    </li>
		    <?php
		endwhile; // end of the loop.
		echo '</ul>';
		wp_reset_query();
		?>

		<?php
		$count++;
		echo '</div>';
	    }//ecnd child_cats for each
	    echo '</div>';
    }
    ?>

    <div style="clear:both;"></div>

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


    $cat_query = new WP_Query($args);
    ?>

    <h1 class="parent-cat entry-title">Glossary
        <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button></h1>
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
