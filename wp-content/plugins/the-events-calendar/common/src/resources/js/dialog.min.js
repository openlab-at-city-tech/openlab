/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

var tribe=tribe||{};tribe.dialogs=tribe.dialogs||{},function($,obj){"use strict";obj.dialogs=obj.dialogs||[],obj.events=obj.events||{},obj.getDialogName=function(dialog){return"dialog_obj_"+dialog.id},obj.init=function(){obj.dialogs.forEach((function(dialog){var objName=obj.getDialogName(dialog),a11yInstance=new window.A11yDialog({appendTarget:dialog.appendTarget,bodyLock:dialog.bodyLock,closeButtonAriaLabel:dialog.closeButtonAriaLabel,closeButtonClasses:dialog.closeButtonClasses,contentClasses:dialog.contentClasses,effect:dialog.effect,effectEasing:dialog.effectEasing,effectSpeed:dialog.effectSpeed,overlayClasses:dialog.overlayClasses,overlayClickCloses:dialog.overlayClickCloses,trigger:dialog.trigger,wrapperClasses:dialog.wrapperClasses});window[objName]=a11yInstance,dialog.a11yInstance=a11yInstance,window[objName].on("show",(function(dialogEl,event){event&&(event.preventDefault(),event.stopPropagation()),$(obj.events).trigger(dialog.showEvent,[dialogEl,event])})),window[objName].on("hide",(function(dialogEl,event){event&&(event.preventDefault(),event.stopPropagation()),$(obj.events).trigger(dialog.closeEvent,[dialogEl,event])}))}))},$(obj.init)}(jQuery,tribe.dialogs);