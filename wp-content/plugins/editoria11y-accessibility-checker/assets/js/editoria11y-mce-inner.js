
let readyCount = 0;
const letsGo = function() {
  if (typeof parent.startMCEEd11y === 'function') {

    // Both parent and iframe are ready; init Ed11y.
    parent.startMCEEd11y(document.body);

    /*
    * Local copies of Editoria11y functions that don't work across frames.
    * */
    document.addEventListener('keydown', () => {
      parent.Ed11y.interaction = true;
    });
    document.addEventListener('click', () => {
      parent.Ed11y.interaction = true;
    });
    document.addEventListener('scroll', function() {
      parent.Ed11y.scrollPending = parent.Ed11y.scrollPending < 2 ? parent.Ed11y.scrollPending + 1 : parent.Ed11y.scrollPending;
      requestAnimationFrame(() => parent.Ed11y.updateTipLocations());
      // Trigger on scrolling other containers, unless it will flicker a tip.
      if (parent.Ed11y.openTip.button) {
        parent.Ed11y.alignTip(parent.Ed11y.openTip.button.shadowRoot.querySelector('button'), parent.Ed11y.openTip.tip);
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
      if (!parent.Ed11y.running && parent.Ed11y.rangeChange(window.getSelection()?.anchorNode)) {
        parent.Ed11y.updateTipLocations();
        parent.Ed11y.checkEditableIntersects(true);
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
