jQuery(document).ready(function($) {

	

	// Array: selector of toggle element, selector of element to show/hide, checkable value for select || null
	var genesis_responsive_slider_toggles = [
		['#genesis_responsive_slider_settings\\[post_type\\]', '#genesis-slider-taxonomy', 'page']
	];

	$.each( genesis_responsive_slider_toggles, function( k, v ) {
		$( v[0] ).live( 'change', function() {
			genesis_responsive_slider_toggle_settings( v[0], v[1], v[2] );
		});
		genesis_responsive_slider_toggle_settings( v[0], v[1], v[2] ); // Check when page loads too.
	});

	function genesis_responsive_slider_toggle_settings( selector, show_selector, check_value ) {
		if (
			( check_value === null && $( selector ).is( ':checked' ) ) ||
			( check_value !== null && $( selector ).val() !== check_value )
		) {
			$( show_selector ).slideDown( 'fast' );
		} else {
			$( show_selector ).slideUp( 'fast' );
		}
	}

	function genesis_responsive_slider_checklist_toggle() {
		$('<p><span id="genesis-category-checklist-toggle" class="button">' + genesis.category_checklist_toggle + '</span></p>').insertBefore('ul.categorychecklist');

		$('#genesis-category-checklist-toggle').live('click.genesis', function (event) {
			var $this = $(this),
				checkboxes = $this.parent().next().find(':checkbox');

			if ($this.data('clicked')) {
				checkboxes.attr('checked', false);
				$this.data('clicked', false);
			} else {
				checkboxes.attr('checked', true);
				$this.data('clicked', true);
			}
		});
	}
	genesis_responsive_slider_checklist_toggle();
	
	$('.genesis-layout-selector input[type="radio"]').change(function() {
	    var tmp=$(this).attr('name');
	    $('input[name="'+tmp+'"]').parent("label").removeClass("selected");
	    $(this).parent("label").toggleClass("selected", this.selected);      
	});

});