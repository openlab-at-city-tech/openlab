import { Ed11y, Lang, State, UI, findElements, elements, computeAccessibleName, createDismissalKey, getElements, refresh, sanitizeHTML, setFixedRoots, version } from 'editoria11y-js';
import { lang as ed11yUiLang } from 'editoria11y-lang';
import { lang as ed11yContentLang } from 'editoria11y-lang-content';
import {
  ed11yLoadStaticConfig,
  ed11yApplyStaticConfig,
  ed11yApplyOptionTranslations as ed11ySharedOptionTranslations,
} from './editoria11y-option-translations.js';

// Build the dual-language dictionary the bundled library consumes.
//
// The checker reads ONE flat string table (Lang.langStrings) for both the
// panel/tip UI *and* the content-detection ruleset (stopword lists, the
// "click here" set, suspicious-alt words). We want those two halves in
// different languages: UI in the editor's locale, ruleset in the locale of
// the content being scanned.
//
// PHP enqueues two packs (editoria11y-lang = UI locale, editoria11y-lang-content
// = content locale). Each pack exports `{ strings, testNames, ruleset }`,
// where `strings` already contains the ruleset keys; assigning the content
// pack's `ruleset` over the UI pack's `strings` swaps just the
// content-detection half. When both packs are the same locale the assign is
// a harmless no-op. Pure helper, exported for the unit suite.
export function ed11yBuildLang(uiLang, contentLang) {
  const ui = uiLang && typeof uiLang === 'object' ? uiLang : {};
  const content = contentLang && typeof contentLang === 'object' ? contentLang : {};
  return {
    strings: Object.assign({}, ui.strings, content.ruleset),
    testNames: ui.testNames,
    ruleset: content.ruleset || ui.ruleset || {},
  };
}

const ed11yLang = ed11yBuildLang(ed11yUiLang, ed11yContentLang);

// Seed the runtime table now so any early lookup resolves. The library
// re-applies this from `options.lang` during construction (see ed11yInit),
// which is what actually survives — without `options.lang` set, the library
// resets Lang back to its built-in English pack. testNames are panel labels,
// always the editor's (UI) locale.
Lang.addI18n(ed11yLang.strings);
Lang.testNames = { ...(Lang.testNames || {}), ...(ed11yUiLang.testNames || {}) };

// Shared translation layer (see editoria11y-option-translations.js).
// Front-end binding: autodetect a checkRoot fallback when the admin
// configured none. Exported for the unit suite.
export function ed11yApplyOptionTranslations(options) {
  ed11ySharedOptionTranslations(options, { autodetectCheckRoot: true });
}

let ed11yOptions = {};
let ed11yResetID = false;

// Create callback to see if document is ready.
function ed11yReady(fn) {
  if (document.readyState != 'loading') {
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

// Resolve a WordPress attachment ID from an <img> class attribute.
//
// Featured/template images get a `data-id` from the
// wp_get_attachment_image_attributes PHP filter, but body-content images
// (block/classic editor) are stored as static HTML.
function ed11yWpImageIdFromClass(classAttr) {
  if (typeof classAttr !== 'string') {
    return null;
  }
  for (const token of classAttr.split(/\s+/)) {
    const match = /^wp-image-(\d+)$/.exec(token);
    if (match) {
      const id = parseInt(match[1], 10);
      if (id > 0) {
        return id;
      }
    }
  }
  return null;
}

// Tests whose warnings may be dismissed "on all pages" (a global dismissal /
// okAll). Ported from the Drupal sync's `okTests` allowlist
// (editoria11ySync.js). The match is a substring test, so `BTN` covers
// BTN_EMPTY / BTN_ROLE_IN_NAME, `LABELS` covers every LABELS_* variant, etc.
// Only site-furniture warnings that recur identically across pages belong
// here — content-specific problems should be fixed per page, not silenced
// site-wide, so they never get the button.
//
// Gated with the okAll UI it serves: the free build has no site-wide button,
// so the constant + predicate strip too (the unit suite duplicates the helper
// rather than importing it, so gating the source doesn't cost coverage).

// Notify the parent crawler that this iframe's scan completed (or was
// skipped for a benign reason — password form, missing config, etc.).
//
// The parent (assets/js/editoria11y-crawler.js) sets `window.ed11ySynced`
// as a sentinel before opening any iframes, then listens for
// `{ done: <iframe-src> }` postMessage events to drain the iframe pool.
// Without this emitter, iframes that complete a scan fast enough never
// signal "done" and the parent's stuck-iframe decay loop has to time
// them out at ~20s each — which compounds badly across thousands of URLs.
//
// Same-origin only: cross-origin parents reject the postMessage and the
// try/catch eats the error silently. The sentinel check (`!== undefined`)
// also guards against the everyday case of the iframe being opened by
// something other than the crawler (a normal browser window).
function reportSyncDone() {
  try {
    if (
      window.parent &&
      window.parent !== window &&
      window.parent.ed11ySynced !== undefined
    ) {
      window.parent.postMessage(
        { done: String(window.location) },
        window.location.origin
      );
    }
  } catch (_e) {
    /* cross-origin parent — fail silently */
  }
}

function ed11ySync() {
  let postData = function (action, data) {
    // Returns the promise so callers can chain reportSyncDone after the
    // PUT settles. Previously fire-and-forget — the promise's resolution
    // had no observers, which is why crawler-driven iframes never told
    // the parent they were done.
    return fetch(wpApiSettings.root + 'ed11y/v1/' + action, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'accept': 'application/json',
        'X-WP-Nonce': wpApiSettings.nonce,
      },
      body: JSON.stringify({
        data,
      })
    }).then(function (response) {
      if (response.status === 401 || response.status === 403) {
        // Nonce lifetime is ~12-24h; a page left open past it can no
        // longer sync. Name the cause instead of failing silently.
        console.warn('Editoria11y: sync rejected (' + response.status + '). Your login session likely expired; reload the page to resume syncing results and dismissals.');
      } else if (!response.ok) {
        console.warn('Editoria11y: sync failed with HTTP ' + response.status + '.');
      }
      return response.json();
    }).catch(function (err) {
      console.warn('Editoria11y: sync request failed.', err);
    });
  };

  // Build the results-report payload from the library's live result set.
  //
  // Counts are the DISPLAYED (okAll-adjusted) counts: items marked OK — a
  // per-page `ok` or a site-wide `okAll` — are excluded, so a page's stored
  // total already reflects its global dismissals. Because okAll is cross-page,
  // pages that adjust down while it's active can't restore themselves on a
  // reset without re-scanning; the server does that from the okAll mapping
  // below (see ApiDismissals reset). `hide` is a personal visibility toggle,
  // not a "this is fine" judgement, so hidden items still count.
  //
  // Each result is bucketed content vs. developer by `outsideContentRoots`
  // (page furniture the editor can't reach from the content region), mirroring
  // Drupal's `element.closest(contentSyncRoot)` split, so the dashboard's
  // content/dev columns — and a later okAll reset — land in the right bucket.
  let extractResults = function () {
    let results = {};
    let dismissals = [];
    let contentTotal = 0;
    let devTotal = 0;
    // okAll elements present on this page, tagged with their bucket, so the
    // server can refresh the per-page global-dismissal mapping a reset needs
    // to know which pages (and buckets) to restore.
    let okAllApplied = [];
    State.results.forEach((result) => {
      const testKey = result.test;
      const testName = (Lang.testNames && Lang.testNames[testKey]) || testKey;
      // outsideContentRoots is set by the library's dev/content split; absent
      // (free / non-CSA) everything is content, which is the correct default.
      const inContent = result.outsideContentRoots ? 0 : 1;

      // Count everything except items marked OK (per-page `ok` or site-wide
      // `okAll`); `hide` still counts.
      if (result.dismissalStatus !== 'ok' && result.dismissalStatus !== 'okAll') {
        if (!results[testKey]) {
          results[testKey] = { content_count: 0, dev_count: 0, result_name: testName };
        }
        results[testKey].content_count += inContent;
        results[testKey].dev_count += 1 - inContent;
        contentTotal += inContent;
        devTotal += 1 - inContent;
      }

      // The dismiss key lives on `result.dismiss` (the library's pepper-hashed
      // digest) — the previous `result.dismissalKey` was always undefined, so
      // the server's stale-refresh matched nothing. Using `dismiss` also makes
      // element_ids agree with what the dismiss endpoint stored.
      if (result.dismissalStatus === 'ok' || result.dismissalStatus === 'hide') {
        // Refresh existing page-scoped rows (keeps them off the stale sweep).
        dismissals.push([testKey, result.dismiss]);
      } else if (result.dismissalStatus === 'okAll') {
        okAllApplied.push({
          result_key: testKey,
          element_id: result.dismiss,
          result_name: testName,
          in_content: inContent,
        });
      }
    });
    return { results, dismissals, contentTotal, devTotal, okAllApplied };
  };

  let url = State.option.currentPage;
  url = url.length > 190 ? url.substring(0, 189) : url;
  let queryString = window.location.search;
  let urlParams = new URLSearchParams(queryString);
  ed11yResetID = urlParams.get('ed1ref');

  let sendResults = function () {
    window.setTimeout(function () {
      if (ed11yOptions.post_id && document.getElementsByClassName('post-password-form').length > 0) {
        // Don't sync "0 results" if the post has been replaced by a login form.
        // Still notify the crawler parent so it doesn't wait the 20s decay.
        reportSyncDone();
        return;
      }
      let results = extractResults();
      let data = {
        page_title: ed11yOptions.title,
        post_id: ed11yOptions.post_id ? ed11yOptions.post_id : 0,
        // page_count is the content bucket (ed11y_urls.page_total); dev_count
        // the developer bucket (ed11y_urls.dev_total).
        page_count: results.contentTotal,
        dev_count: results.devTotal,
        entity_type: ed11yOptions.entity_type, // node or false
        results: results.results,
        dismissals: results.dismissals,
        // Per-page global-dismissal mapping (empty unless CSA okAll is in play).
        okAllApplied: results.okAllApplied,
        page_url: url,
        created: 0,
        pid: ed11yResetID ? parseInt(ed11yResetID) : -1,
      };
      // Fire reportSyncDone after the PUT settles, success or failure —
      // the crawler only cares that this iframe is no longer in flight.
      postData('result', data).then(reportSyncDone, reportSyncDone);
      ed11yResetID = false;
      // Short timeout to let execution queue clear.
    }, 100);
  };

  let resetResults = function () {
    window.setTimeout(function () {
      // Explicit empties: this path clears a route that should have no
      // results (arrived from the dashboard via ?ed1ref). The old positional
      // `results[0..2]` reads were always undefined; the server defaulted
      // them, but sending real empties is clearer and shape-stable.
      let data = {
        page_title: ed11yOptions.title,
        page_count: 0,
        dev_count: 0,
        entity_type: ed11yOptions.entity_type, // node or false
        results: {},
        dismissals: [],
        okAllApplied: [],
        page_url: url,
        created: 0,
        pid: ed11yResetID ? parseInt(ed11yResetID) : -1,
        post_id: ed11yOptions.post_id ? ed11yOptions.post_id : 0,
      };
      postData('result', data).then(reportSyncDone, reportSyncDone);
      // Short timeout to let execution queue clear.
    }, 100);
  };
  if (ed11yResetID && ed11yOptions.preventCheckingIfPresent && !!document.querySelector(ed11yOptions.preventCheckingIfPresent)) {
    // We just got here from the dashboard and there should not be results at this route.
    resetResults();
  }

  document.addEventListener('ed11yResults', function () {
    sendResults();
  });

  let sendDismissal = function (detail) {
    if (detail) {
      let dismissalStatus = detail.dismissAction; // ok, hide or reset
      // Buckets a global dismissal (content vs. page furniture) so a later
      // reset restores each affected page's count into the correct column.
      // Always 1 (content) for non-global dismissals, where the server ignores it.
      let inContent = 1;

      let data = {
        // Reuse the truncated `url` from the results sender: the column is
        // varchar(190), and a dismissal keyed on the full URL while the
        // result row keyed on the truncated one would never match.
        page_url: url,
        page_title: ed11yOptions.title,
        entity_type: ed11yOptions.entity_type,
        page_count: UI.totalCount,
        result_key: detail.dismissTest, // which test is sending a result
        element_id: detail.dismissKey, // the library's pepper-hashed dismiss digest
        dismissal_status: dismissalStatus, // ok, okAll, hide or reset
        in_content: inContent,
        post_id: ed11yOptions.post_id ? ed11yOptions.post_id : 0,
      };
      postData('dismiss', data);
    }
  };
  document.addEventListener('ed11yDismissalUpdate', function (e) {
    sendDismissal(e.detail);
  }, false);

}

const ed11yCustomTests = function () {
  document.addEventListener('ed11yRunCustomTests', function () {

    // Find empty WP buttons.
    findElements('emptyWpButton', 'a.wp-element-button:not([href], [tabindex])');

    // Register a human-readable test name. Strings ride the init blob
    // (this is a plugin-side test the library lang packs don't know);
    // English fallbacks cover a stale cached blob. The name here must
    // match the label the dashboard blob ships for the same key, so the
    // tip, front-end panel and report all agree.
    const i18n = ed11yOptions.i18n || {};
    Lang.testNames.emptyWpButton = i18n.emptyWpButtonName || 'Empty button-style link';
    const wrapper = document.createElement('div');
    const title = document.createElement('div');
    title.textContent = i18n.emptyWpButtonTitle || 'Empty link';
    title.classList.add('title');
    const p = document.createElement('p');
    p.textContent = i18n.emptyWpButtonTip || 'The button style link is missing its URL.';
    wrapper.appendChild(title);
    wrapper.appendChild(p);

    elements.emptyWpButton?.forEach((el) => {
      State.results.push({
        element: el,
        test: 'emptyWpButton',
        content: wrapper,
        position: 'beforebegin',
        dismissalKey: false,
        type: 'warning',
      });
    });
    let allDone = new CustomEvent('ed11yResume');
    document.dispatchEvent(allDone);
  });
};

const ed11yInit = async function () {

  let ed11yOpts = document.getElementById('editoria11y-init');
  if (!!ed11yOpts && window.location.href.indexOf('elementor-preview') === -1) {
    ed11yOptions = JSON.parse(ed11yOpts.innerHTML);
    // Merge the immutable static config blob into dynamic options.
    ed11yApplyStaticConfig(
      await ed11yLoadStaticConfig(ed11yOptions.config_url),
      ed11yOptions
    );

    // +1 for the built-in `emptyWpButton` test.
    ed11yOptions.customTests = (Number.parseInt(ed11yOptions.customTests, 10) || 0) + 1;

    // Hard-coded known conflicts for panel placement.
    ed11yOptions.panelNoCover = ed11yOptions.panelNoCover
      ? `${ed11yOptions.panelNoCover}, #edac-highlight-panel`
      : '#edac-highlight-panel';

    ed11yOptions.reportsURL = !ed11yOptions.hideReportLink && ed11yOptions.adminUrl ? ed11yOptions.adminUrl + '?page=editoria11y' : false;

    if (ed11yOptions.title.length < 3) {
      ed11yOptions.title = document.title;
    }

    // All the static config → library option translations (RegExp build,
    // CSV → array, embeddedContentWarning → checks.EMBED_CUSTOM,
    // ignoreTests → checks[KEY]=false, dev-profile alertMode swap).
    ed11yApplyOptionTranslations(ed11yOptions);

    // CSA: build splitConfiguration + enable dev/contrast/readability
    // plugins when the per-page blob set profile. No-op otherwise.

    if (window.location.href.indexOf('preview=true') > -1) {
      ed11yOptions['alertMode'] = 'assertive';
    }
    if (window.location.href.indexOf('ed1ref') > -1) {
      ed11yOptions['alertMode'] = 'assertive';
      ed11yOptions['showDismissed'] = true;
    }
    ed11yOptions.cssUrls = [ed11yOptions.cssLocation];

    // Pin the merged dual-language dictionary so the library uses it during
    // preProcessOptions. The library calls Lang.addI18n(State.option.lang.strings)
    // on construction and, when options.lang is unset, falls back to its
    // built-in English pack — which is what previously clobbered the
    // enqueued translation back to English for non-English sites.
    ed11yOptions.lang = ed11yLang;

    let lateResultsReady;
    document.addEventListener('ed11yResults', function () {
      // Delay to make sure page has painted. Not needed until a tip is drawn.
      if (lateResultsReady) {
        return;
      }
      lateResultsReady = true;
      const editLink = document.querySelector('#wp-admin-bar-edit a');
      // todo: this is not always detected due to race condition.
      const elementorLink = document.querySelector('#wp-admin-bar-elementor_edit_page a');
      if (editLink || elementorLink) {
        const editIcon = document.createElement('span');
        editIcon.classList.add('ed11y-custom-edit-icon');
        editIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="currentColor" d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152L0 424c0 48.6 39.4 88 88 88l272 0c48.6 0 88-39.4 88-88l0-112c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 112c0 22.1-17.9 40-40 40L88 464c-22.1 0-40-17.9-40-40l0-272c0-22.1 17.9-40 40-40l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24L88 64z"/></svg>';
        const reLink = function (link, type) {
          const linkButton = document.createElement('a');
          linkButton.href = link.href;
          linkButton.textContent = link.textContent;
          if (type === 'elementor') {
            const eIcon = editIcon.cloneNode(true);
            eIcon.style.fontSize = '1.125em';
            eIcon.style.lineHeight = '.9em';
            eIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" fill="none"><g clip-path="url(#clip0)"><path d="M200 -1.52588e-05C89.5321 -1.52588e-05 0 89.5321 0 200C0 310.431 89.5321 400 200 400C310.468 400 400 310.468 400 200C399.964 89.5321 310.431 -1.52588e-05 200 -1.52588e-05ZM150.009 283.306H116.694V116.658H150.009V283.306ZM283.306 283.306H183.324V249.991H283.306V283.306ZM283.306 216.639H183.324V183.324H283.306V216.639ZM283.306 149.973H183.324V116.658H283.306V149.973Z" fill="currentColor"></path></g><defs><clipPath id="clip0"><rect width="400" height="400" fill="white"></rect></clipPath></defs></svg>';
            linkButton.prepend(eIcon);
          } else {
            linkButton.prepend(editIcon.cloneNode(true));
          }
          return linkButton;
        };
        const editLinks = document.createElement('div');
        if (editLink) {
          editLinks.appendChild(reLink(editLink));
        }
        if (elementorLink) {
          editLinks.appendChild(reLink(elementorLink, 'elementor'));
        }
        State.option.editLinks = editLinks;

        // Honor the `hide_edit_links` admin setting (PHP emits it as
        // `hideEditLinks`): suppress the wp-admin-bar edit shortcut on
        // any tip whose element matches the configured selector. An
        // asterisk hides the links everywhere.
        if (typeof ed11yOptions.hideEditLinks === 'string' && ed11yOptions.hideEditLinks.trim().length > 0) {
          const hideSelector = ed11yOptions.hideEditLinks.trim();
          document.addEventListener('ed11yPop', (e) => {
            const matches = '*' === hideSelector
              ? true
              : e.detail.result.element?.closest(hideSelector);
            if (matches) {
              e.detail.tip.shadowRoot.querySelector('.ed11y-custom-edit-links')?.setAttribute('hidden', '');
            }
          });
        }
      }
    });

    document.addEventListener('ed11yPop', (e) => {
      if (!e.detail.result.element.matches('img')) {
        return;
      }
      // Get image ID from data attribute or class.
      let imageId = e.detail.result.element.dataset.id;
      if (!imageId) {
        imageId = ed11yWpImageIdFromClass(e.detail.result.element.getAttribute('class'));
      }

      const alreadyDecorated = e.detail.tip.dataset.alreadyDecorated;
      if (imageId && !alreadyDecorated) {
        const editIcon = document.createElement('span');
        editIcon.classList.add('ed11y-custom-edit-icon');
        editIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="currentColor" d="M448 80c8.8 0 16 7.2 16 16l0 319.8-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3L48 96c0-8.8 7.2-16 16-16l384 0zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>';
        const linkButton = document.createElement('a');
        editIcon.style.fontSize = '1em';
        // Use the attachment edit screen (post.php) rather than
        // upload.php?item=, whose details modal only opens in the media
        // library's grid view and silently no-ops in list view.
        linkButton.href = `${ed11yOptions.adminUrl}post.php?post=${imageId}&action=edit`;
        linkButton.textContent = ed11yOptions.i18n?.editMedia || 'Edit Media';
        linkButton.prepend(editIcon);
        const buttonBar = e.detail.tip.shadowRoot.querySelector('.ed11y-custom-edit-links > div');
        buttonBar?.appendChild(linkButton);
      }
      e.detail.tip.dataset.alreadyDecorated = 'true';
    });

    window.ed11y = new Ed11y(ed11yOptions);
    // Expose the library's State / UI / Lang namespaces alongside the
    // instance so tooling (browser-console diagnostics, the Playwright
    // parity suite, third-party integrations) can read the live runtime
    // dictionaries without re-importing the bundle. Mirrors what the
    // editor shim does for the MCE iframe loader.
    window.Ed11y = Ed11y;
    window.Ed11y.State = State;
    window.Ed11y.UI = UI;
    window.Ed11y.Lang = Lang;
    window.Ed11y.refresh = refresh;
    window.Ed11y.createDismissalKey = createDismissalKey;
    window.Ed11y.computeAccessibleName = computeAccessibleName;
    window.Ed11y.getElements = getElements;
    window.Ed11y.sanitizeHTML = sanitizeHTML;
    window.Ed11y.setFixedRoots = setFixedRoots;
    window.Ed11y.version = version;
    ed11yCustomTests();
    ed11ySync();
  } else {
    // Either the page has no editoria11y-init blob (a route the plugin
    // doesn't try to scan) or we're inside an Elementor preview iframe.
    // No scan will run, so no postData/reportSyncDone chain will fire —
    // tell the crawler parent we're done so it doesn't wait the 20s decay.
    reportSyncDone();
  }
};

// Call callback, init Editoria11y.
ed11yReady(
  function () {
    window.setTimeout(() => {
      ed11yInit();
    }, 0);
  }
);
