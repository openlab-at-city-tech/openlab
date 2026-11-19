
let readyCount = 0;
const letsGo = function() {
  if (typeof parent.startMCEEd11y === 'function') {

    // Both parent and iframe are ready; init Ed11y.
    parent.startMCEEd11y(document.body);

    /*
    * Local copies of Editoria11y functions that don't work across frames.
    * In 3.x, the parent module exposes UI/State/refresh on parent.Ed11y for this iframe to use.
    * @todo verify: scroll/selection helpers (updateTipLocations, alignTip, rangeChange,
    * checkEditableIntersects) are no longer exported. Library 3.x watchForChanges may
    * cover most of this internally; calling parent.Ed11y.refresh() is the closest public analogue.
    * */
    document.addEventListener('keydown', () => {
      parent.Ed11y.UI.interaction = true;
    });
    document.addEventListener('click', () => {
      parent.Ed11y.UI.interaction = true;
    });
    // Debounced: scroll fires continuously and refresh() re-walks tip
    // positions — undebounced this ran a full recompute per scroll frame.
    let ed11yScrollTimer = null;
    document.addEventListener('scroll', function() {
      // Trigger a refresh so the public API recomputes positions.
      // Trigger on scrolling other containers, unless it will flicker a tip.
      if (parent.Ed11y.UI?.openTip?.button) {
        window.clearTimeout(ed11yScrollTimer);
        ed11yScrollTimer = window.setTimeout(() => parent.Ed11y.refresh(), 150);
      }
    }, true);
    const debounce = (callback, wait) => {
      let timeoutId = null;
      return (...args) => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(() => {
          callback.apply(null, args);
        }, wait);
      };
    };
    const selectionChanged = debounce(() => {
      // @todo verify: rangeChange / checkEditableIntersects not exported in 3.x.
      if (!parent.Ed11y.UI.running) {
        parent.Ed11y.refresh();
      }
    }, 100);
    document.addEventListener('selectionchange', function() {
      selectionChanged();
    });

  } else if (readyCount < 60) {
    readyCount++;
    window.setTimeout(letsGo, 1000);
  }
};
window.setTimeout(() => {
  letsGo();
},100);
