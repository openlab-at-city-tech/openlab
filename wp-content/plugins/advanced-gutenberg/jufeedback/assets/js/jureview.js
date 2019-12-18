jQuery(document).ready(function($){
    if (typeof ajaxurl === "undefined") {
        ajaxurl = ju_review.ajaxurl;
    }

    $('.jureview-hide-review').click(function(e){
        var slug = ($(e.target).parents('.jureview-notice').attr('data-slug'));
        hide_review(slug);

    });

    $('.jureview-already-review').on('click', function (e) {
        e.preventDefault();
        var slug = ($(e.target).parents('.jureview-notice').attr('data-slug'));
        var linkreview = $(this).attr('href');

        window.open(linkreview);
        hide_review(slug);
    });

    function hide_review(slug) {
        $.ajax({
            url: ajaxurl,
            dataType: 'json',
            method: 'POST',
            data: {
                action: 'jureview_ajax_hide_review_' + slug,
                ajaxnonce: ju_review.token
            },
            success: function () {
                $('.jureview-notice[data-slug="' + slug + '"]').hide('fade');
            }
        });
    }
});