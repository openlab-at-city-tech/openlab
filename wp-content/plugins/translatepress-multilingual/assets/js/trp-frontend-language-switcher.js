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

        this.bindKeyboard(this.root);
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
        const bonusWidth = 20;

        const cs = getComputedStyle(this.root);
        const declaredWidth = cs.getPropertyValue('--switcher-width').trim();

        if (declaredWidth === 'auto') {
            const initialWidth = this.root.getBoundingClientRect().width;

            this.root.style.setProperty('--switcher-width', (initialWidth + bonusWidth) + 'px');
        }
    }

    setExpanded(open) {
        const val = String(!!open);
        this.root.setAttribute('aria-expanded', val);
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
            if (prefersReduced) {
                this.setExpanded(false);
                this.list.hidden = true;
                this.list.setAttribute('inert', '');
                this.root.classList.remove('is-transitioning');
            } else {
                this.root.classList.add('is-transitioning');
                // Removing is-open triggers CSS max-height → 0 animation
                this.setExpanded(false);
            }
        }
    }

    bindKeyboard(target) {
        target.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.setOpen(!this.isOpen, { source: e });
            }
            if (e.key === 'Escape') {
                this.setOpen(false, { source: e });
                target.focus?.();
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

        if (!overlay) {
            console.warn('[TRP] Shortcode overlay not found inside wrapper:', wrapper);
            return;
        }

        // Overlay must be interactable; ensure no accidental hidden/inert from server
        overlay.hidden = false;
        overlay.removeAttribute('hidden');
        overlay.removeAttribute('inert');
        if ('inert' in overlay) overlay.inert = false;

        super(overlay);
        if (!this.root || !this.list) return;

        // ARIA on overlay (focusable container)
        this.root.setAttribute('role', 'listbox');
        this.root.setAttribute('aria-haspopup', 'listbox');
        this.root.setAttribute('aria-controls', this.list.id);
        if (!this.root.hasAttribute('tabindex')) this.root.setAttribute('tabindex', '0');

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
        if (el.classList.contains('trp-opposite-language')) return;
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
        !getEditorDoc().querySelector('.trp-shortcode-switcher__wrapper')
    ) {
        observeWrapperUntilFound();
    }
});

/** For shortcode switcher
 *  Mark the shortcodes that were initialized
 *
 * */
const TRP_BOUND = new WeakSet();
const mark = (el) => TRP_BOUND.add(el);
const isMarked = (el) => TRP_BOUND.has(el);

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
    root.querySelectorAll('.trp-language-switcher.trp-ls-dropdown:not(.trp-shortcode-switcher)')
        .forEach(el => { if (!isMarked(el)) { mark(el); new FloaterSwitcher(el); } });

    root.querySelectorAll('.trp-shortcode-switcher__wrapper')
        .forEach(wrapper => {
            const overlay = wrapper.querySelector('.trp-language-switcher');

            if (overlay && !isMarked(overlay)) {
                mark(overlay);
                new ShortcodeSwitcher(wrapper);
            }
        });
}

function observeWrapperUntilFound() {
    const edDoc = getEditorDoc();

    // If it already exists, init and stop.
    if (edDoc.querySelector('.trp-shortcode-switcher__wrapper')) {
        initLanguageSwitchers(edDoc);

        return;
    }

    const mo = new MutationObserver((mutations) => {
        for (const m of mutations) {
            for (const n of m.addedNodes) {
                if (!(n instanceof Element)) continue;

                if (
                    n.matches?.('.trp-shortcode-switcher__wrapper') ||
                    n.querySelector?.('.trp-shortcode-switcher__wrapper')
                ) {
                    initLanguageSwitchers(edDoc);
                }
            }
        }
    });

    mo.observe(edDoc, { childList: true, subtree: true });
}

