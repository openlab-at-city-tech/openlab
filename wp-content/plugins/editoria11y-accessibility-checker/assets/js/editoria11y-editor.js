
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
  ed11yInit.options['ignoreByKey'] = {
    table: '.is-selected.wp-block-table table, [role="presentation"]',
  };
  ed11yInit.options['headingsOnlyFromCheckRoots'] = true;
  ed11yInit.options['ignoreAriaOnElements'] = 'h1,h2,h3,h4,h5,h6,.wp-element-button,.block-editor-rich-text__editable,.wp-block-table';
  ed11yInit.options['altPlaceholder'] = 'This image has an empty alt attribute;';

  // WordPress does not render empty post titles, so we don't need to flag them.

  ed11yInit.options['showResults'] = true;
  ed11yInit.options['buttonZIndex'] = 99999;
  ed11yInit.options['customTests'] = 1;
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
document.addEventListener('ed11yRunCustomTests', function() {

  Ed11y.findElements('wpButtonBlock','.wp-element-button:not(.is-selected .wp-element-button)');

  Ed11y.elements.wpButtonBlock?.forEach((el) => {
    // Straight copy of link test checks as of library 2.2.13
    // Todo: not needed if the library exposes a parameter for link selector.
    let linkText = Ed11y.computeText(el, 0, !!Ed11y.options.linkIgnoreSelector);
    let img = el.querySelectorAll('img');
    let hasImg = img.length > 0;
    let document = el.matches(Ed11y.options.documentLinks);

    if (el?.getAttribute('target') === '_blank') {
      // Nothing was stripped AND we weren't warned.
      if (
        !(
          (Ed11y.options.linkIgnoreSelector &&
            el?.querySelector(Ed11y.options.linkIgnoreSelector))
          || linkText.toLowerCase().match(Ed11y.options.linkStringsNewWindows)
        )
      ) {
        let dismissKey = Ed11y.dismissalKey(linkText);
        Ed11y.results.push({
          element: el,
          test: 'linkNewWindow',
          content: Ed11y.M.linkNewWindow.tip(),
          position: 'beforebegin',
          dismissalKey: dismissKey,
        });
      }
    }

    // Todo: add test for title === textContent. Don't use computedText().

    // Tests to see if this link is empty
    if (
      linkText.replace(/"|'|\?|\.|-|\s+/g, '').length === 0 &&
      !( Ed11y.options.linkIgnoreSelector &&
        el.querySelector(Ed11y.options.linkIgnoreSelector)
      )
    ) {
      // Link with no text at all.
      if (hasImg === false) {
        Ed11y.results.push({
          element: el,
          test: 'linkNoText',
          content: Ed11y.M.linkNoText.tip(),
          position: 'beforebegin',
          dismissalKey: false,
        });
      } else {
        Ed11y.results.push({
          element: el,
          test: 'altEmptyLinked',
          content: Ed11y.M.altEmptyLinked.tip(),
          position: 'beforebegin',
          dismissalKey: false,
        });
      }
    }
    else {
      let linkTextCheck = function (textContent) {
        // Checks if link text is not descriptive.
        let linkStrippedText = textContent.toLowerCase();
        // Create version of text without "open in new window" warnings.

        if (Ed11y.options.linkStringsNewWindows &&
          Ed11y.options.linkStringsNewWindows !== Ed11y.M.linkStringsNewWindows) {
          // don't strip on the default, which is loose.
          linkStrippedText = linkStrippedText.replace(Ed11y.options.linkIgnoreStrings, '');
        }
        if (Ed11y.options.linkIgnoreStrings) {
          linkStrippedText = Ed11y.options.linkIgnoreStrings ?
            linkStrippedText.replace(Ed11y.options.linkIgnoreStrings, '')
            : linkStrippedText;
        }
        if (linkStrippedText.replace(/"|'|\?|\.|-|\s+/g, '').length === 0) {
          // No Text because of stripping out ignoreStrings.
          return 'generic';
        }

        // todo later: use regex to find any three-letter TLD followed by a slash.
        // todo later: parameterize TLD list
        let linksUrls = Ed11y.options.linksUrls ? Ed11y.options.linksUrls : Ed11y.M.linksUrls;
        let linksMeaningless = Ed11y.options.linksMeaningless ? Ed11y.options.linksMeaningless : Ed11y.M.linksMeaningless;
        let hit = 'none';

        if (linkStrippedText.replace(linksMeaningless, '').length === 0) {
          // If no partial words were found, then check for total words.
          hit = 'generic';
        }
        else {
          for (let i = 0; i < linksUrls.length; i++) {
            if (textContent.indexOf(linksUrls[i]) > -1) {
              hit = 'url';
              break;
            }
          }
        }
        return hit;
      };
      let textCheck = linkTextCheck(linkText);
      if (textCheck !== 'none') {
        let error = false;
        if (!hasImg && textCheck === 'url') {
          // Images test will pick this up.
          error = 'linkTextIsURL';
        }
        if (textCheck === 'generic') {
          error = 'linkTextIsGeneric';
          if (linkText.length < 4) {
            // Reinsert ignored link strings.
            linkText = Ed11y.computeText(el, 0);
          }
        }
        if (error) {
          Ed11y.results.push({
            element: el,
            test: error,
            content: Ed11y.M[error].tip(Ed11y.sanitizeForHTML(linkText)),
            position: 'beforebegin',
            dismissalKey: Ed11y.dismissalKey(linkText),
          });
        }
      }
    }
    // Warning: Find all PDFs.
    if ( document ) {
      let dismissKey = Ed11y.dismissalKey(el?.getAttribute('href'));
      Ed11y.results.push(
        {
          element: el,
          test: 'linkDocument',
          content: Ed11y.M.linkDocument.tip(),
          position: 'beforebegin',
          dismissalKey: dismissKey,
        });
    }
  });

  let allDone = new CustomEvent('ed11yResume');
  document.dispatchEvent(allDone);
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
