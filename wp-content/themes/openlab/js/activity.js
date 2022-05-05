/**
 * This is the JavaScript related to the activity stream.
 * 
 */

jQuery( document ).ready(
	function($) {
        
        $(document).on( 'change', '#activity-loop-filter-form select', function(e) {
            $(this).parent('form').submit();
        });

        $(document).on( 'click', '.activity-header-meta .button', function(e) {
            e.preventDefault();

            let button = $(this);
            let activityId = $(this).attr('data-activity_id');
            let userAction = $(this).hasClass('fav') ? 'fav' : 'unfav';

            if( activityId ) {
                $.ajax({
                    url: activityVars.ajax_url,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        'action': 'openlab_fav_activity',
                        'user_action': userAction,
                        'activity_id': activityId
                    },
                    success: function( response ) {
                        if( response.success ) {
                            if( response.action == 'fav' ) {
                                button.removeClass('fav').addClass('unfav');
                                button.attr('Unpin activity');
                            } else {
                                button.removeClass('unfav').addClass('fav');
                                button.attr('Pin activity');
                            }
                        }
                    }
                })
            }
        });
        
	},
	(jQuery)
);
