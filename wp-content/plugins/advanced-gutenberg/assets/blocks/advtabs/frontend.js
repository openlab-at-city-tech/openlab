jQuery(document).ready(function ($) {
    // Fix display of tab headers in mobile view for existing content for https://github.com/publishpress/PublishPress-Blocks/issues/1483
    $('.advgb-tabs-wrapper').each(function() {
        var $wrapper = $(this);
        var activeTab = parseInt($wrapper.data('tab-active')) || 0;
        var $containers = $wrapper.find('.advgb-tab-body-container');

        $containers.each(function(index) {
            var $container = $(this);
            var $body = $container.find('.advgb-tab-body');
            var $header = $container.find('.advgb-tab-body-header');

            // Remove display:none from all containers
            if ($container.css('display') === 'none') {
                $container.css('display', '');
            }

            // Set display for inner tab bodies based on active tab
            if (index === activeTab) {
                $body.css('display', 'block');
                $header.addClass('header-active');
            } else {
                $body.css('display', 'none');
                $header.removeClass('header-active');
            }
        });
    });


    $(".advgb-tab a:not(.ui-tabs-anchor)").unbind("click");
    $(".advgb-tabs-block").tabs();

    var a11yUid = 0;

    function getTabControl($tab) {
        return $tab.children('button, a').first();
    }

    /**
     * Runtime ARIA wiring (saved markup is left untouched):
     * - the content (.advgb-tab-body) is the tabpanel, labelled by its tab;
     * - the stacked mobile header becomes a disclosure button for that panel.
     */
    function setupTabsA11y($wrapper) {
        var tabs = $wrapper.find('.advgb-tab');
        var bodyContainers = $wrapper.find('.advgb-tab-body-container');

        a11yUid++;
        $wrapper.find('ul.advgb-tabs-panel').first().attr('role', 'tablist');

        tabs.each(function (index) {
            var $tab = $(this);
            var $control = getTabControl($tab);
            var $container = bodyContainers.eq(index);
            var $header = $container.children('.advgb-tab-body-header');
            var $body = $container.children('.advgb-tab-body');

            if (!$control.length || !$body.length) {
                return;
            }

            var controlId = $control.attr('id');
            if (!controlId) {
                controlId = 'advgb-tab-a11y-' + a11yUid + '-' + index;
                $control.attr('id', controlId);
            }

            var bodyId = $body.attr('id');
            if (!bodyId) {
                bodyId = ($header.attr('id') || 'advgb-tab-panel-a11y-' + a11yUid + '-' + index) + '-content';
                $body.attr('id', bodyId);
            }

            $tab.attr('role', 'presentation');
            $control.attr({
                role: 'tab',
                'aria-controls': bodyId
            });

            $body.attr({
                role: 'tabpanel',
                'aria-labelledby': controlId,
                tabindex: '0'
            });

            if ($header.length) {
                $header
                    .removeAttr('aria-labelledby')
                    .attr({
                        role: 'button',
                        tabindex: '0',
                        'aria-controls': bodyId,
                        'aria-expanded': 'false'
                    });
            }
        });
    }

    function syncTabsA11y($wrapper, index) {
        var tabs = $wrapper.find('.advgb-tab');
        var bodyHeaders = $wrapper.find('.advgb-tab-body-header');

        tabs.each(function (i) {
            var $control = getTabControl($(this));
            $control.attr({
                'aria-selected': i === index ? 'true' : 'false',
                tabindex: i === index ? '0' : '-1'
            });
        });

        bodyHeaders.each(function (i) {
            $(this).attr('aria-expanded', i === index ? 'true' : 'false');
        });
    }

    function activateTab($wrapper, index) {
        var tabs = $wrapper.find('.advgb-tab');
        var bodyContainers = $wrapper.find('.advgb-tab-body-container');
        var bodyHeaders = $wrapper.find('.advgb-tab-body-header');

        tabs.removeClass('advgb-tab-active ui-tabs-active ui-state-active');
        bodyContainers.find('.advgb-tab-body').hide();
        bodyHeaders.removeClass('header-active');

        tabs.eq(index).addClass('advgb-tab-active ui-tabs-active ui-state-active');
        bodyContainers.eq(index).find('.advgb-tab-body').show();
        bodyHeaders.eq(index).addClass('header-active');

        syncTabsA11y($wrapper, index);

        var $targetPanel = bodyContainers.eq(index);
        if ($targetPanel.find('.advgb-images-slider-block').length && $.fn.slick) {
            // refresh slick content
            $targetPanel.find('.advgb-images-slider-block > .slick-initialized').slick(
                'slickSetOption',
                'refresh',
                true,
                true
            );
        }

        if ($targetPanel.find('.pdfemb-viewer').length && $.fn.pdfEmbedder) {
            // refresh pdf embedder content
            $targetPanel.find('.pdfemb-viewer').pdfEmbedder(pdfemb_trans.cmap_url);
        }
    }

    function handleAnchorNavigation() {
        var hash = window.location.hash;
        if (!hash) return;

        var anchor = hash.substring(1);
        if (!anchor) return;

        $('.advgb-tabs-wrapper').each(function() {
            var $wrapper = $(this);

            // Check both custom anchor classes and IDs
            var $matches = $wrapper.find('.advgb-tab-class-' + anchor + ', [id="' + anchor + '"]');

            if ($matches.length) {
                var $target = $matches.first();
                var index;
                var $scrollTarget = null;

                if ($target.is('.advgb-tab, .advgb-tab a, .advgb-tab button')) {
                    // Handle tab buttons
                    index = $wrapper.find('.advgb-tab').index($target.closest('.advgb-tab'));
                    $scrollTarget = $target;
                }
                else if ($target.is('.advgb-tab-body-header, [role="tabpanel"]')) {
                    // Handle tab body headers (mobile)
                    index = $wrapper.find('.advgb-tab-body-container').index($target.closest('.advgb-tab-body-container'));

                    // If this is a mobile header and it's hidden (on desktop view)
                    if ($target.is(':hidden')) {
                        // Find the corresponding desktop tab button by position
                        // (aria-controls now points at the tab content, not the header)
                        $scrollTarget = getTabControl($wrapper.find('.advgb-tab').eq(index));
                    } else {
                        $scrollTarget = $target;
                    }
                }

                if (typeof index !== 'undefined') {
                    activateTab($wrapper, index);

                    // Only scroll if we have a valid target and it's visible
                    if ($scrollTarget && $scrollTarget.length && $scrollTarget.is(':visible')) {
                        $scrollTarget[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                    return false;
                }
            }
        });
    }

    // Tab initialization
    $('.advgb-tabs-wrapper').each(function () {
        var $wrapper = $(this);
        var activeTab = parseInt($wrapper.data('tab-active')) || 0;
        var tabs = $wrapper.find('.advgb-tab');
        var bodyHeaders = $wrapper.find('.advgb-tab-body-header');
        var bodyContainers = $wrapper.find('.advgb-tab-body-container');

        // Get styles from inactive tab
        var inactiveTab = $wrapper.find('li.advgb-tab:not(".advgb-tab-active")');
        if($wrapper.prop('id') !== '') {
            inactiveTab = $wrapper.find('li.advgb-tab:not(".ui-state-active")');
        }

        var tabStyles = {
            bgColor: inactiveTab.css('background-color'),
            borderColor: inactiveTab.css('border-color'),
            borderWidth: inactiveTab.css('border-width'),
            borderStyle: inactiveTab.css('border-style'),
            borderRadius: inactiveTab.css('border-radius'),
            textColor: inactiveTab.find('a, button').css('color')
        };

        // Tab click handler
        tabs.on('click', 'a, button', function(event) {
            event.preventDefault();
            var $currentTab = $(this).closest('.advgb-tab');
            activateTab($wrapper, $wrapper.find('.advgb-tab').index($currentTab));
        });

        // Keyboard support for the tab list (Left/Right, Up/Down when vertical, Home/End)
        var isVertical = /advgb-tab-vert-/.test($wrapper.attr('class') || '');
        tabs.on('keydown', 'a, button', function(event) {
            var $controls = tabs.map(function () { return getTabControl($(this)).get(0); });
            var current = tabs.index($(this).closest('.advgb-tab'));
            var last = tabs.length - 1;
            var next = null;

            switch (event.key) {
                case 'ArrowRight':
                case 'Right':
                    next = current >= last ? 0 : current + 1;
                    break;
                case 'ArrowLeft':
                case 'Left':
                    next = current <= 0 ? last : current - 1;
                    break;
                case 'ArrowDown':
                case 'Down':
                    if (isVertical) {
                        next = current >= last ? 0 : current + 1;
                    }
                    break;
                case 'ArrowUp':
                case 'Up':
                    if (isVertical) {
                        next = current <= 0 ? last : current - 1;
                    }
                    break;
                case 'Home':
                    next = 0;
                    break;
                case 'End':
                    next = last;
                    break;
                case ' ':
                case 'Spacebar':
                    // Links (legacy markup) are not activated by Space natively.
                    if ($(this).is('a')) {
                        event.preventDefault();
                        activateTab($wrapper, current);
                    }
                    return;
            }

            if (next === null) {
                return;
            }

            event.preventDefault();
            activateTab($wrapper, next);
            if ($controls[next]) {
                $controls[next].focus();
            }
        });

        // Header click handler (mobile)
        bodyHeaders.on('click', function() {
            var $header = $(this);
            var index = bodyContainers.index($header.closest('.advgb-tab-body-container'));
            activateTab($wrapper, index);
            $header[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        // Mobile headers act as buttons: Enter/Space activate them
        bodyHeaders.on('keydown', function(event) {
            if (event.target !== this) {
                return;
            }
            if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                event.preventDefault();
                $(this).trigger('click');
            }
        });

        // Initialize
        if (tabs.length) {
            setupTabsA11y($wrapper);
            activateTab($wrapper, activeTab);
            bodyHeaders.css({
                backgroundColor: tabStyles.bgColor,
                color: tabStyles.textColor,
                borderColor: tabStyles.borderColor,
                borderWidth: tabStyles.borderWidth,
                borderStyle: tabStyles.borderStyle,
                borderRadius: tabStyles.borderRadius
            });
        }
    });

    // Initial anchor handling
    handleAnchorNavigation();
    $(window).on('hashchange', handleAnchorNavigation);
});