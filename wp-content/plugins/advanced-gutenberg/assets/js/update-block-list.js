window.addEventListener('load', function () {
    if (typeof wp.blocks !== 'undefined') {
        if (wp.blockLibrary && typeof wp.blockLibrary.registerCoreBlocks === 'function') {
            wp.blockLibrary.registerCoreBlocks();
        }

        var $ = jQuery;
        var allBlocks = wp.blocks.getBlockTypes();
        var allCategories = wp.blocks.getCategories();
        var listBlocks = [];
        var nonce = '';

        // Add Widgets Legacy and Area blocks manually
        if( advgbBlocks.blocks_widget_support ) {
            allBlocks.push({
              "name": "core/legacy-widget",
              "icon": {
                "src": "block-default",
              },
              "title": "Legacy Widget",
              "category": "widgets",
            },
            {
              "name": "core/widget-area",
              "icon": {
                "src": "block-default",
              },
              "category": "widgets",
              "title": "Widget Area",
            });
        }

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
});
