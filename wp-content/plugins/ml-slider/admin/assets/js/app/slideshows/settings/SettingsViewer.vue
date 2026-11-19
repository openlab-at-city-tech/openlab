<script>
import { EventManager } from '../../utils'
export default {
	props: {
	},
	data() {
		return {}
	},
	mounted() {
		let $ = window.jQuery

		// This code was ported from admin.js and will be refactored in a later branch
        $(".useWithCaution").on("change", function(){
            if(!this.checked) {
                return alert(metaslider.useWithCaution);
            }
		});
		
    	$(".metaslider-ui").on('click', '.ms-toggle .hndle, .ms-toggle .handlediv', function() {
            $(this).parent().toggleClass('closed');
		});
		
		// Switch slider types when on the label and pressing enter
        $('.metaslider-ui').on('keypress', '.slider-lib-row label', function (event) {
            if (32 === event.which) {
                event.preventDefault();
                $('.slider-lib-row #' + $(this).attr('for')).trigger('click');
            }
		});
		
        /**
         * Show/hide a setting based on the value of another setting
         * 
         * @since 3.60
         * 
         * @param {object} el           The element to monitor changes
         * @param {string|array} show   The element to show or hide
         * @param {string} target       The setting to show/hide its <tr> wrapper or direct ID
         * 
         * @return void
         **/ 
        var toggleSomeRow = function (el, show, when) {
            var type = el.is('input[type="checkbox"]') ? 'checkbox' : 'select';

            /* If is a checkbox input and match with the when value 
             *
             * Possible cases: 
             * a) when is true and is checked, returns true
             * b) when is false and is NOT checked, returns true
             * c) when is true and is NOT checked, returns false 
             * d) when is false and is checked, returns false */
            var checbox_rule = type === 'checkbox' && el.is(':checked') === when ? true : false;

            /* Check if is a select field and the selected value match when 
             * (as string or as one of the array values) */
            var select_rule = type === 'select' 
                                && (el.val() === when 
                                || (Array.isArray(when) && when.indexOf(el.val()) !== -1))
                            ? true : false;

            // Show or hide slideshow settings or slide settings, whatever match the find()
            if (checbox_rule || select_rule) {
                if (show.charAt(0) === '#' || show.charAt(0) === '.') { // ID or class
                    // ID match
                    $('#metaslider-slides-list').find(show).show(); 
                } else {
                    // Form element's <tr> match
                    $('.ms-settings-table').find(`[name="settings[${show}]"]`).closest('tr').show();
                }
            } else {
                if (show.charAt(0) === '#' || show.charAt(0) === '.') { // ID or class
                    // ID match
                    $('#metaslider-slides-list').find(show).hide();
                } else {
                    // Form element's <tr> match
                    $('.ms-settings-table').find(`[name="settings[${show}]"]`).closest('tr').hide();
                }
            }
        }

        /* Show/hide settings based on the value of other settings 
             * by checking data-dependencies attribute */
        /**
         * Initialize toggleSomeRow()
         * 
         * @since 3.70
         * 
         * @param {string} selector CSS selector must ends with '[data-dependencies]'
         * 
         * @return void
         */
        var initToggle = function (selector) {
            $(selector).each(function() {
                var el = $(this);
                var data = JSON.parse($(this).attr('data-dependencies'));

                // Loop through the array of objects
                data.forEach(function(item) {
                    toggleSomeRow(el, item.show, item.when);

                    $(document).on('change', '.metaslider-ui', el, function() {
                        toggleSomeRow(el, item.show, item.when);
                    });
                });
            });
        }

		// Enable the correct options for this slider type
        var switchType = function(slider) {
            $('.metaslider .option:not(.' + slider + ')').attr('disabled', 'disabled').parents('tr').hide();
            $('.metaslider .option.' + slider).removeAttr('disabled').parents('tr').show();
            $('.metaslider input.radio:not(.' + slider + ')').attr('disabled', 'disabled');
            $('.metaslider input.radio.' + slider).removeAttr('disabled');
    
            $('.metaslider .showNextWhenChecked:visible').closest("tr").next('tr').hide();
            $('.metaslider .showNextWhenChecked:visible:checked').closest("tr").next('tr').show();
    
            // make sure that the selected option is available for this slider type
            if ($('.effect option:selected').attr('disabled') === 'disabled') {
                $('.effect option:enabled:first').attr('selected', 'selected');
            }
    
            // make sure that the selected option is available for this slider type
            if ($('.theme option:selected').attr('disabled') === 'disabled') {
                $('.theme option:enabled:first').attr('selected', 'selected');
            }

            // Add dynamic display of settings based on checkbox and select values
            initToggle('.ms-settings-table [data-dependencies], #metaslider-slides-list [data-dependencies]');

            if (slider == 'flex') {
                $('.flex-setting').show();
            } else {
                $('.flex-setting').hide();
            }
        };
    
        EventManager.$on(['metaslider/app-loaded', 'metaslider/slides-created', 'metaslider/slide-duplicated'], () => { 
            initToggle('#metaslider-slides-list [data-dependencies]');
        })

        // enable the correct options on page load
        switchType($(".metaslider .select-slider:checked").attr("rel"));
    
        var toggleNextRow = function(checkbox) {
            if(checkbox.is(':checked')){
                checkbox.closest("tr").next("tr").show();
            } else {
                checkbox.closest("tr").next("tr").hide();
            }
		}
		
		toggleNextRow($(".showNextWhenChecked"))
		EventManager.$on('metaslider/app-loaded', () => { 
            toggleNextRow($(".showNextWhenChecked"));
        })
    
        $(".metaslider-ui").on("change", ".showNextWhenChecked", function() {
            toggleNextRow($(this));
        });
    
        // mark the slide for resizing when the crop position has changed
        $(".metaslider-ui").on('change', '.left tr.slide .crop_position', function() {
            $(this).closest('tr').data('crop_changed', true);
        });
    
        // handle slide libary switching
        $(".metaslider-ui").on("click", ".select-slider", function() {
            switchType($(this).attr("rel"));
        });

        /**
         * Search settings in the sidebar: filters rows across every settings
         * section and expands only the sections that have a match.
         *
         * @since 3.111
         */
        var $settingsBoxes = $('#metaslider_configuration .ms-settings-box');
        var isSearching = false;

        $('#ms-settings-search').on('input', function() {
            var query = $.trim($(this).val()).toLowerCase();
            var anyBoxVisible = false;

            $('#ms-settings-search-clear').toggle(!!query);

            if (query && !isSearching) {
                // Starting a new search: remember which sections were expanded, so that
                // can be restored once the search is cleared
                $settingsBoxes.each(function() {
                    $(this).data('ms-was-on', $(this).hasClass('ms-on'));
                });
            }
            isSearching = !!query;

            if (!query) {
                // Rows/sections marked "ms-hidden-by-default" are only ever hidden by a
                // fixed, page-load condition (e.g. no trashed slides, an ad row that isn't
                // relevant) that can't change without a page reload, so they stay hidden.
                // Everything else is shown, then switchType()/initToggle() below re-derives
                // the correct visibility from the CURRENT setting values - a controlling
                // setting (e.g. "Container Box") may have been toggled while searching.
                $settingsBoxes.each(function() {
                    var $box = $(this);
                    var wasOn = $box.data('ms-was-on');

                    $box.toggle(!$box.hasClass('ms-hidden-by-default'));
                    $box.find('.ms-settings-box-inner tr').each(function() {
                        var $row = $(this);
                        $row.toggle(!$row.hasClass('ms-hidden-by-default'));
                    });
                    $box.removeClass('ms-on ms-off').addClass(wasOn ? 'ms-on' : 'ms-off');
                    $box.find('.ms-settings-box-inner')[wasOn ? 'show' : 'hide']();
                });

                // Re-apply setting-driven visibility (slider type, data-dependencies,
                // showNextWhenChecked) in case a controlling setting's value changed
                // while the search was active
                switchType($(".metaslider .select-slider:checked").attr("rel"));

                // A number of settings (in both Free and Pro, e.g. the crop source notice,
                // full width options, extra effect, play/pause text) show/hide other rows
                // through their own dedicated "change" handler rather than data-dependencies.
                // Re-firing "change" on every settings field (without altering its value)
                // lets each of those handlers re-derive the correct state on its own,
                // without this file needing to know they exist
                $('.ms-settings-table select, .ms-settings-table input[type="checkbox"], .ms-settings-table input[type="radio"]').trigger('change');

                $('.ms-settings-search-empty').hide();
                return;
            }

            $settingsBoxes.each(function() {
                var $box = $(this);
                var $rows = $box.find('.ms-settings-box-inner tr');
                var titleMatches = $box.find('.ms-highlight').first().text().toLowerCase().indexOf(query) !== -1;
                var rowMatchCount = 0;

                $rows.each(function() {
                    var $row = $(this);
                    var $label = $row.find('> td.tipsy-tooltip').first();

                    if (!$label.length) {
                        // Rows without a label (ad/html rows) can't be matched individually
                        $row.hide();
                        return;
                    }

                    var text = ($label.text() + ' ' + ($label.attr('title') || '')).toLowerCase();

                    if (text.indexOf(query) !== -1) {
                        rowMatchCount++;
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });

                var boxMatches = rowMatchCount > 0 || titleMatches;

                if (boxMatches) {
                    anyBoxVisible = true;

                    // Sections that only matched by title (e.g. Theme, Shortcode) have no
                    // per-row labels to filter by, so show everything they contain
                    if (rowMatchCount === 0 && titleMatches) {
                        $rows.show();
                    }

                    $box.show().removeClass('ms-off').addClass('ms-on');
                    $box.find('.ms-settings-box-inner').show();
                } else {
                    $box.hide();
                }
            });

            $('.ms-settings-search-empty').toggle(!anyBoxVisible);
        });

        $('#ms-settings-search-clear').on('click', function() {
            $('#ms-settings-search').val('').trigger('input').trigger('focus');
        });
	}
}
</script>
