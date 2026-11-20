jQuery(document).ready(function($){
    var triggerUid = 0;

    // Keys jQuery UI's header keydown handler reacts to; we keep them away from it so
    // the native <button> click is the only activation (no double toggle).
    var HANDLED_KEYS = ['Enter', ' ', 'Spacebar', 'ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight', 'Down', 'Up', 'Left', 'Right', 'Home', 'End'];

    function getHeaders($accordion) {
        return $accordion.find('> .advgb-accordion-item > .advgb-accordion-header');
    }

    function getTrigger($header) {
        return $header.find('> .advgb-accordion-header-title > .advgb-accordion-trigger');
    }

    function ensureId($el, prefix) {
        var id = $el.attr('id');
        if (!id) {
            triggerUid++;
            id = prefix + '-' + triggerUid;
            $el.attr('id', id);
        }
        return id;
    }

    /**
     * Keep the heading semantics: put a real <button> inside the heading
     * instead of turning the whole header <div> into a tab/button.
     * Returns false when the title already holds interactive content.
     */
    function addTrigger($header) {
        var $title = $header.children('.advgb-accordion-header-title').first();
        if (!$title.length || $title.find('a[href], button, input, select, textarea').length) {
            return false;
        }

        var $body = $header.next('.advgb-accordion-body');
        var bodyId = ensureId($body, 'advgb-accordion-body');

        triggerUid++;
        var $button = $('<button type="button" class="advgb-accordion-trigger"></button>')
            .attr({
                id: 'advgb-accordion-trigger-' + triggerUid,
                'aria-controls': bodyId,
                'aria-expanded': 'false'
            });

        $button.append($title.contents());
        $title.append($button);

        // Decorative open/closed icons.
        $header.find('.advgb-accordion-header-icon, .ui-accordion-header-icon').attr('aria-hidden', 'true');
        $header.find('.advgb-accordion-header-icon svg').attr({ 'aria-hidden': 'true', focusable: 'false' });

        return true;
    }

    function setState($header, isExpanded) {
        var value = isExpanded ? 'true' : 'false';
        // data-advgb-expanded drives the open/closed icon via CSS.
        $header.attr('data-advgb-expanded', value);
        getTrigger($header).attr('aria-expanded', value);
    }

    function stripWidgetAria($accordion, $headers, allWrapped) {
        if (allWrapped) {
            // No tab-style headers remain, so the wrapper is no longer a tablist.
            $accordion.removeAttr('role');
        }
        $headers.each(function() {
            var $header = $(this);
            if (!getTrigger($header).length) {
                return;
            }
            $header.removeAttr('role tabindex aria-selected aria-expanded aria-controls aria-disabled');
            $header.next('.advgb-accordion-body').removeAttr('role aria-labelledby');
        });
    }

    function bindTriggerKeys($headers) {
        $headers.each(function() {
            getTrigger($(this)).on('keydown', function(e) {
                if (HANDLED_KEYS.indexOf(e.key) === -1) {
                    return;
                }
                // Enter/Space: let the native button fire its click (bubbles to the header).
                e.stopPropagation();

                var $triggers = $headers.map(function() { return getTrigger($(this)).get(0); });
                var idx = $triggers.index(this);
                var target = null;

                switch (e.key) {
                    case 'ArrowDown':
                    case 'Down':
                        target = $triggers.get((idx + 1) % $triggers.length);
                        break;
                    case 'ArrowUp':
                    case 'Up':
                        target = $triggers.get((idx - 1 + $triggers.length) % $triggers.length);
                        break;
                    case 'Home':
                        target = $triggers.get(0);
                        break;
                    case 'End':
                        target = $triggers.get($triggers.length - 1);
                        break;
                }

                if (target) {
                    e.preventDefault();
                    target.focus();
                }
            });
        });
    }

    function initExpandAllMode($accordion) {
        const $headers = getHeaders($accordion);

        $headers.each(function() {
            const $header = $(this);
            const $body = $header.next('.advgb-accordion-body');

            $body.show();
            if (!addTrigger($header)) {
                // Fallback: interactive content in the title, keep a header-level button.
                $header.attr({
                    tabindex: '0',
                    role: 'button',
                    'aria-expanded': 'true',
                    'aria-controls': ensureId($body, 'advgb-accordion-body')
                });
            }
            setState($header, true);
        });

        function isExpanded($header) {
            return $header.attr('data-advgb-expanded') === 'true';
        }

        function toggleItem($header) {
            const $body = $header.next('.advgb-accordion-body');
            const expanded = isExpanded($header);
            if (expanded) {
                $body.stop(true, true).slideUp(200);
            } else {
                $body.stop(true, true).slideDown(200);
            }
            setState($header, !expanded);
            if (!getTrigger($header).length) {
                $header.attr('aria-expanded', expanded ? 'false' : 'true');
            }
        }

        // Clicks on the button bubble here; clicks elsewhere on the header still toggle.
        $headers.on('click', function() {
            toggleItem($(this));
        });

        bindTriggerKeys($headers);

        // Keyboard for fallback headers without an inner button.
        $headers.on('keydown', function(e) {
            if (e.target !== this) {
                return;
            }
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                e.preventDefault();
                toggleItem($(this));
            }
        });
    }

    $(".advgb-accordion-wrapper").each(function() {
        const $wrapper = $(this);

        if ($wrapper.data('expand-all')) {
            initExpandAllMode($wrapper);
            return;
        }

        $wrapper.accordion({
            header: "> div > .advgb-accordion-header",
            heightStyle: "content",
            collapsible: true,
            active: $wrapper.data("collapsed") ? false : 0,
        });

        const $headers = getHeaders($wrapper);
        var allWrapped = true;

        $headers.each(function() {
            if (!addTrigger($(this))) {
                allWrapped = false;
            }
        });

        function syncFromWidget() {
            $headers.each(function() {
                var $header = $(this);
                setState($header, $header.hasClass('ui-accordion-header-active'));
            });
            stripWidgetAria($wrapper, $headers, allWrapped);

            // Headers left without an inner button keep jQuery UI semantics but stay tabbable.
            $headers.filter(function() { return !getTrigger($(this)).length; }).attr('tabindex', '0');
        }

        syncFromWidget();

        // jQuery UI rewrites header ARIA synchronously on every toggle; this handler is bound
        // after the widget's own click handler, so it runs right after it.
        $headers.on('click', syncFromWidget);
        $wrapper.on('accordionactivate', syncFromWidget);

        bindTriggerKeys($headers);
    });
});
