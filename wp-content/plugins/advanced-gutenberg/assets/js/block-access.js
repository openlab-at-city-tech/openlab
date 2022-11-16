window.addEventListener('load', function () {
    var $ = jQuery;

    // Output categories and blocks
    advgbGetBlocks( advgbCUserRole.access.inactive_blocks, '#advgb_access_nonce_field', '#blocks_list_access' );

    // Toggle blocks list in category when click category title
    $('.category-block .category-name').unbind('click').click(function () {
        var categoryWrapper = $(this).closest('.category-block');

        if (categoryWrapper.hasClass('collapsed')) {
            categoryWrapper.removeClass('collapsed');
        } else {
            categoryWrapper.addClass('collapsed');
        }
    });

    // Search blocks function
    $('.blocks-search-input').on('input', function () {
        var searchKey = $(this).val().trim().toLowerCase();

        $('.block-access-item .block-title').each(function () {
            var blockTitle = $(this).text().toLowerCase().trim(),
                blockItem = $(this).closest('.block-access-item');

            if (blockTitle.indexOf(searchKey) > -1) {
                blockItem.show();
            } else {
                blockItem.hide();
            }
        });
    });

    // On change user role dropdown
    $('#user_role').on( 'change', function(){
        window.location = 'admin.php?page=advgb_main&user_role=' + $(this).val();
    });

    // Check/Uncheck all
    $('#toggle_all_blocks').click(function () {
        $('.block-item-editable input').prop('checked', $(this).prop('checked'));
        saveButtonStatus();
    });

    // Enable save when at least one block is enabled
    $('.block-item-editable input').click(function () {
        saveButtonStatus();
    });

    // Show warning and disable save button if all blocks are disabled
    var saveButtonStatus = function() {
        if( $('.block-item-editable input:checked').length === 0 ) {
            $('.advgb-enable-one-block-msg').show();
            $('.save-profile-button').prop('disabled', true);
        } else {
            $('.advgb-enable-one-block-msg').hide();
            $('.save-profile-button').prop('disabled', false);
        }
    }
});
