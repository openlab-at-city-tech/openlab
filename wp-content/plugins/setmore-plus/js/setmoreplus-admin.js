jQuery(document).ready(function ($) {

	Array.max = function (array) {
		return Math.max.apply(Math, array);
	};

	// Add a staff URL
	$("#add-url").click(function () {

		var ids = $("#staff-urls").find("input.original-order").map(function (val, el) {
			return el.value;
		}).get();

		var count = Array.max(ids);

		var data = {
			'count': count,
			'action': 'setmoreplus_add_url',
		};

		$.get(ajaxurl, data, function (response) {
			$("#staff-urls").append(response);
		});
	});

	// Delete a staff URL
	$("#staff-urls").on("click", ".staff-delete", function () {
		var thisField = $(this).closest(".row");

		var id = thisField.find(".staff-id").html();
		id = parseInt(id);

		var yesno = confirm("Remove ID " + id + "?");

		if (yesno) {
			thisField.fadeOut(function () {
				$(this).remove();
				// reindex
				$("#staff-urls").find(".staff-id").each(function (index, el) {
					// Cannot actually change DOM input values. Original values will post instead. So use plain text.
					$(el).html(index + 1);
				});
			});
		}
	});


	// Restore defaults
	$('input.restore-defaults').on('click', function(){
		$(this).closest('div').find('table.dimensions').find(':input').each(function( index, el ) {
			if ( $(el).data('default') ) {
				var defaultValue = $(el).data('default');
				$(el).val( defaultValue );
			}
		});
	});


	// Changing from pixels to percent?
	$('select.pxpct').on('change', function(){
		var targetId = $(this).data('target');
		if ( ! targetId ) return;

		var target = $('#' + targetId);
		if ( ! target ) return;

		var current = target.data('current');
		if ( ! current ) return;

		var pxpct = $(this).val();
		if ( '%' == pxpct ) {
			// set percent to 100
			target.val( '100' );
		} else {
			// restore current pixel setting
			target.val( current );
		}
	});


	// Screenshot gallery on Settings tab
	var $gallery = $('a.screenshot').colorbox({
		rel: 'screenshot',
		transition: 'none',
		onComplete : function() {
			$(this).colorbox.resize();
		}
	});
	$("a#openGallery").click(function(e){
		e.preventDefault();
		$gallery.eq(0).click();
	});

	// Screenshot on Instructions tab
	$("a.screenshot-menu-link").colorbox({
		'transition': 'none',
		'title': 'Adding a menu link',
		'opacity': 0.8,
		'height': document.documentElement.clientHeight - 36,
		'top': 34,
		'maxWidth': 1058,
		'maxHeight': 830,
		'photo': true,
		'scalePhotos': true,
		'returnFocus': false,
		'rel': false,
		onComplete : function() {
			$(this).colorbox.resize();
		}
	});


});
