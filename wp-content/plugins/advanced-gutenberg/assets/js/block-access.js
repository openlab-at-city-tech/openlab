window.addEventListener('load', function () {
    var $ = jQuery;

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
});
