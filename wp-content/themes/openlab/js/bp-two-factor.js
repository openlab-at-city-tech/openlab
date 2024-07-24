(function($){
	$(document).ready(function(){
		// Figure out the position of the 'col-primary' column. It doesn't have
		// the proper class in the table body.
		var col;
		$('.two-factor-methods-table thead th').each(function( k, v ){
			console.log(v)
			if ( $(v).hasClass('col-primary') ) {
				col = k;
				return false;
			}
		});

		if ( col !== undefined ) {
			console.log('col: ' + col);
			$('.two-factor-methods-table tbody tr').each(function(){
				var $thisRowChildren = $(this).children();
				$thisRowChildren.eq(col).addClass('col-primary');
			});
		}

		$('.two-factor-methods-table thead th.col-enabled').html('Enable');
		$('.two-factor-methods-table thead th.col-name').html('');

	});
})(jQuery);
