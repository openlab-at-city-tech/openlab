let ed11yOptions = {};
let ed11yResetID = false;

// Create callback to see if document is ready.
function ed11yReady(fn) {
  if (document.readyState != 'loading') {
    fn();
  } else if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fn);
  } else {
    document.attachEvent('onreadystatechange', function () {
      if (document.readyState != 'loading')
        fn();
    });
  }
}

function ed11ySync() {
  let postData = async function (action, data) {
    fetch(wpApiSettings.root + 'ed11y/v1/' + action, {
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
      return response.json();
    });
  };

  let extractResults = function () {
    let results = {};
    let dismissals = [];
    let total = 0;
    Ed11y.results.forEach(result => {
      /* let test = Ed11y.results[i][1];
						  let dismissKey = Ed11y.results[i][4]; */
      let testName = result.test;
      if (result.dismissalStatus !== 'ok') {
        // log all items not marked as OK
        if (results[testName]) {
          results[testName] = parseInt(results[testName]) + 1;
          total++;
        } else {
          results[testName] = 1;
          total++;
        }
      }
      if (result.dismissalStatus !== 'false') {
        let insert = [testName, result.dismissalKey];
        dismissals.push(
          insert
        );
      }
    });
    return [results, dismissals, total];
  };

  let url = Ed11y.options.currentPage;
  url = url.length > 190 ? url.substring(0, 189) : url;
  let queryString = window.location.search;
  let urlParams = new URLSearchParams(queryString);
  ed11yResetID = urlParams.get('ed1ref');

  let sendResults = function () {
    window.setTimeout(function () {
        if ( ed11yOptions.post_id && document.getElementsByClassName('post-password-form').length > 0 ) {
            // Don't sync "0 results" if the post has been replaced by a login form.
            return;
        }
		let results = extractResults();
		let data = {
			page_title: ed11yOptions.title,
            post_id: ed11yOptions.post_id ? ed11yOptions.post_id : 0,
			page_count: results[2],
			entity_type: ed11yOptions.entity_type, // node or false
			results: results[0],
			dismissals: results[1],
			page_url: url,
			created: 0,
			pid: ed11yResetID ? parseInt(ed11yResetID) : -1,
		};
		postData('result', data);
		ed11yResetID = false;
      // Short timeout to let execution queue clear.
    }, 100);
  };

  let resetResults = function () {
	window.setTimeout(function () {
		let results = {};
		let data = {
			page_title: ed11yOptions.title,
			page_count: results[2],
			entity_type: ed11yOptions.entity_type, // node or false
			results: results[0],
			dismissals: results[1],
			page_url: url,
			created: 0,
			pid: ed11yResetID ? parseInt( ed11yResetID ) : -1,
            post_id: ed11yOptions.post_id ? ed11yOptions.post_id : 0,
		};
		postData('result', data);
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
      let data = {
        page_url: Ed11y.options.currentPage,
        result_key: detail.dismissTest, // which test is sending a result
        element_id: detail.dismissKey, // some recognizable attribute of the item marked
        dismissal_status: detail.dismissAction, // ok, ignore or reset
        post_id: ed11yOptions.post_id ? ed11yOptions.post_id : 0,
      };
      postData('dismiss', data);
    }
  };
  document.addEventListener('ed11yDismissalUpdate', function (e) {
    sendDismissal(e.detail);
  }, false);

}

const ed11yCustomTests = function() {
    document.addEventListener('ed11yRunCustomTests', function() {

        // 1. Write your custom test.
        // This can be anything you want.
        //
        // This example uses Ed11y.findElements(),
        // which respects checkRoots and ignoreElements,
        // to find links with a particular string in their URL.
        Ed11y.findElements('emptyWpButton','a.wp-element-button:not([href], [tabindex])');

        // 2. Create a message for your tooltip.
        // You'll need a title and some contents,
        // as a value for the key you create for your test.
        //
        // Be sure to use the same key as the test name below.

        Ed11y.M.emptyWpButton = {
            title: 'Empty Wordpress Button',
            tip: () =>
                '<p>The button style visually indicates a link, but this button is not linked.</p>',
        };

        // 3. Push each item you want flagged to Ed11y.results.
        //
        // You must provide:
        //   el: The element
        //   test: A key for your test
        //   content: The tip you created above.
        //   position: "beforebegin" for images and links,
        //             "afterbegin" for paragraphs.
        //   dismissalKey: false for errors,
        //             a unique string for manual checks.
        Ed11y.elements.emptyWpButton?.forEach((el) => {
            Ed11y.results.push({
                element: el,
                test: 'emptyWpButton',
                content: Ed11y.M.emptyWpButton.tip(),
                position: 'beforebegin',
                dismissalKey: false,
            });
        });

        // 4. When you are done with all your custom tests,
        // dispatch an "ed11yResume" event:
        let allDone = new CustomEvent('ed11yResume');
        document.dispatchEvent(allDone);
    });
};

// Call callback, init Editoria11y.
ed11yReady(
  function () {
    let ed11yOpts = document.getElementById('editoria11y-init');
    if (!!ed11yOpts && window.location.href.indexOf('elementor-preview') === -1) {
      ed11yOptions = JSON.parse(ed11yOpts.innerHTML);
      ed11yOptions.customTests = 1;
      ed11yOptions.panelNoCover = '#edac-highlight-panel'; // Accessibility Checker module

      ed11yOptions.linkStringsNewWindows = ed11yOptions.linkStringsNewWindows ? new RegExp(ed11yOptions.linkStringsNewWindows, 'g') : /window|\stab|download/g;
      if (ed11yOptions.title.length < 3) {
        ed11yOptions.title = document.title;
      }
      ed11yOptions['checkVisible'] = ed11yOptions['checkVisible'] === 'true';
      // When triggered by the in-editor "issues" link, force assertive.
      if (window.location.href.indexOf('preview=true') > -1) {
        ed11yOptions['alertMode'] = 'assertive';
      }
      if (window.location.href.indexOf('ed1ref') > -1) {
        ed11yOptions['alertMode'] = 'assertive';
        ed11yOptions['showDismissed'] = true;
      }
      ed11yOptions.cssUrls = [ed11yOptions.cssLocation];

	  const ed11y = new Ed11y(ed11yOptions); // eslint-disable-line
      ed11yCustomTests();
      ed11ySync();
    }
  }
);
