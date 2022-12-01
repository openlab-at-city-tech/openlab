<?php
if ( is_page_template( 'templates/full-width.php' ) ) {
    return;
}
if ( function_exists('is_cart') ) {
    if ( is_cart() || is_checkout() || is_account_page() ) {
        return false;
    }
}
if ( is_active_sidebar( 'primary' ) ) : ?>
    <aside class="sidebar sidebar-primary" id="sidebar-primary" role="complementary">
        <h2 class="screen-reader-text"><?php esc_html_e('Sidebar', 'period'); ?></h2>
        <?php dynamic_sidebar( 'primary' ); ?>
    </aside>
<?php endif;