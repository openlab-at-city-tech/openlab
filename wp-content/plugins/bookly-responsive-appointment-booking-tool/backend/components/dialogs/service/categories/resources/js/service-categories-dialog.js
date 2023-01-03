jQuery(function($) {
    'use strict';

    let $dialog = $('#bookly-service-categories-modal'),
        $categories = $('#bookly-services-categories', $dialog),
        $template = $('#bookly-new-category-template').clone().removeAttr('id').addClass('bookly-js-category-wrap'),
        $newCategory = $('#bookly-js-new-category', $dialog),
        $servicesList = $('#services-list'),
        $save = $('#bookly-save', $dialog),
        $thumb_container,
        frame = wp.media({
            library: {type: 'image'},
            multiple: false
        })
            .on('select', function() {
                let selection = frame.state().get('selection').toJSON(),
                    img_src;
                if (selection.length) {
                    if (selection[0].sizes['thumbnail'] !== undefined) {
                        img_src = selection[0].sizes['thumbnail'].url;
                    } else {
                        img_src = selection[0].url;
                    }
                    $('[name="attachment_id"]', $thumb_container).val(selection[0].id).trigger('change');
                    $('.bookly-thumb-delete', $thumb_container).show();
                    $thumb_container
                        .css({'background-image': 'url(' + img_src + ')', 'background-size': 'cover'})
                        .addClass('bookly-thumb-with-image');
                    $(this).hide();
                }
            });

    // Add category
    $newCategory.on('click', function() {
        appendCategory({
            id: 'new-' + Math.random().toString(36).substring(2),
            name: '',
            info: '',
            attachment: null,
            attachment_id: null
        });
        $categories.find('.card:last input[name="name"]').focus();
    });
    // Remove category
    $categories.on('click', '.bookly-js-delete-category', function(e) {
        e.preventDefault();
        $(this).closest('.card').remove();
    });
    // Save categories
    $save.on('click', function(e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            categories = [];
        ladda.start();
        $('.card', $categories).each(function(position, category) {
            let $card = $(category);
            categories.push({
                id: $('[name="id"]', $card).val(),
                name: $('[name="name"]', $card).val(),
                info: $('[name="info"]', $card).val(),
                attachment_id: $('[name="attachment_id"]', $card).val(),
            });
        });
        $.post(
            ajaxurl,
            booklySerialize.buildRequestData('bookly_update_service_categories', {categories: categories}),
            function(response) {
                if (response.success) {
                    BooklyL10n.categories = response.data;
                    $servicesList.DataTable().ajax.reload();
                    $dialog.booklyModal('hide');
                }
                ladda.stop();
            });
    });

    $dialog.off().on('show.bs.modal', function() {
        // Show categories list
        $categories.html('');
        BooklyL10n.categories.forEach(function(category) {
            appendCategory(category);
        });
    });

    function appendCategory(category) {
        let $category = $template.clone(),
            attr_id = 'bookly-category-' + category.id;
        $('[name="id"]', $category).attr('value', category.id);
        $('[name="name"]', $category).attr('value', category.name);
        $('[name="info"]', $category).text(category.info);
        $('.card-header [data-toggle="bookly-collapse"]', $category).attr('href', '#' + attr_id)
        $('.bookly-collapse', $category).attr('id', attr_id)
        if (category.attachment !== null) {
            $('[name="attachment_id"]', $category).attr('value', category.attachment_id);
            $('.bookly-thumb-delete', $category).show();
            $('.bookly-thumb', $category)
                .css({'background-image': 'url(' + category.attachment + ')', 'background-size': 'cover'})
                .addClass('bookly-thumb-with-image');
        }

        $category
            .on('click', '.bookly-thumb label', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $thumb_container = $(this).closest('.bookly-thumb');
                frame.open();
                $(document).off('focusin.modal');
            })
            // Delete img
            .on('click', '.bookly-thumb-delete', function() {
                $('.bookly-thumb', $category).attr('style', '');
                $('[name="attachment_id"]', $category).val('').trigger('change');
                $('.bookly-thumb', $category).removeClass('bookly-thumb-with-image');
                $('.bookly-thumb-delete', $category).hide();
            })

        $categories.append($category);
    }

    Sortable.create($categories[0], {
        handle: '.bookly-js-draghandle',
    });
    $('[data-target="#bookly-service-categories-modal"]').prop('disabled', false);
});