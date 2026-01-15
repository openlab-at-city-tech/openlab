class BaseSwitcher {
    constructor(rootEl) {
        this.root = rootEl;
        this.list = rootEl?.querySelector('.trp-switcher-dropdown-list') || null;
        this.isOpen = false;
        this._pendingFocusOnOpen = false;
        if (!this.root || !this.list) return;

        if (!this.list.id) {
            this.list.id = `trp-list-${Math.random().toString(36).slice(2, 9)}`;
        }

        // Single transitionend handler to drop .is-transitioning and finalize hidden/inert
        this._onTe = (e) => {
            if (e.target !== this.list || e.propertyName !== 'max-height') return;

            this.root.classList.remove('is-transitioning');

            if (!this.isOpen) {
                this.list.hidden = true;
                this.list.setAttribute('inert', '');
            } else if (this._pendingFocusOnOpen) {
                this._pendingFocusOnOpen = false;
                const first = this.list.querySelector(
                    '[role="option"], a, button, [tabindex]:not([tabindex="-1"])'
                );
                first?.focus?.({ preventScroll: true });
            }
        };
        this.list.addEventListener('transitionend', this._onTe);

        this.collapse();
        this.setAutoWidth();
        this.bindKeyboard();
    }

    collapse() {
        this.list.hidden = true;
        this.list.setAttribute('inert', '');
        this.setExpanded(false);
        this.root.classList.remove('is-transitioning');
    }

    /**
     * If width is set to auto, fix it to the calculated width + 20px
     * We do this in order to avoid width shift on hover
     * */
    setAutoWidth() {
        const bonusWidth = 10;

        const cs = getComputedStyle(this.root);
        const declaredWidth = cs.getPropertyValue('--switcher-width').trim();

        if (declaredWidth === 'auto' && this.root.querySelector('.trp-language-item-name')) { // In case trp-language-item-name is not present, we are in flags only mode - so no auto width sizing is needed
            const initialWidth = this.root.getBoundingClientRect().width;

            this.root.style.setProperty('--switcher-width', (initialWidth + bonusWidth) + 'px');
        }
    }

    setExpanded(open) {
        const trigger = this.root.querySelector('.trp-language-item__current[role="button"]');
        const val = String( !!open );
        trigger?.setAttribute('aria-expanded', val);
        this.root.classList.toggle('is-open', !!open);
    }

    setOpen(open, { source = null } = {}) {
        if (!this.root || !this.list || open === this.isOpen) return;

        // Honor reduced motion: skip the transition entirely (still class-driven)
        const prefersReduced = window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;

        this.isOpen = open;

        if (open) {
            // Prepare: must be visible for CSS transition to run
            this.list.hidden = false;
            this.list.removeAttribute('inert');

            if (prefersReduced) {
                this.root.classList.remove('is-transitioning');
                this.setExpanded(true);
            } else {
                this.root.classList.add('is-transitioning');
                // Next frame so the browser registers the pre-open (max-height:0) state
                requestAnimationFrame(() => this.setExpanded(true));
            }

            // keyboard open should move focus after transition completes
            this._pendingFocusOnOpen = (source?.type === 'keydown');

        } else {
            if (prefersReduced){
                this.root.classList.add( 'is-transitioning' );
            }

            this.setExpanded(false);
        }
    }

    bindKeyboard() {
        const trigger = this.root.querySelector('.trp-language-item__current[role="button"]');
        if ( !trigger ) return;

        trigger.addEventListener('keydown', (e) => {
            const inList = !!e.target.closest?.('.trp-switcher-dropdown-list');

            if ( e.key === 'Enter' || e.key === ' ' ) {
                e.preventDefault();
                this.setOpen(!this.isOpen, { source: e });
                return;
            }

            if ( e.key === 'ArrowDown' && !this.isOpen ) {
                e.preventDefault();
                this.setOpen(true, { source: e });
            }

            if ( e.key === 'Escape' ) {
                this.setOpen(false, { source: e });
                trigger.focus?.();
            }
        });
    }
}

class ShortcodeSwitcher extends BaseSwitcher {
    constructor(wrapper) {
        const overlay =
                  wrapper.querySelector('.trp-language-switcher.trp-shortcode-overlay')
                  || [...wrapper.querySelectorAll('.trp-language-switcher')]
                      .find(el => el.classList.contains('trp-shortcode-overlay'));

        // Overlay must be interactable; ensure no accidental hidden/inert from server
        overlay.hidden = false;
        overlay.removeAttribute('hidden');
        overlay.removeAttribute('inert');
        if ('inert' in overlay) overlay.inert = false;

        super(overlay);

        if (!this.root || !this.list) return;

        const control = this.root.querySelector('.trp-language-item__current[role="button"]');
        if (control && this.list && !control.hasAttribute('aria-controls')) {
            control.setAttribute('aria-controls', this.list.id);
        }

        const isClickMode =
                  this.root.classList.contains('trp-open-on-click') ||
                  wrapper.dataset.openMode === 'click' ||
                  wrapper.classList.contains('trp-open-on-click');

        if (isClickMode) {
            // Click anywhere on overlay EXCEPT inside the list
            this.root.addEventListener('click', (e) => {
                const inList = e.target.closest('.trp-switcher-dropdown-list');
                if (!inList) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.setOpen(!this.isOpen, { source: e });
                }
            }, true);

            // Outside click closes
            this.onDocClick = (evt) => {
                if (!wrapper.contains(evt.target)) this.setOpen(false, { source: evt });
            };
            document.addEventListener('click', this.onDocClick, true);

            // Focus leaving wrapper closes
            wrapper.addEventListener('focusout', () => {
                setTimeout(() => {
                    if (!wrapper.contains(document.activeElement)) {
                        this.setOpen(false, { source: 'keyboard' });
                    }
                }, 0);
            });
        } else {
            // Hover mode on overlay
            this.root.addEventListener('mouseenter', (e) => this.setOpen(true,  { source: e }));
            this.root.addEventListener('mouseleave', (e) => this.setOpen(false, { source: e }));
        }
    }
}

class FloaterSwitcher extends BaseSwitcher {
    constructor(el) {
        super(el);

        el.addEventListener('mouseenter', (e) => this.setOpen(true,  { source: e }));
        el.addEventListener('mouseleave', (e) => this.setOpen(false, { source: e }));

        this.onDocClick = (evt) => { if (!el.contains(evt.target)) this.setOpen(false, { source: evt }); };
        document.addEventListener('click', this.onDocClick, true);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Front-end or classic admin
    initLanguageSwitchers(document);

    // If no wrapper and we are in Gutenberg, watch for async SSR insert
    if (
        inGutenberg() &&
        !getEditorDoc().querySelector(WRAPPER)
    ) {
        observeWrapperUntilFound();
    }

    if ( !inGutenberg() )
        observeShortcodeSwitcher();
});

/** For shortcode switcher
 *  Mark the shortcodes that were initialized
 * */
const TRP_BOUND = new WeakSet();
const mark = (el) => TRP_BOUND.add(el);
const isMarked = (el) => TRP_BOUND.has(el);

const WRAPPER = '.trp-shortcode-switcher__wrapper';
const OVERLAY = '.trp-language-switcher:not(.trp-opposite-button)';

// Helpers
function inGutenberg() {
    return document.body?.classList?.contains('block-editor-page')
        || !!(window.wp?.data?.select?.('core/block-editor'));
}

// If editor uses an iframe canvas, work inside it
function getEditorDoc() {
    const ifr = document.querySelector('iframe[name="editor-canvas"], .editor-canvas__iframe');

    return (ifr && ifr.contentDocument) ? ifr.contentDocument : document;
}

function initLanguageSwitchers(root = document) {
    const floater = root.querySelector(
        '.trp-language-switcher.trp-ls-dropdown:not(.trp-shortcode-switcher):not(.trp-opposite-language)'
    );

    if (floater)
        new FloaterSwitcher(floater);

    root.querySelectorAll(WRAPPER)
        .forEach(wrapper => {
            const overlay = wrapper.querySelector('.trp-language-switcher:not(.trp-opposite-button)');

            if (overlay && !isMarked(overlay)) {
                mark(overlay);
                new ShortcodeSwitcher(wrapper);
            }
        });
}

/**
 * Observes the document for dynamically inserted shortcode switchers and initializes them automatically when detected.
 */
function observeShortcodeSwitcher() {
    const initWrapper = ( wrapper ) => {
        if ( !wrapper )
            return;

        const overlay = wrapper.querySelector( OVERLAY );

        if ( !overlay || isMarked( overlay ) )
            return;

        mark( overlay );

        new ShortcodeSwitcher( wrapper );
    }

    const mo = new MutationObserver( ( mutations ) => {
        for ( const m of mutations ) {
            for ( const n of m.addedNodes ) {
                if ( n.nodeType !== 1 )
                    continue;

                if ( n.matches?.( WRAPPER ) )
                    initWrapper( n );

                n.querySelectorAll?.( WRAPPER ).forEach( initWrapper );
            }
        }
    });

    mo.observe( document, { childList: true, subtree: true } );
}

/**
 * Observe Gutenberg for the shortcode wrapper being inserted asynchronously.
 *
 * Supports both Blocks API v2 (no editor iframe; wrapper appears in the outer document)
 * and Blocks API v3 (editor content rendered inside an iframe canvas).
 *
 * Strategy:
 *  1) Check the current editor document for `.trp-shortcode-switcher__wrapper` and init immediately.
 *  2) If an editor canvas iframe exists, watch its document (and reattach on iframe load) for the wrapper.
 *  3) If no iframe yet, watch the outer document for either the iframe (v3) or the wrapper itself (v2).
 *
 * Initialization is performed once per context to avoid duplicate bindings.
 */
function observeWrapperUntilFound() {
    // If wrapper already exists in current editor doc, init
    const edDoc = getEditorDoc();
    const existing = edDoc.querySelector(WRAPPER);

    if ( existing ) {
        initLanguageSwitchers( edDoc );
        return;
    }

    // Helper to locate the editor canvas iframe in the OUTER document
    const findCanvasIframe = () => document.querySelector('iframe[name="editor-canvas"], .editor-canvas__iframe');

    // If iframe is already present in the outer doc, start watching inside it
    const iframeNow = findCanvasIframe();
    if ( iframeNow ) {
        watchIframe( iframeNow );
        return;
    }

    // Otherwise, observe the OUTER document until the iframe appears
    const outerMO = new MutationObserver( ( mutations ) => {
        for ( const m of mutations ) {
            for ( const n of m.addedNodes ) {
                if ( n.nodeType !== 1 ) continue;

                const iframe =
                          n.matches?.('iframe[name="editor-canvas"], .editor-canvas__iframe')
                              ? n
                              : n.querySelector?.('iframe[name="editor-canvas"], .editor-canvas__iframe');

                if ( iframe ) {
                    outerMO.disconnect();
                    watchIframe( iframe );
                    return;
                }

                // Also catch shortcode wrapper added directly to the outer document (API v2, no iframe)
                const wrapper =
                          n.matches?.(WRAPPER)
                              ? n
                              : n.querySelector?.(WRAPPER);

                if ( wrapper ) {
                    outerMO.disconnect();
                    initLanguageSwitchers( document );
                    return;
                }

            }
        }
    } );
    outerMO.observe( document, { childList: true, subtree: true } );

    function watchIframe( iframe ) {
        // Try immediately (some builds inject srcdoc synchronously)
        tryAttachInside();

        // Also on load/navigate (Gutenberg may reload the canvas)
        iframe.addEventListener( 'load', tryAttachInside );

        function tryAttachInside() {
            let doc;
            try {
                doc = iframe.contentDocument || iframe.contentWindow?.document;
            } catch (e) {
                console.warn('Cannot access iframe content due to cross-origin restrictions', e);
                return;
            }
            if ( !doc ) return;

            // If wrapper is already there, init once and stop.
            const hit = doc.querySelector(WRAPPER);
            if ( hit ) {
                initLanguageSwitchers( doc );
                return;
            }

            // Observe INSIDE the iframe until wrapper appears
            const innerMO = new MutationObserver( ( muts ) => {
                for ( const mm of muts ) {
                    for ( const nn of mm.addedNodes ) {
                        if ( nn.nodeType !== 1 ) continue;
                        if (
                            nn.matches?.(WRAPPER) ||
                            nn.querySelector?.(WRAPPER)
                        ) {
                            innerMO.disconnect();
                            initLanguageSwitchers( doc );
                            return;
                        }
                    }
                }
                if ( doc.querySelector(WRAPPER) ) {
                    innerMO.disconnect();
                    initLanguageSwitchers( doc );
                }
            } );

            innerMO.observe( doc, { childList: true, subtree: true } );
        }
    }
}