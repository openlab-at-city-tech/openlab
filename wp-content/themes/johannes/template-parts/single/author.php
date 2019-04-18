<?php if( get_the_author_meta( 'description' ) ): ?>
    <div class="johannes-author johannes-bg-alt-1 section-margin">
        <div class="author-avatar">
            <?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
        </div>
        <div class="author-content">
            <span class="text-small"><?php echo __johannes( 'written_by' );?></span>
            <h6><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author_meta( 'display_name' ); ?></a></h6>
            <div class="author-description social-icons-clean">
                <?php echo wpautop( get_the_author_meta( 'description' ) ); ?>
                <?php echo johannes_get_author_links( get_the_author_meta( 'ID' ), false ); ?>
            </div>
        </div>
    </div>
<?php endif; ?>