/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.events=tribe.events||{},tribe.events.views=tribe.events.views||{},tribe.events.views.navigationScroll={},function($,obj){"use strict";var $document=$(document),$window=$(window);obj.scrollUp=function(event,html,textStatus,qXHR){var $container=$(event.target),windowTop=$window.scrollTop(),containerOffset=$container.offset();.75*windowTop>containerOffset.top&&$window.scrollTop(containerOffset.top)},obj.ready=function(){$document.on("afterAjaxSuccess.tribeEvents",tribe.events.views.manager.selectors.container,obj.scrollUp)},$(obj.ready)}(jQuery,tribe.events.views.navigationScroll);