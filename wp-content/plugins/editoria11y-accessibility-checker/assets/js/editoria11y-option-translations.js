import { Lang } from 'editoria11y-js';

/*
 * Shared WP-blob → library-option translation layer.
 *
 * One copy, imported by editoria11y-wp.js and editoria11y-editor.js.
 *
 * Context differences are explicit parameters:
 *   - ed11yApplyOptionTranslations: `autodetectCheckRoot` (front end only).
 *   - ed11yBuildCsaSplitConfiguration: `editors` (suppresses page-level
 *     checks and the devOptions.checkRoot fallback inside editors).
 *
 * The devChecks map inside the CSA function mirrors Drupal's
 * editoria11yOptions.js (the source of truth); the parity guard in
 * tests/js/unit/devchecks-parity.test.js compares this file against the
 * Drupal checkout and fails if either shim ever grows its own copy again.
 */

async function ed11yLoadStaticConfig(configUrl) {
  if (!configUrl) return null;
  const headers = {};
  if (typeof wpApiSettings !== 'undefined' && wpApiSettings && wpApiSettings.nonce) {
    headers['X-WP-Nonce'] = wpApiSettings.nonce;
  }
  try {
    const res = await fetch(configUrl, {
      credentials: 'same-origin',
      cache: 'force-cache',
      headers,
    });
    if (!res.ok) return null;
    return await res.json();
  } catch (e) {
    console.warn('Editoria11y: static config fetch failed', e);
    return null;
  }
}

// Apply the static config payload.
// Duplicate keys may be present during the 3.0.0 migration cron.
function ed11yApplyStaticConfig(data, options) {
  if (!data) return;
  if (data.testNames && typeof data.testNames === 'object') {
    Lang.testNames = { ...Lang.testNames, ...data.testNames };
  }
  if (!options) return;

  if (data.globalDismissals && typeof data.globalDismissals === 'object') {
    const synced = (options.syncedDismissals && typeof options.syncedDismissals === 'object')
      ? options.syncedDismissals
      : {};
    for (const [resultKey, elementMap] of Object.entries(data.globalDismissals)) {
      synced[resultKey] = { ...(synced[resultKey] || {}), ...elementMap };
    }
    options.syncedDismissals = synced;
  }
  for (const [key, value] of Object.entries(data)) {
    if (key === 'testNames' || key === 'globalDismissals') continue;
    if (value !== undefined && value !== null) {
      options[key] = value;
    }
  }
}

// Mirrors `editoria11yOptions.options()` in Drupal. Runs in both the free
// and premium builds.
export function ed11yApplyOptionTranslations(options, { autodetectCheckRoot = false } = {}) {
  if (!options || typeof options !== 'object') return;

  // Front end only: fall back to <main> (or top-level body children) when
  // the admin configured no check root. The editor shims always set their
  // own canvas root, so autodetecting there would be wrong.
  if (autodetectCheckRoot && !options.checkRoot) {
    options.checkRoot = document.querySelector('main') ? 'main' : 'body > *:not(#wpadminbar, script, style, .ed11y-element)';
  }

  // `linkStringsNewWindows` is a /pipe|delimited|phrases/g regex used to
  // detect "opens in a new tab" warnings inside link text.
  options.linkStringsNewWindows = options.linkStringsNewWindows
    ? new RegExp(options.linkStringsNewWindows, 'g')
    : /window|\stab|download/g;

  // `linkIgnoreStrings` strips from link text *before* running the
  // empty/meaningless-text checks. Library wants an array.
  if (typeof options.linkIgnoreStrings === 'string' && options.linkIgnoreStrings.length > 0) {
    options.linkIgnoreStrings = options.linkIgnoreStrings.split('|').map((s) => s.trim()).filter(Boolean);
  } else if (!Array.isArray(options.linkIgnoreStrings)) {
    options.linkIgnoreStrings = [];
  }

  // The library's checkCustomRuleset() iterates `EMBED_CUSTOM.sources`
  // selectors and pushes EMBED_GENERAL warnings for each match.
  if (typeof options.embeddedContentWarning === 'string' && options.embeddedContentWarning.trim().length > 0) {
    options.checks = options.checks || {};
    options.checks.EMBED_CUSTOM = { sources: options.embeddedContentWarning };
  }

  // Media-source lists are ADDITIVE: the library appends checks.EMBED_*
  // sources to its built-in detection lists. It never reads the flat
  // videoContent/audioContent/dataVizContent payload keys, so they must
  // be mapped here or the settings do nothing. Runs before the
  // ignoreTests pass so a disabled test still wins (checks[KEY] = false).
  const ed11yMediaSourceChecks = {
    videoContent: 'EMBED_VIDEO',
    audioContent: 'EMBED_AUDIO',
    dataVizContent: 'EMBED_DATA_VIZ',
  };
  Object.entries(ed11yMediaSourceChecks).forEach(([optionKey, checkKey]) => {
    if (typeof options[optionKey] === 'string' && options[optionKey].trim().length > 0) {
      options.checks = options.checks || {};
      options.checks[checkKey] = { sources: options[optionKey] };
    }
  });

  // Tests are ignored by setting options.checks[KEY] = false.
  // CSA mode reprocesses this with more complexity.
  if (Array.isArray(options.ignoreTests)) {
    options.checks = options.checks || {};
    options.ignoreTests.forEach((key) => {
      if (typeof key === 'string' && key.length > 0) {
        options.checks[key] = false;
      }
    });
  }

  // CSA: if `profile === 'dev'`, pick the other alert mode.
  if (options.profile === 'dev' && options.devAssertiveness) {
    options.alertMode = options.devAssertiveness;
  }
}

// Build the library's CSA dev/content split configuration.
//
// Mirrors the `if (!!dS.profile)` branch of Drupal's editoria11yOptions.js.
//
// Two distinct jobs:
//   1. `Object.assign(options.checks, devChecks)` turns ON the library
//      tests that ship disabled-by-default (contrast, forms, ARIA, page
//      meta, developer checks) and sets a few default-on tests, so the
//      library actually RUNS them. Without this merge they fall through to
//      their disabled defaults — the original "showing up false even when
//      enabled" bug.
//   2. `splitConfiguration.devChecks` is the separate *visibility* list:
//      which running tests are shown to developers only vs. everyone.
//
// The whole function lives inside the premium markers so the free build
// strips it; importers premium-wrap their import statement to match.

export { ed11yLoadStaticConfig, ed11yApplyStaticConfig };
