/******/ // The require scope
/******/ var __webpack_require__ = {};
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ !function() {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = function(exports, definition) {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ }();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ !function() {
/******/ 	__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ }();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  az: function() { return /* reexport */ y; },
  Aj: function() { return /* reexport */ deepsignal_module_l; },
  XM: function() { return /* reexport */ directive; },
  fw: function() { return /* reexport */ getContext; },
  sb: function() { return /* reexport */ getElement; },
  c4: function() { return /* reexport */ router_navigate; },
  tL: function() { return /* reexport */ prefetch; },
  h: function() { return /* reexport */ store; },
  qp: function() { return /* reexport */ hooks_module_q; },
  d4: function() { return /* reexport */ hooks_module_p; },
  Ye: function() { return /* reexport */ hooks_module_F; }
});

;// CONCATENATED MODULE: ./node_modules/preact/dist/preact.module.js
var preact_module_n,preact_module_l,u,preact_module_i,preact_module_t,r,o,preact_module_f,preact_module_e,c={},s=[],preact_module_a=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i;function h(n,l){for(var u in l)n[u]=l[u];return n}function v(n){var l=n.parentNode;l&&l.removeChild(n)}function y(l,u,i){var t,r,o,f={};for(o in u)"key"==o?t=u[o]:"ref"==o?r=u[o]:f[o]=u[o];if(arguments.length>2&&(f.children=arguments.length>3?preact_module_n.call(arguments,2):i),"function"==typeof l&&null!=l.defaultProps)for(o in l.defaultProps)void 0===f[o]&&(f[o]=l.defaultProps[o]);return p(l,f,t,r,null)}function p(n,i,t,r,o){var f={type:n,props:i,key:t,ref:r,__k:null,__:null,__b:0,__e:null,__d:void 0,__c:null,__h:null,constructor:void 0,__v:null==o?++u:o};return null==o&&null!=preact_module_l.vnode&&preact_module_l.vnode(f),f}function d(){return{current:null}}function _(n){return n.children}function k(n,l){this.props=n,this.context=l}function b(n,l){if(null==l)return n.__?b(n.__,n.__.__k.indexOf(n)+1):null;for(var u;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e)return u.__e;return"function"==typeof n.type?b(n):null}function g(n){var l,u;if(null!=(n=n.__)&&null!=n.__c){for(n.__e=n.__c.base=null,l=0;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e){n.__e=n.__c.base=u.__e;break}return g(n)}}function m(n){(!n.__d&&(n.__d=!0)&&preact_module_t.push(n)&&!w.__r++||r!==preact_module_l.debounceRendering)&&((r=preact_module_l.debounceRendering)||o)(w)}function w(){var n,l,u,i,r,o,e,c;for(preact_module_t.sort(preact_module_f);n=preact_module_t.shift();)n.__d&&(l=preact_module_t.length,i=void 0,r=void 0,e=(o=(u=n).__v).__e,(c=u.__P)&&(i=[],(r=h({},o)).__v=o.__v+1,L(c,o,r,u.__n,void 0!==c.ownerSVGElement,null!=o.__h?[e]:null,i,null==e?b(o):e,o.__h),M(i,o),o.__e!=e&&g(o)),preact_module_t.length>l&&preact_module_t.sort(preact_module_f));w.__r=0}function x(n,l,u,i,t,r,o,f,e,a){var h,v,y,d,k,g,m,w=i&&i.__k||s,x=w.length;for(u.__k=[],h=0;h<l.length;h++)if(null!=(d=u.__k[h]=null==(d=l[h])||"boolean"==typeof d||"function"==typeof d?null:"string"==typeof d||"number"==typeof d||"bigint"==typeof d?p(null,d,null,null,d):Array.isArray(d)?p(_,{children:d},null,null,null):d.__b>0?p(d.type,d.props,d.key,d.ref?d.ref:null,d.__v):d)){if(d.__=u,d.__b=u.__b+1,null===(y=w[h])||y&&d.key==y.key&&d.type===y.type)w[h]=void 0;else for(v=0;v<x;v++){if((y=w[v])&&d.key==y.key&&d.type===y.type){w[v]=void 0;break}y=null}L(n,d,y=y||c,t,r,o,f,e,a),k=d.__e,(v=d.ref)&&y.ref!=v&&(m||(m=[]),y.ref&&m.push(y.ref,null,d),m.push(v,d.__c||k,d)),null!=k?(null==g&&(g=k),"function"==typeof d.type&&d.__k===y.__k?d.__d=e=A(d,e,n):e=C(n,d,y,w,k,e),"function"==typeof u.type&&(u.__d=e)):e&&y.__e==e&&e.parentNode!=n&&(e=b(y))}for(u.__e=g,h=x;h--;)null!=w[h]&&("function"==typeof u.type&&null!=w[h].__e&&w[h].__e==u.__d&&(u.__d=$(i).nextSibling),S(w[h],w[h]));if(m)for(h=0;h<m.length;h++)O(m[h],m[++h],m[++h])}function A(n,l,u){for(var i,t=n.__k,r=0;t&&r<t.length;r++)(i=t[r])&&(i.__=n,l="function"==typeof i.type?A(i,l,u):C(u,i,i,t,i.__e,l));return l}function P(n,l){return l=l||[],null==n||"boolean"==typeof n||(Array.isArray(n)?n.some(function(n){P(n,l)}):l.push(n)),l}function C(n,l,u,i,t,r){var o,f,e;if(void 0!==l.__d)o=l.__d,l.__d=void 0;else if(null==u||t!=r||null==t.parentNode)n:if(null==r||r.parentNode!==n)n.appendChild(t),o=null;else{for(f=r,e=0;(f=f.nextSibling)&&e<i.length;e+=1)if(f==t)break n;n.insertBefore(t,r),o=r}return void 0!==o?o:t.nextSibling}function $(n){var l,u,i;if(null==n.type||"string"==typeof n.type)return n.__e;if(n.__k)for(l=n.__k.length-1;l>=0;l--)if((u=n.__k[l])&&(i=$(u)))return i;return null}function H(n,l,u,i,t){var r;for(r in u)"children"===r||"key"===r||r in l||T(n,r,null,u[r],i);for(r in l)t&&"function"!=typeof l[r]||"children"===r||"key"===r||"value"===r||"checked"===r||u[r]===l[r]||T(n,r,l[r],u[r],i)}function I(n,l,u){"-"===l[0]?n.setProperty(l,null==u?"":u):n[l]=null==u?"":"number"!=typeof u||preact_module_a.test(l)?u:u+"px"}function T(n,l,u,i,t){var r;n:if("style"===l)if("string"==typeof u)n.style.cssText=u;else{if("string"==typeof i&&(n.style.cssText=i=""),i)for(l in i)u&&l in u||I(n.style,l,"");if(u)for(l in u)i&&u[l]===i[l]||I(n.style,l,u[l])}else if("o"===l[0]&&"n"===l[1])r=l!==(l=l.replace(/Capture$/,"")),l=l.toLowerCase()in n?l.toLowerCase().slice(2):l.slice(2),n.l||(n.l={}),n.l[l+r]=u,u?i||n.addEventListener(l,r?z:j,r):n.removeEventListener(l,r?z:j,r);else if("dangerouslySetInnerHTML"!==l){if(t)l=l.replace(/xlink(H|:h)/,"h").replace(/sName$/,"s");else if("width"!==l&&"height"!==l&&"href"!==l&&"list"!==l&&"form"!==l&&"tabIndex"!==l&&"download"!==l&&l in n)try{n[l]=null==u?"":u;break n}catch(n){}"function"==typeof u||(null==u||!1===u&&"-"!==l[4]?n.removeAttribute(l):n.setAttribute(l,u))}}function j(n){return this.l[n.type+!1](preact_module_l.event?preact_module_l.event(n):n)}function z(n){return this.l[n.type+!0](preact_module_l.event?preact_module_l.event(n):n)}function L(n,u,i,t,r,o,f,e,c){var s,a,v,y,p,d,b,g,m,w,A,P,C,$,H,I=u.type;if(void 0!==u.constructor)return null;null!=i.__h&&(c=i.__h,e=u.__e=i.__e,u.__h=null,o=[e]),(s=preact_module_l.__b)&&s(u);try{n:if("function"==typeof I){if(g=u.props,m=(s=I.contextType)&&t[s.__c],w=s?m?m.props.value:s.__:t,i.__c?b=(a=u.__c=i.__c).__=a.__E:("prototype"in I&&I.prototype.render?u.__c=a=new I(g,w):(u.__c=a=new k(g,w),a.constructor=I,a.render=q),m&&m.sub(a),a.props=g,a.state||(a.state={}),a.context=w,a.__n=t,v=a.__d=!0,a.__h=[],a._sb=[]),null==a.__s&&(a.__s=a.state),null!=I.getDerivedStateFromProps&&(a.__s==a.state&&(a.__s=h({},a.__s)),h(a.__s,I.getDerivedStateFromProps(g,a.__s))),y=a.props,p=a.state,a.__v=u,v)null==I.getDerivedStateFromProps&&null!=a.componentWillMount&&a.componentWillMount(),null!=a.componentDidMount&&a.__h.push(a.componentDidMount);else{if(null==I.getDerivedStateFromProps&&g!==y&&null!=a.componentWillReceiveProps&&a.componentWillReceiveProps(g,w),!a.__e&&null!=a.shouldComponentUpdate&&!1===a.shouldComponentUpdate(g,a.__s,w)||u.__v===i.__v){for(u.__v!==i.__v&&(a.props=g,a.state=a.__s,a.__d=!1),a.__e=!1,u.__e=i.__e,u.__k=i.__k,u.__k.forEach(function(n){n&&(n.__=u)}),A=0;A<a._sb.length;A++)a.__h.push(a._sb[A]);a._sb=[],a.__h.length&&f.push(a);break n}null!=a.componentWillUpdate&&a.componentWillUpdate(g,a.__s,w),null!=a.componentDidUpdate&&a.__h.push(function(){a.componentDidUpdate(y,p,d)})}if(a.context=w,a.props=g,a.__P=n,P=preact_module_l.__r,C=0,"prototype"in I&&I.prototype.render){for(a.state=a.__s,a.__d=!1,P&&P(u),s=a.render(a.props,a.state,a.context),$=0;$<a._sb.length;$++)a.__h.push(a._sb[$]);a._sb=[]}else do{a.__d=!1,P&&P(u),s=a.render(a.props,a.state,a.context),a.state=a.__s}while(a.__d&&++C<25);a.state=a.__s,null!=a.getChildContext&&(t=h(h({},t),a.getChildContext())),v||null==a.getSnapshotBeforeUpdate||(d=a.getSnapshotBeforeUpdate(y,p)),H=null!=s&&s.type===_&&null==s.key?s.props.children:s,x(n,Array.isArray(H)?H:[H],u,i,t,r,o,f,e,c),a.base=u.__e,u.__h=null,a.__h.length&&f.push(a),b&&(a.__E=a.__=null),a.__e=!1}else null==o&&u.__v===i.__v?(u.__k=i.__k,u.__e=i.__e):u.__e=N(i.__e,u,i,t,r,o,f,c);(s=preact_module_l.diffed)&&s(u)}catch(n){u.__v=null,(c||null!=o)&&(u.__e=e,u.__h=!!c,o[o.indexOf(e)]=null),preact_module_l.__e(n,u,i)}}function M(n,u){preact_module_l.__c&&preact_module_l.__c(u,n),n.some(function(u){try{n=u.__h,u.__h=[],n.some(function(n){n.call(u)})}catch(n){preact_module_l.__e(n,u.__v)}})}function N(l,u,i,t,r,o,f,e){var s,a,h,y=i.props,p=u.props,d=u.type,_=0;if("svg"===d&&(r=!0),null!=o)for(;_<o.length;_++)if((s=o[_])&&"setAttribute"in s==!!d&&(d?s.localName===d:3===s.nodeType)){l=s,o[_]=null;break}if(null==l){if(null===d)return document.createTextNode(p);l=r?document.createElementNS("http://www.w3.org/2000/svg",d):document.createElement(d,p.is&&p),o=null,e=!1}if(null===d)y===p||e&&l.data===p||(l.data=p);else{if(o=o&&preact_module_n.call(l.childNodes),a=(y=i.props||c).dangerouslySetInnerHTML,h=p.dangerouslySetInnerHTML,!e){if(null!=o)for(y={},_=0;_<l.attributes.length;_++)y[l.attributes[_].name]=l.attributes[_].value;(h||a)&&(h&&(a&&h.__html==a.__html||h.__html===l.innerHTML)||(l.innerHTML=h&&h.__html||""))}if(H(l,p,y,r,e),h)u.__k=[];else if(_=u.props.children,x(l,Array.isArray(_)?_:[_],u,i,t,r&&"foreignObject"!==d,o,f,o?o[0]:i.__k&&b(i,0),e),null!=o)for(_=o.length;_--;)null!=o[_]&&v(o[_]);e||("value"in p&&void 0!==(_=p.value)&&(_!==l.value||"progress"===d&&!_||"option"===d&&_!==y.value)&&T(l,"value",_,y.value,!1),"checked"in p&&void 0!==(_=p.checked)&&_!==l.checked&&T(l,"checked",_,y.checked,!1))}return l}function O(n,u,i){try{"function"==typeof n?n(u):n.current=u}catch(n){preact_module_l.__e(n,i)}}function S(n,u,i){var t,r;if(preact_module_l.unmount&&preact_module_l.unmount(n),(t=n.ref)&&(t.current&&t.current!==n.__e||O(t,null,u)),null!=(t=n.__c)){if(t.componentWillUnmount)try{t.componentWillUnmount()}catch(n){preact_module_l.__e(n,u)}t.base=t.__P=null,n.__c=void 0}if(t=n.__k)for(r=0;r<t.length;r++)t[r]&&S(t[r],u,i||"function"!=typeof n.type);i||null==n.__e||v(n.__e),n.__=n.__e=n.__d=void 0}function q(n,l,u){return this.constructor(n,u)}function B(u,i,t){var r,o,f;preact_module_l.__&&preact_module_l.__(u,i),o=(r="function"==typeof t)?null:t&&t.__k||i.__k,f=[],L(i,u=(!r&&t||i).__k=y(_,null,[u]),o||c,c,void 0!==i.ownerSVGElement,!r&&t?[t]:o?null:i.firstChild?preact_module_n.call(i.childNodes):null,f,!r&&t?t:o?o.__e:i.firstChild,r),M(f,u)}function D(n,l){B(n,l,D)}function E(l,u,i){var t,r,o,f=h({},l.props);for(o in u)"key"==o?t=u[o]:"ref"==o?r=u[o]:f[o]=u[o];return arguments.length>2&&(f.children=arguments.length>3?preact_module_n.call(arguments,2):i),p(l.type,f,t||l.key,r||l.ref,null)}function F(n,l){var u={__c:l="__cC"+preact_module_e++,__:n,Consumer:function(n,l){return n.children(l)},Provider:function(n){var u,i;return this.getChildContext||(u=[],(i={})[l]=this,this.getChildContext=function(){return i},this.shouldComponentUpdate=function(n){this.props.value!==n.value&&u.some(function(n){n.__e=!0,m(n)})},this.sub=function(n){u.push(n);var l=n.componentWillUnmount;n.componentWillUnmount=function(){u.splice(u.indexOf(n),1),l&&l.call(n)}}),n.children}};return u.Provider.__=u.Consumer.contextType=u}preact_module_n=s.slice,preact_module_l={__e:function(n,l,u,i){for(var t,r,o;l=l.__;)if((t=l.__c)&&!t.__)try{if((r=t.constructor)&&null!=r.getDerivedStateFromError&&(t.setState(r.getDerivedStateFromError(n)),o=t.__d),null!=t.componentDidCatch&&(t.componentDidCatch(n,i||{}),o=t.__d),o)return t.__E=t}catch(l){n=l}throw n}},u=0,preact_module_i=function(n){return null!=n&&void 0===n.constructor},k.prototype.setState=function(n,l){var u;u=null!=this.__s&&this.__s!==this.state?this.__s:this.__s=h({},this.state),"function"==typeof n&&(n=n(h({},u),this.props)),n&&h(u,n),null!=n&&this.__v&&(l&&this._sb.push(l),m(this))},k.prototype.forceUpdate=function(n){this.__v&&(this.__e=!0,n&&this.__h.push(n),m(this))},k.prototype.render=_,preact_module_t=[],o="function"==typeof Promise?Promise.prototype.then.bind(Promise.resolve()):setTimeout,preact_module_f=function(n,l){return n.__v.__b-l.__v.__b},w.__r=0,preact_module_e=0;
//# sourceMappingURL=preact.module.js.map

;// CONCATENATED MODULE: ./node_modules/preact/hooks/dist/hooks.module.js
var hooks_module_t,hooks_module_r,hooks_module_u,hooks_module_i,hooks_module_o=0,hooks_module_f=[],hooks_module_c=[],hooks_module_e=preact_module_l.__b,hooks_module_a=preact_module_l.__r,hooks_module_v=preact_module_l.diffed,l=preact_module_l.__c,hooks_module_m=preact_module_l.unmount;function hooks_module_d(t,u){preact_module_l.__h&&preact_module_l.__h(hooks_module_r,t,hooks_module_o||u),hooks_module_o=0;var i=hooks_module_r.__H||(hooks_module_r.__H={__:[],__h:[]});return t>=i.__.length&&i.__.push({__V:hooks_module_c}),i.__[t]}function hooks_module_h(n){return hooks_module_o=1,hooks_module_s(hooks_module_B,n)}function hooks_module_s(n,u,i){var o=hooks_module_d(hooks_module_t++,2);if(o.t=n,!o.__c&&(o.__=[i?i(u):hooks_module_B(void 0,u),function(n){var t=o.__N?o.__N[0]:o.__[0],r=o.t(t,n);t!==r&&(o.__N=[r,o.__[1]],o.__c.setState({}))}],o.__c=hooks_module_r,!hooks_module_r.u)){var f=function(n,t,r){if(!o.__c.__H)return!0;var u=o.__c.__H.__.filter(function(n){return n.__c});if(u.every(function(n){return!n.__N}))return!c||c.call(this,n,t,r);var i=!1;return u.forEach(function(n){if(n.__N){var t=n.__[0];n.__=n.__N,n.__N=void 0,t!==n.__[0]&&(i=!0)}}),!(!i&&o.__c.props===n)&&(!c||c.call(this,n,t,r))};hooks_module_r.u=!0;var c=hooks_module_r.shouldComponentUpdate,e=hooks_module_r.componentWillUpdate;hooks_module_r.componentWillUpdate=function(n,t,r){if(this.__e){var u=c;c=void 0,f(n,t,r),c=u}e&&e.call(this,n,t,r)},hooks_module_r.shouldComponentUpdate=f}return o.__N||o.__}function hooks_module_p(u,i){var o=hooks_module_d(hooks_module_t++,3);!preact_module_l.__s&&hooks_module_z(o.__H,i)&&(o.__=u,o.i=i,hooks_module_r.__H.__h.push(o))}function hooks_module_y(u,i){var o=hooks_module_d(hooks_module_t++,4);!preact_module_l.__s&&hooks_module_z(o.__H,i)&&(o.__=u,o.i=i,hooks_module_r.__h.push(o))}function hooks_module_(n){return hooks_module_o=5,hooks_module_F(function(){return{current:n}},[])}function hooks_module_A(n,t,r){hooks_module_o=6,hooks_module_y(function(){return"function"==typeof n?(n(t()),function(){return n(null)}):n?(n.current=t(),function(){return n.current=null}):void 0},null==r?r:r.concat(n))}function hooks_module_F(n,r){var u=hooks_module_d(hooks_module_t++,7);return hooks_module_z(u.__H,r)?(u.__V=n(),u.i=r,u.__h=n,u.__V):u.__}function hooks_module_T(n,t){return hooks_module_o=8,hooks_module_F(function(){return n},t)}function hooks_module_q(n){var u=hooks_module_r.context[n.__c],i=hooks_module_d(hooks_module_t++,9);return i.c=n,u?(null==i.__&&(i.__=!0,u.sub(hooks_module_r)),u.props.value):n.__}function hooks_module_x(t,r){n.useDebugValue&&n.useDebugValue(r?r(t):t)}function hooks_module_P(n){var u=hooks_module_d(hooks_module_t++,10),i=hooks_module_h();return u.__=n,hooks_module_r.componentDidCatch||(hooks_module_r.componentDidCatch=function(n,t){u.__&&u.__(n,t),i[1](n)}),[i[0],function(){i[1](void 0)}]}function V(){var n=hooks_module_d(hooks_module_t++,11);if(!n.__){for(var u=hooks_module_r.__v;null!==u&&!u.__m&&null!==u.__;)u=u.__;var i=u.__m||(u.__m=[0,0]);n.__="P"+i[0]+"-"+i[1]++}return n.__}function hooks_module_b(){for(var t;t=hooks_module_f.shift();)if(t.__P&&t.__H)try{t.__H.__h.forEach(hooks_module_k),t.__H.__h.forEach(hooks_module_w),t.__H.__h=[]}catch(r){t.__H.__h=[],preact_module_l.__e(r,t.__v)}}preact_module_l.__b=function(n){hooks_module_r=null,hooks_module_e&&hooks_module_e(n)},preact_module_l.__r=function(n){hooks_module_a&&hooks_module_a(n),hooks_module_t=0;var i=(hooks_module_r=n.__c).__H;i&&(hooks_module_u===hooks_module_r?(i.__h=[],hooks_module_r.__h=[],i.__.forEach(function(n){n.__N&&(n.__=n.__N),n.__V=hooks_module_c,n.__N=n.i=void 0})):(i.__h.forEach(hooks_module_k),i.__h.forEach(hooks_module_w),i.__h=[])),hooks_module_u=hooks_module_r},preact_module_l.diffed=function(t){hooks_module_v&&hooks_module_v(t);var o=t.__c;o&&o.__H&&(o.__H.__h.length&&(1!==hooks_module_f.push(o)&&hooks_module_i===preact_module_l.requestAnimationFrame||((hooks_module_i=preact_module_l.requestAnimationFrame)||hooks_module_j)(hooks_module_b)),o.__H.__.forEach(function(n){n.i&&(n.__H=n.i),n.__V!==hooks_module_c&&(n.__=n.__V),n.i=void 0,n.__V=hooks_module_c})),hooks_module_u=hooks_module_r=null},preact_module_l.__c=function(t,r){r.some(function(t){try{t.__h.forEach(hooks_module_k),t.__h=t.__h.filter(function(n){return!n.__||hooks_module_w(n)})}catch(u){r.some(function(n){n.__h&&(n.__h=[])}),r=[],preact_module_l.__e(u,t.__v)}}),l&&l(t,r)},preact_module_l.unmount=function(t){hooks_module_m&&hooks_module_m(t);var r,u=t.__c;u&&u.__H&&(u.__H.__.forEach(function(n){try{hooks_module_k(n)}catch(n){r=n}}),u.__H=void 0,r&&preact_module_l.__e(r,u.__v))};var hooks_module_g="function"==typeof requestAnimationFrame;function hooks_module_j(n){var t,r=function(){clearTimeout(u),hooks_module_g&&cancelAnimationFrame(t),setTimeout(n)},u=setTimeout(r,100);hooks_module_g&&(t=requestAnimationFrame(r))}function hooks_module_k(n){var t=hooks_module_r,u=n.__c;"function"==typeof u&&(n.__c=void 0,u()),hooks_module_r=t}function hooks_module_w(n){var t=hooks_module_r;n.__c=n.__(),hooks_module_r=t}function hooks_module_z(n,t){return!n||n.length!==t.length||t.some(function(t,r){return t!==n[r]})}function hooks_module_B(n,t){return"function"==typeof t?t(n):t}
//# sourceMappingURL=hooks.module.js.map

;// CONCATENATED MODULE: ./node_modules/@preact/signals-core/dist/signals-core.module.js
function signals_core_module_i(){throw new Error("Cycle detected")}function signals_core_module_t(){if(!(signals_core_module_f>1)){var i,t=!1;while(void 0!==signals_core_module_s){var r=signals_core_module_s;signals_core_module_s=void 0;signals_core_module_v++;while(void 0!==r){var n=r.o;r.o=void 0;r.f&=-3;if(!(8&r.f)&&signals_core_module_a(r))try{r.c()}catch(r){if(!t){i=r;t=!0}}r=n}}signals_core_module_v=0;signals_core_module_f--;if(t)throw i}else signals_core_module_f--}function signals_core_module_r(i){if(signals_core_module_f>0)return i();signals_core_module_f++;try{return i()}finally{signals_core_module_t()}}var signals_core_module_n=void 0,signals_core_module_o=0;function signals_core_module_h(i){if(signals_core_module_o>0)return i();var t=signals_core_module_n;signals_core_module_n=void 0;signals_core_module_o++;try{return i()}finally{signals_core_module_o--;signals_core_module_n=t}}var signals_core_module_s=void 0,signals_core_module_f=0,signals_core_module_v=0,signals_core_module_e=0;function signals_core_module_u(i){if(void 0!==signals_core_module_n){var t=i.n;if(void 0===t||t.t!==signals_core_module_n){t={i:0,S:i,p:signals_core_module_n.s,n:void 0,t:signals_core_module_n,e:void 0,x:void 0,r:t};if(void 0!==signals_core_module_n.s)signals_core_module_n.s.n=t;signals_core_module_n.s=t;i.n=t;if(32&signals_core_module_n.f)i.S(t);return t}else if(-1===t.i){t.i=0;if(void 0!==t.n){t.n.p=t.p;if(void 0!==t.p)t.p.n=t.n;t.p=signals_core_module_n.s;t.n=void 0;signals_core_module_n.s.n=t;signals_core_module_n.s=t}return t}}}function signals_core_module_c(i){this.v=i;this.i=0;this.n=void 0;this.t=void 0}signals_core_module_c.prototype.h=function(){return!0};signals_core_module_c.prototype.S=function(i){if(this.t!==i&&void 0===i.e){i.x=this.t;if(void 0!==this.t)this.t.e=i;this.t=i}};signals_core_module_c.prototype.U=function(i){if(void 0!==this.t){var t=i.e,r=i.x;if(void 0!==t){t.x=r;i.e=void 0}if(void 0!==r){r.e=t;i.x=void 0}if(i===this.t)this.t=r}};signals_core_module_c.prototype.subscribe=function(i){var t=this;return signals_core_module_E(function(){var r=t.value,n=32&this.f;this.f&=-33;try{i(r)}finally{this.f|=n}})};signals_core_module_c.prototype.valueOf=function(){return this.value};signals_core_module_c.prototype.toString=function(){return this.value+""};signals_core_module_c.prototype.toJSON=function(){return this.value};signals_core_module_c.prototype.peek=function(){return this.v};Object.defineProperty(signals_core_module_c.prototype,"value",{get:function(){var i=signals_core_module_u(this);if(void 0!==i)i.i=this.i;return this.v},set:function(r){if(signals_core_module_n instanceof signals_core_module_y)!function(){throw new Error("Computed cannot have side-effects")}();if(r!==this.v){if(signals_core_module_v>100)signals_core_module_i();this.v=r;this.i++;signals_core_module_e++;signals_core_module_f++;try{for(var o=this.t;void 0!==o;o=o.x)o.t.N()}finally{signals_core_module_t()}}}});function signals_core_module_d(i){return new signals_core_module_c(i)}function signals_core_module_a(i){for(var t=i.s;void 0!==t;t=t.n)if(t.S.i!==t.i||!t.S.h()||t.S.i!==t.i)return!0;return!1}function signals_core_module_l(i){for(var t=i.s;void 0!==t;t=t.n){var r=t.S.n;if(void 0!==r)t.r=r;t.S.n=t;t.i=-1;if(void 0===t.n){i.s=t;break}}}function signals_core_module_w(i){var t=i.s,r=void 0;while(void 0!==t){var n=t.p;if(-1===t.i){t.S.U(t);if(void 0!==n)n.n=t.n;if(void 0!==t.n)t.n.p=n}else r=t;t.S.n=t.r;if(void 0!==t.r)t.r=void 0;t=n}i.s=r}function signals_core_module_y(i){signals_core_module_c.call(this,void 0);this.x=i;this.s=void 0;this.g=signals_core_module_e-1;this.f=4}(signals_core_module_y.prototype=new signals_core_module_c).h=function(){this.f&=-3;if(1&this.f)return!1;if(32==(36&this.f))return!0;this.f&=-5;if(this.g===signals_core_module_e)return!0;this.g=signals_core_module_e;this.f|=1;if(this.i>0&&!signals_core_module_a(this)){this.f&=-2;return!0}var i=signals_core_module_n;try{signals_core_module_l(this);signals_core_module_n=this;var t=this.x();if(16&this.f||this.v!==t||0===this.i){this.v=t;this.f&=-17;this.i++}}catch(i){this.v=i;this.f|=16;this.i++}signals_core_module_n=i;signals_core_module_w(this);this.f&=-2;return!0};signals_core_module_y.prototype.S=function(i){if(void 0===this.t){this.f|=36;for(var t=this.s;void 0!==t;t=t.n)t.S.S(t)}signals_core_module_c.prototype.S.call(this,i)};signals_core_module_y.prototype.U=function(i){if(void 0!==this.t){signals_core_module_c.prototype.U.call(this,i);if(void 0===this.t){this.f&=-33;for(var t=this.s;void 0!==t;t=t.n)t.S.U(t)}}};signals_core_module_y.prototype.N=function(){if(!(2&this.f)){this.f|=6;for(var i=this.t;void 0!==i;i=i.x)i.t.N()}};signals_core_module_y.prototype.peek=function(){if(!this.h())signals_core_module_i();if(16&this.f)throw this.v;return this.v};Object.defineProperty(signals_core_module_y.prototype,"value",{get:function(){if(1&this.f)signals_core_module_i();var t=signals_core_module_u(this);this.h();if(void 0!==t)t.i=this.i;if(16&this.f)throw this.v;return this.v}});function signals_core_module_(i){return new signals_core_module_y(i)}function signals_core_module_p(i){var r=i.u;i.u=void 0;if("function"==typeof r){signals_core_module_f++;var o=signals_core_module_n;signals_core_module_n=void 0;try{r()}catch(t){i.f&=-2;i.f|=8;signals_core_module_g(i);throw t}finally{signals_core_module_n=o;signals_core_module_t()}}}function signals_core_module_g(i){for(var t=i.s;void 0!==t;t=t.n)t.S.U(t);i.x=void 0;i.s=void 0;signals_core_module_p(i)}function signals_core_module_b(i){if(signals_core_module_n!==this)throw new Error("Out-of-order effect");signals_core_module_w(this);signals_core_module_n=i;this.f&=-2;if(8&this.f)signals_core_module_g(this);signals_core_module_t()}function signals_core_module_x(i){this.x=i;this.u=void 0;this.s=void 0;this.o=void 0;this.f=32}signals_core_module_x.prototype.c=function(){var i=this.S();try{if(8&this.f)return;if(void 0===this.x)return;var t=this.x();if("function"==typeof t)this.u=t}finally{i()}};signals_core_module_x.prototype.S=function(){if(1&this.f)signals_core_module_i();this.f|=1;this.f&=-9;signals_core_module_p(this);signals_core_module_l(this);signals_core_module_f++;var t=signals_core_module_n;signals_core_module_n=this;return signals_core_module_b.bind(this,t)};signals_core_module_x.prototype.N=function(){if(!(2&this.f)){this.f|=2;this.o=signals_core_module_s;signals_core_module_s=this}};signals_core_module_x.prototype.d=function(){this.f|=8;if(!(1&this.f))signals_core_module_g(this)};function signals_core_module_E(i){var t=new signals_core_module_x(i);try{t.c()}catch(i){t.d();throw i}return t.d.bind(t)}//# sourceMappingURL=signals-core.module.js.map

;// CONCATENATED MODULE: ./node_modules/@preact/signals/dist/signals.module.js
var signals_module_c,signals_module_v;function signals_module_s(n,i){preact_module_l[n]=i.bind(null,preact_module_l[n]||function(){})}function signals_module_l(n){if(signals_module_v)signals_module_v();signals_module_v=n&&n.S()}function signals_module_d(n){var r=this,t=n.data,f=useSignal(t);f.value=t;var o=hooks_module_F(function(){var n=r.__v;while(n=n.__)if(n.__c){n.__c.__$f|=4;break}r.__$u.c=function(){r.base.data=o.peek()};return signals_core_module_(function(){var n=f.value.value;return 0===n?0:!0===n?"":n||""})},[]);return o.value}signals_module_d.displayName="_st";Object.defineProperties(signals_core_module_c.prototype,{constructor:{configurable:!0,value:void 0},type:{configurable:!0,value:signals_module_d},props:{configurable:!0,get:function(){return{data:this}}},__b:{configurable:!0,value:1}});signals_module_s("__b",function(n,r){if("string"==typeof r.type){var i,t=r.props;for(var f in t)if("children"!==f){var e=t[f];if(e instanceof signals_core_module_c){if(!i)r.__np=i={};i[f]=e;t[f]=e.peek()}}}n(r)});signals_module_s("__r",function(n,r){signals_module_l();var i,t=r.__c;if(t){t.__$f&=-2;if(void 0===(i=t.__$u))t.__$u=i=function(n){var r;signals_core_module_E(function(){r=this});r.c=function(){t.__$f|=1;t.setState({})};return r}()}signals_module_c=t;signals_module_l(i);n(r)});signals_module_s("__e",function(n,r,i,t){signals_module_l();signals_module_c=void 0;n(r,i,t)});signals_module_s("diffed",function(n,r){signals_module_l();signals_module_c=void 0;var i;if("string"==typeof r.type&&(i=r.__e)){var t=r.__np,f=r.props;if(t){var o=i.U;if(o)for(var e in o){var u=o[e];if(void 0!==u&&!(e in t)){u.d();o[e]=void 0}}else i.U=o={};for(var a in t){var v=o[a],s=t[a];if(void 0===v){v=signals_module_p(i,a,s,f);o[a]=v}else v.o(s,f)}}}n(r)});function signals_module_p(n,r,i,t){var f=r in n&&void 0===n.ownerSVGElement,o=signals_core_module_d(i);return{o:function(n,r){o.value=n;t=r},d:signals_core_module_E(function(){var i=o.value.value;if(t[r]!==i){t[r]=i;if(f)n[r]=i;else if(i)n.setAttribute(r,i);else n.removeAttribute(r)}})}}signals_module_s("unmount",function(n,r){if("string"==typeof r.type){var i=r.__e;if(i){var t=i.U;if(t){i.U=void 0;for(var f in t){var o=t[f];if(o)o.d()}}}}else{var e=r.__c;if(e){var u=e.__$u;if(u){e.__$u=void 0;u.d()}}}n(r)});signals_module_s("__h",function(n,r,i,t){if(t<3)r.__$f|=2;n(r,i,t)});k.prototype.shouldComponentUpdate=function(n,r){var i=this.__$u;if(!(i&&void 0!==i.s||4&this.__$f))return!0;if(3&this.__$f)return!0;for(var t in r)return!0;for(var f in n)if("__source"!==f&&n[f]!==this.props[f])return!0;for(var o in this.props)if(!(o in n))return!0;return!1};function useSignal(n){return hooks_module_F(function(){return signals_core_module_d(n)},[])}function useComputed(n){var r=t(n);r.current=n;signals_module_c.__$f|=4;return i(function(){return e(function(){return r.current()})},[])}function useSignalEffect(n){var r=t(n);r.current=n;f(function(){return a(function(){return r.current()})},[])}//# sourceMappingURL=signals.module.js.map

;// CONCATENATED MODULE: ./node_modules/deepsignal/dist/deepsignal.module.js
var deepsignal_module_a=new WeakMap,deepsignal_module_o=new WeakMap,deepsignal_module_s=new WeakMap,deepsignal_module_c=new WeakSet,deepsignal_module_u=new WeakMap,deepsignal_module_i=/^\$/,deepsignal_module_f=!1,deepsignal_module_l=function(e){if(!deepsignal_module_d(e))throw new Error("This object can't be observed.");return deepsignal_module_o.has(e)||deepsignal_module_o.set(e,deepsignal_module_h(e,deepsignal_module_v)),deepsignal_module_o.get(e)},deepsignal_module_g=function(e,t){deepsignal_module_f=!0;var r=e[t];try{deepsignal_module_f=!1}catch(e){}return r},deepsignal_module_h=function(e,t){var r=new Proxy(e,t);return deepsignal_module_c.add(r),r},deepsignal_module_p=function(){throw new Error("Don't mutate the signals directly.")},deepsignal_module_y=function(e){return function(t,c,u){var l;if(deepsignal_module_f)return Reflect.get(t,c,u);var g=e||"$"===c[0];if(!e&&g&&Array.isArray(t)){if("$"===c)return deepsignal_module_s.has(t)||deepsignal_module_s.set(t,deepsignal_module_h(t,deepsignal_module_w)),deepsignal_module_s.get(t);g="$length"===c}deepsignal_module_a.has(u)||deepsignal_module_a.set(u,new Map);var p=deepsignal_module_a.get(u),y=g?c.replace(deepsignal_module_i,""):c;if(p.has(y)||"function"!=typeof(null==(l=Object.getOwnPropertyDescriptor(t,y))?void 0:l.get)){var b=Reflect.get(t,y,u);if(g&&"function"==typeof b)return;if("symbol"==typeof y&&deepsignal_module_m.has(y))return b;p.has(y)||(deepsignal_module_d(b)&&(deepsignal_module_o.has(b)||deepsignal_module_o.set(b,deepsignal_module_h(b,deepsignal_module_v)),b=deepsignal_module_o.get(b)),p.set(y,signals_core_module_d(b)))}else p.set(y,signals_core_module_(function(){return Reflect.get(t,y,u)}));return g?p.get(y):p.get(y).value}},deepsignal_module_v={get:deepsignal_module_y(!1),set:function(e,n,s,c){deepsignal_module_a.has(c)||deepsignal_module_a.set(c,new Map);var f=deepsignal_module_a.get(c);if("$"===n[0]){s instanceof signals_core_module_c||deepsignal_module_p();var l=n.replace(deepsignal_module_i,"");return f.set(l,s),Reflect.set(e,l,s.peek(),c)}var g=s;deepsignal_module_d(s)&&(deepsignal_module_o.has(s)||deepsignal_module_o.set(s,deepsignal_module_h(s,deepsignal_module_v)),g=deepsignal_module_o.get(s));var y=!(n in e),w=Reflect.set(e,n,s,c);return f.has(n)?f.get(n).value=g:f.set(n,signals_core_module_d(g)),y&&deepsignal_module_u.has(e)&&deepsignal_module_u.get(e).value++,Array.isArray(e)&&f.has("length")&&(f.get("length").value=e.length),w},deleteProperty:function(e,t){"$"===t[0]&&deepsignal_module_p();var r=deepsignal_module_a.get(deepsignal_module_o.get(e)),n=Reflect.deleteProperty(e,t);return r&&r.has(t)&&(r.get(t).value=void 0),deepsignal_module_u.has(e)&&deepsignal_module_u.get(e).value++,n},ownKeys:function(e){return deepsignal_module_u.has(e)||deepsignal_module_u.set(e,signals_core_module_d(0)),deepsignal_module_u._=deepsignal_module_u.get(e).value,Reflect.ownKeys(e)}},deepsignal_module_w={get:deepsignal_module_y(!0),set:deepsignal_module_p,deleteProperty:deepsignal_module_p},deepsignal_module_m=new Set(Object.getOwnPropertyNames(Symbol).map(function(e){return Symbol[e]}).filter(function(e){return"symbol"==typeof e})),deepsignal_module_b=new Set([Object,Array]),deepsignal_module_d=function(e){return"object"==typeof e&&null!==e&&(!("function"==typeof e.constructor&&e.constructor.name in globalThis&&globalThis[e.constructor.name]===e.constructor)||deepsignal_module_b.has(e.constructor))&&!deepsignal_module_c.has(e)},deepsignal_module_k=function(t){return e(function(){return deepsignal_module_l(t)},[])};//# sourceMappingURL=deepsignal.module.js.map

;// CONCATENATED MODULE: ./packages/interactivity/src/portals.js
/**
 * External dependencies
 */


/**
 * @param {import('../../src/index').RenderableProps<{ context: any }>} props
 */
function ContextProvider(props) {
  this.getChildContext = () => props.context;
  return props.children;
}

/**
 * Portal component
 *
 * @this {import('./internal').Component}
 * @param {object | null | undefined} props
 *
 *                                          TODO: use createRoot() instead of fake root
 */
function Portal(props) {
  const _this = this;
  const container = props._container;
  _this.componentWillUnmount = function () {
    B(null, _this._temp);
    _this._temp = null;
    _this._container = null;
  };

  // When we change container we should clear our old container and
  // indicate a new mount.
  if (_this._container && _this._container !== container) {
    _this.componentWillUnmount();
  }

  // When props.vnode is undefined/false/null we are dealing with some kind of
  // conditional vnode. This should not trigger a render.
  if (props._vnode) {
    if (!_this._temp) {
      _this._container = container;

      // Create a fake DOM parent node that manages a subset of `container`'s children:
      _this._temp = {
        nodeType: 1,
        parentNode: container,
        childNodes: [],
        appendChild(child) {
          this.childNodes.push(child);
          _this._container.appendChild(child);
        },
        insertBefore(child) {
          this.childNodes.push(child);
          _this._container.appendChild(child);
        },
        removeChild(child) {
          this.childNodes.splice(
          // eslint-disable-next-line no-bitwise
          this.childNodes.indexOf(child) >>> 1, 1);
          _this._container.removeChild(child);
        }
      };
    }

    // Render our wrapping element into temp.
    B(y(ContextProvider, {
      context: _this.context
    }, props._vnode), _this._temp);
  }
  // When we come from a conditional render, on a mounted
  // portal we should clear the DOM.
  else if (_this._temp) {
    _this.componentWillUnmount();
  }
}

/**
 * Create a `Portal` to continue rendering the vnode tree at a different DOM node
 *
 * @param {import('./internal').VNode}         vnode     The vnode to render
 * @param {import('./internal').PreactElement} container The DOM node to continue rendering in to.
 */
function createPortal(vnode, container) {
  const el = y(Portal, {
    _vnode: vnode,
    _container: container
  });
  el.containerInfo = container;
  return el;
}
;// CONCATENATED MODULE: ./packages/interactivity/src/utils.js
/**
 * External dependencies
 */


const afterNextFrame = callback => {
  return new Promise(resolve => {
    const done = () => {
      clearTimeout(timeout);
      window.cancelAnimationFrame(raf);
      setTimeout(() => {
        callback();
        resolve();
      });
    };
    const timeout = setTimeout(done, 100);
    const raf = window.requestAnimationFrame(done);
  });
};

// Using the mangled properties:
// this.c: this._callback
// this.x: this._compute
// https://github.com/preactjs/signals/blob/main/mangle.json
function createFlusher(compute, notify) {
  let flush;
  const dispose = signals_core_module_E(function () {
    flush = this.c.bind(this);
    this.x = compute;
    this.c = notify;
    return compute();
  });
  return {
    flush,
    dispose
  };
}

// Version of `useSignalEffect` with a `useEffect`-like execution. This hook
// implementation comes from this PR, but we added short-cirtuiting to avoid
// infinite loops: https://github.com/preactjs/signals/pull/290
function utils_useSignalEffect(callback) {
  hooks_module_p(() => {
    let eff = null;
    let isExecuting = false;
    const notify = async () => {
      if (eff && !isExecuting) {
        isExecuting = true;
        await afterNextFrame(eff.flush);
        isExecuting = false;
      }
    };
    eff = createFlusher(callback, notify);
    return eff.dispose;
  }, []);
}

// For wrapperless hydration.
// See https://gist.github.com/developit/f4c67a2ede71dc2fab7f357f39cff28c
const createRootFragment = (parent, replaceNode) => {
  replaceNode = [].concat(replaceNode);
  const s = replaceNode[replaceNode.length - 1].nextSibling;
  function insert(c, r) {
    parent.insertBefore(c, r || s);
  }
  return parent.__k = {
    nodeType: 1,
    parentNode: parent,
    firstChild: replaceNode[0],
    childNodes: replaceNode,
    insertBefore: insert,
    appendChild: insert,
    removeChild(c) {
      parent.removeChild(c);
    }
  };
};
;// CONCATENATED MODULE: ./packages/interactivity/src/store.ts
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */

const isObject = item => !!item && typeof item === 'object' && !Array.isArray(item);
const deepMerge = (target, source) => {
  if (isObject(target) && isObject(source)) {
    for (const key in source) {
      const getter = Object.getOwnPropertyDescriptor(source, key)?.get;
      if (typeof getter === 'function') {
        Object.defineProperty(target, key, {
          get: getter
        });
      } else if (isObject(source[key])) {
        if (!target[key]) Object.assign(target, {
          [key]: {}
        });
        deepMerge(target[key], source[key]);
      } else {
        Object.assign(target, {
          [key]: source[key]
        });
      }
    }
  }
};
const parseInitialState = () => {
  const storeTag = document.querySelector(`script[type="application/json"]#wp-interactivity-initial-state`);
  if (!storeTag?.textContent) return {};
  try {
    const initialState = JSON.parse(storeTag.textContent);
    if (isObject(initialState)) return initialState;
    throw Error('Parsed state is not an object');
  } catch (e) {
    // eslint-disable-next-line no-console
    console.log(e);
  }
  return {};
};
const stores = new Map();
const rawStores = new Map();
const storeLocks = new Map();
const objToProxy = new WeakMap();
const proxyToNs = new WeakMap();
const scopeToGetters = new WeakMap();
const proxify = (obj, ns) => {
  if (!objToProxy.has(obj)) {
    const proxy = new Proxy(obj, handlers);
    objToProxy.set(obj, proxy);
    proxyToNs.set(proxy, ns);
  }
  return objToProxy.get(obj);
};
const handlers = {
  get: (target, key, receiver) => {
    const ns = proxyToNs.get(receiver);

    // Check if the property is a getter and we are inside an scope. If that is
    // the case, we clone the getter to avoid overwriting the scoped
    // dependencies of the computed each time that getter runs.
    const getter = Object.getOwnPropertyDescriptor(target, key)?.get;
    if (getter) {
      const scope = getScope();
      if (scope) {
        const getters = scopeToGetters.get(scope) || scopeToGetters.set(scope, new Map()).get(scope);
        if (!getters.has(getter)) {
          getters.set(getter, signals_core_module_(() => {
            setNamespace(ns);
            setScope(scope);
            try {
              return getter.call(target);
            } finally {
              resetScope();
              resetNamespace();
            }
          }));
        }
        return getters.get(getter).value;
      }
    }
    const result = Reflect.get(target, key, receiver);

    // Check if the proxy is the store root and no key with that name exist. In
    // that case, return an empty object for the requested key.
    if (typeof result === 'undefined' && receiver === stores.get(ns)) {
      const obj = {};
      Reflect.set(target, key, obj, receiver);
      return proxify(obj, ns);
    }

    // Check if the property is a generator. If it is, we turn it into an
    // asynchronous function where we restore the default namespace and scope
    // each time it awaits/yields.
    if (result?.constructor?.name === 'GeneratorFunction') {
      return async (...args) => {
        const scope = getScope();
        const gen = result(...args);
        let value;
        let it;
        while (true) {
          setNamespace(ns);
          setScope(scope);
          try {
            it = gen.next(value);
          } finally {
            resetScope();
            resetNamespace();
          }
          try {
            value = await it.value;
          } catch (e) {
            gen.throw(e);
          }
          if (it.done) break;
        }
        return value;
      };
    }

    // Check if the property is a synchronous function. If it is, set the
    // default namespace. Synchronous functions always run in the proper scope,
    // which is set by the Directives component.
    if (typeof result === 'function') {
      return (...args) => {
        setNamespace(ns);
        try {
          return result(...args);
        } finally {
          resetNamespace();
        }
      };
    }

    // Check if the property is an object. If it is, proxyify it.
    if (isObject(result)) return proxify(result, ns);
    return result;
  }
};
const universalUnlock = 'I acknowledge that using a private store means my plugin will inevitably break on the next store release.';

/**
 * Extends the Interactivity API global store adding the passed properties to
 * the given namespace. It also returns stable references to the namespace
 * content.
 *
 * These props typically consist of `state`, which is the reactive part of the
 * store ― which means that any directive referencing a state property will be
 * re-rendered anytime it changes ― and function properties like `actions` and
 * `callbacks`, mostly used for event handlers. These props can then be
 * referenced by any directive to make the HTML interactive.
 *
 * @example
 * ```js
 *  const { state } = store( 'counter', {
 *    state: {
 *      value: 0,
 *      get double() { return state.value * 2; },
 *    },
 *    actions: {
 *      increment() {
 *        state.value += 1;
 *      },
 *    },
 *  } );
 * ```
 *
 * The code from the example above allows blocks to subscribe and interact with
 * the store by using directives in the HTML, e.g.:
 *
 * ```html
 * <div data-wp-interactive='{ "namespace": "counter" }'>
 *   <button
 *     data-wp-text="state.double"
 *     data-wp-on--click="actions.increment"
 *   >
 *     0
 *   </button>
 * </div>
 * ```
 * @param namespace The store namespace to interact with.
 * @param storePart Properties to add to the store namespace.
 * @param options   Options for the given namespace.
 *
 * @return A reference to the namespace content.
 */

function store(namespace, {
  state = {},
  ...block
} = {}, {
  lock = false
} = {}) {
  if (!stores.has(namespace)) {
    // Lock the store if the passed lock is different from the universal
    // unlock. Once the lock is set (either false, true, or a given string),
    // it cannot change.
    if (lock !== universalUnlock) {
      storeLocks.set(namespace, lock);
    }
    const rawStore = {
      state: deepsignal_module_l(state),
      ...block
    };
    const proxiedStore = new Proxy(rawStore, handlers);
    rawStores.set(namespace, rawStore);
    stores.set(namespace, proxiedStore);
    proxyToNs.set(proxiedStore, namespace);
  } else {
    // Lock the store if it wasn't locked yet and the passed lock is
    // different from the universal unlock. If no lock is given, the store
    // will be public and won't accept any lock from now on.
    if (lock !== universalUnlock && !storeLocks.has(namespace)) {
      storeLocks.set(namespace, lock);
    } else {
      const storeLock = storeLocks.get(namespace);
      const isLockValid = lock === universalUnlock || lock !== true && lock === storeLock;
      if (!isLockValid) {
        if (!storeLock) {
          throw Error('Cannot lock a public store');
        } else {
          throw Error('Cannot unlock a private store with an invalid lock code');
        }
      }
    }
    const target = rawStores.get(namespace);
    deepMerge(target, block);
    deepMerge(target.state, state);
  }
  return stores.get(namespace);
}

// Parse and populate the initial state.
Object.entries(parseInitialState()).forEach(([namespace, state]) => {
  store(namespace, {
    state
  });
});
;// CONCATENATED MODULE: ./node_modules/preact/jsx-runtime/dist/jsxRuntime.module.js
var jsxRuntime_module_=0;function jsxRuntime_module_o(o,e,n,t,f,l){var s,u,a={};for(u in e)"ref"==u?s=e[u]:a[u]=e[u];var i={type:o,props:a,key:n,ref:s,__k:null,__:null,__b:0,__e:null,__d:void 0,__c:null,__h:null,constructor:void 0,__v:--jsxRuntime_module_,__source:f,__self:l};if("function"==typeof o&&(s=o.defaultProps))for(u in s)void 0===a[u]&&(a[u]=s[u]);return preact_module_l.vnode&&preact_module_l.vnode(i),i}
//# sourceMappingURL=jsxRuntime.module.js.map

;// CONCATENATED MODULE: ./packages/interactivity/src/hooks.tsx
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


// Main context.
const context = F({});

// Wrap the element props to prevent modifications.
const immutableMap = new WeakMap();
const immutableError = () => {
  throw new Error('Please use `data-wp-bind` to modify the attributes of an element.');
};
const immutableHandlers = {
  get(target, key, receiver) {
    const value = Reflect.get(target, key, receiver);
    return !!value && typeof value === 'object' ? deepImmutable(value) : value;
  },
  set: immutableError,
  deleteProperty: immutableError
};
const deepImmutable = target => {
  if (!immutableMap.has(target)) immutableMap.set(target, new Proxy(target, immutableHandlers));
  return immutableMap.get(target);
};

// Store stacks for the current scope and the default namespaces and export APIs
// to interact with them.
const scopeStack = [];
const namespaceStack = [];

/**
 * Retrieves the context inherited by the element evaluating a function from the
 * store. The returned value depends on the element and the namespace where the
 * function calling `getContext()` exists.
 *
 * @param namespace Store namespace. By default, the namespace where the calling
 *                  function exists is used.
 * @return The context content.
 */
const getContext = namespace => getScope()?.context[namespace || namespaceStack.slice(-1)[0]];

/**
 * Retrieves a representation of the element where a function from the store
 * is being evalutated. Such representation is read-only, and contains a
 * reference to the DOM element, its props and a local reactive state.
 *
 * @return Element representation.
 */
const getElement = () => {
  if (!getScope()) {
    throw Error('Cannot call `getElement()` outside getters and actions used by directives.');
  }
  const {
    ref,
    state,
    props
  } = getScope();
  return Object.freeze({
    ref: ref.current,
    state,
    props: deepImmutable(props)
  });
};
const getScope = () => scopeStack.slice(-1)[0];
const setScope = scope => {
  scopeStack.push(scope);
};
const resetScope = () => {
  scopeStack.pop();
};
const setNamespace = namespace => {
  namespaceStack.push(namespace);
};
const resetNamespace = () => {
  namespaceStack.pop();
};

// WordPress Directives.
const directiveCallbacks = {};
const directivePriorities = {};

/**
 * Register a new directive type in the Interactivity API runtime.
 *
 * @example
 * ```js
 * directive(
 *   'alert', // Name without the `data-wp-` prefix.
 *   ( { directives: { alert }, element, evaluate } ) => {
 *     const defaultEntry = alert.find( entry => entry.suffix === 'default' );
 *     element.props.onclick = () => { alert( evaluate( defaultEntry ) ); }
 *   }
 * )
 * ```
 *
 * The previous code registers a custom directive type for displaying an alert
 * message whenever an element using it is clicked. The message text is obtained
 * from the store under the inherited namespace, using `evaluate`.
 *
 * When the HTML is processed by the Interactivity API, any element containing
 * the `data-wp-alert` directive will have the `onclick` event handler, e.g.,
 *
 * ```html
 * <div data-wp-interactive='{ "namespace": "messages" }'>
 *   <button data-wp-alert="state.alert">Click me!</button>
 * </div>
 * ```
 * Note that, in the previous example, the directive callback gets the path
 * value (`state.alert`) from the directive entry with suffix `default`. A
 * custom suffix can also be specified by appending `--` to the directive
 * attribute, followed by the suffix, like in the following HTML snippet:
 *
 * ```html
 * <div data-wp-interactive='{ "namespace": "myblock" }'>
 *   <button
 *     data-wp-color--text="state.text"
 *     data-wp-color--background="state.background"
 *   >Click me!</button>
 * </div>
 * ```
 *
 * This could be an hypothetical implementation of the custom directive used in
 * the snippet above.
 *
 * @example
 * ```js
 * directive(
 *   'color', // Name without prefix and suffix.
 *   ( { directives: { color }, ref, evaluate } ) =>
 *     colors.forEach( ( color ) => {
 *       if ( color.suffix = 'text' ) {
 *         ref.style.setProperty(
 *           'color',
 *           evaluate( color.text )
 *         );
 *       }
 *       if ( color.suffix = 'background' ) {
 *         ref.style.setProperty(
 *           'background-color',
 *           evaluate( color.background )
 *         );
 *       }
 *     } );
 *   }
 * )
 * ```
 *
 * @param name             Directive name, without the `data-wp-` prefix.
 * @param callback         Function that runs the directive logic.
 * @param options          Options object.
 * @param options.priority Option to control the directive execution order. The
 *                         lesser, the highest priority. Default is `10`.
 */
const directive = (name, callback, {
  priority = 10
} = {}) => {
  directiveCallbacks[name] = callback;
  directivePriorities[name] = priority;
};

// Resolve the path to some property of the store object.
const resolve = (path, namespace) => {
  let current = {
    ...stores.get(namespace),
    context: getScope().context[namespace]
  };
  path.split('.').forEach(p => current = current[p]);
  return current;
};

// Generate the evaluate function.
const getEvaluate = ({
  scope
}) => (entry, ...args) => {
  let {
    value: path,
    namespace
  } = entry;
  if (typeof path !== 'string') {
    throw new Error('The `value` prop should be a string path');
  }
  // If path starts with !, remove it and save a flag.
  const hasNegationOperator = path[0] === '!' && !!(path = path.slice(1));
  setScope(scope);
  const value = resolve(path, namespace);
  const result = typeof value === 'function' ? value(...args) : value;
  resetScope();
  return hasNegationOperator ? !result : result;
};

// Separate directives by priority. The resulting array contains objects
// of directives grouped by same priority, and sorted in ascending order.
const getPriorityLevels = directives => {
  const byPriority = Object.keys(directives).reduce((obj, name) => {
    if (directiveCallbacks[name]) {
      const priority = directivePriorities[name];
      (obj[priority] = obj[priority] || []).push(name);
    }
    return obj;
  }, {});
  return Object.entries(byPriority).sort(([p1], [p2]) => parseInt(p1) - parseInt(p2)).map(([, arr]) => arr);
};

// Component that wraps each priority level of directives of an element.
const Directives = ({
  directives,
  priorityLevels: [currentPriorityLevel, ...nextPriorityLevels],
  element,
  originalProps,
  previousScope
}) => {
  // Initialize the scope of this element. These scopes are different per each
  // level because each level has a different context, but they share the same
  // element ref, state and props.
  const scope = hooks_module_({}).current;
  scope.evaluate = hooks_module_T(getEvaluate({
    scope
  }), []);
  scope.context = hooks_module_q(context);
  /* eslint-disable react-hooks/rules-of-hooks */
  scope.ref = previousScope?.ref || hooks_module_(null);
  scope.state = previousScope?.state || hooks_module_(deepsignal_module_l({})).current;
  /* eslint-enable react-hooks/rules-of-hooks */

  // Create a fresh copy of the vnode element and add the props to the scope.
  element = E(element, {
    ref: scope.ref
  });
  scope.props = element.props;

  // Recursively render the wrapper for the next priority level.
  const children = nextPriorityLevels.length > 0 ? jsxRuntime_module_o(Directives, {
    directives: directives,
    priorityLevels: nextPriorityLevels,
    element: element,
    originalProps: originalProps,
    previousScope: scope
  }) : element;
  const props = {
    ...originalProps,
    children
  };
  const directiveArgs = {
    directives,
    props,
    element,
    context,
    evaluate: scope.evaluate
  };
  setScope(scope);
  for (const directiveName of currentPriorityLevel) {
    const wrapper = directiveCallbacks[directiveName]?.(directiveArgs);
    if (wrapper !== undefined) props.children = wrapper;
  }
  resetScope();
  return props.children;
};

// Preact Options Hook called each time a vnode is created.
const old = preact_module_l.vnode;
preact_module_l.vnode = vnode => {
  if (vnode.props.__directives) {
    const props = vnode.props;
    const directives = props.__directives;
    if (directives.key) vnode.key = directives.key.find(({
      suffix
    }) => suffix === 'default').value;
    delete props.__directives;
    const priorityLevels = getPriorityLevels(directives);
    if (priorityLevels.length > 0) {
      vnode.props = {
        directives,
        priorityLevels,
        originalProps: props,
        type: vnode.type,
        element: y(vnode.type, props),
        top: true
      };
      vnode.type = Directives;
    }
  }
  if (old) old(vnode);
};
;// CONCATENATED MODULE: ./packages/interactivity/src/slots.js
/**
 * External dependencies
 */




const slotsContext = F();
const Fill = ({
  slot,
  children
}) => {
  const slots = hooks_module_q(slotsContext);
  hooks_module_p(() => {
    if (slot) {
      slots.value = {
        ...slots.value,
        [slot]: children
      };
      return () => {
        slots.value = {
          ...slots.value,
          [slot]: null
        };
      };
    }
  }, [slots, slot, children]);
  return !!slot ? null : children;
};
const SlotProvider = ({
  children
}) => {
  return (
    // TODO: We can change this to use deepsignal once this PR is merged.
    // https://github.com/luisherranz/deepsignal/pull/38
    jsxRuntime_module_o(slotsContext.Provider, {
      value: signals_core_module_d({}),
      children: children
    })
  );
};
const Slot = ({
  name,
  children
}) => {
  const slots = hooks_module_q(slotsContext);
  return slots.value[name] || children;
};
;// CONCATENATED MODULE: ./packages/interactivity/src/constants.js
const directivePrefix = 'wp';
;// CONCATENATED MODULE: ./packages/interactivity/src/vdom.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */

const ignoreAttr = `data-${directivePrefix}-ignore`;
const islandAttr = `data-${directivePrefix}-interactive`;
const fullPrefix = `data-${directivePrefix}-`;
let namespace = null;

// Regular expression for directive parsing.
const directiveParser = new RegExp(`^data-${directivePrefix}-` +
// ${p} must be a prefix string, like 'wp'.
// Match alphanumeric characters including hyphen-separated
// segments. It excludes underscore intentionally to prevent confusion.
// E.g., "custom-directive".
'([a-z0-9]+(?:-[a-z0-9]+)*)' +
// (Optional) Match '--' followed by any alphanumeric charachters. It
// excludes underscore intentionally to prevent confusion, but it can
// contain multiple hyphens. E.g., "--custom-prefix--with-more-info".
'(?:--([a-z0-9_-]+))?$', 'i' // Case insensitive.
);

// Regular expression for reference parsing. It can contain a namespace before
// the reference, separated by `::`, like `some-namespace::state.somePath`.
// Namespaces can contain any alphanumeric characters, hyphens, underscores or
// forward slashes. References don't have any restrictions.
const nsPathRegExp = /^([\w-_\/]+)::(.+)$/;
const hydratedIslands = new WeakSet();

// Recursive function that transforms a DOM tree into vDOM.
function toVdom(root) {
  const treeWalker = document.createTreeWalker(root, 205 // ELEMENT + TEXT + COMMENT + CDATA_SECTION + PROCESSING_INSTRUCTION
  );

  function walk(node) {
    const {
      attributes,
      nodeType
    } = node;
    if (nodeType === 3) return [node.data];
    if (nodeType === 4) {
      const next = treeWalker.nextSibling();
      node.replaceWith(new window.Text(node.nodeValue));
      return [node.nodeValue, next];
    }
    if (nodeType === 8 || nodeType === 7) {
      const next = treeWalker.nextSibling();
      node.remove();
      return [null, next];
    }
    const props = {};
    const children = [];
    const directives = [];
    let ignore = false;
    let island = false;
    for (let i = 0; i < attributes.length; i++) {
      const n = attributes[i].name;
      if (n[fullPrefix.length] && n.slice(0, fullPrefix.length) === fullPrefix) {
        if (n === ignoreAttr) {
          ignore = true;
        } else {
          let [ns, value] = nsPathRegExp.exec(attributes[i].value)?.slice(1) ?? [null, attributes[i].value];
          try {
            value = JSON.parse(value);
          } catch (e) {}
          if (n === islandAttr) {
            island = true;
            namespace = value?.namespace ?? null;
          } else {
            directives.push([n, ns, value]);
          }
        }
      } else if (n === 'ref') {
        continue;
      }
      props[n] = attributes[i].value;
    }
    if (ignore && !island) return [y(node.localName, {
      ...props,
      innerHTML: node.innerHTML,
      __directives: {
        ignore: true
      }
    })];
    if (island) hydratedIslands.add(node);
    if (directives.length) {
      props.__directives = directives.reduce((obj, [name, ns, value]) => {
        const [, prefix, suffix = 'default'] = directiveParser.exec(name);
        if (!obj[prefix]) obj[prefix] = [];
        obj[prefix].push({
          namespace: ns ?? namespace,
          value,
          suffix
        });
        return obj;
      }, {});
    }
    let child = treeWalker.firstChild();
    if (child) {
      while (child) {
        const [vnode, nextChild] = walk(child);
        if (vnode) children.push(vnode);
        child = nextChild || treeWalker.nextSibling();
      }
      treeWalker.parentNode();
    }
    return [y(node.localName, props, children)];
  }
  return walk(treeWalker.currentNode);
}
;// CONCATENATED MODULE: ./packages/interactivity/src/router.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */




// The cache of visited and prefetched pages.
const pages = new Map();

// Keep the same root fragment for each interactive region node.
const regionRootFragments = new WeakMap();
const getRegionRootFragment = region => {
  if (!regionRootFragments.has(region)) {
    regionRootFragments.set(region, createRootFragment(region.parentElement, region));
  }
  return regionRootFragments.get(region);
};

// Helper to remove domain and hash from the URL. We are only interesting in
// caching the path and the query.
const cleanUrl = url => {
  const u = new URL(url, window.location);
  return u.pathname + u.search;
};

// Fetch a new page and convert it to a static virtual DOM.
const fetchPage = async (url, {
  html
}) => {
  try {
    if (!html) {
      const res = await window.fetch(url);
      if (res.status !== 200) return false;
      html = await res.text();
    }
    const dom = new window.DOMParser().parseFromString(html, 'text/html');
    return regionsToVdom(dom);
  } catch (e) {
    return false;
  }
};

// Return an object with VDOM trees of those HTML regions marked with a
// `navigation-id` directive.
const regionsToVdom = dom => {
  const regions = {};
  const attrName = `data-${directivePrefix}-navigation-id`;
  dom.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    regions[id] = toVdom(region);
  });
  const title = dom.querySelector('title')?.innerText;
  return {
    regions,
    title
  };
};

/**
 * Prefetchs the page with the passed URL.
 *
 * The function normalizes the URL and stores internally the fetch promise, to
 * avoid triggering a second fetch for an ongoing request.
 *
 * @param {string}  url             The page URL.
 * @param {Object}  [options]       Options object.
 * @param {boolean} [options.force] Force fetching the URL again.
 * @param {string}  [options.html]  HTML string to be used instead of fetching
 *                                  the requested URL.
 */
const prefetch = (url, options = {}) => {
  url = cleanUrl(url);
  if (options.force || !pages.has(url)) {
    pages.set(url, fetchPage(url, options));
  }
};

// Render all interactive regions contained in the given page.
const renderRegions = page => {
  const attrName = `data-${directivePrefix}-navigation-id`;
  document.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    const fragment = getRegionRootFragment(region);
    B(page.regions[id], fragment);
  });
  if (page.title) {
    document.title = page.title;
  }
};

// Variable to store the current navigation.
let navigatingTo = '';

/**
 * Navigates to the specified page.
 *
 * This function normalizes the passed href, fetchs the page HTML if needed, and
 * updates any interactive regions whose contents have changed. It also creates
 * a new entry in the browser session history.
 *
 * @param {string}  href              The page href.
 * @param {Object}  [options]         Options object.
 * @param {boolean} [options.force]   If true, it forces re-fetching the URL.
 * @param {string}  [options.html]    HTML string to be used instead of fetching
 *                                    the requested URL.
 * @param {boolean} [options.replace] If true, it replaces the current entry in
 *                                    the browser session history.
 * @param {number}  [options.timeout] Time until the navigation is aborted, in
 *                                    milliseconds. Default is 10000.
 *
 * @return {Promise} Promise that resolves once the navigation is completed or
 *                   aborted.
 */
const router_navigate = async (href, options = {}) => {
  const url = cleanUrl(href);
  navigatingTo = href;
  prefetch(url, options);

  // Create a promise that resolves when the specified timeout ends. The
  // timeout value is 10 seconds by default.
  const timeoutPromise = new Promise(resolve => setTimeout(resolve, options.timeout ?? 10000));
  const page = await Promise.race([pages.get(url), timeoutPromise]);

  // Once the page is fetched, the destination URL could have changed (e.g.,
  // by clicking another link in the meantime). If so, bail out, and let the
  // newer execution to update the HTML.
  if (navigatingTo !== href) return;
  if (page) {
    renderRegions(page);
    window.history[options.replace ? 'replaceState' : 'pushState']({}, '', href);
  } else {
    window.location.assign(href);
    await new Promise(() => {});
  }
};

// Listen to the back and forward buttons and restore the page if it's in the
// cache.
window.addEventListener('popstate', async () => {
  const url = cleanUrl(window.location); // Remove hash.
  const page = pages.has(url) && (await pages.get(url));
  if (page) {
    renderRegions(page);
  } else {
    window.location.reload();
  }
});

// Initialize the router with the initial DOM.
const init = async () => {
  document.querySelectorAll(`[data-${directivePrefix}-interactive]`).forEach(node => {
    if (!hydratedIslands.has(node)) {
      const fragment = getRegionRootFragment(node);
      const vdom = toVdom(node);
      D(vdom, fragment);
    }
  });

  // Cache the current regions.
  pages.set(cleanUrl(window.location), Promise.resolve(regionsToVdom(document)));
};
;// CONCATENATED MODULE: ./packages/interactivity/src/directives.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */








const directives_isObject = item => item && typeof item === 'object' && !Array.isArray(item);
const mergeDeepSignals = (target, source, overwrite) => {
  for (const k in source) {
    if (directives_isObject(deepsignal_module_g(target, k)) && directives_isObject(deepsignal_module_g(source, k))) {
      mergeDeepSignals(target[`$${k}`].peek(), source[`$${k}`].peek(), overwrite);
    } else if (overwrite || typeof deepsignal_module_g(target, k) === 'undefined') {
      target[`$${k}`] = source[`$${k}`];
    }
  }
};
/* harmony default export */ var directives = (() => {
  // data-wp-context
  directive('context', ({
    directives: {
      context
    },
    props: {
      children
    },
    context: inheritedContext
  }) => {
    const {
      Provider
    } = inheritedContext;
    const inheritedValue = hooks_module_q(inheritedContext);
    const currentValue = hooks_module_(deepsignal_module_l({}));
    const passedValues = context.map(({
      value
    }) => value);
    currentValue.current = hooks_module_F(() => {
      const newValue = context.map(c => deepsignal_module_l({
        [c.namespace]: c.value
      })).reduceRight(mergeDeepSignals);
      mergeDeepSignals(newValue, inheritedValue);
      mergeDeepSignals(currentValue.current, newValue, true);
      return currentValue.current;
    }, [inheritedValue, ...passedValues]);
    return jsxRuntime_module_o(Provider, {
      value: currentValue.current,
      children: children
    });
  }, {
    priority: 5
  });

  // data-wp-body
  directive('body', ({
    props: {
      children
    }
  }) => {
    return createPortal(children, document.body);
  });

  // data-wp-watch--[name]
  directive('watch', ({
    directives: {
      watch
    },
    evaluate
  }) => {
    watch.forEach(entry => {
      utils_useSignalEffect(() => evaluate(entry));
    });
  });

  // data-wp-init--[name]
  directive('init', ({
    directives: {
      init
    },
    evaluate
  }) => {
    init.forEach(entry => {
      hooks_module_p(() => evaluate(entry), []);
    });
  });

  // data-wp-on--[event]
  directive('on', ({
    directives: {
      on
    },
    element,
    evaluate
  }) => {
    on.forEach(entry => {
      element.props[`on${entry.suffix}`] = event => {
        evaluate(entry, event);
      };
    });
  });

  // data-wp-class--[classname]
  directive('class', ({
    directives: {
      class: className
    },
    element,
    evaluate
  }) => {
    className.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const name = entry.suffix;
      const result = evaluate(entry, {
        className: name
      });
      const currentClass = element.props.class || '';
      const classFinder = new RegExp(`(^|\\s)${name}(\\s|$)`, 'g');
      if (!result) element.props.class = currentClass.replace(classFinder, ' ').trim();else if (!classFinder.test(currentClass)) element.props.class = currentClass ? `${currentClass} ${name}` : name;
      hooks_module_p(() => {
        // This seems necessary because Preact doesn't change the class
        // names on the hydration, so we have to do it manually. It doesn't
        // need deps because it only needs to do it the first time.
        if (!result) {
          element.ref.current.classList.remove(name);
        } else {
          element.ref.current.classList.add(name);
        }
      }, []);
    });
  });
  const newRule = /(?:([\u0080-\uFFFF\w-%@]+) *:? *([^{;]+?);|([^;}{]*?) *{)|(}\s*)/g;
  const ruleClean = /\/\*[^]*?\*\/|  +/g;
  const ruleNewline = /\n+/g;
  const empty = ' ';

  /**
   * Convert a css style string into a object.
   *
   * Made by Cristian Bote (@cristianbote) for Goober.
   * https://unpkg.com/browse/goober@2.1.13/src/core/astish.js
   *
   * @param {string} val CSS string.
   * @return {Object} CSS object.
   */
  const cssStringToObject = val => {
    const tree = [{}];
    let block, left;
    while (block = newRule.exec(val.replace(ruleClean, ''))) {
      if (block[4]) {
        tree.shift();
      } else if (block[3]) {
        left = block[3].replace(ruleNewline, empty).trim();
        tree.unshift(tree[0][left] = tree[0][left] || {});
      } else {
        tree[0][block[1]] = block[2].replace(ruleNewline, empty).trim();
      }
    }
    return tree[0];
  };

  // data-wp-style--[style-key]
  directive('style', ({
    directives: {
      style
    },
    element,
    evaluate
  }) => {
    style.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const key = entry.suffix;
      const result = evaluate(entry, {
        key
      });
      element.props.style = element.props.style || {};
      if (typeof element.props.style === 'string') element.props.style = cssStringToObject(element.props.style);
      if (!result) delete element.props.style[key];else element.props.style[key] = result;
      hooks_module_p(() => {
        // This seems necessary because Preact doesn't change the styles on
        // the hydration, so we have to do it manually. It doesn't need deps
        // because it only needs to do it the first time.
        if (!result) {
          element.ref.current.style.removeProperty(key);
        } else {
          element.ref.current.style[key] = result;
        }
      }, []);
    });
  });

  // data-wp-bind--[attribute]
  directive('bind', ({
    directives: {
      bind
    },
    element,
    evaluate
  }) => {
    bind.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const attribute = entry.suffix;
      const result = evaluate(entry);
      element.props[attribute] = result;
      // Preact doesn't handle the `role` attribute properly, as it doesn't remove it when `null`.
      // We need this workaround until the following issue is solved:
      // https://github.com/preactjs/preact/issues/4136
      hooks_module_y(() => {
        if (attribute === 'role' && (result === null || result === undefined)) {
          element.ref.current.removeAttribute(attribute);
        }
      }, [attribute, result]);

      // This seems necessary because Preact doesn't change the attributes
      // on the hydration, so we have to do it manually. It doesn't need
      // deps because it only needs to do it the first time.
      hooks_module_p(() => {
        const el = element.ref.current;

        // We set the value directly to the corresponding
        // HTMLElement instance property excluding the following
        // special cases.
        // We follow Preact's logic: https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L110-L129
        if (attribute !== 'width' && attribute !== 'height' && attribute !== 'href' && attribute !== 'list' && attribute !== 'form' &&
        // Default value in browsers is `-1` and an empty string is
        // cast to `0` instead
        attribute !== 'tabIndex' && attribute !== 'download' && attribute !== 'rowSpan' && attribute !== 'colSpan' && attribute !== 'role' && attribute in el) {
          try {
            el[attribute] = result === null || result === undefined ? '' : result;
            return;
          } catch (err) {}
        }
        // aria- and data- attributes have no boolean representation.
        // A `false` value is different from the attribute not being
        // present, so we can't remove it.
        // We follow Preact's logic: https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L131C24-L136
        if (result !== null && result !== undefined && (result !== false || attribute[4] === '-')) {
          el.setAttribute(attribute, result);
        } else {
          el.removeAttribute(attribute);
        }
      }, []);
    });
  });

  // data-wp-navigation-link
  directive('navigation-link', ({
    directives: {
      'navigation-link': navigationLink
    },
    props: {
      href
    },
    element
  }) => {
    const {
      value: link
    } = navigationLink.find(({
      suffix
    }) => suffix === 'default');
    hooks_module_p(() => {
      // Prefetch the page if it is in the directive options.
      if (link?.prefetch) {
        // prefetch( href );
      }
    });

    // Don't do anything if it's falsy.
    if (link !== false) {
      element.props.onclick = async event => {
        event.preventDefault();

        // Fetch the page (or return it from cache).
        await router_navigate(href);

        // Update the scroll, depending on the option. True by default.
        if (link?.scroll === 'smooth') {
          window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
        } else if (link?.scroll !== false) {
          window.scrollTo(0, 0);
        }
      };
    }
  });

  // data-wp-ignore
  directive('ignore', ({
    element: {
      type: Type,
      props: {
        innerHTML,
        ...rest
      }
    }
  }) => {
    // Preserve the initial inner HTML.
    const cached = hooks_module_F(() => innerHTML, []);
    return jsxRuntime_module_o(Type, {
      dangerouslySetInnerHTML: {
        __html: cached
      },
      ...rest
    });
  });

  // data-wp-text
  directive('text', ({
    directives: {
      text
    },
    element,
    evaluate
  }) => {
    const entry = text.find(({
      suffix
    }) => suffix === 'default');
    element.props.children = evaluate(entry);
  });

  // data-wp-slot
  directive('slot', ({
    directives: {
      slot
    },
    props: {
      children
    },
    element
  }) => {
    const {
      value
    } = slot.find(({
      suffix
    }) => suffix === 'default');
    const name = typeof value === 'string' ? value : value.name;
    const position = value.position || 'children';
    if (position === 'before') {
      return jsxRuntime_module_o(_, {
        children: [jsxRuntime_module_o(Slot, {
          name: name
        }), children]
      });
    }
    if (position === 'after') {
      return jsxRuntime_module_o(_, {
        children: [children, jsxRuntime_module_o(Slot, {
          name: name
        })]
      });
    }
    if (position === 'replace') {
      return jsxRuntime_module_o(Slot, {
        name: name,
        children: children
      });
    }
    if (position === 'children') {
      element.props.children = jsxRuntime_module_o(Slot, {
        name: name,
        children: element.props.children
      });
    }
  }, {
    priority: 4
  });

  // data-wp-fill
  directive('fill', ({
    directives: {
      fill
    },
    props: {
      children
    },
    evaluate
  }) => {
    const entry = fill.find(({
      suffix
    }) => suffix === 'default');
    const slot = evaluate(entry);
    return jsxRuntime_module_o(Fill, {
      slot: slot,
      children: children
    });
  }, {
    priority: 4
  });

  // data-wp-slot-provider
  directive('slot-provider', ({
    props: {
      children
    }
  }) => jsxRuntime_module_o(SlotProvider, {
    children: children
  }), {
    priority: 4
  });
});
;// CONCATENATED MODULE: ./packages/interactivity/src/index.js
/**
 * Internal dependencies
 */








document.addEventListener('DOMContentLoaded', async () => {
  directives();
  await init();
});
var __webpack_exports__createElement = __webpack_exports__.az;
var __webpack_exports__deepSignal = __webpack_exports__.Aj;
var __webpack_exports__directive = __webpack_exports__.XM;
var __webpack_exports__getContext = __webpack_exports__.fw;
var __webpack_exports__getElement = __webpack_exports__.sb;
var __webpack_exports__navigate = __webpack_exports__.c4;
var __webpack_exports__prefetch = __webpack_exports__.tL;
var __webpack_exports__store = __webpack_exports__.h;
var __webpack_exports__useContext = __webpack_exports__.qp;
var __webpack_exports__useEffect = __webpack_exports__.d4;
var __webpack_exports__useMemo = __webpack_exports__.Ye;
export { __webpack_exports__createElement as createElement, __webpack_exports__deepSignal as deepSignal, __webpack_exports__directive as directive, __webpack_exports__getContext as getContext, __webpack_exports__getElement as getElement, __webpack_exports__navigate as navigate, __webpack_exports__prefetch as prefetch, __webpack_exports__store as store, __webpack_exports__useContext as useContext, __webpack_exports__useEffect as useEffect, __webpack_exports__useMemo as useMemo };
