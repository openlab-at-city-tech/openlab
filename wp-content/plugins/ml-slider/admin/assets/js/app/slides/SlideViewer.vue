<script>
import { EventManager } from '../utils'
export default {
	props: {
	},
	data() {
		return {}
	},
	mounted() {
		let $ = window.jQuery
		// This code was ported from admin.js and will be refactored in a later branch
		// Drag and drop slides, update the slide order on drop (ported from admin.js)
		// TODO: remove table layout for more flexability (grid drag & drop)
		var metaslider_sortable_helper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        // Slide can't be edited in trashed slides screen
        $('.metaslider .ms-edit-slideshow--trashed-slides table#metaslider-slides-list td.col-2').on('click', function () {
            EventManager.$emit('metaslider/cant-edit-trashed-slide');
        });

        // Disable drag and drop slides reorder in trashed slides screen
        if(!$('#post-body').hasClass('ms-edit-slideshow--trashed-slides')) {

            // Reorder slides with drag and drop
            $(".metaslider table#metaslider-slides-list > tbody").sortable({
                helper: metaslider_sortable_helper,
                handle: "td.col-1",
                stop: () => {
                    EventManager.$emit('metaslider/save')
                }
            });

            // Switch tabs within a slide on space press
            $('.metaslider-ui').on('keypress', 'ul.tabs > li > a', function(event) {
                if (32 === event.which) {
                    event.preventDefault();
                    $(':focus').trigger('click');
                }
            });

            // Event to switch tabs within a slide
            $(".metaslider-ui").on('click', 'ul.tabs > li > a', function(event) {
                event.preventDefault();
                var tab = $(this);

                // Hide all the tabs
                tab.parents('.metaslider-ui-inner')
                .children('.tabs-content')
                .find('div.tab').hide();
                
                // Show the selected tab
                tab.parents('.metaslider-ui-inner')
                .children('.tabs-content')
                .find('div.' + tab.data('tab_id')).show();

                // Add the class
                tab.parent().addClass("selected")
                .siblings().removeClass("selected");
            });
        }
		
		$(".metaslider-ui").on('change', "input.width, input.height", function(e) {
            $(".metaslider table#metaslider-slides-list").trigger('metaslider/size-has-changed', {
                width: $("input.width").val(),
                height: $("input.height").val()
            });
        });
	}
}
</script>
