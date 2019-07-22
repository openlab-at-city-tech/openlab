jQuery(document).ready(function ($) {
    $('.advgb-toc-header').unbind('click').click(function () {
        $(this).toggleClass('collapsed');
        $(this).closest('.wp-block-advgb-summary').find('.advgb-toc').slideToggle();
    })
});