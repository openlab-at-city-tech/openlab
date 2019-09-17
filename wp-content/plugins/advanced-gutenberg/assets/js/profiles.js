(function ( $ ) {
    $(document).ready(function ( $ ) {
        // Sort profiles
        function sortProfiles(sortBy, asc) {
            if (typeof asc === 'undefined') asc = false;

            var tbody = $('#profiles-list').find('tbody');
            tbody.find('tr').sort(function(a, b) {
                if (asc) {
                    return $('td.profile-' + sortBy, a).text().localeCompare($('td.profile-' + sortBy, b).text());
                } else {
                    return $('td.profile-' + sortBy, b).text().localeCompare($('td.profile-' + sortBy, a).text());
                }
            }).appendTo(tbody);
        }

        // Clicking header to sort
        $('#profiles-list thead .sorting-header').unbind('click').click(function () {
            var sortBy = $(this).data('sort');
            var asc = true;

            if ($(this).hasClass('asc')) {
                asc = false;
                $('#profiles-list').find('.sorting-header').removeClass('desc').removeClass('asc');
                $('#profiles-list').find('.profile-header-'+ sortBy).addClass('desc');
            } else {
                $('#profiles-list').find('.sorting-header').removeClass('desc').removeClass('asc');
                $('#profiles-list').find('.profile-header-'+ sortBy).addClass('asc');
            }

            sortProfiles(sortBy, asc);
            return false;
        });

        // Search profiles
        $('.profiles-search-input').on('input', function () {
            var searchKey = $(this).val().trim().toLowerCase();

            $('#profiles-list .advgb-profile').each(function () {
                var profileTitle = $(this).find('.profile-title').text().trim().toLowerCase(),
                    profileAuthor = $(this).find('.profile-author').text().trim().toLowerCase();

                if (profileTitle.indexOf(searchKey) > -1 || profileAuthor.indexOf(searchKey) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })
        });

        // Check all checkboxes
        $('.select-all-profiles').click(function () {
            $('.select-all-profiles').prop('checked', this.checked);
            $(this).closest('#profiles-list').find('tbody .profile-checkbox input[type=checkbox]').prop('checked', this.checked);
        });

        $('.profile-checkbox input[type=checkbox]').click(function () {
            if (!this.checked) {
                $('.select-all-profiles').prop('checked', this.checked);
            }
        });

        // Click delete single profile
        $('.profile-delete').unbind('click').click(function () {
            var willDelete = confirm('Are you sure to delete this profile? This action cannot be undone.');
            var profileID = $(this).data('profile-id');

            if (willDelete) deleteProfiles([profileID]);
        });

        // Click delete multi-profiles
        $('#delete-selected-profiles').unbind('click').click(function () {
            var profileIDs = [];
            var profilesChecked = $('#profiles-list').find('.profile-checkbox input:checkbox:checked');

            if (profilesChecked.length < 1) {
                alert( 'No profiles selected!' );
                return false;
            }

            profilesChecked.each(function () {
                profileIDs.push($(this).val());
            });

            var willDelete = confirm('Are you sure to delete these profiles? This action cannot be undone.');
            if (willDelete) deleteProfiles(profileIDs);
        });

        // Delete profiles
        function deleteProfiles(ids) {
            var profilesNonce = $('#advgb_profiles_nonce').val();

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'advgb_delete_profiles',
                    pNonce: profilesNonce,
                    ids: ids
                },
                success: function (res) {
                    res.deleted.forEach(function (id, index) {
                        setTimeout(function () {
                            $('.advgb-profile[data-profile-id='+ id +']').fadeOut(300, function () {
                                $(this).remove();
                            });
                        }, index * 500);
                    })
                },
                error: function ( xhr, error ) {
                    alert(error + ' - ' + xhr.responseText);
                }
            })
        }
    })
})( jQuery );