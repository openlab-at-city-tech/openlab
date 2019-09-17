window.onload = function () {
    if (typeof wp.blocks !== 'undefined') {
        if (wp.blockLibrary && typeof wp.blockLibrary.registerCoreBlocks === 'function') {
            wp.blockLibrary.registerCoreBlocks();
        }

        var $ = jQuery;
        var allBlocks = wp.blocks.getBlockTypes();
        var allCategories = wp.blocks.getCategories();
        var listBlocks = [];
        var nonce = '';

        allBlocks.forEach(function (block) {
            var blockItemIcon = '';
            var blockItem = {
                name: block.name,
                icon: block.icon.src,
                title: block.title,
                category: block.category,
                parent: block.parent
            };

            var savedIcon = !!block.icon.src ? block.icon.src : block.icon;

            if (block.icon.foreground !== undefined) blockItem.iconColor = block.icon.foreground;

            if (typeof savedIcon === 'function') {
                if (!!savedIcon.prototype.render) {
                    blockItem.icon = wp.element.renderToString(wp.element.createElement(savedIcon));
                } else {
                    blockItem.icon = wp.element.renderToString(savedIcon());
                }

                blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
                blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
            } else if (typeof savedIcon === 'object') {
                blockItem.icon = wp.element.renderToString(savedIcon);
                blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
                blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
            } else if (typeof savedIcon === 'string') {
                blockItemIcon = wp.element.createElement(wp.components.Dashicon, {icon: savedIcon});
                blockItem.icon = wp.element.renderToString(blockItemIcon);
            }

            listBlocks.push(blockItem);
            return block;
        });

        if (typeof updateListNonce !== 'undefined') {
            nonce = updateListNonce.nonce;
        } else {
            nonce = $('#advgb_nonce_field').val();
        }

        // Update categories
        allCategories.forEach(function (category) {
            var categoryBlock = $('.category-block[data-category="'+ category.slug +'"]');
            if (categoryBlock.length > 0) {
                categoryBlock.find('h3.category-name').text(category.title);
            } else {
                var categoryHTML = '';
                categoryHTML += '<div class="category-block clearfix" data-category='+ category.slug +'>';
                categoryHTML +=     '<h3 class="category-name">';
                categoryHTML +=         '<span>'+ category.title +'</span>';
                categoryHTML +=         '<i class="mi"></i>';
                categoryHTML +=     '</h3>';
                categoryHTML +=     '<ul class="blocks-list"></ul>';
                categoryHTML += '</div>';

                $('.blocks-section').append(categoryHTML);
            }
        });

        var list_blocks_names = [];

        // Update blocks
        listBlocks.forEach(function (block) {
            list_blocks_names.push(block.name);

            var blockHTML = '';
            blockHTML += '<li class="block-item ju-settings-option" data-type="'+ block.name +'">';
            blockHTML +=    '<label for="'+ block.name +'" class="ju-setting-label">';
            blockHTML +=        '<span class="block-icon"';
            if (block.iconColor) {
                blockHTML += ' style="color:'+ block.iconColor +'"';
            }
            blockHTML += '>';
            if (block.icon.indexOf('<svg') > -1) {
                blockHTML +=    block.icon;
            } else {
                blockHTML +=    '<i class="dashicons dashicons-'+ block.icon +'"></i>';
            }
            var checked = '';
            if (typeof advgb_blocks_vars.blocks.inactive_blocks === 'object' && advgb_blocks_vars.blocks.inactive_blocks !== null) {
                checked = advgb_blocks_vars.blocks.inactive_blocks.indexOf(block.name)===-1?'checked="checked"':'';
            } else {
                checked = 'checked="checked"';
            }

            blockHTML +=        '</span>';
            blockHTML +=        '<span class="block-title">'+ block.title +'</span>';
            blockHTML +=    '</label>';
            blockHTML +=    '<div class="ju-switch-button">';
            blockHTML +=        '<label class="switch">';
            blockHTML +=            '<input id="'+ block.name +'" type="checkbox" name="active_blocks[]" value="'+ block.name +'" '+checked+'/>';
            blockHTML +=            '<span class="slider"></span>';
            blockHTML +=        '</label>';
            blockHTML +=    '</div>';
            blockHTML += '</li>';

            var categoryBlock = $('.category-block[data-category="'+ block.category +'"]');
            categoryBlock.find('.blocks-list').append(blockHTML);

        });

        $('#blocks_list').val(JSON.stringify(list_blocks_names));

        // Use this ajax query to update the block list in db
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'advgb_update_blocks_list',
                blocksList: listBlocks,
                nonce: nonce
            }
        });
    }
};