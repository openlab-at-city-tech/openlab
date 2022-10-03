(function ( $ ) {
    $.expr[":"].contains = $.expr.createPseudo(function (arg) {
        return function ( elem ) {
            return $(elem).text().toLowerCase().indexOf(arg.toLowerCase()) >= 0;
        };
    });

    $(document).ready(function ( $ ) {
        $('.ju-main-wrapper').show();

        // Toggle left panel on small screen
        $('.ju-left-panel-toggle').unbind('click').click(function () {
            var leftPanel = $('.ju-left-panel');
            var wpLeftPanel = $('#adminmenuwrap');
            var rtl = $('body').hasClass('rtl');

            if (leftPanel.is(':visible')) {
                if (wpLeftPanel.is(':visible')) {
                    if (!rtl) {
                        $(this).css('left', 35);
                    } else {
                        $(this).css('right', 35);
                    }
                } else {
                    if (!rtl) {
                        $(this).css('left', 0);
                    } else {
                        $(this).css('right', 0);
                    }
                }
            } else {
                if (wpLeftPanel.is(':visible')) {
                    if (!rtl) {
                        $(this).css('left', 335);
                    } else {
                        $(this).css('right', 335);
                    }
                } else {
                    if (!rtl) {
                        $(this).css('left', 290);
                    } else {
                        $(this).css('right', 290);
                    }
                }
            }

            leftPanel.toggle()
        });

        // Function for searching menus
        $('.ju-menu-search-input').on('input', function () {
            $('.ju-right-panel li.ju-settings-option').removeClass('search-result');
            $('.ju-menu-tabs .tab').show();

            var searchKey = $(this).val().trim().toLowerCase();
            if (searchKey === '') {
                $('.ju-menu-tabs .tab').show();
                return false;
            }

            var searchResult = $('.ju-right-panel li.ju-settings-option label:contains("'+searchKey+'")').closest('li.ju-settings-option');
            var searchParent = searchResult.closest('.ju-content-wrapper');
            var searchSub = searchResult.closest('.tab-content');
            var tabID = [], subID = [];

            searchResult.addClass('search-result');

            searchParent.each(function () {
                tabID.push($(this).attr('id'));
            });

            searchSub.each(function () {
                subID.push($(this).attr('id'));
            });

            $('.ju-menu-tabs .tab .link-tab').each(function () {
                var href = $(this).attr('href');
                var text = $(this).text().trim().toLowerCase();
                var dataHref = $(this).data('href');

                if (href !== undefined) {
                    href = href.replace(/#/g, '');
                }

                if (dataHref !== undefined) {
                    dataHref = dataHref.replace(/#/, '');
                }

                if (tabID.indexOf(href) < 0 && text.indexOf(searchKey) < 0 && subID.indexOf(dataHref) < 0) {
                    $(this).closest('li.tab').hide();
                } else {
                    if ($(this).closest('.ju-submenu-tabs').length > 0) {
                        $(this).closest('.ju-submenu-tabs').closest('li.tab').show();
                    }
                }
            });
        });

        $('.advgb-search-input').on('focus', function () {
            $(this).parent('.advgb-search-wrapper').addClass('focused');
        }).on('blur', function () {
            $(this).parent('.advgb-search-wrapper').removeClass('focused');
        });

        $('.ju-notice-close').click(function () {
            $(this).closest('.ju-notice-msg').slideUp();
        });

        $('.ju-menu-tabs li.tab a').one('click', function () {
            var tabId = $(this).attr('href');
            setTimeout(function () {
                $(tabId).find('ul.tabs').itabs();
            }, 100);
        });

        // Add submenus
        $('.ju-top-tabs').each(function () {
            var topTab = $(this);
            var tabClone = $(this).clone();
            var parentHref = $(this).closest('.ju-content-wrapper').attr('id');
            tabClone.removeClass('ju-top-tabs').removeClass('tabs').addClass('ju-submenu-tabs');

            tabClone.find('li.tab').each(function () {
                var currentSubMenu = $(this).closest('.ju-submenu-tabs');
                var currentTab = $(this).find('a.link-tab').removeClass('waves-effect');
                var tabClass = currentTab.attr('class');
                var tabHref = currentTab.attr('href');

                $(this).html('<div class="'+ tabClass +'" data-href="'+ tabHref +'">'+ $(this).text() +'</div>');

                $(this).find('div.link-tab').click(function () {
                    topTab.find('li.tab a[href="'+ tabHref +'"]').click();
                    currentSubMenu.find('li.tab div.link-tab').removeClass('active');
                    $(this).addClass('active');
                })
            });

            $('.ju-menu-tabs .tab a.link-tab[href="#'+ parentHref +'"]').closest('.tab').append(tabClone);
        });

        // Top tab click also navigate submenu tabs
        $('.ju-top-tabs li.tab').click(function () {
            var parentHref = $(this).closest('.ju-content-wrapper').attr('id');
            var tabHref = $(this).find('a.link-tab').attr('href');
            var subMenu = $('.ju-menu-tabs .tab a.link-tab[href="#'+ parentHref +'"]').closest('li.tab').find('.ju-submenu-tabs');

            subMenu.find('div.link-tab').removeClass('active');
            subMenu.find('div.link-tab[data-href="'+ tabHref +'"]').addClass('active');

            // Save tab to cookie
            document.cookie = 'advgbRightTab=' + tabHref;
        });

        // Collapsed the menu when clicking if it opened
        $('.ju-menu-tabs li.tab a.link-tab').click(function () {
            if (!$(this).hasClass('active')) {
                $(this).closest('.ju-menu-tabs').find('li.tab a.link-tab').removeClass('expanded');
            }

            if ($(this).closest('li.tab').find('.ju-submenu-tabs').length > 0) {
                $(this).toggleClass('expanded');
            }

            // Save tab to cookie
            var tabHref = $(this).attr('href');
            document.cookie = 'advgbLeftTab=' + tabHref;

            setTimeout(function () {
                var rightTabHref = $(tabHref).find('.ju-top-tabs').find('a.link-tab.active').attr('href');
                document.cookie = 'advgbRightTab=' + rightTabHref;
            }, 500)
        });

        // Not show expand icon if this tab has no sub menus
        $('.ju-menu-tabs li.tab').each(function () {
            if ($(this).find('.ju-submenu-tabs').length > 0) {
                var linkTab = $(this).find('a.link-tab');

                linkTab.addClass('with-submenus');
                if (linkTab.hasClass('active')) {
                    linkTab.addClass('expanded');
                }
            }
        });

        function setTabFromCookie() {
            var lastLeftTab = advgbGetCookie('advgbLeftTab');
            var lastRightTab = advgbGetCookie('advgbRightTab');

            if (lastLeftTab !== '') {
                var leftTab = $('.ju-menu-tabs a.link-tab[href="'+ lastLeftTab +'"]');
                if (!leftTab.hasClass('active')) {
                    leftTab.click();
                }

                if (lastRightTab !== '') {
                    $('.ju-top-tabs a.link-tab[href="'+ lastRightTab +'"]').click();
                }
            }
        }

        if (!window.location.hash) {
            setTabFromCookie();
        }

        Waves.attach('.waves-effect');
        Waves.init();
    })
})(jQuery);

// Get cookie
function advgbGetCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)===' ') c = c.substring(1);
        if (c.indexOf(name) === 0) return c.substring(name.length,c.length);
    }
    return "";
}

/**
 * Output categories and blocks inside a form
 *
 * @param {array}   inactive_blocks The inactive blocks - e.g. advgbCUserRole.access.inactive_blocks
 * @param {string}  nonce_field_id  The nonce field id - e.g. '#advgb_access_nonce_field'
 * @param {string}  blocks_list_id  The block list id field - e.g. '#blocks_list_access'
 *
 * @return {number} x raised to the n-th power.
 */
function advgbGetBlocks( inactive_blocks, nonce_field_id, blocks_list_id ) {
    if (typeof wp.blocks !== 'undefined') {
        if (wp.blockLibrary && typeof wp.blockLibrary.registerCoreBlocks === 'function') {
            wp.blockLibrary.registerCoreBlocks();
        }

        var $ = jQuery;
        var allBlocks = wp.blocks.getBlockTypes();
        var allCategories = wp.blocks.getCategories();
        var listBlocks = [];
        var nonce = '';

        // Get blocks saved in advgb_blocks_list option to include the ones that are missing in allBlocks.
        // e.g. blocks registered only via PHP
        if(
            typeof advgbBlocks.block_extend !== 'undefined'
            && parseInt(advgbBlocks.block_extend)
            && typeof advgb_blocks_list !== 'undefined'
            && advgb_blocks_list.length > 0
        ) {
            let diff_blocks = advgb_blocks_list.filter(
                blocksA => !allBlocks.some( blocksB => blocksA.name === blocksB.name )
            );
            if( diff_blocks.length > 0 ) {
                diff_blocks.forEach(function (block) {
                    allBlocks.push(block);
                });
            }
        }

        // Array of block names already available through wp.blocks.getBlockTypes()
        var force_deactivate_blocks = []; // 'advgb/container'

        // Array of objects not available through wp.blocks.getBlockTypes()
        // As example: the ones that loads only in Appearance > Widget
        var force_activate_blocks = [
            {
              'name': 'core/legacy-widget',
              'icon': 'block-default',
              'title': 'Legacy Widget',
              'category': 'widgets'
            }
        ];

        // Include force_activate_blocks in the blocks list
        force_activate_blocks.forEach(function (block) {
            allBlocks.push(block);
        });


        allBlocks.forEach(function (block) {
            var blockItemIcon = '';
            var blockItem = {
                name: block.name,
                icon: block.icon.src || block.icon,
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
            } else if (
                typeof savedIcon === 'string'
                && !savedIcon.includes('<span') // Merged blocks icons from 'advgb_blocks_list' are stored as html
                && !savedIcon.includes('<svg') // Merged blocks icons from 'advgb_blocks_list' are stored as html
            ) {
                blockItemIcon = wp.element.createElement(wp.components.Dashicon, {icon: savedIcon});
                blockItem.icon = wp.element.renderToString(blockItemIcon);
            } else {
                blockItem.icon = savedIcon; // Pure html for merged blocks icons from 'advgb_blocks_list'
            }

            listBlocks.push(blockItem);
            return block;
        });

        if (typeof updateListNonce !== 'undefined') {
            nonce = updateListNonce.nonce;
        } else {
            nonce = $(nonce_field_id).val();
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
            blockHTML += '<li class="block-item block-access-item ju-settings-option ' + (force_deactivate_blocks.indexOf(block.name) > -1 || force_activate_blocks.find(item => item.name === block.name) ? 'block-item-readonly' : 'block-item-editable' ) + '" data-type="'+ block.name +'">';
            blockHTML +=    '<label for="'+ block.name +'" class="ju-setting-label">';
            blockHTML +=        '<span class="block-icon"';
            if (block.iconColor) {
                blockHTML += ' style="color:'+ block.iconColor +'"';
            }
            blockHTML += '>';
            blockHTML += block.icon;
            var checked = '';

            if (
                typeof inactive_blocks === 'object'
                && inactive_blocks !== null
            ) {
                checked = inactive_blocks.indexOf(block.name) === -1
                && (
                    force_deactivate_blocks.indexOf(block.name) === -1
                    || force_activate_blocks.find(item => item.name === block.name)
                )
                    ? 'checked="checked"' : '';
            } else {
                checked = 'checked="checked"';
            }

            blockHTML +=        '</span>';
            blockHTML +=        '<span class="block-title">'+ block.title +'</span>';
            blockHTML +=    '</label>';
            blockHTML +=    '<div class="ju-switch-button">';
            blockHTML +=        '<label class="switch">';
            blockHTML +=            '<input id="'+ block.name +'" type="checkbox" name="active_blocks[]" value="'+ block.name +'" '+checked+' ' + ( force_deactivate_blocks.indexOf(block.name) > -1 || force_activate_blocks.find(item => item.name === block.name) ? 'onclick="return false;"' : '' ) + '/>';
            blockHTML +=            '<span class="slider"></span>';
            blockHTML +=        '</label>';
            blockHTML +=    '</div>';
            blockHTML += '</li>';

            var categoryBlock = $('.category-block[data-category="'+ block.category +'"]');
            categoryBlock.find('.blocks-list').append(blockHTML);

        });

        $(blocks_list_id).val(JSON.stringify(list_blocks_names));
    }
}
