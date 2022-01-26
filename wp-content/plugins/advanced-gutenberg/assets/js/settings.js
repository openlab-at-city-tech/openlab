jQuery(document).ready(function ($) {
    $('.ju-top-tabs .tab a').click(function () {
        var currentText = $(this).text().trim();
        $(this).closest('.ju-content-wrapper').find('.advgb-settings-header').text(currentText);
    });

    $('.advgb_qtip').qtip({
        content: {
            attr: 'data-qtip'
        },
        position: {
            my: 'top left',
            at: 'bottom bottom'
        }
    });

    $('.minicolors-input').minicolors('settings', {
        change: function() {
            jQuery(this).trigger('change');
        }
    }).attr('maxlength', '7');

    // Post default thumbnail selector
    $('#thumb_edit').click(function (e) {
        e.preventDefault();

        var media_frame;
        if (media_frame) {
            media_frame.open();
            return true;
        }

        media_frame = wp.media({
            title: 'Select an image',
            multiple: false,
            library: {type: 'image'}
        });

        media_frame.on('select', function () {
            var selection = media_frame.state().get('selection').first();
            var media_id = selection.id;
            var media_url = selection.attributes.url;

            $('#post_default_thumb_id').val(media_id);
            $('#post_default_thumb').val(media_url);
            $('.thumb-selected').attr('src', media_url);
        });

        media_frame.on('open', function () {
            var selection = media_frame.state().get('selection');
            var media_id = $('#post_default_thumb_id').val();
            var media = wp.media.attachment(parseInt(media_id));
            media.fetch();
            selection.add(media ? [media] : []);
        });

        media_frame.open();
    });

    // Post default thumbnail remove
    $('#thumb_remove').click(function (e) {
        e.preventDefault();
        var thumbImg = $('.thumb-selected');
        var thumbDefault = thumbImg.data('default');

        $('#post_default_thumb_id').val(0);
        $('#post_default_thumb').val(thumbDefault);
        thumbImg.attr('src', thumbDefault);
    });

    // Search block in blocks config tab
    $('.blocks-config-search').on('input', function () {
        var searchKey = $(this).val().trim().toLowerCase();

        $('.blocks-config-list .block-config-item .block-title').each(function () {
            var blockTitle = $(this).text().trim().toLowerCase();

            if (blockTitle.indexOf(searchKey) > -1) {
                $(this).closest('.block-config-item').show();
            } else {
                $(this).closest('.block-config-item').hide();
            }
        })
    });

    initBlockConfigButton();
});

function initBlockConfigButton() {
    var $ = jQuery;
    var { __, _x, _n, _nx } = wp.i18n;
    // Open the block config modal
    $('.blocks-config-list .block-config-item .block-config-button').unbind('click').click(function () {
        var blockName = $(this).data('block');
        blockName = blockName.replace('/', '-');
        var blockLabel = $(this).closest('.block-config-item').find('.block-title').text().trim();
        window.blockLabel = blockLabel;

        tb_show(__('Edit block', 'advanced-gutenberg') + ' ' + blockLabel + ' ' + __('default config', 'advanced-gutenberg'), 'admin.php?page=' + blockName + '&noheader=1&width=550&height=600&TB_iframe=1');
    })
}
