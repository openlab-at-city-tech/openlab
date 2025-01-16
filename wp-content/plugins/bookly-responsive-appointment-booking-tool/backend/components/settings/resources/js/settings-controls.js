jQuery(function ($) {
    'use strict';

    window.booklyInitCopyToClipboard = function($element) {
        $element
            .find('label, span').on('click', function() {
                let range = document.createRange();
                range.selectNodeContents($(this).closest('div').find('span').get(0));
                let sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            })
            .end()
            .find('a').on('click', function(e) {
                e.preventDefault();
                const text = $(this).prev('span').text();
                const $temp = $('<input/>', {type: 'text', value: text});
                const $button = $(this);
                const $copied = $button.next('small');
                $(this).append($temp);
                $temp.select();

                document.execCommand('copy');
                $temp.remove();
                $button.hide();
                $copied.show();
                setTimeout(function() {
                    $copied.hide();
                    $button.show();
                }, 1000);
            });
    }

    window.booklyInitCopyToClipboard($('.bookly-js-copy-to-clipboard'));
});