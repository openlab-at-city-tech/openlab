<?php
    $authors_query = new WP_User_Query( johannes_get( 'authors_query_args' ) );
    $authors = $authors_query->get_results();
?>

<?php if ( !empty( $authors ) ) : ?>
    <?php foreach ( $authors as $author ) : ?>
        <div class="d-flex johannes-section author-list">
            <div class="author-avatar ml-0">
                <?php echo get_avatar( $author->ID, 100 ); ?>
            </div>
            <div class="author-content">
                <h4 class="mt-0"><a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>"><?php echo get_the_author_meta( 'display_name', $author->ID ); ?></a></h4>
                <div class="author-description social-icons-clean">
                    <?php echo wpautop( get_the_author_meta( 'description', $author->ID ) ); ?>
                    <?php echo johannes_get_author_links( $author->ID, false ); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
