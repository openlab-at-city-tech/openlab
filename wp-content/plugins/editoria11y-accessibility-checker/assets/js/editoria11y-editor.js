// eslint-disable-next-line no-undef
let ed11yOptions = ed11yVars.options;
let ed11yOpen = localStorage.getItem('ed11yOpen');
ed11yOpen = 'open' === ed11yOpen ? true : false;
let ed11yReadyCount = 0;
let ed11yScriptIs = false; // onPage, inIframe, outsideIframe
let ed11yButtonWrapper = false;
let ed11yWPBlocks = [];
// eslint-disable-next-line no-undef
const ed11yWorker = window.SharedWorker ? new SharedWorker(ed11yVars.worker) : false;


// Possible todo: create aria-live region? Populate it with the issues for the is-active block?

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

// Call callback, scan page for compatible editors.
ed11yReady(
  function () {
    ed11yFindCompatibleEditor();
  }
);

let ed11yUpdateButton = function(count) {
  if (!Ed11y.wpIssueToggle) {
    // Set up issue counter link.
    let ed11yButtonDescription = document.createElement('span');
    ed11yButtonDescription.setAttribute('hidden', '');
    ed11yButtonDescription.setAttribute('id', 'ed11y-button-description');
    ed11yButtonDescription.textContent = 'Screen reader accessible issue descriptions have been added to the preview page.';

    Ed11y.wpIssueToggle = document.createElement('button');
    Ed11y.wpIssueToggle.classList.add('components-button', 'is-secondary', 'hidden');
    Ed11y.wpIssueToggle.setAttribute('id', 'ed11y-issue-link');
    Ed11y.wpIssueToggle.setAttribute('title', 'Accessibility checker alerts');
    Ed11y.wpIssueToggle.setAttribute('aria-describedby', 'ed11y-button-description');
    Ed11y.wpIssueToggle.addEventListener('click', function () {
      if (ed11yOpen) {
        // closing
        let newStyles = document.querySelector('#ed11y-live-highlighter');
        if (newStyles) {
          newStyles.innerHTML = '';
        }
      } else {
        Ed11y.wpIssueToggle.textContent = 'Hide issues';
      }
      ed11yOpen = !ed11yOpen;
      let newState = ed11yOpen ? 'open' : 'shut';
      localStorage.setItem('ed11yOpen', newState);
      ed11yOptions['showResults'] = ed11yOpen;
      if (ed11yScriptIs === 'onPage') {
        Ed11y.checkAll(false, false);
      } else if (ed11yWorker) {
        ed11yWorker.port.postMessage([false, false, newState]);
      }


    });
    Ed11y.wpIssueToggle.textContent = '0';
    if (ed11yButtonWrapper.matches('.editor-header__center')) {
      ed11yButtonWrapper.append(Ed11y.wpIssueToggle);
      Ed11y.wpIssueToggle.style.marginLeft = '.5rem';
      Ed11y.wpIssueToggle.style.marginRight = '.75rem';
    } else {
      ed11yButtonWrapper.prepend(Ed11y.wpIssueToggle);
    }
    Ed11y.wpIssueToggle.insertAdjacentElement('afterend', ed11yButtonDescription);

    let ed11yStyle = document.createElement('div');
    ed11yStyle.setAttribute('hidden', '');
    ed11yStyle.innerHTML = `
 <style>
   .edit-site-layout #ed11y-issue-link {
      display: none;
  }
   .is-full-canvas #ed11y-issue-link {
      display: inline-block;
      position: absolute;
      top: 59px;
      background: white;
      border-radius: 0;
      box-shadow: 1px 1px var(--wp-components-color-accent,var(--wp-admin-theme-color,#3858e9));
  }
  #ed11y-issue-link.ed11y-warning {
   background-color: #fad859;
   color: #000b;
   box-shadow: none;
  }

  #ed11y-issue-link.ed11y-alert {
   background-color: #b80519;
   color: #fff;
   box-shadow: none;
  }
  #ed11y-issue-link:hover {
   background: var(--wp-admin-theme-color-darker-10);
   color: white;
  }
  #ed11y-issue-link:focus-visible {
   box-shadow: 0 0 0 1px white, 0 0 0 2px var(--wp-admin-theme-color-darker-10);
  }
  ed11y-element-panel { display: none !important; }
 </style>`;
    Ed11y.wpIssueToggle.insertAdjacentElement('afterend', ed11yStyle);
  }
  let buttonText = ed11yOpen ? 'Hide alerts' : `${count}`;
  Ed11y.wpIssueToggle.textContent = buttonText;
  if ((ed11yOptions['liveCheck'] === 'all') && Ed11y.totalCount === 0 || ed11yOptions['liveCheck'] === 'errors' && Ed11y.errorCount === 0) {
    Ed11y.wpIssueToggle.classList.add('hidden');
  } else if (ed11yOpen) {
    Ed11y.wpIssueToggle.classList.remove('ed11y-warning', 'hidden', 'ed11y-alert');
  } else if (!ed11yOpen && Ed11y.errorCount > 0) {
    Ed11y.wpIssueToggle.classList.remove('ed11y-warning', 'hidden');
    Ed11y.wpIssueToggle.classList.add('ed11y-alert');
  } else if (!ed11yOpen && Ed11y.warningCount > 0 && ed11yOptions['liveCheck'] !== 'errors') {
    Ed11y.wpIssueToggle.classList.remove('ed11y-alert', 'hidden');
    Ed11y.wpIssueToggle.classList.add('ed11y-warning');
  }
};


// Get issue count from Ed11y object and apply to alert link.
let  ed11yReadResults = function () {
  // todo inject from WP
  let count = ed11yOptions['liveCheck'] === 'errors' ? Ed11y.errorCount : Ed11y.totalCount;
  count = parseInt(count);
  if (ed11yScriptIs === 'inIframe') {
    ed11yWorker.port.postMessage([false, {
      totalCount: Ed11y.totalCount,
      warningCount: Ed11y.warningCount,
      errorCount: Ed11y.errorCount,
    }]);
  } else {
    ed11yUpdateButton(count);
  }

  if (ed11yScriptIs === 'outsideIframe') {
    return;
  }

  // Otherwise highlight items.
  let newStyles = document.querySelector('#ed11y-live-highlighter');
  if (!newStyles) {
    newStyles = document.createElement('div');
    newStyles.setAttribute('hidden', '');
    newStyles.setAttribute('id', 'ed11y-live-highlighter');
    document.querySelector('body').append(newStyles);
  }

  // Possible todo: aria-live announcements.
  if (Ed11y.results.length > 0 && ed11yOptions['showResults'] === true) {
    let ed11yStyles = '';
    let ed11yKnownContainers = {};
    Ed11y.results.forEach(result => {
      let ed11yContainerId = result.element.closest('.wp-block').getAttribute('id');
      // Skip dismissed items, and only show warnings if they have not been suppressed in plugin settings.
      if (ed11yOpen && result.dismissalStatus === false && !(ed11yOptions['liveCheck'] === 'errors' && result.dismissalKey)) {
        let subSelector = false;
        if (result.element.closest('a[href]')) {
          subSelector = `a[href="${CSS.escape(result.element.closest('a[href]').getAttribute('href'))}"]`;
        } else if (result.element.closest('img[src]')) {
          subSelector = `img[src="${CSS.escape(result.element.closest('img[src]').getAttribute('src'))}"]`;
        }
        let ed11yRingColor = !result.dismissalKey ? Ed11y.theme.alert : Ed11y.theme.warning;
        let ed11yFontColor = !result.dismissalKey ? '#fff' : '#111';
        let subRing = {
            ring: ed11yRingColor,
            subSelector : subSelector,
        };
        // Concatenate results when multiple hits in same black.
        if (!ed11yKnownContainers[ed11yContainerId]) {
          // First alert in block.
          ed11yKnownContainers[ed11yContainerId] = {
            title: Ed11y.M[result.test]['title'],
            ring: ed11yRingColor,
            font: ed11yFontColor,
            subSelector : [subRing],
          };
        } else {
          if (ed11yKnownContainers[ed11yContainerId]['title'].indexOf(Ed11y.M[result.test]['title']) === -1) {
            // First alert of this type in block.
            if (ed11yKnownContainers[ed11yContainerId]['ring'] !== ed11yRingColor) {
              // If either is red, red wins.
              ed11yRingColor = Ed11y.theme.alert;
              ed11yFontColor = '#fff';
            }
            // Put question marks at end.
            let ed11yNewTitle = '';
            if (Ed11y.M[result.test]['title'].indexOf('?') === -1) {
              ed11yNewTitle = Ed11y.M[result.test]['title'] + ', ' + ed11yKnownContainers[ed11yContainerId]['title'];
            } else {
              ed11yNewTitle = ed11yKnownContainers[ed11yContainerId]['title'] + ', ' + Ed11y.M[result.test]['title'];
            }
            ed11yKnownContainers[ed11yContainerId].title = ed11yNewTitle;
            ed11yKnownContainers[ed11yContainerId].ring = ed11yRingColor;
            ed11yKnownContainers[ed11yContainerId].font = ed11yFontColor;
            ed11yKnownContainers[ed11yContainerId].subSelector.push(subRing);
          }
        }
      }

    });

    for (const [key, value] of Object.entries(ed11yKnownContainers)) {
      ed11yStyles += `
    #${key}::after {
     position: absolute !important;
     font-size: 13px !important;
     background: ${value.ring} !important;
     color: ${value.font} !important;
     display: inline-block !important;
     padding: 4px 4px 2px 6px !important;
     content: "${value.title.replace('"', "'").replace('?,', ',') /* eslint-disable-line */}";
     z-index: -1 !important;
     opacity: 0 !important;
     font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
     font-weight: 500 !important;
     line-height: 15px !important;
     bottom: 0 !important;
     right: 0 !important;
          top: auto !important;
          left: auto !important;
          width: auto !important;
          height: auto !important;
     border-radius: 2px 0 0 0 !important;
          letter-spacing: 0 !important;
    }
    #${key}:not(.is-selected)::after {
     opacity: 1 !important;
     z-index: 1 !important;
    }
    #${key}:not(.is-selected) {
     box-shadow: 0 0 0 2px ${value.ring};
     outline: 1px solid ${value.ring};
     border-radius: 2px;
    }
   `;
   value.subSelector.forEach(subSelector => {
    if (subSelector) {
        ed11yStyles += `
        #${key}:not(.is-selected) ${subSelector.subSelector} {
            box-shadow: 0 0 0 2px ${subSelector.ring}, 0 0 0 3px ;
            outline: 2px solid ${subSelector.ring};
            border-radius: 2px;
           }
        `;
       }
   });

    }




    newStyles.innerHTML = '';
    let styleWrapper = document.createElement('style');
    styleWrapper.textContent = ed11yStyles;
    newStyles.append(styleWrapper);
  }
  else {
    newStyles.innerHTML = '';
  }
};

let ed11yFindNewBlocks = function () {
  ed11yOptions['ignoreElements'] = ed11yOptions['originalIgnore'];
  let ed11yActiveBlock = document.querySelector('.wp-block.is-selected')?.getAttribute('id');
  // Ignoring a new block until it is edited.
  if (!!ed11yActiveBlock && ed11yActiveBlock !== 'undefined' && !ed11yWPBlocks.includes(ed11yActiveBlock)) {
    ed11yWPBlocks.push(ed11yActiveBlock);
    ed11yOptions['ignoreElements'] += `, #${ed11yActiveBlock}, #${ed11yActiveBlock} *`;
  }
};

let ed11yFirstScan = function() {
  let ed11yInitialBlocks = document.querySelectorAll('.wp-block');
  if (ed11yInitialBlocks.length !== null) {
    ed11yInitialBlocks.forEach(block => {
      ed11yWPBlocks.push(block.getAttribute('id'));
    });
  }
  ed11yFindNewBlocks();
  const ed11y = new Ed11y(ed11yOptions); // eslint-disable-line
  document.addEventListener('ed11yResults', function () {
    ed11yReadResults();
  });
};

let ed11yGetOptions = function() {
  ed11yOptions.linkStringsNewWindows = ed11yOptions.linkStringsNewWindows ? new RegExp(ed11yOptions.linkStringsNewWindows, 'g') : /window|\stab|download/g;

  // Initiate Ed11y with admin options.
  // Possible todo: pick checkRoot dynamically based on ed11yTarget.
  ed11yOptions['checkRoots'] = '.editor-styles-wrapper';
  ed11yOptions['ignoreByKey'] = { img: '' };
  ed11yOptions['ignoreByKey']['h'] = '.wp-block-post-title';
  ed11yOptions['altPlaceholder'] = 'This image has an empty alt attribute;';

  // Wordpress does not render empty post titles so we don't need to flag them.
  ed11yOptions['originalIgnore'] = ed11yOptions['ignoreElements'];

  ed11yOptions['showResults'] = true;
  ed11yOptions['alertMode'] = 'headless';
};

let ed11yChangeObserverInit = function () {
  // Set up change observer.
  const ed11yTargetNode = document.querySelector('.editor-styles-wrapper');
  // Observe for class changes and typing.
  const ed11yObserverConfig = { attributeFilter: ['class'], characterData: true, subtree: true };
  // Immediately recheck on class change; wait for typing pauses for typing.
  const ed11yMutationCallback = (callback) => {
    if (callback[0].type === 'characterData') {
      ed11yMutationTimeoutWatch(750);
    } else {
      ed11yMutationTimeoutWatch(0);
    }

  };

  // Create an observer instance linked to the callback function
  const ed11yObserver = new MutationObserver(ed11yMutationCallback);

  // Start observing the target node for configured mutations
  ed11yObserver.observe(ed11yTargetNode, ed11yObserverConfig);
};

let ed11yInnerInit = function() {
  ed11yWorker.port.onmessage = (e) => {
    if (e.data[2]) {
      // open or shut
      ed11yOpen = e.data[2] === 'open';
      ed11yOptions['showResults'] = ed11yOpen;
      Ed11y.checkAll(false, false);
    }
  };
  ed11yGetOptions();
  ed11yFirstScan();
  ed11yChangeObserverInit();
};

let ed11yOuterInit = function() {
  ed11yGetOptions();
  // todo: set up button update
  ed11yWorker.port.onmessage = function (e) {
    if (e.data[1]) {
      Ed11y.totalCount = e.data[1].totalCount;
      Ed11y.warningCount = e.data[1].warningCount;
      Ed11y.errorCount = e.data[1].errorCount;
    }
    ed11yReadResults();
  };
  // todo check for messages
};

// Initiate Editoria11y create alert link, initiate content change watcher.
let ed11yPageInit = function () {
  ed11yGetOptions();
  ed11yFirstScan();
  ed11yChangeObserverInit();
};

// Look to see if Gutenberg has loaded.
// Possible todo: add checks/markup for other common editors.
let ed11yFindCompatibleEditor = function () {

  if (!ed11yScriptIs) {

    if (document.querySelector('body.editor-styles-wrapper') && ed11yWorker) {
      // inside iFrame
      ed11yScriptIs = 'inIframe';
    } else if (document.querySelector('[class*="-visual-editor"] iframe') && ed11yWorker) {
      ed11yScriptIs = 'outsideIframe'; // onPage, inIframe, outsideIframe
    } else if ( document.querySelector('.has-inline-canvas')) {
      ed11yScriptIs = 'onPage';
    }
  }

  switch (ed11yScriptIs) {
  case 'inIframe':
    ed11yInnerInit();
    return;
  case 'onPage':
  case 'outsideIframe':
    ed11yButtonWrapper = ed11yButtonWrapper ? ed11yButtonWrapper : document.querySelector('.edit-post-header__settings, .editor-header__center, .edit-site-layout__header-container');
  }
  if (!!ed11yButtonWrapper && !!ed11yOptions) {
    if (ed11yScriptIs === 'onPage') {
      ed11yPageInit();
    } else {
      ed11yOuterInit();
    }
    return;
  }
  if (ed11yReadyCount < 600) {
    window.setTimeout(function () {
      ed11yReadyCount++;
      ed11yFindCompatibleEditor();
    }, 1000);
  } else {
    console.log('No editor found');
  }

};

/**
 * Debounced recheck:
 * Immediately, on block selection change.
 * After .75s pause, when typing.
 * No more frequently than every 1500s.
 */
let ed11yMutationTimeout;
let ed11yMutationRacer = 0;
function ed11yMutationTimeoutWatch(wait) {
  clearTimeout(ed11yMutationTimeout);

  let timeOut = ed11yMutationRacer - Date.now();
  timeOut = timeOut < 0 ? wait : timeOut;

  ed11yMutationTimeout = setTimeout(function () {
    ed11yMutationRacer = Date.now() + 1500;
    if (Ed11y && Ed11y.running === false) {
      ed11yFindNewBlocks();
      Ed11y.options.ignoreElements = ed11yOptions['ignoreElements'];
      Ed11y.checkAll(false, false);
    }
  }, timeOut);
}
