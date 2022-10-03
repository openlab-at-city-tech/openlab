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
                if(typeof savedIcon.prototype !== 'undefined') {
                    blockItem.icon = wp.element.renderToString(wp.element.createElement(savedIcon));
                    blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
                    blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
                } else {
                    blockItemIcon = wp.element.createElement(wp.components.Dashicon, {icon: 'block-default'});
                    blockItem.icon = wp.element.renderToString(blockItemIcon);
                }
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
            nonce = $('#advgb_access_nonce_field').val();
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
