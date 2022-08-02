/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
// Open on click functionality.
function closeSubmenus(element) {
  element.querySelectorAll('[aria-expanded="true"]').forEach(function (toggle) {
    toggle.setAttribute('aria-expanded', 'false'); // Always focus the trigger, this becomes especially useful in closing submenus with escape key to ensure focus doesn't get trapped.

    toggle.focus();
  });
}

function toggleSubmenuOnClick(event) {
  const buttonToggle = event.target.closest('[aria-expanded]');
  const isSubmenuOpen = buttonToggle.getAttribute('aria-expanded');

  if (isSubmenuOpen === 'true') {
    closeSubmenus(buttonToggle.closest('.wp-block-navigation-item'));
  } else {
    // Close all sibling submenus.
    const parentElement = buttonToggle.closest('.wp-block-navigation-item');
    const navigationParent = buttonToggle.closest('.wp-block-navigation__submenu-container, .wp-block-navigation__container, .wp-block-page-list');
    navigationParent.querySelectorAll('.wp-block-navigation-item').forEach(function (child) {
      if (child !== parentElement) {
        closeSubmenus(child);
      }
    }); // Open submenu.

    buttonToggle.setAttribute('aria-expanded', 'true');
  }
} // Necessary for some themes such as TT1 Blocks, where
// scripts could be loaded before the body.


window.addEventListener('load', () => {
  const submenuButtons = document.querySelectorAll('.wp-block-navigation-submenu__toggle');
  submenuButtons.forEach(function (button) {
    button.addEventListener('click', toggleSubmenuOnClick);
  }); // Close on click outside.

  document.addEventListener('click', function (event) {
    const navigationBlocks = document.querySelectorAll('.wp-block-navigation');
    navigationBlocks.forEach(function (block) {
      if (!block.contains(event.target)) {
        closeSubmenus(block);
      }
    });
  }); // Close on focus outside or escape key.

  document.addEventListener('keyup', function (event) {
    const submenuBlocks = document.querySelectorAll('.wp-block-navigation-item.has-child');
    submenuBlocks.forEach(function (block) {
      if (!block.contains(event.target) || block.contains(event.target) && event.key === 'Escape') {
        closeSubmenus(block);
      }
    });
  });
});

/******/ })()
;