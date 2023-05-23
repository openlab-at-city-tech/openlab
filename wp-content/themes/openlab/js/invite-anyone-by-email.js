(function($){
	$(document).ready(function(){
		var $acInput = $('#invite-anyone-group-list-autocomplete')
		var $groupList = $( '#invite-anyone-group-list' )
		var cache = []

		$acInput.autocomplete({
			minLength: 2,
			source: function( request, response ) {
        var term = request.term;
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }

        $.getJSON( ajaxurl + "?action=openlab_search_user_groups", request, function( data, status, xhr ) {
          cache[ term ] = data;
          response( data );
        });
			},
      select: function( event, ui ) {
				var newEl = '<li class="ia-group-for-invite" data-groupid="' + ui.item.itemId + '"><button class="remove-group"><span class="screen-reader-text">Remove group invitation</span><i class="fa fa-times"></i></button>' + ui.item.icon + '<span>' + ui.item.label + '</span><input type="hidden" name="invite_anyone_groups[]" value="' + ui.item.itemId + '" /></li>';
				$groupList.append( newEl )
				$acInput.val( '' )
        return false;
      }
		}).autocomplete('instance')._renderItem = function( ul, item ) {
			var newEl = '<div class="ia-autocomplete-hit">' + item.icon + '<div>' + item.label + '</div></div>'
      return $( "<li>" )
        .append( newEl )
        .appendTo( ul );
		}

		$groupList.on( 'click', '.remove-group', function( e ) {
			e.preventDefault()
			e.target.closest( '.ia-group-for-invite' ).remove()
		} )
	})
})(jQuery)
