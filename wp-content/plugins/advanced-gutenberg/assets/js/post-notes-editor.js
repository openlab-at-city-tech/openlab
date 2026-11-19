/* global wp, navigator, document, MutationObserver */
(function () {
    if (! wp || ! wp.element || ! wp.data || ! wp.hooks || ! wp.blockEditor || ! wp.compose) {
        return;
    }

    // Setting from PHP (wp_localize_script). Default on if absent.
    var settings         = window.advgbPostNotes || {};
    var showBlockToolbar = settings.blockToolbar === undefined ? true : !! settings.blockToolbar;

    var el = wp.element.createElement;

    // Speech bubble with a "+" knocked out as an evenodd cutout, so the plus
    // stays visible whatever color the toolbar fills the icon with.
    function NoteIcon() {
        return el(
            'svg',
            {
                xmlns:   'http://www.w3.org/2000/svg',
                viewBox: '0 0 24 24',
                width:   '24',
                height:  '24',
            },
            el('path', {
                fill:     'currentColor',
                fillRule: 'evenodd',
                clipRule: 'evenodd',
                d: 'M5 4h14a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H8l-4 3V5a1 1 0 0 1 1-1z'
                 + 'M11 7h2v2h2v2h-2v2h-2v-2H9V9h2z',
            })
        );
    }

    /**
     * Add a note to a block using WordPress core's own Notes feature.
     *
     * Core does not expose a public action to create/anchor a note — the logic
     * lives in the editor's collab-sidebar and is bound to the
     * `core/editor/new-note` keyboard shortcut (⌥⌘M / Ctrl+Alt+M), which also
     * opens core's note composer. We select the block and fire that shortcut so
     * core performs the create + anchor exactly as its own "Add note" menu item
     * does. (Core only offers "Add note" in the block options menu; this adds a
     * top-level toolbar button for it.)
     */
    function triggerCoreAddNote(clientId) {
        var blockEditorDispatch = wp.data.dispatch('core/block-editor');
        var blockEditorSelect   = wp.data.select('core/block-editor');

        if (! clientId && blockEditorSelect) {
            clientId = blockEditorSelect.getSelectedBlockClientId();
        }
        if (clientId && blockEditorDispatch && blockEditorDispatch.selectBlock) {
            blockEditorDispatch.selectBlock(clientId);
        }

        // Dispatch from a node inside the editor's React tree so the delegated
        // keydown listener (React root) receives the shortcut.
        var node = (clientId && document.querySelector('[data-block="' + clientId + '"]'))
            || document.activeElement
            || document.body;
        if (node && node.focus) {
            try { node.focus(); } catch (e) {}
        }

        // primaryAlt = Cmd+Alt on macOS, Ctrl+Alt elsewhere; character "m"
        var isMac = /Mac|iPhone|iPad/i.test((navigator && navigator.platform) || '');
        var event = new KeyboardEvent('keydown', {
            key:        'm',
            code:       'KeyM',
            keyCode:    77,
            which:      77,
            metaKey:    isMac,
            ctrlKey:    ! isMac,
            altKey:     true,
            bubbles:    true,
            cancelable: true,
        });

        // Defer a tick so block selection settles before the shortcut fires
        setTimeout(function () {
            (node || document).dispatchEvent(event);
        }, 0);
    }

    // Top-level "Add note" button on every block's toolbar
    if (showBlockToolbar) {
        var BlockControls = wp.blockEditor.BlockControls;
        var ToolbarGroup  = wp.components.ToolbarGroup;
        var ToolbarButton = wp.components.ToolbarButton;
        var createHOC     = wp.compose.createHigherOrderComponent;

        var withNoteToolbarButton = createHOC(function (BlockEdit) {
            return function (props) {
                return el(
                    wp.element.Fragment,
                    null,
                    el(BlockEdit, props),
                    props.isSelected && el(
                        BlockControls,
                        { group: 'other' },
                        el(
                            ToolbarGroup,
                            null,
                            el(ToolbarButton, {
                                icon:    el(NoteIcon, null),
                                label:   ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__( 'Add note', 'advanced-gutenberg' ) : 'Add note',
                                onClick: function () { triggerCoreAddNote(props.clientId); },
                            })
                        )
                    )
                );
            };
        }, 'withNoteToolbarButton');

        wp.hooks.addFilter(
            'editor.BlockEdit',
            'advgb/post-notes-toolbar-button',
            withNoteToolbarButton
        );
    }

    /**
     * Open-notes counter on core's "All notes" toolbar icon.
     *
     * The count is computed server-side and passed in via wp_localize_script
     * (advgbPostNotes.openNotesCount) — no REST request needed here. This JS
     * only finds core's button and overlays a badge. Core re-renders its
     * header on state changes, so a light interval re-applies the badge if
     * React wipes it.
     */
    (function () {
        var count = parseInt(settings.openNotesCount, 10) || 0;
        if (! count) {
            return;
        }

        var styleInjected = false;

        // Core's pinned "All notes" button controls the complementary area
        // "edit-post/collab-history-sidebar" (aria-controls="edit-post:collab-history-sidebar").
        // That id is not translated, so this works in every site language.
        var NOTES_SIDEBAR = 'edit-post:collab-history-sidebar';

        function findNotesButton() {
            var button = document.querySelector(
                '.interface-pinned-items button[aria-controls="' + NOTES_SIDEBAR + '"], '
                + '.editor-header__settings button[aria-controls="' + NOTES_SIDEBAR + '"]'
            );
            if (button) {
                return button;
            }

            // Fallback for editors without that id: match core's translated "All notes" label.
            var label = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__( 'All notes' ) : 'All notes';
            var candidates = document.querySelectorAll(
                '.interface-pinned-items button, '
                + '.editor-header__settings button, '
                + '.edit-post-header__settings button'
            );
            for (var i = 0; i < candidates.length; i++) {
                if (candidates[i].getAttribute('aria-label') === label) {
                    return candidates[i];
                }
            }

            return null;
        }

        function applyBadge() {
            var button = findNotesButton();
            if (! button || button.querySelector('.advgb-notes-badge')) {
                return;
            }

            if (! styleInjected) {
                styleInjected = true;
                var style = document.createElement('style');
                style.textContent =
                    '.advgb-notes-badge{position:absolute;top:-2px;right:-2px;min-width:15px;'
                    + 'height:15px;padding:0 3px;border-radius:999px;background:#d63638;'
                    + 'color:#fff;font-size:10px;font-weight:600;line-height:15px;'
                    + 'text-align:center;pointer-events:none;z-index:1;}';
                document.head.appendChild(style);
            }

            if (! button.style.position) {
                button.style.position = 'relative';
            }
            var badge = document.createElement('span');
            badge.className   = 'advgb-notes-badge';
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.setAttribute('aria-hidden', 'true');
            button.appendChild(badge);
        }

        // The header renders asynchronously (and re-renders); keep re-applying.
        applyBadge();
        setInterval(applyBadge, 1000);
    })();
})();
