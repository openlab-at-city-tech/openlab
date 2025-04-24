
const ed11yInit = {};
// eslint-disable-next-line no-undef
ed11yInit.options = ed11yVars.options;
ed11yInit.ed11yReadyCount = 0;
ed11yInit.editorType = false; // onPage, inIframe, outsideIframe
// Prevent multiple inits in modules that re-trigger the document context.
ed11yInit.once = false;
ed11yInit.noRun = '.editor-styles-wrapper > .is-root-container.wp-site-blocks, .edit-site-visual-editor__editor-canvas';
ed11yInit.editRoot = '.editor-styles-wrapper > .is-root-container:not(.wp-site-blocks)'; // differentiate page from iframe
ed11yInit.scrollRoot = false;


ed11yInit.syncDismissals = function () {
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

  let sendDismissal = function (detail) {
    if (detail) {
      let data = {
        page_url: Ed11y.options.currentPage,
        result_key: detail.dismissTest, // which test is sending a result
        element_id: detail.dismissKey, // some recognizable attribute of the item marked
        dismissal_status: detail.dismissAction, // ok, ignore or reset
        post_id: ed11yInit.options.post_id ? ed11yInit.options.post_id : 0,
      };
      postData('dismiss', data);
    }
  };
  document.addEventListener('ed11yDismissalUpdate', function (e) {
    sendDismissal(e.detail);
  }, false);

};


ed11yInit.getOptions = function() {
  // Initiate Ed11y with admin options.

  ed11yInit.options.linkStringsNewWindows = ed11yInit.options.linkStringsNewWindows ?
    new RegExp(ed11yInit.options.linkStringsNewWindows, 'g') :
    /window|\stab|download/g;
  ed11yInit.options['inlineAlerts'] = false;
  ed11yInit.autoDetectShadowComponents = false; // too slow for editor.
  ed11yInit.options.checkRoots = ed11yInit.editRoot;
  ed11yInit.options['preventCheckingIfPresent'] = ed11yInit.noRun ?
    ed11yInit.noRun + ', .block-editor-block-preview__content-iframe' :
    '.block-editor-block-preview__content-iframe';

  ed11yInit.options['ignoreAllIfAbsent'] = ed11yInit.editRoot;
  if (ed11yInit.scrollRoot) {
    ed11yInit.options['editableContent'] = ed11yInit.scrollRoot;
  }
  //ed11yInit.options['ignoreByKey'] = { img: '' }; Restore default ignores.
  ed11yInit.options['headingsOnlyFromCheckRoots'] = true;
  ed11yInit.options['ignoreAriaOnElements'] = 'h1,h2,h3,h4,h5,h6';
  ed11yInit.options['altPlaceholder'] = 'This image has an empty alt attribute;';

  // WordPress does not render empty post titles, so we don't need to flag them.

  ed11yInit.options['showResults'] = true;
  ed11yInit.options['buttonZIndex'] = 99999;
  ed11yInit.options['alertMode'] = ed11yInit.options['liveCheck'] &&  ed11yInit.options['liveCheck'] === 'errors' ? 'userPreference' : 'active';
  ed11yInit.options['editorHeadingLevel'] = [{
    selector: '.editor-styles-wrapper > .is-root-container',
    previousHeading: 1,
  }];
};

ed11yInit.firstCheck = function() {
  if (!ed11yInit.once) {
    ed11yInit.once = true;
    const ed11y = new Ed11y(ed11yInit.options); // eslint-disable-line
  }
};

ed11yInit.nextCheck = Date.now();
ed11yInit.waiting = false;
ed11yInit.lastText = '';
ed11yInit.recheck = (forceFull) => {
  // Debouncing to 1x per second.
  let nextRun = ed11yInit.nextCheck + Ed11y.browserLag - Date.now();
  if (nextRun > 0) {
    // Not time to go yet.
    if (!ed11yInit.waiting) {
      // Wait and start debouncing.
      ed11yInit.waiting = true;
      window.setTimeout(() => {ed11yInit.recheck(forceFull);}, nextRun, forceFull);
    }
  } else {
    // Check now.
    ed11yInit.nextCheck = Date.now() + 1000 + Ed11y.browserLag;
    ed11yInit.waiting = false;
    if (ed11yInit.once && !Ed11y.running && Ed11y.panel && Ed11y.roots) {
      window.setTimeout(() => {
        // Quick align.
        Ed11y.incrementalAlign();
        Ed11y.alignPending = false;
      }, 0 + Ed11y.browserLag);
      window.setTimeout((forceFull) => {
        // Then recheck.
        if (!Ed11y.running) {
          if (forceFull) {
            Ed11y.forceFullCheck = true;
          }
          Ed11y.incrementalCheck();
        }
      }, 250 + Ed11y.browserLag, forceFull);
      window.setTimeout((forceFull) => {
        if (!Ed11y.running && !ed11yInit.waiting) {
          // Recheck unless another cycle has begun.
          if (forceFull) {
            Ed11y.forceFullCheck = true;
          }
          Ed11y.incrementalCheck();
        }
      }, 1250 + Ed11y.browserLag, forceFull);
    } else {
      if (!Ed11y.running) {
        Ed11y.checkAll(); // this case should never be reached.
      }
    }
  }
};

ed11yInit.ed11yShutMenu = () => {
  if (Ed11y.openTip.button) {
    if (ed11yInit.editorType === 'inIframe') {
      ed11yInit.innerWorker.port.postMessage([true, false]);
    } else {
      // eslint-disable-next-line no-undef
      wp.data.dispatch('core/block-editor').clearSelectedBlock();
    }
  }
};
document.addEventListener('ed11yPop', function() {
  window.setTimeout(() => {
    ed11yInit.ed11yShutMenu();
  }, 1000);
});
document.addEventListener('ed11yPop', (e) => {
  const alreadyDecorated = e.detail.tip.dataset.alreadyDecorated;
  if (e.detail.result.element.matches('img') && !alreadyDecorated) {
    const transferFocus = e.detail.tip.shadowRoot.querySelector('.ed11y-transfer-focus');
    transferFocus?.parentNode.style.setProperty('display', 'none');
  }
  e.detail.tip.dataset.alreadyDecorated = 'true';
});

ed11yInit.interaction = false;

ed11yInit.createObserver = function () {
  // Ed11y misses many Gutenberg changes without help.

  // Recheck inner when something was clicked outside the iframe.
  ed11yInit.innerWorker.port.onmessage = (message) => {
    // Something was clicked outside the iframe.
    if (message.data[1]) {
      ed11yInit.recheck(false);
    }
  };
  ed11yInit.innerWorker.port.start();
  if (ed11yInit.editorType === 'inIframe') {
    return;
  }

  // Listen for events outside checkRoot that may modify content without triggering a mutation.
  window.addEventListener('keyup', (e) => {
    if (!e.target.closest('.ed11y-wrapper, [contenteditable="true"]')) {
      // Arrow changes of radio and select controls.
      ed11yInit.interaction = true;
    }
  });
  window.addEventListener('click', (e) => {
    // Click covers mouse, keyboard and touch.
    if (!e.target.closest('.ed11y-wrapper')) {
      ed11yInit.interaction = true;
    }
  });

  // Observe for DOM mutations.

  const ed11yTargetNode = document.querySelector(ed11yInit.scrollRoot);
  const ed11yObserverConfig = { attributeFilter: ['class'], characterData: true, subtree: true };
  const ed11yMutationCallback = (callback) => {
    // Ignore mutations that do not result from user interactions.
    if (callback[0].type !== 'characterData' && ed11yInit.interaction && !Ed11y.running) {
      ed11yInit.recheck(false);
      // Could get blockID via Web worker to check less often.
      // let newBlockId = wp.data.select( 'core/block-editor' ).getSelectedBlockClientId();
    }
  };
  const ed11yObserver = new MutationObserver(ed11yMutationCallback);
  ed11yObserver.observe(ed11yTargetNode, ed11yObserverConfig);
};

ed11yInit.ed11yOuterInit = function() {

  // Tell iframe if block editor might be up to something.
  // eslint-disable-next-line no-undef
  ed11yInit.outerWorker = window.SharedWorker ? new SharedWorker(ed11yVars.worker) : false;
  window.addEventListener('keyup', () => {
    // Arrow changes of radio and select controls.
    ed11yInit.outerWorker.port.postMessage([false, true]);
  });
  window.addEventListener('click', () => {
    ed11yInit.outerWorker.port.postMessage([false, true]);
  });

  // Clear active block selection when a tip opens to hide floating menup.
  ed11yInit.outerWorker.port.onmessage = (message) => {
    if (message.data[0]) {
      // eslint-disable-next-line no-undef
      wp.data.dispatch('core/block-editor').clearSelectedBlock();
    }
  };

  ed11yInit.outerWorker.port.onmessageerror = (data) => {
    console.warn(data);
  };
  ed11yInit.outerWorker.port.onerror = (data) => {
    console.warn(data);
  };
  ed11yInit.outerWorker.port.start();
};

/*
// Classic editor watching failed.
// The Tiny MCE iframe has editable elements touching <body>.
// In theory I could bring back the outlines.
// Leaving code in case I get any bright ideas down the road.
const ed11yClassicInsertScripts = function() {
  //"https://editoria11y-wp.ddev.site/wp-content/plugins/editoria11y-wp/assets/lib/editoria11y.min.css"
  const library = document.createElement('script');
  library.src = ed11yInit.options.cssLocation.replace('editoria11y.min.css', 'editoria11y.min.js');
  const editorInit = document.createElement('script');
  const varPile = document.createElement('script');
  varPile.setAttribute('id', 'ed11yVarPile');
  varPile.innerHTML = 'var ed11yInit = ' + JSON.stringify({
    options: ed11yInit.options,
    worker: workerURL,
  });
  editorInit.src = ed11yInit.options.cssLocation.replace('lib/editoria11y.min.css', 'js/editoria11y-editor.js');
  const workerScript = document.createElement('script');
  workerScript.src = ed11yInit.options.cssLocation.replace('lib/editoria11y.min.css', 'js/editoria11y-editor-worker.js');
  const style = document.createElement('link');
  style.rel = 'stylesheet';
  style.href = ed11yInit.options.cssLocation;
  const iframe = document.getElementById('content_ifr');
   // Check if the iframe has loaded
  if (iframe.contentDocument) {
      // Access the iframe's document object
    const iframeDoc = iframe.contentDocument;
    // Insert content into the iframe
    iframeDoc.head.append(library);
    iframeDoc.head.append(varPile);
    iframeDoc.head.append(style);
    iframeDoc.head.append(workerScript);
    iframeDoc.head.append(editorInit);
  } else {
    // Wait for the iframe to load
    iframe.addEventListener('load', () => {
      console.log('later');
      const iframeDoc = iframe.contentDocument;
      iframeDoc.head.append(library);
      iframeDoc.head.append(varPile);
      iframeDoc.head.append(style);
      iframeDoc.head.append(workerScript);
      iframeDoc.head.append(editorInit);
    });
  }
}*/

// Initiate Editoria11y create alert link, initiate content change watcher.
ed11yInit.ed11yPageInit = function () {
  // eslint-disable-next-line no-undef
  ed11yInit.innerWorker = window.SharedWorker ? new SharedWorker(ed11yVars.worker) : false;
  window.setTimeout(() => {
    ed11yInit.getOptions();
    ed11yInit.firstCheck();
    ed11yInit.syncDismissals();
  },1000);
  window.setTimeout(() => {
    ed11yInit.createObserver();
    ed11yInit.recheck(true);
  }, 2500);
};

// Look to see if Gutenberg has loaded.
// Possible todo: add checks/markup for other common editors.
ed11yInit.findCompatibleEditor = function () {
  if (ed11yInit.editorType) {
    // Do nothing.
  } else if (document.querySelector(ed11yInit.noRun)) {
    ed11yInit.editorType = 'forbidden';
  } else if (document.querySelector('body' + ed11yInit.editRoot)) {
    // inside iFrame
    ed11yInit.editorType = 'inIframe';
    ed11yInit.editRoot = '.editor-visual-editor__post-title-wrapper:not(:has([data-rich-text-placeholder])), .editor-styles-wrapper > .is-root-container:not(.wp-site-blocks)'; // include title
    ed11yInit.scrollRoot = 'body';
    ed11yInit.ed11yPageInit();
  } else if (document.querySelector('[class*="-visual-editor"] iframe')) {
    ed11yInit.editorType = 'outsideIframe';
    ed11yInit.ed11yOuterInit();
  } else if ( document.querySelector('#editor .editor-styles-wrapper')) {
    ed11yInit.editorType = 'onPage';
    // Todo: Is this still being called?
    ed11yInit.editRoot = '.editor-visual-editor__post-title-wrapper:not(:has([data-rich-text-placeholder])), #editor .is-root-container'; // include title
    ed11yInit.scrollRoot = '.interface-interface-skeleton__content';
    ed11yInit.ed11yPageInit();
  } else if (document.getElementById('content_ifr')) {
    ed11yInit.editorType = 'mce';
  } else if (ed11yInit.ed11yReadyCount < 60) {
    window.setTimeout(function () {
      ed11yInit.ed11yReadyCount++;
      ed11yInit.findCompatibleEditor();
    }, 1000);
  } else {
    console.log('Editoria11y called on page, but no block editor found');
  }
};

// Scan page for compatible editors once page has loaded.
window.addEventListener('load', () => {
  window.setTimeout(() => {
    if (!ed11yInit.editorType) {
      ed11yInit.findCompatibleEditor();
    }
    });
  }, 0);
// Belt & suspenders if load never fires.
window.setTimeout(() => {
  if (!ed11yInit.editorType) {
    ed11yInit.findCompatibleEditor();
  }
}, 2500);
