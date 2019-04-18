<?php if ( class_exists( 'Meks_Instagram_Widget' ) ): ?>
    
    <?php

        $instagram_username = str_replace('@', '', johannes_get('footer', 'instagram') );
        $instagram_title = __johannes('instagram_follow') . ' ' . '<a href="https://www.instagram.com/'.$instagram_username.'" target="_blank">@'.$instagram_username.'</a>';
    
        the_widget(
                    'Meks_Instagram_Widget',
                    array(
                        'title' => ' ',
                        'username_hashtag' => johannes_get('footer', 'instagram'),
                        'photos_number' => 12,
                        'columns' => 3,
                        'photo_space' => 20,
                        'container_size' => 760,
                        'link_text' => '',
                    ),
                    array(
                        'before_widget' => '<div id="%1$s" class="johannes-section-instagram johannes-section has-arrows"><div class="container"><div class="johannes-bg-alt-1">',
                        'after_widget' => '</div></div></div>',
                        'before_title' => '<div class="section-head"><h3 class="section-title h2">' . $instagram_title,
                        'after_title' => '</h3></div>',
                    )
                ); 
    ?>

<?php else: ?>

    <?php if( current_user_can( 'manage_options' ) ): ?>
        <div class="container">
            <div class="johannes-empty-message">
                <p><?php echo wp_kses_post( sprintf( __( 'In order to use the Instagram feature, please install and activate <a href="%s">Meks Instagram Widget plugin </a>.', 'johannes' ), admin_url( 'themes.php?page=johannes-plugins' ) ) ); ?></p>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>