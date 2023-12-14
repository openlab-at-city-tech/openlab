jQuery(document).ready(function($){
    $('.ml-slider').each(function() {
        var slideshow_id = this.id.split('-');
        var id = slideshow_id[2];
        var title = $(this).attr('aria-label');
        var base_url = window.location.origin;
        var html = '<li id="wp-admin-bar-all-slideshows-list" class="ms_admin_menu_item"><a class="ab-item" href="' +  base_url  + '/wp-admin/admin.php?page=metaslider&id=' + id + '" target="_blank" tabindex="-1">Edit ' + title + '</a></li>';
        $('#wp-admin-bar-ms-main-menu-default').append(html);
    });
});
