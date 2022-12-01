<?php get_header(); ?>
    <div class="search-header archive-header">
        <h1 class="post-title">
            <?php
            global $wp_query;
            $total_results = $wp_query->found_posts;
            $s             = htmlentities( $s );
            if ( $total_results ) {
                printf( esc_html( _n( '%1$d search result for "%2$s"', '%1$d search results for "%2$s"', $total_results, 'period' ) ), $total_results, $s );
            } else {
                printf( esc_html__( 'No search results for "%s"', 'period' ), $s );
            }
            ?>
        </h1>
    </div>
    <div id="loop-container" class="loop-container">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                get_template_part( 'content', 'archive' );
            endwhile;
        endif;
        ?>
    </div>

<?php the_posts_pagination();

// only display bottom search bar if there are search results
$total_results = $wp_query->found_posts;
if ( $total_results ) {
    ?>
    <div class="search-bottom">
        <p><?php esc_html_e( "Can't find what you're looking for?  Try refining your search:", "period" ); ?></p>
        <?php get_search_form(); ?>
    </div>
<?php }

get_footer();