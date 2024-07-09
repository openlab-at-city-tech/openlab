class Ed1 {
  constructor() {

    /**
             * Gather query variables into arrays.
             * Clicking sort buttons will update arrays before
             * buildRequest assembles values into API call.
             */
    Ed1.params = function () {
      // Custom test names
      ed11yLang.en.emptyWpButton = {title: 'Empty Wordpress Button'};

      let queryString = window.location.search;
      let urlParams = new URLSearchParams(queryString);
      Ed1.url = '//' + window.location.host + window.location.pathname + '?';
      if (urlParams.get('page')) {
        Ed1.url += 'page=' + urlParams.get('page') + '&';
      }
      let nonceWrapper = document.getElementById('editoria11y-nonce');
      Ed1.nonce = JSON.parse(nonceWrapper.innerHTML);

      // Only accept numerical offsets
      let resultOffset = urlParams.get('roff');
      resultOffset = !isNaN(resultOffset) ? +resultOffset : 0;
      let pageOffset = urlParams.get('poff');
      pageOffset = !isNaN(pageOffset) ? +pageOffset : 0;
      let recentOffset = urlParams.get('recentoff');
      recentOffset = !isNaN(recentOffset) ? +recentOffset : 0;

      // Allow list for sorts.
      let validSorts = [
        'page_title',
        'page_total',
        'result_count',
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
      Ed1.author = urlParams.get('author');
      Ed1.dismissor = urlParams.get('dismissor');

      Ed1.post_status = urlParams.get('post_status') || false;

      Ed1.openDetails = !!Ed1.resultKey || !!Ed1.type || !!Ed1.author || !!Ed1.post_status;

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
        author: Ed1.author,
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
        author: Ed1.author,
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
        author: Ed1.author,
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
        author: Ed1.author,
        dismissor: Ed1.dismissor,
      };
    };

    /**
     * Make nicename for page status.
     */
    const prettyStatus = function( page_status ) {
      if ( !page_status || page_status.length < 2) {
        return page_status;
      }
      page_status = page_status[0].toUpperCase() + page_status.slice(1);
      return page_status?.replace( 'Publish', 'Published' );
    };

    /**
     * Assemble request array into API call.
     * @param {*} request
     * @returns string
     */
    Ed1.buildRequest = function (request) {
      let q = Ed1.requests[request];
      let req = `${q.base}?view=${q.view}&count=${q.count}&offset=${q.offset}&sort=${q.sort}&direction=${q.direction}&result_key=${q.result_key}&author=${q.author}&entity_type=${q.entity_type}&post_status=${q.post_status}&dismissor=${q.dismissor}&nocache=${Date.now()}`;
      return req;
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
      if (!!Ed1.resultKey || !!Ed1.type || !!Ed1.post_status || !! Ed1.author || !! Ed1.dismissor ) {
        Ed1.h1 = Ed1.wrapper.querySelector('#ed1 h1');
        let resetType = 'View all issues';
        if (Ed1.resultKey) {
          Ed1.h1.textContent = 'Issue report: "' + ed11yLang.en[Ed1.resultKey].title + '"';
        } else if ( Ed1.type ) {
          Ed1.h1.textContent = 'Issues on pages of type "' + Ed1.type + '"';
          resetType = 'View issues on all pages';
        } else if ( Ed1.author ) {
          Ed1.h1.textContent = 'Issues on pages created by author';
        } else if ( Ed1.dismissor ) {
          Ed1.h1.textContent = 'Issues dismissed by';
        }
        else {
          Ed1.h1.textContent = prettyStatus( Ed1.post_status ) + ' pages';
          resetType = 'View issues on all pages';
        }
        let reset = Ed1.render.a(resetType, false, Ed1.url);
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
      if ( !Ed1.dismissor ) {
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
          el.textContent = 'API error.';
        });
      }, 3000);
    };

    Ed1.show = function () {
      if ( Ed1.dismissor ) {
        Ed1.wrapRecent.setAttribute('hidden', '');
        Ed1.wrapPage.setAttribute( 'hidden', '' );
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
        let direction = 'DESC' === sorted ? 'descending' : 'ascending';
        button.setAttribute('title', direction);
        button.setAttribute('class', direction);
      }
      return button;
    };

    // Render a link with url sanitized and html encoded.
    Ed1.render.a = function (text, hash = false, url = false, pid = false) {
      let link = document.createElement('a');
      link.textContent = text;
      let href;
      if (url) {
        let sep = url.indexOf('?') === -1 ? '?' : '&';
        url = encodeURI(url);
        href = pid ? url + sep + 'ed1ref=' + parseInt(pid) + '&_wpnonce=' + Ed1.nonce : url;
      } else {
        href = '#' + encodeURIComponent(hash);
      }
      link.setAttribute('href', href);
      return link;
    };

    Ed1.render.td = function (text, hash = false, url = false, pid = false, cls = false) {
      let cell = document.createElement('td');
      if (url) {
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
        pageNumber.setAttribute('aria-label', 'Page ' + index);
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

    Ed1.readyTriggers = function () {
      document.querySelectorAll('#ed1 button');
    };

    Ed1.render.tableHeaders = function () {

      let head = false;
      let loadWrap = document.createElement('tr');
      let loading = Ed1.render.td('loading...', false, false, false, 'loading');
      loadWrap.append(loading);

      // Pages table
      Ed1.tables['ed1page'] = document.createElement('table');
      Ed1.tables['ed1page'].setAttribute('id', 'ed1page');

      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th('Issues', 'page_total', 'DESC'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Page', 'page_title'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Path', 'page_url'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Type', 'entity_type'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Status', 'post_status'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Updated', 'post_modified'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Author'));
      Ed1.tables['ed1page'].insertAdjacentElement('beforeend', head);

      loading.setAttribute('colspan', '6');
      Ed1.tables['ed1page'].append(loadWrap.cloneNode('deep'));
      let pageDetails = Ed1.render.details('Issues by page', 'ed1page-title', true);
      Ed1.wrapPage.append(pageDetails);
      pageDetails.append(Ed1.tables['ed1page']);
      Ed1.tables['ed1page'].querySelectorAll('button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1page(Ed1.buildRequest('ed1page'));
        });
      });

      // Recent table
      Ed1.tables['ed1recent'] = document.createElement('table');
      Ed1.tables['ed1recent'].setAttribute('id', 'ed1recent');

      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th('Detected', 'detected', 'DESC'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Page', 'page_title'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Path', 'page_url'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Issue', 'result_key'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Count', 'result_count'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Type', 'entity_type'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Status', 'post_status'));
      Ed1.tables['ed1recent'].insertAdjacentElement('beforeend', head);

      loading.setAttribute('colspan', '6');
      Ed1.tables['ed1recent'].append(loadWrap.cloneNode('deep'));
      let recentDetails = Ed1.render.details('Recent issues', 'ed1page-title', true);
      Ed1.wrapRecent.append(recentDetails);
      recentDetails.append(Ed1.tables['ed1recent']);
      Ed1.tables['ed1recent'].querySelectorAll('button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1recent(Ed1.buildRequest('ed1recent'));
        });
      });

      // Results table
      Ed1.tables['ed1result'] = document.createElement('table');
      Ed1.tables['ed1result'].setAttribute('id', 'ed1result');
      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th('Pages', 'count', 'DESC'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Issue', 'result_key'));
      Ed1.tables['ed1result'].insertAdjacentElement('beforeend', head);

      let resultDetails = Ed1.render.details('Issue types', 'ed1result-title');
      Ed1.wrapResults.append(resultDetails);
      loading.setAttribute('colspan', '2');
      Ed1.tables['ed1result'].append(loadWrap.cloneNode('deep'));
      resultDetails.append(Ed1.tables['ed1result']);

      Ed1.tables['ed1result'].querySelectorAll('th button').forEach((el) => {
        el.addEventListener('click', function () {
          Ed1.reSort();
          Ed1.get.ed1result(Ed1.buildRequest('ed1result'));
        });
      });

      // Dismissals table
      Ed1.tables['ed1dismiss'] = document.createElement('table');
      Ed1.tables['ed1dismiss'].setAttribute('id', 'ed1dismiss');
      head = document.createElement('tr');
      head.insertAdjacentElement('beforeend', Ed1.render.th('On', 'created', 'DESC'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Page', 'page_title'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Path', 'page_url'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Dismissed alert', 'result_key'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Marked', 'dismissal_status'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('Current', 'stale'));
      head.insertAdjacentElement('beforeend', Ed1.render.th('By'));
      Ed1.tables['ed1dismiss'].insertAdjacentElement('beforeend', head);

      loading.setAttribute('colspan', '6');
      Ed1.tables['ed1dismiss'].append(loadWrap.cloneNode('deep'));

      let detailTitle = Ed1.openDetails ? 'Dismissals' : 'Recent dismissals';

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
          row.insertAdjacentElement('beforeend', pageCount);

          let keyName = ed11yLang.en[result['result_key']] ? ed11yLang.en[result['result_key']].title : result['result_key'];

          // URL sanitized on build...
          let key = Ed1.render.td(keyName, false, Ed1.url + 'rkey=' + result['result_key'], false, 'rkey');
          row.insertAdjacentElement('beforeend', key);

          Ed1.tables['ed1result'].insertAdjacentElement('beforeend', row);
        });

        if (!Ed1.csvLink) {
          Ed1.csvLink = Ed1.render.a('Download results as CSV', '' , Ed1.url + '&ed11y_export_results_csv=download&_wpnonce=' + Ed1.nonce );
          Ed1.csvLink.classList.add('ed11y-export');
          Ed1.wrapper.append( Ed1.csvLink );
        }
      }

      if (announce) {
        Ed1.announce(post.length + ' results');
      }

      Ed1.show();

    };

    Ed1.authorList = {};
    Ed1.matchAuthors = function( author_query ) {
      author_query?.forEach( ( author ) => {
        Ed1.authorList[author.ID] = author.display_name;
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

          let path = result['page_url'].replace(window.location.protocol + '//' + window.location.host, '');
          path = Ed1.render.td( path ? path : '/' );
          row.insertAdjacentElement('beforeend', path);

          // need to sanitize URL in response?
          let keyName = ed11yLang.en[result['result_key']].title;
          let key = Ed1.render.td(keyName, false, Ed1.url + 'rkey=' + result['result_key'], false, 'rkey');
          row.insertAdjacentElement('beforeend', key);

          let pageCount = Ed1.render.td(result['result_count']);
          row.insertAdjacentElement('beforeend', pageCount);

          let type = Ed1.render.td(result['entity_type'], false, `${Ed1.url}type=${result['entity_type']}`);
          row.insertAdjacentElement('beforeend', type);

          let post_status = result['post_status'] ?
              Ed1.render.td( prettyStatus( result['post_status'] ), false, `${Ed1.url}post_status=${result['post_status']}` )
              : Ed1.render.td('Published', false, `${Ed1.url}post_status=publish`);
          row.insertAdjacentElement('beforeend', post_status);


          Ed1.tables['ed1recent'].insertAdjacentElement('beforeend', row);
        });
      }

      if (announce) {
        Ed1.announce(post.length + ' results');
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
          row.insertAdjacentElement('beforeend', pageCount);

          let pageLink = Ed1.render.td(result['page_title'], false, result['page_url'], result['pid']);
          row.insertAdjacentElement('beforeend', pageLink);

          let path = result['page_url'].replace(window.location.protocol + '//' + window.location.host, '');
          path = Ed1.render.td( path ? path : '/' );
          row.insertAdjacentElement('beforeend', path);

          let type = Ed1.render.td(result['entity_type'], false, `${Ed1.url}type=${result['entity_type']}`);
          row.insertAdjacentElement('beforeend', type);

          let post_status = result['post_status'] ?
              Ed1.render.td( prettyStatus( result['post_status'] ), false, `${Ed1.url}post_status=${result['post_status']}` )
              : Ed1.render.td('Published', false, `${Ed1.url}post_status=publish`);
          row.insertAdjacentElement('beforeend', post_status);

          let date = result['post_modified'] ?
              Ed1.render.td( result['post_modified'].split(' ')[0].replace(/[^\-0-9]/g, '') )
              : Ed1.render.td('n/a', false, false, false, 'muted' );

          row.insertAdjacentElement('beforeend', date);

          if ( result['post_author'] ) {
            row.insertAdjacentElement(
                'beforeend',
                Ed1.render.td(
                    Ed1.authorList[ result['post_author'] ] || result['post_author'],
                    false,
                    `${Ed1.url}author=${result['post_author']}`,
                ),
            );
          } else {
            row.insertAdjacentElement(
                'beforeend',
                Ed1.render.td('n/a', false, false, false, 'muted'),
            );
          }

          Ed1.tables['ed1page'].insertAdjacentElement('beforeend', row);

        });
      }

      if (announce) {
        Ed1.announce(post.length + ' results');
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
          let notFound = Ed1.render.noResults('None', '6');
          Ed1.tables['ed1dismiss'].insertAdjacentElement('beforeend', notFound);
        } else {
          post.forEach((result) => {
            let row = document.createElement('tr');

            let cleanDate = result['created'].split(' ')[0].replace(/[^\-0-9]/g, '');
            let on = Ed1.render.td(cleanDate);
            row.insertAdjacentElement('beforeend', on);

            let pageLink = Ed1.render.td(result['page_title'], false, result['page_url'], result['pid']);
            row.insertAdjacentElement('beforeend', pageLink);

            let path = result['page_url'].replace(window.location.protocol + '//' + window.location.host, '');
            path = Ed1.render.td( path ? path : '/' );
            row.insertAdjacentElement('beforeend', path);

            // need to sanitize URL in response?
            let keyName = ed11yLang.en[result['result_key']].title;
            let key = Ed1.render.td(keyName, false, Ed1.url + 'rkey=' + result['result_key'], false, 'rkey');
            row.insertAdjacentElement('beforeend', key);

            let marked = Ed1.render.td(result['dismissal_status']);
            row.insertAdjacentElement('beforeend', marked);

            // Still on page?
            let stale = Ed1.render.td(!result['stale'] ? 'No' : 'Yes');
            row.insertAdjacentElement('beforeend', stale);

            let dismissor;
            if ( Ed1.dismissor && !dismissor) {
              Ed1.h1.textContent = 'Issues dismissed by ' + Ed1.authorList[ Ed1.dismissor ];
            }
            let by = Ed1.render.td( Ed1.authorList[ result['user'] ] || result['user'] , false, Ed1.url + 'dismissor=' + result['user']);
            row.insertAdjacentElement('beforeend', by);

            Ed1.tables['ed1dismiss'].insertAdjacentElement('beforeend', row);
          });
        }


      }


      if (announce) {
        Ed1.announce(post.length + ' results');
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
        'X-WP-Nonce': wpApiSettings.nonce,
      }
    };

    Ed1.get = {};
    Ed1.get.ed1page = async function (action, announce = false) {
      fetch(wpApiSettings.root + 'ed11y/v1/' + action, Ed1.api,
      ).then(function (response) {
        return response.json();
      }).then(function (post) {
        if (post?.data?.status === 500) {
          console.error(post.data.status + ': ' + post.message);
        } else {
          Ed1.matchAuthors( post[2] );
          if ( Ed1.author && Ed1.authorList[ Ed1.author ]) {
            Ed1.h1.textContent = 'Issues on pages created by ' + Ed1.authorList[ Ed1.author ];
          }
          Ed1.render.ed1page(post[0], post[1], announce);
        }
      });
    };
    Ed1.get.ed1recent = async function (action, announce = false) {
      fetch(wpApiSettings.root + 'ed11y/v1/' + action, Ed1.api,
      ).then(function (response) {
        return response.json();
      }).then(function (post) {
        if (post?.data?.status === 500) {
          console.error(post.data.status + ': ' + post.message);
        } else {
          Ed1.render.ed1recent(post[0], post[1], announce);
        }
      });
    };
    Ed1.get.ed1result = async function (action, announce = false) {
      fetch(wpApiSettings.root + 'ed11y/v1/' + action, Ed1.api,
      ).then(function (response) {
        return response.json();
      }).then(function (post) {
        if (post?.data?.status === 500) {
          console.error(post.data.status + ': ' + post.message);
        } else {
          Ed1.render.ed1result(post[0], post[1], announce);
        }
      });
    };
    Ed1.get.ed1dismiss = async function (action, announce = false) {
      fetch(wpApiSettings.root + 'ed11y/v1/' + action, Ed1.api,
      ).then(function (response) {
        return response.json();
      }).then(function (post) {
        if (post?.data?.status === 500) {
          console.error(post.data.status + ': ' + post.message);
        } else {
          Ed1.matchAuthors( post[2] );
          Ed1.render.ed1dismiss(post[0], post[1], announce);
        }
      });
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

new Ed1();
Ed1.init();


