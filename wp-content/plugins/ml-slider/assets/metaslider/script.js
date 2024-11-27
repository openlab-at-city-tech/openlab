jQuery(document).ready(function($){
    if ($('#wp-admin-bar-ms-main-menu').length) {
        var slideshow_ids = $('.ml-slider').map(function() {
            return this.id;
        }).toArray();
        var clean_slideshow_ids = [...new Set(slideshow_ids)];
    
        $.each(clean_slideshow_ids, function(index, item) {
            var slideshow_id = this.split('-');
            var id = slideshow_id[2];
            var title = $('#' + item).attr('aria-label');
            var html = '<li id="wp-admin-bar-all-slideshows-list" class="ms_admin_menu_item"><a class="ab-item" href="' +  wpData.baseUrl  + '/wp-admin/admin.php?page=metaslider&id=' + id + '" target="_blank">Edit ' + title + '</a></li>';
            $('#wp-admin-bar-ms-main-menu-default').append(html);
        });
    }

    //Accessibility
    $('.metaslider .flex-next, .metaslider .flex-prev, .metaslider .flex-control-nav li a, .metaslider .flex-pauseplay a').attr('role', 'button');
});
