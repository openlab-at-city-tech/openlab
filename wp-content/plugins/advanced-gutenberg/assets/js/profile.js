jQuery(document).ready(function ($) {
    // Switch top tab we will change title text and hide unneeded buttons
    $('#profiles-container .ju-top-tabs .tab a').unbind('click').click(function () {
        var currentText = $(this).text().trim();
        var currentHref = $(this).attr('href');

        $('.profile-header .header-title').text(currentText);

        if (currentHref.indexOf('users') > -1) {
            $('#update-list-btn').hide();
        } else {
            $('#update-list-btn').show();
        }
    });

    if ($('.ju-top-tabs a.link-tab.active').attr('href').indexOf('users') > -1) {
        $('#update-list-btn').hide();
    }

    // Click update blocks list button
    $('#update-list-btn').unbind('click').click(function () {
        var willUpdate = confirm('Make sure everything is saved before updating. Continue?');
        if (willUpdate) {
            $(this).find('i').addClass('rotating');
            $(this).find('span').text('Refreshing...');
            window.location.href += '&update_blocks_list=true';
        }
    });

    // Toggle blocks list in category when click category title
    $('.category-block .category-name').unbind('click').click(function () {
        var categoryWrapper = $(this).closest('.category-block');

        if (categoryWrapper.hasClass('collapsed')) {
            categoryWrapper.removeClass('collapsed');
        } else {
            categoryWrapper.addClass('collapsed');
        }
    });

    if (typeof advgb !== undefined) {
        if (advgb.onProfileView) {
            $('.ju-menu-tabs a.link-tab[href="#profiles"]').click(function () {
                window.location = advgb.toProfilesList;
                return false;
            });
        }
    }

    $('.users-search-toggle').unbind('click').click(function () {
        $(this).closest('.users-search').find('#user-search-input').animate({width: 'toggle'});
    });

    // Search blocks function
    $('.blocks-search-input').on('input', function () {
        var searchKey = $(this).val().trim().toLowerCase();

        $('.block-item .block-title').each(function () {
            var blockTitle = $(this).text().toLowerCase().trim(),
                blockItem = $(this).closest('.block-item');

            if (blockTitle.indexOf(searchKey) > -1) {
                blockItem.show();
            } else {
                blockItem.hide();
            }
        })
    });

    // Ajax for displaying users list
    $('#user-search-input').bind('searchUsers', function () {
        var searchKey = $(this).val();
        var roleKey = $('#advgb-roles-filter').val();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'advgb_get_users',
                search: searchKey,
                role: roleKey
            },
            success: function (res) {
                $('#advgb-users-body').html(res.users_list);
                $('#pagination').html(res.pages_list);
                selectedUsers();
                switchPage();
            }
        })
    });

    // Search users input
    $('#user-search-input').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).trigger('searchUsers');
        }
    });

    // Role filter
    $('#advgb-roles-filter').change(function () {
        var roleKey = $(this).val();
        var searchKey = $('#user-search-input').val();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'advgb_get_users',
                search: searchKey,
                role: roleKey
            },
            success: function (res) {
                $('#advgb-users-body').html(res.users_list);
                $('#pagination').html(res.pages_list);
                selectedUsers();
                switchPage();
            }
        })
    });

    // Clear search users
    $('#advgb-clear-btn').click(function () {
        $('#user-search-input').val('');
        $('#advgb-roles-filter').val('');
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'advgb_get_users'
            },
            success: function (res) {
                $('#advgb-users-body').html(res.users_list);
                $('#pagination').html(res.pages_list);
                selectedUsers();
                switchPage();
            }
        })
    });

    // Switch page
    function switchPage() {
        $('.switch-page').unbind('click').click(function () {
            var paged = $(this).text();
            paged = parseInt(paged);
            getPagination(paged);
        });
        $('#pagination a#first-page').unbind('click').click(function () {
            var paged = 'first';
            getPagination(paged);
        });
        $('#pagination a#last-page').unbind('click').click(function () {
            var paged = 'last';
            getPagination(paged);
        });
    }
    switchPage();

    // Ajax for pagination
    function getPagination(page_num) {
        var searchKey = $('#user-search-input').val();
        var roleKey = $('#advgb-roles-filter').val();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'advgb_get_users',
                search: searchKey,
                role: roleKey,
                paged: page_num
            },
            success: function (res) {
                $('#advgb-users-body').html(res.users_list);
                $('#pagination').html(res.pages_list);
                selectedUsers();
                switchPage();
            }
        })
    }

    // Function for selecting users
    function selectedUsers() {
        $('#advgb-users-body :checkbox').change(function () {
            if (this.checked) {
                // Action when checked
                var val = $(this).val();
                $('#advgb-users-access-list').val($('#advgb-users-access-list').val() + " " + val);
            } else {
                // Action  when unchecked
                var vals = $(this).val();
                var split_val = $('#advgb-users-access-list').val().split(' ');
                split_val.splice($.inArray(vals, split_val),1);
                var final_val = split_val.join(' ');
                $('#advgb-users-access-list').val(final_val);
            }
        });

        var split_vals = $('#advgb-users-access-list').val().split(' ');
        $('#advgb-users-body :checkbox').each(function (e) {
            var val = $(this).val();
            var checked = $.inArray(val, split_vals);
            //Check if users is checked
            if (checked !== -1) {
                $(this).attr('checked', 'checked');
            }
        })
    }
    selectedUsers();
});