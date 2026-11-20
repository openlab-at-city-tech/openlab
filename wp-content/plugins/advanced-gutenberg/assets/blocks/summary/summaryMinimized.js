jQuery(document).ready(function ($) {
    // Accessibility: expose the collapse header as a disclosure button
    $('.advgb-toc-header').each(function (index) {
        var $header = $(this);
        var $toc = $header.closest('.wp-block-advgb-summary').find('.advgb-toc').first();
        if (!$toc.attr('id')) {
            $toc.attr('id', 'advgb-toc-' + (index + 1));
        }
        $header.attr({
            role: 'button',
            tabindex: '0',
            'aria-controls': $toc.attr('id'),
            'aria-expanded': $header.hasClass('collapsed') ? 'false' : 'true'
        });
    }).on('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    $('.advgb-toc-header').unbind('click').click(function () {
        $(this).toggleClass('collapsed');
        $(this).attr('aria-expanded', $(this).hasClass('collapsed') ? 'false' : 'true');
        $(this).closest('.wp-block-advgb-summary').find('.advgb-toc').slideToggle();
    })
});
