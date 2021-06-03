if (typeof wp !== 'undefined' && typeof wp.domReady !== 'undefined'){
    wp.domReady(()=>{
        let gutenberg_init_function = null;
        if (typeof window._wpLoadGutenbergEditor !== 'undefined') {
            // Using WP core Gutenberg
            gutenberg_init_function = window._wpLoadGutenbergEditor;
        } else if (typeof window._wpLoadBlockEditor !== 'undefined') {
            // Using Gutenberg plugin
            gutenberg_init_function = window._wpLoadBlockEditor;
        }

        if (gutenberg_init_function !== null) {
            // Wait for Gutenberg editor to be ready
            gutenberg_init_function.then(() => {
                if (advgb_blocks_vars.original_settings.allowedBlockTypes !== true) {
                    // allowed_block_types filter has been used, in this case we do nothing as we don't know why blocks have been filtered
                    return;
                }

                let list_blocks = [];
                let granted_blocks = [];
                let missing_block = false;
                // Retrieve all registered blocks
                let blocks = wp.blocks.getBlockTypes();
                const savedBlocks = {
                    active_blocks: Object.values(advgb_blocks_vars.blocks.active_blocks),
                    inactive_blocks: Object.values(advgb_blocks_vars.blocks.inactive_blocks),
                };

                for (let block in blocks) {
                    var blockItemIcon = '';
                    var blockItem = {
                        name: blocks[block].name,
                        icon: blocks[block].icon.src,
                        title: blocks[block].title,
                        category: blocks[block].category,
                        parent: blocks[block].parent
                    };

                    var savedIcon = !!blocks[block].icon.src ? blocks[block].icon.src : blocks[block].icon;

                    if (blocks[block].icon.foreground !== undefined) blockItem.iconColor = blocks[block].icon.foreground;

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
                    list_blocks.push(blockItem);


                    // Compare current block with the list of blocks we have
                    if (savedBlocks.active_blocks.indexOf(blocks[block].name) >= 0) {
                        // Block is active
                        granted_blocks.push(blocks[block].name);
                    } else if (savedBlocks.inactive_blocks.indexOf(blocks[block].name) >= 0) {
                        // Block is inactive
                    } else {
                        // This block is not in our database yet, but by default we allow the usage
                        granted_blocks.push(blocks[block].name);
                        missing_block = true;
                    }
                }

                //console.log('missing_block: ' + missing_block);
                
                if (missing_block) {
                    if (console !== undefined && console.error !== undefined) {
                        //console.log('console: ' + console);
                        console.error('Reloading editor by PublishPress Blocks plugin');
                    }
                    // Replace original allowed block settings by our modified list
                    let new_settings = advgb_blocks_vars.original_settings;
                    new_settings.allowedBlockTypes = granted_blocks;
                    const target = document.getElementById('editor');

                    // Initialize again the editor
                    wp.editPost.initializeEditor('editor', advgb_blocks_vars.post_type, advgb_blocks_vars.post_id, new_settings, window._wpGutenbergDefaultPost);

                    var list_categories = wp.blocks.getCategories();

                    try {
                        // Use this ajax query to update the block list in db
                        jQuery.ajax({
                            url: advgb_blocks_vars.ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'advgb_update_blocks_list',
                                blocksList: JSON.stringify(list_blocks),
                                categoriesList: JSON.stringify(list_categories),
                                nonce: advgb_blocks_vars.nonce
                            },
                            success: function (data) {
                                //console.log(data);
                            }
                        });
                    } catch (e) {
                        //console.log(e);
                    }
                }
            });
        }
    });

}