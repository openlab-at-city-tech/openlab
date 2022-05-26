/**
 * This is the JavaScript related to the membership privacy functionality.
 * 
 */

 jQuery( document ).ready(
	function($) {

        $(document).on( 'change', 'input#membership_privacy', function(e) {
            let groupId = $(this).attr('data-group_id');
            let isPrivate = $(this).is(':checked');

            $.ajax({
                url: membershipVars.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: {
                    'action': 'openlab_update_member_group_privacy',
                    'group_id': groupId,
                    'is_private': isPrivate
                },
                beforeSend: function() {
                    // Disable checkbox 
                    $('input#membership_privacy').attr('disabled', true );
                },
                success: function( response ) {
                    console.log( response );
                    $('input#membership_privacy').attr('disabled', false );
                },
                complete: function() {
                    $('input#membership_privacy').attr('disabled', false );
                }
            });

        });
        
	},
	(jQuery)
);
