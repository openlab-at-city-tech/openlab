<?php
if (!defined('ABSPATH')) {
    die('No direct access.');
}
?>
<div id="slideshows-list">
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo esc_html_e('Slideshows', 'ml-slider'); ?></h1> <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=metaslider_create_slider'), 'metaslider_create_slider'));?>" class="page-title-action"><?php echo esc_html_e('Add New', 'ml-slider'); ?></a>
        <?php
            if (isset($_REQUEST['slideshows'])) {
                if(is_array($_REQUEST['slideshows'])) {
                    $count = count($_REQUEST['slideshows']);
                } else {
                    $count = 1;
                }
                if ('delete' === $listtable->current_action()) {
                    echo '<div class="updated below-h2" id="message"><p>' . sprintf( esc_html( _n('%d slideshow moved to the Trash.', '%d slideshows moved to the Trash.', $count), 'ml-slider'), esc_html($count) ) . '</p></div>';
                }
                if ('restore' === $listtable->current_action()) {
                    echo '<div class="updated below-h2" id="message"><p>' . sprintf( esc_html( _n('%d slideshow restored from the Trash.', '%d slideshows restored from the Trash.', $count), 'ml-slider'), esc_html($count) ) . '</p></div>';
                }
                if ('permanent' === $listtable->current_action()) {
                    echo '<div class="updated below-h2" id="message"><p>' . sprintf( esc_html( _n('%d slideshow permanently deleted.', '%d slideshows permanently deleted.', $count), 'ml-slider'), esc_html($count) ). '</p></div>';
                }
            }

            if (isset($_REQUEST['delete_all'])) {
                echo '<div class="updated below-h2" id="message"><p>' . esc_html('Slideshows permanently deleted.', 'ml-slider') .'</p></div>';
            }
        ?>
        <hr class="wp-header-end">
        <h2 class="screen-reader-text">Filter posts list</h2>
        <?php $listtable->views(); ?>
        <form id="metaslider-list-form" method="POST">
            <?php
                $listtable->search_box(esc_html__('Search', 'ml-slider'), 'search_slideshow');
                if(isset($_REQUEST['page'])) {
            ?>
                    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php
                }
                wp_nonce_field('metaslider_search_slideshows', 'search_wpnonce');
                $listtable->display();
            ?> 
        </form>
    </div>
</div>
