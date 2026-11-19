import { lang } from 'editoria11y-lang';

// Server-side i18n source of truth: `result_name` is stamped into the DB at
// scan time, but the canonical label for a given test_key is the bundled
// lang pack — tests get renamed and `result_name` lags on legacy rows. Order:
//   1. lang.testNames[key]   (current canonical label, shifts with releases)
//   2. result_name           (whatever was stamped at scan time)
//   3. raw key               (final fallback for unknown keys)
// `||` not `??`: legacy v1.2 rows have `result_name = ''` (the migration's
// ADD COLUMN default), so coerce empty strings through to the raw-key fallback
// rather than rendering a blank cell.
// Exported so unit tests can verify the chain without the full DOM bootstrap.
export const labelFor = (key, resultName) =>
  lang.testNames[key] || resultName || key;

// page_url values come from editor-submitted scan payloads (semi-trusted):
// `new URL()` happily preserves schemes like `javascript:`, which would
// render as a clickable XSS link in the report tables. Returns a URL object
// for http(s) targets, null for anything else (caller falls back to '#').
// Exported for unit tests.
export const safeReportUrl = (url, base) => {
  let parsed;
  try {
    parsed = new URL(url, base);
  } catch {
    return null;
  }
  return 'http:' === parsed.protocol || 'https:' === parsed.protocol ? parsed : null;
};

// Config blob printed by Admin\Dashboard::render(). `wp_add_inline_script`
// does not attach to script modules, so server → JS data flows through a
// JSON <script> element rather than wpApiSettings or ed11yDashboard globals.
// Falls back to a sentinel when the element is missing so importing this
// module from the unit tests (or any non-dashboard context) doesn't throw.
const configEl = document.getElementById('editoria11y-dash-config');
const config = configEl ? JSON.parse(configEl.textContent) : null;

/**
 * Join a route (which may carry its own query string) onto the REST root.
 * On plain-permalink sites rest_url() is `index.php?rest_route=/`, so a
 * literal `?` in the route would truncate the rest_route param and 404
 * every request; mirror @wordpress/api-fetch and demote it to `&`.
 */
export const restUrl = (root, route) =>
  root.includes('?') ? root + route.replace('?', '&') : root + route;

/**
 * sprintf-lite for the small set of placeholders we use server-side.
 * Supports %s / %d (positional). Numbered (%1$s) is unused on the
 * dashboard so we keep this trivial.
 */
const format = (template, ...args) => {
  let i = 0;
  return template.replace(/%[ds]/g, () => String(args[i++]));
};

/**
 * Pick the right plural form and substitute the count.
 * `forms` is a [singular, plural] pair sourced from PHP _n() in
 * config.i18n — server already handled the locale's plural rule, so
 * JS only distinguishes "exactly 1" from "everything else".
 */
const pluralize = (forms, count) =>
  (count === 1 ? forms[0] : forms[1]).replace(/%d/g, count);

/**
 * Translated label for a WP post status, falling back to the raw slug if
 * the dashboard config doesn't ship a translation for it (custom post
 * statuses registered by other plugins land here).
 */
const statusLabel = (slug) =>
  (config?.i18n?.statusLabels && config.i18n.statusLabels[slug]) || slug;

class Ed1 {
  constructor() {

    /**
     * Gather query variables into arrays.
     * Clicking sort buttons will update arrays before
     * buildRequest assembles values into API call.
     */
    Ed1.params = function () {
      // WP-only custom rule label. Use ??= so an upstream lang pack that
      // already provides a name (e.g. a future locale) wins. The fallback
      // comes through PHP so it picks up the active wp-admin locale.
      lang.testNames.emptyWpButton ??= config.i18n.emptyWpButton;

      let urlParams = new URLSearchParams(window.location.search);
      Ed1.url = new URL(window.location.pathname, window.location.origin);
      if (urlParams.has('page')) {
        Ed1.url.searchParams.set('page', urlParams.get('page'));
      }
      // Used as the _wpnonce query param on the CSV download link and on
      // ed1ref-tagged page jumps (both verified against the 'ed1ref' action
      // server-side). REST X-WP-Nonce uses config.restNonce instead.
      Ed1.nonce = config.csvNonce;

      // Only accept numerical offsets
      let resultOffset = urlParams.get('roff');
      resultOffset = !isNaN(resultOffset) ? +resultOffset : 0;
      let pageOffset = urlParams.get('poff');
      pageOffset = !isNaN(pageOffset) ? +pageOffset : 0;
      let recentOffset = urlParams.get('recentoff');
      recentOffset = !isNaN(recentOffset) ? +recentOffset : 0;

      // Allow list for sorts. dev_total / dev_count gate the CSA dev-alerts
      // column header sort keys; the PHP Validate::sort() allow-list mirrors
      // this list.
      let validSorts = [
        'page_title',
        'page_total',
        'dev_total',
        'result_count',
        'dev_count',
        'page_url',
        'entity_type',
        'created',
        'count',
        'result_key',
        'dismissal_status',
        //'display_name',
        'stale',
        'post_status',
        'post_modified',
        //'post_author',
      ];
      let resultSort = urlParams.get('rsort');
      resultSort = !!resultSort && validSorts.includes(resultSort) ? resultSort : 'count';

      let pageSort = urlParams.get('psort');
      pageSort = !!pageSort && validSorts.includes(pageSort) ? pageSort : 'page_total';
      let dismissSort = urlParams.get('dsort');
      dismissSort = !!dismissSort && validSorts.includes(dismissSort) ? dismissSort : 'created';
      let recentSort = urlParams.get('recentsort');
      recentSort = !!recentSort && validSorts.includes(recentSort) ? recentSort : 'created';

      // Validate sort direction
      let resultDir = urlParams.get('rdir');
      resultDir = resultDir === 'DESC' || resultDir === 'ASC' ? resultDir : 'DESC';
      let pageDir = urlParams.get('pdir');
      pageDir = pageDir === 'DESC' || pageDir === 'ASC' ? pageDir : 'DESC';
      let dismissDir = urlParams.get('ddir');
      dismissDir = dismissDir === 'DESC' || dismissDir === 'ASC' ? dismissDir : 'DESC';
      let recentDir = urlParams.get('recentdir');
      recentDir = recentDir === 'DESC' || recentDir === 'ASC' ? recentDir : 'DESC';

      // Test name to filter by; will be validated.
      Ed1.resultKey = urlParams.get('rkey');
      Ed1.resultKey = Ed1.resultKey ? Ed1.resultKey : false;

      // Page type to filter by; will be validated.
      Ed1.type = urlParams.get('type');
      Ed1.p_author = urlParams.get('p_author');
      Ed1.dismissor = urlParams.get('dismissor');

      Ed1.post_status = urlParams.get('post_status') || false;

      Ed1.openDetails = !!Ed1.resultKey || !!Ed1.type || !!Ed1.p_author || !!Ed1.post_status;

      // Key arrays to be assembled into URLs on request.
      Ed1.requests = {};
      Ed1.requests['ed1page'] = {
        base: 'dashboard',
        view: 'pages',
        count: 25,
        offset: pageOffset,
        sort: pageSort,
        direction: pageDir,
        result_key: Ed1.resultKey,
        entity_type: Ed1.type,
        post_status: Ed1.post_status,
        p_author: Ed1.p_author,
      };
      Ed1.requests['ed1recent'] = {
        base: 'dashboard',
        view: 'recent',
        count: 25,
        offset: recentOffset,
        sort: recentSort,
        direction: recentDir,
        result_key: Ed1.resultKey,
        entity_type: Ed1.type,
        post_status: Ed1.post_status,
        p_author: Ed1.p_author,
      };
      Ed1.requests['ed1result'] = {
        base: 'dashboard',
        view: 'keys',
        count: 25,
        offset: resultOffset,
        sort: resultSort,
        direction: resultDir,
        result_key: Ed1.resultKey,
        entity_type: Ed1.type,
        post_status: Ed1.post_status,
        p_author: Ed1.p_author,
      };
      Ed1.requests['ed1dismiss'] = {
        base: 'dismiss',
        view: '',
        count: 25,
        offset: pageOffset,
        sort: dismissSort,
        direction: dismissDir,
        result_key: Ed1.resultKey,
        entity_type: Ed1.type,
        post_status: Ed1.post_status,
        p_author: Ed1.p_author,
        dismissor: Ed1.dismissor,
      };
    };

    /**
     * Translated label for a WP post status. Falls back to the raw slug
     * for custom statuses the dashboard config doesn't ship a translation
     * for. Replaces the previous English-only title-case + "Publish" →
     * "Published" hack — labels now flow through the WP translation layer
     * via config.i18n.statusLabels.
     */
    const prettyStatus = function (page_status) {
      return page_status ? statusLabel(page_status) : page_status;
    };

    /**
     * Build a URL by extending Ed1.url with the given params.
     * @param {Object} params Key/value pairs to set as search parameters.
     * @returns string
     */
    Ed1.buildUrl = function (params) {
      let url = new URL(Ed1.url);
      for (const [key, value] of Object.entries(params)) {
        url.searchParams.set(key, value);
      }
      return url.toString();
    };

    /**
     * Assemble request array into API call.
     * @param {*} request
     * @returns string
     */
    Ed1.buildRequest = function (request) {
      let q = Ed1.requests[request];
      // Can't use &author as param when author enumeration blocking is activated.
      // URLSearchParams handles encoding — raw interpolation let a
      // filter value containing & or = silently corrupt every later param.
      const params = new URLSearchParams({
        view: q.view,
        count: q.count,
        offset: q.offset,
        sort: q.sort,
        direction: q.direction,
        result_key: q.result_key,
        p_author: q.p_author,
        entity_type: q.entity_type,
        post_status: q.post_status,
        dismissor: q.dismissor,
        nocache: Date.now(),
      });
      return `${q.base}?${params.toString()}`;
    };

    /**
     * Gather GET requests and make API calls.
     */
    Ed1.init = async function () {
      // Get results with default params

      Ed1.params();
      Ed1.tables = {};
      Ed1.wrapper = document.getElementById('ed1');
      Ed1.wrapPage = Ed1.wrapper.querySelector('#ed1-page-wrapper');
      Ed1.wrapRecent = Ed1.wrapper.querySelector('#ed1-recent-wrapper');
      Ed1.wrapResults = Ed1.wrapper.querySelector('#ed1-results-wrapper');
      Ed1.wrapDismiss = Ed1.wrapper.querySelector('#ed1-dismissals-wrapper');
      Ed1.render.tableHeaders();

      // Only build result table if there is no result or type filter.
      if (!!Ed1.resultKey || !!Ed1.type || !!Ed1.post_status || !!Ed1.p_author || !!Ed1.dismissor) {
        Ed1.h1 = Ed1.wrapper.querySelector('#ed1 h1');
        let resetType = config.i18n.viewAllIssues;
        if (Ed1.resultKey) {
          Ed1.h1.textContent = format(config.i18n.alertReport, lang.testNames[Ed1.resultKey] ?? Ed1.resultKey);
        } else if (Ed1.type) {
          Ed1.h1.textContent = format(config.i18n.alertsOnType, Ed1.type);
          resetType = config.i18n.viewAllPages;
        } else if (Ed1.p_author) {
          Ed1.h1.textContent = config.i18n.alertsByAuthor;
        } else if (Ed1.dismissor) {
          // Display name is filled in by Ed1.get.ed1dismiss once the API
          // returns the matching author record.
          Ed1.h1.textContent = config.i18n.dismissedBy;
        }
        else {
          Ed1.h1.textContent = format(config.i18n.statusPages, prettyStatus(Ed1.post_status));
          resetType = config.i18n.viewAllPages;
        }
        let reset = Ed1.render.a(resetType, false, Ed1.url.toString());
        reset.classList.add('reset');
        let leftArrow = document.createElement('span');
        leftArrow.textContent = '< ';
        leftArrow.setAttribute('aria-hidden', 'true');
        reset.insertAdjacentElement('afterbegin', leftArrow);
        Ed1.h1.insertAdjacentElement('afterend', reset);
        Ed1.wrapResults.style.display = 'none';
      } else {
        // Possible todo: we could wait until the Details is open to do this.
        window.setTimeout(function () { Ed1.get.ed1result(Ed1.buildRequest('ed1result'), false); }, 500);
      }

      let ed1Lag = Ed1.openDetails ? 0 : 500;

      // Always build page table.
      if (!Ed1.dismissor) {
        Ed1.get.ed1recent(Ed1.buildRequest('ed1recent'), false);
        Ed1.get.ed1page(Ed1.buildRequest('ed1page'), false);
      }

      // Possible todo: we could wait until the Details is open to do this.
      window.setTimeout(function () {
        Ed1.get.ed1dismiss(Ed1.buildRequest('ed1dismiss'), false);
      }, ed1Lag);

      // Show whatever is drawn after one second.
      window.setTimeout(function () { Ed1.show(); }, 500);
      window.setTimeout(function () {
        let neverLoaded = document.querySelectorAll('#ed1 .loading');
        Array.from(neverLoaded).forEach((el) => {
          el.textContent = config.i18n.apiError;
        });
      }, 3000);
    };

    Ed1.show = function () {
      if (Ed1.dismissor) {
        Ed1.wrapRecent.setAttribute('hidden', '');
        Ed1.wrapPage.setAttribute('hidden', '');
        Ed1.wrapDismiss.querySelector('details').setAttribute('open', '');
      }
      Ed1.wrapper.classList.add('show');

    };

    Ed1.announce = function (string) {
      if (!Ed1.liveRegion) {
        Ed1.liveRegion = document.createElement('div');
        Ed1.liveRegion.setAttribute('class', 'visually-hidden');
        Ed1.liveRegion.setAttribute('aria-live', 'polite');
        document.getElementById('ed1').insertAdjacentElement('beforeend', Ed1.liveRegion);
      }
      Ed1.liveRegion.textContent = '';
      window.setTimeout(function () {
        Ed1.liveRegion.textContent = string;
      }, 1500);
    };

    /**
     *
     * Builder functions to quickly assemble HTML elements.
     * @param {*} text
     * @param {*} hash
     * @param {*} sorted
     * @returns th
     */
    Ed1.render = {};

    Ed1.render.th = function (text, hash = false, sorted = false) {
      let header = document.createElement('th');
      if (!hash) {
        header.textContent = text;
      } else {
        let sorter = Ed1.render.button(text, hash, sorted);
        header.insertAdjacentElement('afterbegin', sorter);
      }
      return header;
    };

    Ed1.render.button = function (text, hash, sorted = false) {
      let button = document.createElement('button');
      button.textContent = text;
      button.setAttribute('data-ed1-action', hash);
      if (sorted) {
        button.setAttribute('aria-pressed', 'true');
        // 'descending' / 'ascending' are also CSS class names elsewhere
        // in the dashboard, so the class stays English while the visible
        // tooltip flows through the WP translation layer.
        let direction = 'DESC' === sorted ? 'descending' : 'ascending';
        button.setAttribute('title', 'descending' === direction ? config.i18n.sortDescending : config.i18n.sortAscending);
        button.setAttribute('class', direction);
      }
      return button;
    };

    // Render a link with url sanitized and html encoded.
    Ed1.render.a = function (text, hash = false, url = false, pid = false) {
      let link = document.createElement('a');
      link.textContent = text;
      let href;
      const parsedUrl = url ? safeReportUrl(url, window.location.origin) : null;
      if (parsedUrl) {
        if (pid) {
          parsedUrl.searchParams.set('ed1ref', parseInt(pid));
          parsedUrl.searchParams.set('_wpnonce', Ed1.nonce);
        }
        href = parsedUrl.toString();
      } else {
        href = '#' + (hash ? encodeURIComponent(hash) : '');
      }
      link.href = href;
      return link;
    };

    Ed1.render.td = function (text, hash = false, url = false, pid = false, cls = false) {
      let cell = document.createElement('td');
      if (url) {
        if (!url.includes('admin.php')) {
          cell.classList.add('widen');
        }
        cell.insertAdjacentElement('afterbegin', Ed1.render.a(text, hash, url, pid));
      } else if (hash) {
        cell.insertAdjacentElement('afterbegin', Ed1.render.button(text, hash));
      } else {
        cell.textContent = text;
      }
      if (cls) {
        cell.setAttribute('class', cls);
      }
      return cell;
    };

    Ed1.render.details = function (text, id, open = false) {
      let details = document.createElement('details');
      if (open || Ed1.openDetails === true) {
        details.setAttribute('open', '');
      }
      let summary = document.createElement('summary');
      summary.textContent = text;
      summary.setAttribute('id', id);
      details.append(summary);
      return details;
    };
    Ed1.render.noResults = function (text, colspan) {
      let row = document.createElement('tr');
      let td = Ed1.render.td(text);
      td.setAttribute('colspan', colspan);
      row.append(td);
      return row;
    };

    /**
     * Build a single-cell `<tr>` placeholder used until the API responds.
     * Each table needs its own copy so per-table colspans don't collide.
     */
    Ed1.render.loadingRow = function (colspan) {
      let row = document.createElement('tr');
      let cell = Ed1.render.td(config.i18n.loading, false, false, false, 'loading');
      cell.setAttribute('colspan', String(colspan));
      row.append(cell);
      return row;
    };
    /**
     * Hat tip to https://webdesign.tutsplus.com/tutorials/pagination-with-vanilla-javascript--cms-41896
     * @param {*} after
     * @param {*} rows
     * @param {*} perPage
     * @param {*} offset
     * @param {*} labelId
     * @returns
     */
    Ed1.render.pagination = function (after, rows, perPage, offset, labelId = false) {
      if (rows <= perPage) {
        return false;
      }

      let pageWrap = document.createElement('nav');
      if (labelId) {
        pageWrap.setAttribute('aria-labelledby', labelId);
      }

      let appendPageNumber = (index, first = false, hidden = false, last = false) => {
        let pageNumber = document.createElement('button');
        pageNumber.className = 'pagination-number';
        pageNumber.textContent = index;
        pageNumber.setAttribute('page-index', index);
        pageNumber.setAttribute('aria-label', format(config.i18n.pageN, index));
        if (first) {
          pageNumber.setAttribute('aria-current', 'page');
          let ellipse = document.createElement('span');
          ellipse.classList.add('ellipses');
          ellipse.textContent = '...';
          ellipse.setAttribute('hidden', 'hidden');
          pageWrap.appendChild(pageNumber);
          pageWrap.appendChild(ellipse);
        } else if (hidden) {
          pageNumber.setAttribute('hidden', '');
          pageWrap.appendChild(pageNumber);
        } else if (last && index > 7) {
          let ellipse = document.createElement('span');
          ellipse.classList.add('ellipses');
          ellipse.textContent = '...';
          pageWrap.appendChild(ellipse);
          pageWrap.appendChild(pageNumber);
        } else {
          pageWrap.appendChild(pageNumber);
        }
      };

      let pageCount = Math.ceil(rows / perPage);
      for (let i = 1; i <= pageCount; i++) {
        let first = i === 1;
        let last = i === pageCount;
        let hidden = !(i <= 6 || last);
        last = pageCount < 5 ? false : last;
        appendPageNumber(i, first, hidden, last);
      }

      Ed1.tables[after].insertAdjacentElement('afterend', pageWrap);

      let buttons = pageWrap.querySelectorAll('button');
      buttons.forEach((button) => {
        const pageIndex = Number(button.getAttribute('page-index'));

        if (pageIndex) {
          button.addEventListener('click', (e) => {
            Ed1.setPage(e, after, (pageIndex - 1) * perPage);
          });
        }
      });
    };

    Ed1.setPage = function (e, table, offset) {
      // Get new content.
      Ed1.requests[table]['offset'] = offset;
      Ed1.get[table](Ed1.buildRequest(table), true);

      // Update selected state
      e.target.closest('nav').querySelector('[aria-current]').removeAttribute('aria-current');
      e.target.setAttribute('aria-current', 'true');

      // Determine which buttons should be visible.
      let current = e.target.getAttribute('page-index');
      let ellipses = e.target.closest('nav').querySelectorAll('.ellipses');
      let buttons = e.target.closest('nav').querySelectorAll('.pagination-number');
      buttons.forEach((el) => {
        let page = el.getAttribute('page-index');

        // First and last always show.
        let show = page == 1 || page == buttons.length;
        if (!show) {
          if (current <= 4) {
            // At left edge, pin 6.
            show = (page <= 6);
          } else if (current >= buttons.length - 4) {
            // At right edge, pin 6
            show = (page >= buttons.length - 6);
          } else {
            show = current - page <= 2 && page - current <= 2;
          }
        }

        if (show) {
          el.removeAttribute('hidden');
          // Hide ellipses when penultimate number is revealed.
          if (page == 2) {
            Array.from(ellipses)[0].setAttribute('hidden', 'hidden');
          } else if (page == buttons.length - 1) {
            Array.from(ellipses)[1]?.setAttribute('hidden', 'hidden');
          }
        } else {
          el.setAttribute('hidden', true);
          if (page == 2) {
            Array.from(ellipses)[0].removeAttribute('hidden');
          } else if (page == buttons.length - 1) {
            Array.from(ellipses)[1]?.removeAttribute('hidden');
          }
        }

      });
    };

    Ed1.render.tableHeaders = function () {
      let head;

      // Pages table — Alerts (+ Dev alerts when CSA), Page, Path, Type, Status, Updated, Author.
      Ed1.tables['ed1page'] = document.createElement('table');
      Ed1.tables['ed1page'].setAttribute('id', 'ed1page');

      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colAlerts, 'page_total', 'DESC'));

      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPage, 'page_title'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPath, 'page_url'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colType, 'entity_type'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colStatus, 'post_status'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colUpdated, 'post_modified'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colAuthor));
      Ed1.tables['ed1page'].insertAdjacentElement('beforeend', head);
      Ed1.tables['ed1page'].append(Ed1.render.loadingRow(head.children.length));

      let pageDetails = Ed1.render.details(config.i18n.sectionAlertsByPage, 'ed1page-title');
      Ed1.wrapPage.append(pageDetails);
      pageDetails.append(Ed1.tables['ed1page']);
      Ed1.tables['ed1page'].querySelectorAll('button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1page(Ed1.buildRequest('ed1page'));
        });
      });

      // Recent table — Detected, Page, Path, Alert (+ Dev alerts when CSA), Count, Type, Status.
      Ed1.tables['ed1recent'] = document.createElement('table');
      Ed1.tables['ed1recent'].setAttribute('id', 'ed1recent');

      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colDetected, 'detected', 'DESC'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPage, 'page_title'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPath, 'page_url'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colAlert, 'result_key'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colCount, 'result_count'));

      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colType, 'entity_type'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colStatus, 'post_status'));
      Ed1.tables['ed1recent'].insertAdjacentElement('beforeend', head);
      Ed1.tables['ed1recent'].append(Ed1.render.loadingRow(head.children.length));

      let recentDetails = Ed1.render.details(config.i18n.sectionRecent, 'ed1page-title', false);
      Ed1.wrapRecent.append(recentDetails);
      recentDetails.append(Ed1.tables['ed1recent']);
      Ed1.tables['ed1recent'].querySelectorAll('button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1recent(Ed1.buildRequest('ed1recent'));
        });
      });

      // Results table — Pages (+ Dev alerts when CSA), Alert.
      Ed1.tables['ed1result'] = document.createElement('table');
      Ed1.tables['ed1result'].setAttribute('id', 'ed1result');
      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPages, 'count', 'DESC'));

      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colAlert, 'result_key'));
      Ed1.tables['ed1result'].insertAdjacentElement('beforeend', head);
      Ed1.tables['ed1result'].append(Ed1.render.loadingRow(head.children.length));

      let resultDetails = Ed1.render.details(config.i18n.sectionAlertTypes, 'ed1result-title');
      Ed1.wrapResults.append(resultDetails);
      resultDetails.append(Ed1.tables['ed1result']);

      Ed1.tables['ed1result'].querySelectorAll('th button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1result(Ed1.buildRequest('ed1result'));
        });
      });

      // Dismissals table — On, Page, Path, Dismissed alert, Marked, Current, By.
      // Dev alerts column is intentionally omitted: dismissals are per-element
      // and don't carry a content-vs-dev distinction.
      Ed1.tables['ed1dismiss'] = document.createElement('table');
      Ed1.tables['ed1dismiss'].setAttribute('id', 'ed1dismiss');
      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colOn, 'created', 'DESC'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPage, 'page_title'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colPath, 'page_url'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colDismissedAlert, 'result_key'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colMarked, 'dismissal_status'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colCurrent, 'stale'));
      head.insertAdjacentElement('beforeend', Ed1.render.th(config.i18n.colBy));
      Ed1.tables['ed1dismiss'].insertAdjacentElement('beforeend', head);
      Ed1.tables['ed1dismiss'].append(Ed1.render.loadingRow(head.children.length));

      let detailTitle = Ed1.openDetails ? config.i18n.sectionDismissals : config.i18n.sectionRecentDismissals;

      let dismissDetails = Ed1.render.details(detailTitle, 'ed1dismiss-title');
      Ed1.wrapDismiss.append(dismissDetails);
      dismissDetails.append(Ed1.tables['ed1dismiss']);
      Ed1.tables['ed1dismiss'].querySelectorAll('th button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1dismiss(Ed1.buildRequest('ed1dismiss'));
        });
      });

    };

    /**
     * Renderer for viewing results by test name.
     *
     * @param {*} post
     * @param {*} count
     */
    Ed1.render.ed1result = function (post, count, announce) {

      Ed1.tables['ed1result'].querySelectorAll('tr + tr').forEach(el => {
        el.remove();
      });

      if (post) {
        if (!Ed1.wrapResults.querySelector('nav')) {
          Ed1.render.pagination('ed1result', count, Ed1.requests['ed1result']['count'], 0, 'ed1result-title');
        }

        post.forEach((result) => {
          let row = document.createElement('tr');

          let pageCount = Ed1.render.td(result['count']);
          pageCount.classList.add('numeric');
          row.insertAdjacentElement('beforeend', pageCount);

          let keyName = labelFor(result['result_key'], result['result_name']);

          // URL sanitized on build...
          let key = Ed1.render.td(keyName, false, Ed1.buildUrl({ rkey: result['result_key'] }), false, 'rkey');
          row.insertAdjacentElement('beforeend', key);

          Ed1.tables['ed1result'].insertAdjacentElement('beforeend', row);
        });

        if (!Ed1.csvLink) {
          Ed1.csvLink = Ed1.render.a(config.i18n.csvDownload, '', Ed1.buildUrl({ ed11y_export_results_csv: 'download', _wpnonce: Ed1.nonce }));
          Ed1.csvLink.classList.add('ed11y-export');
          Ed1.wrapper.append(Ed1.csvLink);
        }
      }

      if (announce) {
        Ed1.announce(pluralize(config.i18n.results, post.length));
      }

      Ed1.show();

    };

    Ed1.authorList = {};
    Ed1.matchAuthors = function (author_query) {
      author_query?.forEach((p_author) => {
        Ed1.authorList[p_author.ID] = p_author.display_name;
      });
    };

    /**
     * Renderer for viewing recent issues.
     *
     * @param {*} post
     * @param {*} count
     */
    Ed1.render.ed1recent = function (post, count, announce) {

      Ed1.tables['ed1recent'].querySelectorAll('tr + tr').forEach(el => {
        el.remove();
      });

      if (post) {
        if (!Ed1.wrapRecent.querySelector('nav')) {
          Ed1.render.pagination('ed1recent', count, Ed1.requests['ed1recent']['count'], 0, 'ed1recent-title');
        }

        post.forEach((result) => {
          let row = document.createElement('tr');

          let cleanDate = result['created']?.split(' ')[0].replace(/[^\-0-9]/g, '');
          let date = Ed1.render.td(cleanDate, false, '');
          row.insertAdjacentElement('beforeend', date);

          let pageLink = Ed1.render.td(result['page_title'], false, result['page_url'], result['pid']);
          row.insertAdjacentElement('beforeend', pageLink);

          let path = decodeURI(result['page_url'].replace(window.location.protocol + '//' + window.location.host, ''));
          if (path && path !== '/' && path.startsWith('/')) {
            path = path.substring(1);
          }
          path = Ed1.render.td(path ? path : '/');
          row.insertAdjacentElement('beforeend', path);

          let keyName = labelFor(result['result_key'], result['result_name']);
          let key = Ed1.render.td(keyName, false, Ed1.buildUrl({ rkey: result['result_key'] }), false, 'rkey');
          row.insertAdjacentElement('beforeend', key);

          let pageCount = Ed1.render.td(result['result_count']);
          pageCount.classList.add('numeric');
          row.insertAdjacentElement('beforeend', pageCount);

          let type = Ed1.render.td(result['entity_type'], false, Ed1.buildUrl({ type: result['entity_type'] }));
          row.insertAdjacentElement('beforeend', type);

          let post_status = result['post_status'] ?
            Ed1.render.td(prettyStatus(result['post_status']), false, Ed1.buildUrl({ post_status: result['post_status'] }))
            : Ed1.render.td(statusLabel('publish'), false, Ed1.buildUrl({ post_status: 'publish' }));
          row.insertAdjacentElement('beforeend', post_status);

          Ed1.tables['ed1recent'].insertAdjacentElement('beforeend', row);
        });
      }

      if (announce) {
        Ed1.announce(pluralize(config.i18n.results, post.length));
      }

      Ed1.show();

    };

    /**
     * Renderer for viewing results by page.
     *
     * @param {*} post
     * @param {*} count
     */
    Ed1.render.ed1page = function (post, count, announce) {

      Ed1.tables['ed1page'].querySelectorAll('tr + tr').forEach(el => {
        el.remove();
      });

      if (post) {
        if (!Ed1.wrapPage.querySelector('nav')) {
          Ed1.render.pagination('ed1page', count, Ed1.requests['ed1page']['count'], 0, 'ed1page-title');
        }

        post.forEach((result) => {
          let row = document.createElement('tr');

          let pageCount = Ed1.render.td(result['page_total']);
          pageCount.classList.add('numeric');
          row.insertAdjacentElement('beforeend', pageCount);

          let pageLink = Ed1.render.td(result['page_title'], false, result['page_url'], result['pid']);
          row.insertAdjacentElement('beforeend', pageLink);

          let path = decodeURI(result['page_url'].replace(window.location.protocol + '//' + window.location.host, ''));
          if (path && path !== '/' && path.startsWith('/')) {
            path = path.substring(1);
          }
          path = Ed1.render.td(path ? path : '/');
          row.insertAdjacentElement('beforeend', path);

          let type = Ed1.render.td(result['entity_type'], false, Ed1.buildUrl({ type: result['entity_type'] }));
          row.insertAdjacentElement('beforeend', type);

          let post_status = result['post_status'] ?
            Ed1.render.td(prettyStatus(result['post_status']), false, Ed1.buildUrl({ post_status: result['post_status'] }))
            : Ed1.render.td(statusLabel('publish'), false, Ed1.buildUrl({ post_status: 'publish' }));
          row.insertAdjacentElement('beforeend', post_status);

          let date = result['post_modified'] ?
            Ed1.render.td(result['post_modified'].split(' ')[0].replace(/[^\-0-9]/g, ''))
            : Ed1.render.td(config.i18n.na, false, false, false, 'muted');

          row.insertAdjacentElement('beforeend', date);

          if (result['post_author']) {
            row.insertAdjacentElement(
              'beforeend',
              Ed1.render.td(
                Ed1.authorList[result['post_author']] || result['post_author'],
                false,
                Ed1.buildUrl({ p_author: result['post_author'] }),
              ),
            );
          } else {
            row.insertAdjacentElement(
              'beforeend',
              Ed1.render.td(config.i18n.na, false, false, false, 'muted'),
            );
          }

          Ed1.tables['ed1page'].insertAdjacentElement('beforeend', row);

        });
      }

      if (announce) {
        Ed1.announce(pluralize(config.i18n.results, post.length));
      }

      Ed1.show();

    };

    /**
     * Renderer for viewing dismissed alerts.
     * @param {*} post
     * @param {*} count
     */
    Ed1.render.ed1dismiss = function (post, count, announce) {

      Ed1.tables['ed1dismiss'].querySelectorAll('tr + tr').forEach(el => {
        el.remove();
      });

      if (post) {
        if (!Ed1.wrapDismiss.querySelector('nav')) {
          Ed1.render.pagination('ed1dismiss', count, Ed1.requests['ed1dismiss']['count'], 0, 'ed1dismiss-title');
        }

        if (post.length === 0) {
          let notFound = Ed1.render.noResults(config.i18n.noneCell, '7');
          Ed1.tables['ed1dismiss'].insertAdjacentElement('beforeend', notFound);
        } else {
          post.forEach((result) => {
            let row = document.createElement('tr');

            let cleanDate = result['created'].split(' ')[0].replace(/[^\-0-9]/g, '');
            let on = Ed1.render.td(cleanDate);
            row.insertAdjacentElement('beforeend', on);

            let pageLink = Ed1.render.td(result['page_title'], false, result['page_url'], result['pid']);
            row.insertAdjacentElement('beforeend', pageLink);

            let path = decodeURI(result['page_url'].replace(window.location.protocol + '//' + window.location.host, ''));
            if (path && path !== '/' && path.startsWith('/')) {
              path = path.substring(1);
            }
            path = Ed1.render.td(path ? path : '/');
            row.insertAdjacentElement('beforeend', path);

            let keyName = labelFor(result['result_key'], result['result_name']);
            let key = Ed1.render.td(keyName, false, Ed1.buildUrl({ rkey: result['result_key'] }), false, 'rkey');
            row.insertAdjacentElement('beforeend', key);

            let marked = Ed1.render.td(result['dismissal_status']);
            row.insertAdjacentElement('beforeend', marked);

            // Still on page?
            let stale = Ed1.render.td(!result['stale'] ? config.i18n.no : config.i18n.yes);
            stale.classList.add('numeric');
            row.insertAdjacentElement('beforeend', stale);

            let by = Ed1.render.td(Ed1.authorList[result['user']] || result['user'], false, Ed1.buildUrl({ dismissor: result['user'] }));
            row.insertAdjacentElement('beforeend', by);

            Ed1.tables['ed1dismiss'].insertAdjacentElement('beforeend', row);
          });
        }

      }

      if (announce) {
        Ed1.announce(pluralize(config.i18n.results, post.length));
      }

      Ed1.show();

    };

    /**
     * API calls.
     */
    Ed1.api = {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'accept': 'application/json',
        'X-WP-Nonce': config.restNonce,
      }
    };

    Ed1.get = {};

    // Replace any still-loading cells with a failure message NOW instead
    // of waiting for the generic 3s sweep.
    Ed1.apiFail = function (message) {
      const neverLoaded = document.querySelectorAll('#ed1 .loading');
      Array.from(neverLoaded).forEach((el) => {
        el.textContent = message;
        el.classList.remove('loading');
      });
    };

    // Shared REST reader. The original getters piped straight into
    // response.json() with no ok-check and no catch, so an expired nonce
    // (401/403 after the dashboard sat open past the nonce lifetime), a
    // 502 HTML page, or a network drop either threw an unhandled
    // rejection or rendered the WP error object as table data. Returns
    // the payload array or null after surfacing a visible message.
    Ed1.get.rows = async function (action) {
      let response;
      try {
        response = await fetch(restUrl(config.root, 'ed11y/v1/' + action), Ed1.api);
      } catch (err) {
        console.error('Editoria11y dashboard fetch failed', err);
        Ed1.apiFail(config.i18n.apiError);
        return null;
      }
      if (response.status === 401 || response.status === 403) {
        Ed1.apiFail(config.i18n.sessionExpired || config.i18n.apiError);
        return null;
      }
      let post = null;
      try {
        post = await response.json();
      } catch (err) {
        console.error('Editoria11y dashboard returned non-JSON', err);
      }
      if (!response.ok || !Array.isArray(post)) {
        console.error('Editoria11y dashboard API error', response.status, post);
        Ed1.apiFail(config.i18n.apiError);
        return null;
      }
      return post;
    };

    Ed1.get.ed1page = async function (action, announce = false) {
      const post = await Ed1.get.rows(action);
      if (!post) {
        return;
      }
      Ed1.matchAuthors(post[2]);
      if (Ed1.p_author && Ed1.authorList[Ed1.p_author]) {
        Ed1.h1.textContent = format(config.i18n.alertsByAuthorN, Ed1.authorList[Ed1.p_author]);
      }
      Ed1.render.ed1page(post[0], post[1], announce);
    };
    Ed1.get.ed1recent = async function (action, announce = false) {
      const post = await Ed1.get.rows(action);
      if (!post) {
        return;
      }
      Ed1.render.ed1recent(post[0], post[1], announce);
    };
    Ed1.get.ed1result = async function (action, announce = false) {
      const post = await Ed1.get.rows(action);
      if (!post) {
        return;
      }
      Ed1.render.ed1result(post[0], post[1], announce);
    };
    Ed1.get.ed1dismiss = async function (action, announce = false) {
      const post = await Ed1.get.rows(action);
      if (!post) {
        return;
      }
      Ed1.matchAuthors(post[2]);
      // Display name for the dismissor filter is only available after
      // the API returns the matching user record. Update the H1 here
      // (set as a placeholder in init()) once we have the lookup.
      if (Ed1.dismissor && Ed1.h1 && Ed1.authorList[Ed1.dismissor]) {
        Ed1.h1.textContent = format(config.i18n.dismissedByN, Ed1.authorList[Ed1.dismissor]);
      }
      Ed1.render.ed1dismiss(post[0], post[1], announce);
    };

    /**
     * User Interactions.
     */
    Ed1.reSort = function () {
      let el = document.activeElement;
      let table = el.closest('table');
      let req = table.getAttribute('id');
      Ed1.requests[req]['sort'] = el.getAttribute('data-ed1-action');
      let sort = 'descending' == el.getAttribute('class') ? 'ASC' : 'DESC';
      Ed1.requests[req]['direction'] = sort;
      let siblings = el.closest('tr').querySelectorAll('button');
      siblings.forEach(btn => {
        btn.removeAttribute('aria-pressed');
        btn.classList.remove('ascending', 'descending');
      });
      el.setAttribute('aria-pressed', 'true');
      el.classList.add(sort === 'ASC' ? 'ascending' : 'descending');
    };
  }

}

// Only auto-bootstrap when the dashboard config element is present —
// otherwise the module is being imported in a test or non-dashboard
// context and Ed1.init() would blow up looking for missing wrappers.
if (config) {
  new Ed1();
  Ed1.init();
}
