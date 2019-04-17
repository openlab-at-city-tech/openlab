<?php if ( johannes_get( 'archive_content' ) ): ?>
    <div class="section-head no-separator section-vertical-margin">
        
        <?php echo johannes_breadcrumbs(); ?>

        <?php if ( johannes_get( 'archive_avatar' ) ) : ?>
            <div class="section-avatar"><?php echo johannes_get( 'archive_avatar' ); ?></div>
        <?php endif; ?>

        <?php if ( johannes_get( 'archive_title' ) ) : ?>
            <h1 class="section-title"><?php echo johannes_get( 'archive_title' ); ?></h1>
        <?php endif; ?>
        <?php if ( johannes_get( 'archive_subnav' ) ) : ?>
            <span class="section-subnav social-icons-clean">
                <?php echo johannes_get( 'archive_subnav' ); ?>
            </span>
        <?php endif; ?>
        <?php if ( johannes_get( 'archive_meta' ) ) : ?>
            <span class="section-meta">
                <?php echo johannes_get( 'archive_meta' ); ?>
                <?php echo __johannes( 'articles' ); ?>
            </span>
        <?php endif; ?>
        <?php if (  johannes_get( 'archive_description' ) ) : ?>
             <div class="section-description johannes-content"><?php echo wpautop( johannes_get( 'archive_description' ) ); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
