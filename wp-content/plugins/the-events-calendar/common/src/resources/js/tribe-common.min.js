/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

String.prototype.className=function(){return"string"!=typeof this&&!this instanceof String||"function"!=typeof this.replace?this:this.replace(".","")},String.prototype.varName=function(){return"string"!=typeof this&&!this instanceof String||"function"!=typeof this.replace?this:this.replace("-","_")},function(){const hash=new URL(window.location.href).hash;if(!hash||!hash.match("#(tribe|tec)"))return;let updatesDidOccurr=!0;const mutationObserver=new MutationObserver((function(){updatesDidOccurr=!0}));mutationObserver.observe(window.document,{attributes:!0,childList:!0,characterData:!0,subtree:!0});let mutationCallback=function(){if(updatesDidOccurr)updatesDidOccurr=!1,setTimeout(mutationCallback,250);else{mutationObserver.takeRecords(),mutationObserver.disconnect();const scrollTo=document.getElementById(hash.substring(1));scrollTo&&scrollTo.scrollIntoView()}};mutationCallback()}();var tribe=tribe||{};window.tec=window.tec||{};