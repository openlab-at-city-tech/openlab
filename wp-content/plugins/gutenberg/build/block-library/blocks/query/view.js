/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wp","interactivity"]
var external_wp_interactivity_namespaceObject = window["wp"]["interactivity"];
;// CONCATENATED MODULE: ./packages/block-library/build-module/query/view.js
/**
 * WordPress dependencies
 */

const isValidLink = ref => ref && ref instanceof window.HTMLAnchorElement && ref.href && (!ref.target || ref.target === '_self') && ref.origin === window.location.origin;
const isValidEvent = event => event.button === 0 &&
// Left clicks only.
!event.metaKey &&
// Open in new tab (Mac).
!event.ctrlKey &&
// Open in new tab (Windows).
!event.altKey &&
// Download.
!event.shiftKey && !event.defaultPrevented;
(0,external_wp_interactivity_namespaceObject.store)('core/query', {
  state: {
    get startAnimation() {
      return (0,external_wp_interactivity_namespaceObject.getContext)().animation === 'start';
    },
    get finishAnimation() {
      return (0,external_wp_interactivity_namespaceObject.getContext)().animation === 'finish';
    }
  },
  actions: {
    *navigate(event) {
      const ctx = (0,external_wp_interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      const isDisabled = ref.closest('[data-wp-navigation-id]')?.dataset.wpNavigationDisabled;
      if (isValidLink(ref) && isValidEvent(event) && !isDisabled) {
        event.preventDefault();
        const id = ref.closest('[data-wp-navigation-id]').dataset.wpNavigationId;

        // Don't announce the navigation immediately, wait 400 ms.
        const timeout = setTimeout(() => {
          ctx.message = ctx.loadingText;
          ctx.animation = 'start';
        }, 400);
        yield (0,external_wp_interactivity_namespaceObject.navigate)(ref.href);

        // Dismiss loading message if it hasn't been added yet.
        clearTimeout(timeout);

        // Announce that the page has been loaded. If the message is the
        // same, we use a no-break space similar to the @wordpress/a11y
        // package: https://github.com/WordPress/gutenberg/blob/c395242b8e6ee20f8b06c199e4fc2920d7018af1/packages/a11y/src/filter-message.js#L20-L26
        ctx.message = ctx.loadedText + (ctx.message === ctx.loadedText ? '\u00A0' : '');
        ctx.animation = 'finish';
        ctx.url = ref.href;

        // Focus the first anchor of the Query block.
        const firstAnchor = `[data-wp-navigation-id=${id}] .wp-block-post-template a[href]`;
        document.querySelector(firstAnchor)?.focus();
      }
    },
    *prefetch() {
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      const isDisabled = ref.closest('[data-wp-navigation-id]')?.dataset.wpNavigationDisabled;
      if (isValidLink(ref) && !isDisabled) {
        yield (0,external_wp_interactivity_namespaceObject.prefetch)(ref.href);
      }
    }
  },
  callbacks: {
    *prefetch() {
      const {
        url
      } = (0,external_wp_interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      if (url && isValidLink(ref)) {
        yield (0,external_wp_interactivity_namespaceObject.prefetch)(ref.href);
      }
    }
  }
});

/******/ })()
;