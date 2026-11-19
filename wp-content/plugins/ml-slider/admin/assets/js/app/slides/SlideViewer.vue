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

            // Build the reorder sidebar from current table row order.
            // Clones .thumb directly so all slide types (image, video, icon, etc.) render correctly.
            function buildSidebar() {
                const $list = $('#ms-slide-sidebar-list')
                if (!$list.length) return
                $list.empty()
                $('#metaslider-slides-list > tbody > tr:not(.ms-deleted)').each(function(i) {
                    const slideId = this.id.replace('slide-', '')
                    const $thumb = $(this).find('.thumb').first().clone()
                    const slideType = $(this).data('slide-type')
                    if (slideType) $thumb.addClass(slideType)
                    $('<li>')
                        .addClass('ms-sidebar-slide')
                        .attr('data-slide-id', slideId)
                        .append($('<span>').addClass('ms-sidebar-num').text(i + 1))
                        .append($thumb)
                        .appendTo($list)
                })
            }

            // Position the floating sidebar at the left edge of the WP content area
            function positionSidebar() {
                const contentEl = document.getElementById('wpbody-content')
                if (contentEl) {
                    const left = contentEl.getBoundingClientRect().left
                    // Reveal once the position is set (hidden by default to avoid a load flash)
                    $('#ms-slide-sidebar').css('left', left + 'px').addClass('ms-slide-sidebar--ready')
                }
            }
            positionSidebar()
            $(window).on('resize.ms-sidebar', positionSidebar)

            // Reposition when the WP admin menu is collapsed/expanded.
            // Run on the next frame and after the menu's CSS transition completes.
            $(document).on('click.ms-sidebar', '#collapse-menu', function() {
                requestAnimationFrame(positionSidebar)
                setTimeout(positionSidebar, 300)
            })

            // Only show the sidebar when there are at least 2 slides to reorder
            function updateSidebarVisibility() {
                const count = $('#metaslider-slides-list > tbody > tr:not(.ms-deleted)').length
                const $sidebar = $('#ms-slide-sidebar')
                if (count >= 2) {
                    $sidebar.show()
                } else {
                    $sidebar.removeClass('ms-slide-sidebar--open').hide()
                }
            }
            updateSidebarVisibility()

            // Toggle sidebar open/closed. Always starts closed on page load.
            $('#ms-slide-reorder-toggle').on('click', function() {
                const $sidebar = $('#ms-slide-sidebar')
                const opening = !$sidebar.hasClass('ms-slide-sidebar--open')
                $sidebar.toggleClass('ms-slide-sidebar--open', opening)
                if (opening) buildSidebar()
            })

            // Sidebar sortable — reorders the main table rows to match
            $('#ms-slide-sidebar-list').sortable({
                axis: 'y',
                stop: function(e, ui) {
                    const $tbody = $('#metaslider-slides-list > tbody')

                    // Destroy TinyMCE editors before moving DOM nodes — iframes lose state on DOM moves
                    if (typeof tinymce !== 'undefined') {
                        $('#metaslider-slides-list').find('textarea.wysiwyg, textarea[class^="wysiwyg-"]').each(function() {
                            const editor = tinymce.get($(this).attr('id'))
                            if (editor) editor.destroy()
                            $(this).attr('disabled', true)
                        })
                    }

                    $('#ms-slide-sidebar-list li').each(function() {
                        const slideId = $(this).data('slide-id')
                        $tbody.append($('#slide-' + slideId))
                    })

                    // Reinitialize TinyMCE editors after DOM reorder
                    if (typeof tinymce !== 'undefined') {
                        $('#metaslider-slides-list').find('textarea.wysiwyg, textarea[class^="wysiwyg-"]').each(function() {
                            const slide_type = $(this).data('type')
                            const slide_id = $(this).attr('id')
                            if (slide_type && slide_id) {
                                const tinymce_data = metaslider.tinymce.find(obj => obj.type === slide_type)
                                if (typeof tinymce_data !== 'undefined') {
                                    $(this).attr('disabled', false)
                                    tinymce.init({
                                        ...{ selector: `#${slide_id}` },
                                        ...tinymce_data.configuration
                                    })
                                }
                            }
                        })
                    }

                    buildSidebar()
                    EventManager.$emit('metaslider/save')

                    // Scroll to the row the slide was dropped into (delayed to let save feedback settle)
                    const movedId = ui.item.data('slide-id')
                    const $row = $('#slide-' + movedId)
                    if ($row.length) {
                        setTimeout(function() {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: $row.offset().top - 100
                            }, 400)
                        }, 1000)
                    }
                }
            })

            // Re-evaluate visibility and rebuild whenever the slide list changes —
            // covers adds, duplicates, deletes (.ms-deleted class) and undeletes uniformly.
            const tbodyEl = document.querySelector('#metaslider-slides-list > tbody')
            if (tbodyEl && typeof MutationObserver !== 'undefined') {
                const observer = new MutationObserver(() => {
                    updateSidebarVisibility()
                    if ($('#ms-slide-sidebar').hasClass('ms-slide-sidebar--open')) buildSidebar()
                })
                observer.observe(tbodyEl, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['class']
                })
            }

            // Reorder slides with drag and drop
            $(".metaslider table#metaslider-slides-list > tbody").sortable({
                helper: metaslider_sortable_helper,
                handle: "td.col-1",
                start: (e, ui) => {
                    if (typeof tinymce !== 'undefined') {
                        $('#metaslider-slides-list').find('textarea.wysiwyg, textarea[class^="wysiwyg-"]').each(function() {
                            tinymce.get($(this).attr('id')).destroy();
                            $(this).attr('disabled', true);
                        });
                    }
                },
                stop: (e, ui) => {
                    $('#metaslider-slides-list').find('textarea.wysiwyg, textarea[class^="wysiwyg-"]').each(function() {
                        const slide_type = $(this).data('type');
                        const slide_id = $(this).attr('id');

                        if (slide_type.length && slide_id.length) {
                            const tinymce_data = metaslider.tinymce.find(obj => obj.type === slide_type);

                            if (typeof tinymce_data !== 'undefined') {
                                $(this).attr('disabled', false);
                                tinymce.init({
                                    ...{ selector: `#${slide_id}` },
                                    ...tinymce_data.configuration
                                });
                            }
                        }
                    });

                    EventManager.$emit('metaslider/save')
                    buildSidebar()
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
