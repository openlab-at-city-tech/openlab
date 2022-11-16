jQuery(function ($) {
    'use strict';

    $('.bookly-js-copy-to-clipboard')
        .find('label, span').on('click', function () {
            let range = document.createRange();
            range.selectNodeContents($(this).closest('div').find('span').get(0));
            let sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }).end()
        .find('a').on('click', function (e) {
            e.preventDefault();
            const text = $(this).prev('span').html();
            const $temp = $('<input/>');
            const $button = $(this);
            const $copied = $button.next('small');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            $button.hide();
            $copied.show();
            setTimeout(function () {
                $copied.hide();
                $button.show();
            }, 1000);
        })
    ;
});