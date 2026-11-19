(function ($) {
    'use strict';

    $(document).ready(function () {
        initAutoInsertAdmin();
    });

    /**
     * Copy the label of each enhanced <select> (label[for], aria-label or aria-labelledby)
     * to the select2 search input and selection, which are what keyboard users focus.
     */
    function labelSelect2SearchFields() {
        $('.advg-insert-taxonomy-select2, .advg-insert-author-select2, .advg-insert-block-select2, .advg-insert-post-select2').each(function () {
            var select = $(this);
            var name = select.attr('aria-label') || '';
            var id = select.attr('id');

            if (!name && id) {
                name = $.trim($('label[for="' + id + '"]').first().text());
            }
            if (!name && select.attr('aria-labelledby')) {
                name = $.trim($('#' + select.attr('aria-labelledby')).text());
            }
            if (!name) {
                return;
            }

            var container = select.next('.pp_select2-container, .select2-container');
            container.find('.pp_select2-search__field, .select2-search__field').attr('aria-label', name);
            container.find('.pp_select2-selection, .select2-selection').attr('aria-label', name);
        });
    }

    function initAutoInsertAdmin() {
        setSelect2Translations();

        // taxonomy select2
        $('.advg-insert-taxonomy-select2').pp_select2({
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function (params) {
                    return {
                        action: 'advgb_search_taxonomy_terms',
                        taxonomy: $(this).data('taxonomy'),
                        search: params.term,
                        nonce: advgbAutoInsertI18n.nonce
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.success ? data.data : []
                    };
                },
                cache: true
            }
        });
        // author select2
        $('.advg-insert-author-select2').pp_select2({
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function (params) {
                    return {
                        action: 'advgb_insert_search_author',
                        search: params.term,
                        nonce: advgbAutoInsertI18n.nonce
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.success ? data.data : []
                    };
                },
                cache: true
            }
        });
        // block select2
        $('.advg-insert-block-select2').pp_select2({
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function (params) {
                    return {
                        action: 'advgb_insert_search_block',
                        search: params.term,
                        nonce: advgbAutoInsertI18n.nonce
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.success ? data.data : []
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            var data = e.params.data;

            var input = $('<input>', {
                type: 'hidden',
                name: 'advgb_blocks[' + data.id + ']',
                value: data.text,
                'data-block-id': data.id
            });

            $('.advg-insert-block-values').append(input);
        }).on('select2:unselect', function (e) {
            var data = e.params.data;

            $('.advg-insert-block-values').find('input[data-block-id="' + data.id + '"]').remove();
        });

        // include post select2
        $('.advg-insert-post-select2.include-posts').pp_select2({
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function (params) {
                    var selectedPostTypes = [];
                    $('.post-type-checkbox:checked').each(function() {
                        selectedPostTypes.push($(this).val());
                    });
                    return {
                        action: 'advgb_insert_search_posts',
                        search: params.term,
                        post_types: selectedPostTypes,
                        nonce: advgbAutoInsertI18n.nonce
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.success ? data.data : []
                    };
                },
                cache: true
            }
        }).on('change', function() {
            var ids = $(this).val() ? $(this).val().join(',') : '';
            $('#advgb_post_ids').val(ids);
            $(this).closest('.post-search-wrap').find('.post-ids-manual-input').val(ids);
        });

        // exclude post select2
        $('.advg-insert-post-select2.exclude-posts').pp_select2({
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function (params) {
                    var selectedPostTypes = [];
                    $('.post-type-checkbox:checked').each(function() {
                        selectedPostTypes.push($(this).val());
                    });
                    return {
                        action: 'advgb_insert_search_posts',
                        search: params.term,
                        post_types: selectedPostTypes,
                        nonce: advgbAutoInsertI18n.nonce
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.success ? data.data : []
                    };
                },
                cache: true
            }
        }).on('change', function() {
            var ids = $(this).val() ? $(this).val().join(',') : '';
            $('#advgb_exclude_post_ids').val(ids);
            $(this).closest('.post-search-wrap').find('.post-ids-manual-input').val(ids);
        });

        // posts manual input handling
        $('.post-ids-manual-input').on('change', function() {
            var manualIds = $(this).val();
            var tabContent = $(this).closest('.post-search-wrap');
            var select2 = tabContent.find('.advg-insert-post-select2');
            var hiddenField = tabContent.attr('id') === 'tab-include-posts' ? $('#advgb_post_ids') : $('#advgb_exclude_post_ids');

            var currentSelected = select2.val() || [];

            // Process manual IDs
            if (manualIds) {
                var manualIdArray = manualIds.split(',').map(id => id.trim()).filter(id => id);

                // Add new manual IDs that don't already exist
                manualIdArray.forEach(function(id) {
                    if (!currentSelected.includes(id)) {
                        var existingOption = select2.find('option[value="' + id + '"]');
                        if (existingOption.length === 0) {
                            // Add new option if it doesn't exist
                            var option = new Option('ID: ' + id, id, true, true);
                            select2.append(option);
                        }
                        currentSelected.push(id);
                    }
                });

                // Remove IDs that are no longer in manual input
                var newSelected = currentSelected.filter(id => manualIdArray.includes(id));

                // Update select2 with new selection
                select2.val(newSelected).trigger('change');
                hiddenField.val(manualIds);
            } else {
                // If manual input is empty, clear everything
                select2.val(null).trigger('change');
                hiddenField.val('');
            }
        });

        // other select2
        $('.advgb-editor-aib-select2').pp_select2();

        // Give select2 search fields the accessible name of their <select>
        labelSelect2SearchFields();
        $('.advg-insert-taxonomy-select2, .advg-insert-author-select2, .advg-insert-block-select2, .advg-insert-post-select2')
            .on('change pp_select2:open pp_select2:close', labelSelect2SearchFields);

        // remove select2 title
        $('#advgb_block_id').on('pp_select2:open', function() {
            $('.pp_select2-results__option').removeAttr('title');
            $('.pp_select2-selection__rendered').removeAttr('title');
        });
        $('.pp_select2-selection__rendered').removeAttr('title');


        // Handle post type changes for taxonomy visibility
        $('.post-type-checkbox').on('change', toggleTaxonomyVisibility);

        // Handle position changes for showing/hiding options
        $('#advgb_position').on('change', togglePositionOptions);

        // Initialize on page load
        toggleTaxonomyVisibility();
        togglePositionOptions();
    }

    function setSelect2Translations() {
        if (! $.fn.pp_select2 || ! advgbAutoInsertI18n.select2) {
            return;
        }

        var strings = advgbAutoInsertI18n.select2;

        $.fn.pp_select2.defaults.set('language', {
            errorLoading: function () {
                return strings.errorLoading;
            },
            inputTooLong: function (args) {
                var count = args.input.length - args.maximum;

                return formatCountString(strings.inputTooLong, count);
            },
            inputTooShort: function (args) {
                var count = args.minimum - args.input.length;

                return formatCountString(strings.inputTooShort, count);
            },
            loadingMore: function () {
                return strings.loadingMore;
            },
            maximumSelected: function (args) {
                return formatCountString(strings.maximumSelected, args.maximum);
            },
            noResults: function () {
                return strings.noResults;
            },
            searching: function () {
                return strings.searching;
            },
            removeAllItems: function () {
                return strings.removeAllItems;
            },
            removeItem: function () {
                return strings.removeItem;
            },
            search: function () {
                return strings.search;
            }
        });
    }

    function formatCountString(stringSet, count) {
        var template = count === 1 ? stringSet.one : stringSet.other;

        return template.replace('%s', count);
    }

    function toggleTaxonomyVisibility() {
        var selectedPostTypes = [];
        $('.post-type-checkbox:checked').each(function () {
            selectedPostTypes.push($(this).val());
        });

        $('.taxonomy-group').hide();

        if (selectedPostTypes.length > 0) {
            selectedPostTypes.forEach(function (postType) {
                $('.taxonomy-group.post-type-' + postType).show();
            });
        }
    }

    function togglePositionOptions() {
        var position = $('#advgb_position').val();

        if (['after_heading', 'before_heading', 'after_paragraph', 'before_paragraph', 'after_block', 'before_block', 'after_specific_block', 'before_specific_block'].includes(position)) {
            $('#position-value-row').show();
        } else {
            $('#position-value-row').hide();
        }

        if (position === 'after_block' || position === 'before_block') {
            $('#excluded-blocks-row').show();
        } else {
            $('#excluded-blocks-row').hide();
        }

        if (position === 'after_specific_block' || position === 'before_specific_block') {
            $('#specific-blocks-row').show();
        } else {
            $('#specific-blocks-row').hide();
        }

        if (! advgbAutoInsertI18n.proActive && !['beginning', 'end'].includes(position)) {
            $('.position-table-row .invalid-position').show();
            lockSaveButton(true);
        } else {
            $('.position-table-row .invalid-position').hide();
            lockSaveButton(false);
        }
    }

    function lockSaveButton(lock = true) {

    }

})(jQuery);
