/*! Smooth Scroll - v1.4.7 - 2012-10-29
* Copyright (c) 2012 Karl Swedberg; Licensed MIT, GPL */
(function(a){function f(a){return a.replace(/(:|\.)/g,"\\$1")}var b="1.4.7",c={exclude:[],excludeWithin:[],offset:0,direction:"top",scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficent:2},d=function(b){var c=[],d=!1,e=b.dir&&b.dir=="left"?"scrollLeft":"scrollTop";return this.each(function(){if(this==document||this==window)return;var b=a(this);b[e]()>0?c.push(this):(b[e](1),d=b[e]()>0,d&&c.push(this),b[e](0))}),c.length||this.each(function(a){this.nodeName==="BODY"&&(c=[this])}),b.el==="first"&&c.length>1&&(c=[c[0]]),c},e="ontouchend"in document;a.fn.extend({scrollable:function(a){var b=d.call(this,{dir:a});return this.pushStack(b)},firstScrollable:function(a){var b=d.call(this,{el:"first",dir:a});return this.pushStack(b)},smoothScroll:function(b){b=b||{};var c=a.extend({},a.fn.smoothScroll.defaults,b),d=a.smoothScroll.filterPath(location.pathname);return this.unbind("click.smoothscroll").bind("click.smoothscroll",function(b){var e=this,g=a(this),h=c.exclude,i=c.excludeWithin,j=0,k=0,l=!0,m={},n=location.hostname===e.hostname||!e.hostname,o=c.scrollTarget||(a.smoothScroll.filterPath(e.pathname)||d)===d,p=f(e.hash);if(!c.scrollTarget&&(!n||!o||!p))l=!1;else{while(l&&j<h.length)g.is(f(h[j++]))&&(l=!1);while(l&&k<i.length)g.closest(i[k++]).length&&(l=!1)}l&&(b.preventDefault(),a.extend(m,c,{scrollTarget:c.scrollTarget||p,link:e}),a.smoothScroll(m))}),this}}),a.smoothScroll=function(b,c){var d,e,f,g,h=0,i="offset",j="scrollTop",k={},l={},m=[];typeof b=="number"?(d=a.fn.smoothScroll.defaults,f=b):(d=a.extend({},a.fn.smoothScroll.defaults,b||{}),d.scrollElement&&(i="position",d.scrollElement.css("position")=="static"&&d.scrollElement.css("position","relative"))),d=a.extend({link:null},d),j=d.direction=="left"?"scrollLeft":j,d.scrollElement?(e=d.scrollElement,h=e[j]()):e=a("html, body").firstScrollable(),d.beforeScroll.call(e,d),f=typeof b=="number"?b:c||a(d.scrollTarget)[i]()&&a(d.scrollTarget)[i]()[d.direction]||0,k[j]=f+h+d.offset,g=d.speed,g==="auto"&&(g=k[j]||e.scrollTop(),g=g/d.autoCoefficent),l={duration:g,easing:d.easing,complete:function(){d.afterScroll.call(d.link,d)}},d.step&&(l.step=d.step),e.length?e.stop().animate(k,l):d.afterScroll.call(d.link,d)},a.smoothScroll.version=b,a.smoothScroll.filterPath=function(a){return a.replace(/^\//,"").replace(/(index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")},a.fn.smoothScroll.defaults=c})(jQuery);;/** @license Hyphenator 5.1.0 - client side hyphenation for webbrowsers
 *  Copyright (C) 2015  Mathias Nater, Zürich (mathiasnater at gmail dot com)
 *  https://github.com/mnater/Hyphenator
 * 
 *  Released under the MIT license
 *  http://mnater.github.io/Hyphenator/LICENSE.txt
 */

/*
 * Comments are jsdoc3 formatted. See http://usejsdoc.org
 * Use mergeAndPack.html to get rid of the comments and to reduce the file size of this script!
 */

/* The following comment is for JSLint: */
/*jslint browser: true */

/**
 * @desc Provides all functionality to do hyphenation, except the patterns that are loaded externally
 * @global
 * @namespace Hyphenator
 * @author Mathias Nater, <mathias@mnn.ch>
 * @version 5.1.0
 * @example
 * &lt;script src = "Hyphenator.js" type = "text/javascript"&gt;&lt;/script&gt;
 * &lt;script type = "text/javascript"&gt;
 *   Hyphenator.run();
 * &lt;/script&gt;
 */
var Hyphenator = (function (window) {
    'use strict';

        /**
         * @member Hyphenator~contextWindow
         * @access private
         * @desc
         * contextWindow stores the window for the actual document to be hyphenated.
         * If there are frames this will change.
         * So use contextWindow instead of window!
         */
    var contextWindow = window,


        /**
         * @member {Object.<string, Hyphenator~supportedLangs~supportedLanguage>} Hyphenator~supportedLangs
         * @desc
         * A generated key-value object that stores supported languages and meta data.
         * The key is the {@link http://tools.ietf.org/rfc/bcp/bcp47.txt bcp47} code of the language and the value
         * is an object of type {@link Hyphenator~supportedLangs~supportedLanguage}
         * @namespace Hyphenator~supportedLangs
         * @access private
         * //Check if language lang is supported:
         * if (supportedLangs.hasOwnProperty(lang))
         */
        supportedLangs = (function () {
            /**
             * @typedef {Object} Hyphenator~supportedLangs~supportedLanguage
             * @property {string} file - The name of the pattern file
             * @property {number} script - The script type of the language (e.g. 'latin' for english), this type is abbreviated by an id
             * @property {string} prompt - The sentence prompted to the user, if Hyphenator.js doesn't find a language hint
             */

            /**
             * @lends Hyphenator~supportedLangs
             */
            var r = {},
                /**
                 * @method Hyphenator~supportedLangs~o
                 * @desc
                 * Sets a value of Hyphenator~supportedLangs
                 * @access protected
                 * @param {string} code The {@link http://tools.ietf.org/rfc/bcp/bcp47.txt bcp47} code of the language
                 * @param {string} file The name of the pattern file
                 * @param {Number} script A shortcut for a specific script: latin:0, cyrillic: 1, arabic: 2, armenian:3, bengali: 4, devangari: 5, greek: 6
                 * gujarati: 7, kannada: 8, lao: 9, malayalam: 10, oriya: 11, persian: 12, punjabi: 13, tamil: 14, telugu: 15
                 * @param {string} prompt The sentence prompted to the user, if Hyphenator.js doesn't find a language hint
                 */
                o = function (code, file, script, prompt) {
                    r[code] = {'file': file, 'script': script, 'prompt': prompt};
                };

            o('be', 'be.js', 1, 'Мова гэтага сайта не можа быць вызначаны аўтаматычна. Калі ласка пакажыце мову:');
            o('ca', 'ca.js', 0, '');
            o('cs', 'cs.js', 0, 'Jazyk této internetové stránky nebyl automaticky rozpoznán. Určete prosím její jazyk:');
            o('da', 'da.js', 0, 'Denne websides sprog kunne ikke bestemmes. Angiv venligst sprog:');
            o('bn', 'bn.js', 4, '');
            o('de', 'de.js', 0, 'Die Sprache dieser Webseite konnte nicht automatisch bestimmt werden. Bitte Sprache angeben:');
            o('el', 'el-monoton.js', 6, '');
            o('el-monoton', 'el-monoton.js', 6, '');
            o('el-polyton', 'el-polyton.js', 6, '');
            o('en', 'en-us.js', 0, 'The language of this website could not be determined automatically. Please indicate the main language:');
            o('en-gb', 'en-gb.js', 0, 'The language of this website could not be determined automatically. Please indicate the main language:');
            o('en-us', 'en-us.js', 0, 'The language of this website could not be determined automatically. Please indicate the main language:');
            o('eo', 'eo.js', 0, 'La lingvo de ĉi tiu retpaĝo ne rekoneblas aŭtomate. Bonvolu indiki ĝian ĉeflingvon:');
            o('es', 'es.js', 0, 'El idioma del sitio no pudo determinarse autom%E1ticamente. Por favor, indique el idioma principal:');
            o('et', 'et.js', 0, 'Veebilehe keele tuvastamine ebaõnnestus, palun valige kasutatud keel:');
            o('fi', 'fi.js', 0, 'Sivun kielt%E4 ei tunnistettu automaattisesti. M%E4%E4rit%E4 sivun p%E4%E4kieli:');
            o('fr', 'fr.js', 0, 'La langue de ce site n%u2019a pas pu %EAtre d%E9termin%E9e automatiquement. Veuillez indiquer une langue, s.v.p.%A0:');
            o('grc', 'grc.js', 6, '');
            o('gu', 'gu.js', 7, '');
            o('hi', 'hi.js', 5, '');
            o('hu', 'hu.js', 0, 'A weboldal nyelvét nem sikerült automatikusan megállapítani. Kérem adja meg a nyelvet:');
            o('hy', 'hy.js', 3, 'Չհաջողվեց հայտնաբերել այս կայքի լեզուն։ Խնդրում ենք նշեք հիմնական լեզուն՝');
            o('it', 'it.js', 0, 'Lingua del sito sconosciuta. Indicare una lingua, per favore:');
            o('kn', 'kn.js', 8, 'ಜಾಲ ತಾಣದ ಭಾಷೆಯನ್ನು ನಿರ್ಧರಿಸಲು ಸಾಧ್ಯವಾಗುತ್ತಿಲ್ಲ. ದಯವಿಟ್ಟು ಮುಖ್ಯ ಭಾಷೆಯನ್ನು ಸೂಚಿಸಿ:');
            o('la', 'la.js', 0, '');
            o('lt', 'lt.js', 0, 'Nepavyko automatiškai nustatyti šios svetainės kalbos. Prašome įvesti kalbą:');
            o('lv', 'lv.js', 0, 'Šīs lapas valodu nevarēja noteikt automātiski. Lūdzu norādiet pamata valodu:');
            o('ml', 'ml.js', 10, 'ഈ വെ%u0D2C%u0D4D%u200Cസൈറ്റിന്റെ ഭാഷ കണ്ടുപിടിയ്ക്കാ%u0D28%u0D4D%u200D കഴിഞ്ഞില്ല. ഭാഷ ഏതാണെന്നു തിരഞ്ഞെടുക്കുക:');
            o('nb', 'nb-no.js', 0, 'Nettstedets språk kunne ikke finnes automatisk. Vennligst oppgi språk:');
            o('no', 'nb-no.js', 0, 'Nettstedets språk kunne ikke finnes automatisk. Vennligst oppgi språk:');
            o('nb-no', 'nb-no.js', 0, 'Nettstedets språk kunne ikke finnes automatisk. Vennligst oppgi språk:');
            o('nl', 'nl.js', 0, 'De taal van deze website kan niet automatisch worden bepaald. Geef de hoofdtaal op:');
            o('or', 'or.js', 11, '');
            o('pa', 'pa.js', 13, '');
            o('pl', 'pl.js', 0, 'Języka tej strony nie można ustalić automatycznie. Proszę wskazać język:');
            o('pt', 'pt.js', 0, 'A língua deste site não pôde ser determinada automaticamente. Por favor indique a língua principal:');
            o('ru', 'ru.js', 1, 'Язык этого сайта не может быть определен автоматически. Пожалуйста укажите язык:');
            o('sk', 'sk.js', 0, '');
            o('sl', 'sl.js', 0, 'Jezika te spletne strani ni bilo mogoče samodejno določiti. Prosim navedite jezik:');
            o('sr-cyrl', 'sr-cyrl.js', 1, 'Језик овог сајта није детектован аутоматски. Молим вас наведите језик:');
            o('sr-latn', 'sr-latn.js', 0, 'Jezika te spletne strani ni bilo mogoče samodejno določiti. Prosim navedite jezik:');
            o('sv', 'sv.js', 0, 'Spr%E5ket p%E5 den h%E4r webbplatsen kunde inte avg%F6ras automatiskt. V%E4nligen ange:');
            o('ta', 'ta.js', 14, '');
            o('te', 'te.js', 15, '');
            o('tr', 'tr.js', 0, 'Bu web sitesinin dili otomatik olarak tespit edilememiştir. Lütfen dökümanın dilini seçiniz%A0:');
            o('uk', 'uk.js', 1, 'Мова цього веб-сайту не може бути визначена автоматично. Будь ласка, вкажіть головну мову:');
            o('ro', 'ro.js', 0, 'Limba acestui sit nu a putut fi determinată automat. Alege limba principală:');

            return r;
        }()),


        /**
         * @member {string} Hyphenator~basePath
         * @desc
         * A string storing the basepath from where Hyphenator.js was loaded.
         * This is used to load the pattern files.
         * The basepath is determined dynamically by searching all script-tags for Hyphenator.js
         * If the path cannot be determined {@link http://mnater.github.io/Hyphenator/} is used as fallback.
         * @access private
         * @see {@link Hyphenator~loadPatterns}
         */
        basePath = (function () {
            var s = contextWindow.document.getElementsByTagName('script'), i = 0, p, src, t = s[i], r = '';
            while (!!t) {
                if (!!t.src) {
                    src = t.src;
                    p = src.indexOf('Hyphenator.js');
                    if (p !== -1) {
                        r = src.substring(0, p);
                    }
                }
                i += 1;
                t = s[i];
            }
            return !!r ? r : '//mnater.github.io/Hyphenator/';
        }()),

        /**
         * @member {boolean} Hyphenator~isLocal
         * @access private
         * @desc
         * isLocal is true, if Hyphenator is loaded from the same domain, as the webpage, but false, if
         * it's loaded from an external source (i.e. directly from github)
         */
        isLocal = (function () {
            var re = false;
            if (window.location.href.indexOf(basePath) !== -1) {
                re = true;
            }
            return re;
        }()),

        /**
         * @member {boolean} Hyphenator~documentLoaded
         * @access private
         * @desc
         * documentLoaded is true, when the DOM has been loaded. This is set by {@link Hyphenator~runWhenLoaded}
         */
        documentLoaded = false,

        /**
         * @member {boolean} Hyphenator~persistentConfig
         * @access private
         * @desc
         * if persistentConfig is set to true (defaults to false), config options and the state of the 
         * toggleBox are stored in DOM-storage (according to the storage-setting). So they haven't to be
         * set for each page.
         * @default false
         * @see {@link Hyphenator.config}
         */
        persistentConfig = false,

        /**
         * @member {boolean} Hyphenator~doFrames
         * @access private
         * @desc
         * switch to control if frames/iframes should be hyphenated, too.
         * defaults to false (frames are a bag of hurt!)
         * @default false
         * @see {@link Hyphenator.config}
         */
        doFrames = false,

        /**
         * @member {Object.<string,boolean>} Hyphenator~dontHyphenate
         * @desc
         * A key-value object containing all html-tags whose content should not be hyphenated
         * @access private
         */
        dontHyphenate = {'video': true, 'audio': true, 'script': true, 'code': true, 'pre': true, 'img': true, 'br': true, 'samp': true, 'kbd': true, 'var': true, 'abbr': true, 'acronym': true, 'sub': true, 'sup': true, 'button': true, 'option': true, 'label': true, 'textarea': true, 'input': true, 'math': true, 'svg': true, 'style': true},

        /**
         * @member {boolean} Hyphenator~enableCache
         * @desc
         * A variable to set if caching is enabled or not
         * @default true
         * @access private
         * @see {@link Hyphenator.config}
         */
        enableCache = true,

        /**
         * @member {string} Hyphenator~storageType
         * @desc
         * A variable to define what html5-DOM-Storage-Method is used ('none', 'local' or 'session')
         * @default 'local'
         * @access private
         * @see {@link Hyphenator.config}
         */
        storageType = 'local',

        /**
         * @member {Object|undefined} Hyphenator~storage
         * @desc
         * An alias to the storage defined in storageType. This is set by {@link Hyphenator~createStorage}.
         * Set by {@link Hyphenator.run}
         * @default null
         * @access private
         * @see {@link Hyphenator~createStorage}
         */
        storage,

        /**
         * @member {boolean} Hyphenator~enableReducedPatternSet
         * @desc
         * A variable to set if storing the used patterns is set
         * @default false
         * @access private
         * @see {@link Hyphenator.config}
         * @see {@link Hyphenator.getRedPatternSet}
         */
        enableReducedPatternSet = false,

        /**
         * @member {boolean} Hyphenator~enableRemoteLoading
         * @desc
         * A variable to set if pattern files should be loaded remotely or not
         * @default true
         * @access private
         * @see {@link Hyphenator.config}
         */
        enableRemoteLoading = true,

        /**
         * @member {boolean} Hyphenator~displayToggleBox
         * @desc
         * A variable to set if the togglebox should be displayed or not
         * @default false
         * @access private
         * @see {@link Hyphenator.config}
         */
        displayToggleBox = false,

        /**
         * @method Hyphenator~onError
         * @desc
         * A function that can be called upon an error.
         * @see {@link Hyphenator.config}
         * @access private
         */
        onError = function (e) {
            window.alert("Hyphenator.js says:\n\nAn Error occurred:\n" + e.message);
        },

        /**
         * @method Hyphenator~onWarning
         * @desc
         * A function that can be called upon a warning.
         * @see {@link Hyphenator.config}
         * @access private
         */
        onWarning = function (e) {
            window.console.log(e.message);
        },

        /**
         * @method Hyphenator~createElem
         * @desc
         * A function alias to document.createElementNS or document.createElement
         * @access private
         */
        createElem = function (tagname, context) {
            context = context || contextWindow;
            var el;
            if (window.document.createElementNS) {
                el = context.document.createElementNS('http://www.w3.org/1999/xhtml', tagname);
            } else if (window.document.createElement) {
                el = context.document.createElement(tagname);
            }
            return el;
        },

        /**
         * @member {boolean} Hyphenator~css3
         * @desc
         * A variable to set if css3 hyphenation should be used
         * @default false
         * @access private
         * @see {@link Hyphenator.config}
         */
        css3 = false,

        /**
         * @typedef {Object} Hyphenator~css3_hsupport
         * @property {boolean} support - if css3-hyphenation is supported
         * @property {string} property - the css property name to access hyphen-settings (e.g. -webkit-hyphens)
         * @property {Object.<string, boolean>} supportedBrowserLangs - an object caching tested languages
         * @property {function} checkLangSupport - a method that checks if the browser supports a requested language
         */

        /**
         * @member {Hyphenator~css3_h9n} Hyphenator~css3_h9n
         * @desc
         * A generated object containing information for CSS3-hyphenation support
         * This is set by {@link Hyphenator~css3_gethsupport}
         * @default undefined
         * @access private
         * @see {@link Hyphenator~css3_gethsupport}
         * @example
         * //Check if browser supports a language
         * css3_h9n.checkLangSupport(&lt;lang&gt;)
         */
        css3_h9n,

        /**
         * @method Hyphenator~css3_gethsupport
         * @desc
         * This function sets {@link Hyphenator~css3_h9n} for the current UA
         * @type function
         * @access private
         * @see Hyphenator~css3_h9n
         */
        css3_gethsupport = function () {
            var s,
                createLangSupportChecker = function (prefix) {
                    var testStrings = [
                        //latin: 0
                        'aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz',
                        //cyrillic: 1
                        'абвгдеёжзийклмнопрстуфхцчшщъыьэюя',
                        //arabic: 2
                        'أبتثجحخدذرزسشصضطظعغفقكلمنهوي',
                        //armenian: 3
                        'աբգդեզէըթժիլխծկհձղճմյնշոչպջռսվտրցւփքօֆ',
                        //bengali: 4
                        'ঁংঃঅআইঈউঊঋঌএঐওঔকখগঘঙচছজঝঞটঠডঢণতথদধনপফবভমযরলশষসহ়ঽািীুূৃৄেৈোৌ্ৎৗড়ঢ়য়ৠৡৢৣ',
                        //devangari: 5
                        'ँंःअआइईउऊऋऌएऐओऔकखगघङचछजझञटठडढणतथदधनपफबभमयरलळवशषसहऽािीुूृॄेैोौ्॒॑ॠॡॢॣ',
                        //greek: 6
                        'αβγδεζηθικλμνξοπρσςτυφχψω',
                        //gujarati: 7
                        'બહઅઆઇઈઉઊઋૠએઐઓઔાિીુૂૃૄૢૣેૈોૌકખગઘઙચછજઝઞટઠડઢણતથદધનપફસભમયરલળવશષ',
                        //kannada: 8
                        'ಂಃಅಆಇಈಉಊಋಌಎಏಐಒಓಔಕಖಗಘಙಚಛಜಝಞಟಠಡಢಣತಥದಧನಪಫಬಭಮಯರಱಲಳವಶಷಸಹಽಾಿೀುೂೃೄೆೇೈೊೋೌ್ೕೖೞೠೡ',
                        //lao: 9
                        'ກຂຄງຈຊຍດຕຖທນບປຜຝພຟມຢຣລວສຫອຮະັາິີຶືຸູົຼເແໂໃໄ່້໊໋ໜໝ',
                        //malayalam: 10
                        'ംഃഅആഇഈഉഊഋഌഎഏഐഒഓഔകഖഗഘങചഛജഝഞടഠഡഢണതഥദധനപഫബഭമയരറലളഴവശഷസഹാിീുൂൃെേൈൊോൌ്ൗൠൡൺൻർൽൾൿ',
                        //oriya: 11
                        'ଁଂଃଅଆଇଈଉଊଋଌଏଐଓଔକଖଗଘଙଚଛଜଝଞଟଠଡଢଣତଥଦଧନପଫବଭମଯରଲଳଵଶଷସହାିୀୁୂୃେୈୋୌ୍ୗୠୡ',
                        //persian: 12
                        'أبتثجحخدذرزسشصضطظعغفقكلمنهوي',
                        //punjabi: 13
                        'ਁਂਃਅਆਇਈਉਊਏਐਓਔਕਖਗਘਙਚਛਜਝਞਟਠਡਢਣਤਥਦਧਨਪਫਬਭਮਯਰਲਲ਼ਵਸ਼ਸਹਾਿੀੁੂੇੈੋੌ੍ੰੱ',
                        //tamil: 14
                        'ஃஅஆஇஈஉஊஎஏஐஒஓஔகஙசஜஞடணதநனபமயரறலளழவஷஸஹாிீுூெேைொோௌ்ௗ',
                        //telugu: 15
                        'ఁంఃఅఆఇఈఉఊఋఌఎఏఐఒఓఔకఖగఘఙచఛజఝఞటఠడఢణతథదధనపఫబభమయరఱలళవశషసహాిీుూృౄెేైొోౌ్ౕౖౠౡ'
                    ],
                        f = function (lang) {
                            var shadow,
                                computedHeight,
                                bdy,
                                r = false;

                            //check if lang has already been tested
                            if (this.supportedBrowserLangs.hasOwnProperty(lang)) {
                                r = this.supportedBrowserLangs[lang];
                            } else if (supportedLangs.hasOwnProperty(lang)) {
                                //create and append shadow-test-element
                                bdy = window.document.getElementsByTagName('body')[0];
                                shadow = createElem('div', window);
                                shadow.id = 'Hyphenator_LanguageChecker';
                                shadow.style.width = '5em';
                                shadow.style[prefix] = 'auto';
                                shadow.style.hyphens = 'auto';
                                shadow.style.fontSize = '12px';
                                shadow.style.lineHeight = '12px';
                                shadow.style.visibility = 'hidden';
                                shadow.lang = lang;
                                shadow.style['-webkit-locale'] = "'" + lang + "'";
                                shadow.innerHTML = testStrings[supportedLangs[lang].script];
                                bdy.appendChild(shadow);
                                //measure its height
                                computedHeight = shadow.offsetHeight;
                                //remove shadow element
                                bdy.removeChild(shadow);
                                r = (computedHeight > 12) ? true : false;
                                this.supportedBrowserLangs[lang] = r;
                            } else {
                                r = false;
                            }
                            return r;
                        };
                    return f;
                },
                r = {
                    support: false,
                    supportedBrowserLangs: {},
                    property: '',
                    checkLangSupport: {}
                };

            if (window.getComputedStyle) {
                s = window.getComputedStyle(window.document.getElementsByTagName('body')[0], null);
            } else {
                //ancient Browsers don't support CSS3 anyway
                css3_h9n = r;
                return;
            }

            if (s.hyphens !== undefined) {
                r.support = true;
                r.property = 'hyphens';
                r.checkLangSupport = createLangSupportChecker('hyphens');
            } else if (s['-webkit-hyphens'] !== undefined) {
                r.support = true;
                r.property = '-webkit-hyphens';
                r.checkLangSupport = createLangSupportChecker('-webkit-hyphens');
            } else if (s.MozHyphens !== undefined) {
                r.support = true;
                r.property = '-moz-hyphens';
                r.checkLangSupport = createLangSupportChecker('MozHyphens');
            } else if (s['-ms-hyphens'] !== undefined) {
                r.support = true;
                r.property = '-ms-hyphens';
                r.checkLangSupport = createLangSupportChecker('-ms-hyphens');
            }
            css3_h9n = r;
        },

        /**
         * @member {string} Hyphenator~hyphenateClass
         * @desc
         * A string containing the css-class-name for the hyphenate class
         * @default 'hyphenate'
         * @access private
         * @example
         * &lt;p class = "hyphenate"&gt;Text&lt;/p&gt;
         * @see {@link Hyphenator.config}
         */
        hyphenateClass = 'hyphenate',

        /**
         * @member {string} Hyphenator~urlHyphenateClass
         * @desc
         * A string containing the css-class-name for the urlhyphenate class
         * @default 'urlhyphenate'
         * @access private
         * @example
         * &lt;p class = "urlhyphenate"&gt;Text&lt;/p&gt;
         * @see {@link Hyphenator.config}
         */
        urlHyphenateClass = 'urlhyphenate',

        /**
         * @member {string} Hyphenator~classPrefix
         * @desc
         * A string containing a unique className prefix to be used
         * whenever Hyphenator sets a CSS-class
         * @access private
         */
        classPrefix = 'Hyphenator' + Math.round(Math.random() * 1000),

        /**
         * @member {string} Hyphenator~hideClass
         * @desc
         * The name of the class that hides elements
         * @access private
         */
        hideClass = classPrefix + 'hide',

        /**
         * @member {RegExp} Hyphenator~hideClassRegExp
         * @desc
         * RegExp to remove hideClass from a list of classes
         * @access private
         */
        hideClassRegExp = new RegExp("\\s?\\b" + hideClass + "\\b", "g"),

        /**
         * @member {string} Hyphenator~hideClass
         * @desc
         * The name of the class that unhides elements
         * @access private
         */
        unhideClass = classPrefix + 'unhide',

        /**
         * @member {RegExp} Hyphenator~hideClassRegExp
         * @desc
         * RegExp to remove unhideClass from a list of classes
         * @access private
         */
        unhideClassRegExp = new RegExp("\\s?\\b" + unhideClass + "\\b", "g"),

        /**
         * @member {string} Hyphenator~css3hyphenateClass
         * @desc
         * The name of the class that hyphenates elements with css3
         * @access private
         */
        css3hyphenateClass = classPrefix + 'css3hyphenate',

        /**
         * @member {CSSEdit} Hyphenator~css3hyphenateClass
         * @desc
         * The var where CSSEdit class is stored
         * @access private
         */
        css3hyphenateClassHandle,

        /**
         * @member {string} Hyphenator~dontHyphenateClass
         * @desc
         * A string containing the css-class-name for elements that should not be hyphenated
         * @default 'donthyphenate'
         * @access private
         * @example
         * &lt;p class = "donthyphenate"&gt;Text&lt;/p&gt;
         * @see {@link Hyphenator.config}
         */
        dontHyphenateClass = 'donthyphenate',

        /**
         * @member {number} Hyphenator~min
         * @desc
         * A number wich indicates the minimal length of words to hyphenate.
         * @default 6
         * @access private
         * @see {@link Hyphenator.config}
         */
        min = 6,

        /**
         * @member {number} Hyphenator~orphanControl
         * @desc
         * Control how the last words of a line are handled:
         * level 1 (default): last word is hyphenated
         * level 2: last word is not hyphenated
         * level 3: last word is not hyphenated and last space is non breaking
         * @default 1
         * @access private
         */
        orphanControl = 1,

        /**
         * @member {boolean} Hyphenator~isBookmarklet
         * @desc
         * True if Hyphanetor runs as bookmarklet.
         * @access private
         */
        isBookmarklet = (function () {
            var loc = null,
                re = false,
                scripts = contextWindow.document.getElementsByTagName('script'),
                i = 0,
                l = scripts.length;
            while (!re && i < l) {
                loc = scripts[i].getAttribute('src');
                if (!!loc && loc.indexOf('Hyphenator.js?bm=true') !== -1) {
                    re = true;
                }
                i += 1;
            }
            return re;
        }()),

        /**
         * @member {string|null} Hyphenator~mainLanguage
         * @desc
         * The general language of the document. In contrast to {@link Hyphenator~defaultLanguage},
         * mainLanguage is defined by the client (i.e. by the html or by a prompt).
         * @access private
         * @see {@link Hyphenator~autoSetMainLanguage}
         */
        mainLanguage = null,

        /**
         * @member {string|null} Hyphenator~defaultLanguage
         * @desc
         * The language defined by the developper. This language setting is defined by a config option.
         * It is overwritten by any html-lang-attribute and only taken in count, when no such attribute can
         * be found (i.e. just before the prompt).
         * @access private
         * @see {@link Hyphenator.config}
         * @see {@link Hyphenator~autoSetMainLanguage}
         */
        defaultLanguage = '',

        /**
         * @member {ElementCollection} Hyphenator~elements
         * @desc
         * A class representing all elements (of type Element) that have to be hyphenated. This var is filled by
         * {@link Hyphenator~gatherDocumentInfos}
         * @access private
         */
        elements = (function () {
            /**
             * @constructor Hyphenator~elements~ElementCollection~Element
             * @desc represents a DOM Element with additional information
             * @access private
             */
            var Element = function (element) {
                /**
                 * @member {Object} Hyphenator~elements~ElementCollection~Element~element
                 * @desc A DOM Element
                 * @access protected
                 */
                this.element = element;
                /**
                 * @member {boolean} Hyphenator~elements~ElementCollection~Element~hyphenated
                 * @desc Marks if the element has been hyphenated
                 * @access protected
                 */
                this.hyphenated = false;
                /**
                 * @member {boolean} Hyphenator~elements~ElementCollection~Element~treated
                 * @desc Marks if information of the element has been collected but not hyphenated (e.g. dohyphenation is off)
                 * @access protected
                 */
                this.treated = false;
            },
                /**
                 * @constructor Hyphenator~elements~ElementCollection
                 * @desc A collection of Elements to be hyphenated
                 * @access protected
                 */
                ElementCollection = function () {
                    /**
                     * @member {number} Hyphenator~elements~ElementCollection~count
                     * @desc The Number of collected Elements
                     * @access protected
                     */
                    this.count = 0;
                    /**
                     * @member {number} Hyphenator~elements~ElementCollection~hyCount
                     * @desc The Number of hyphenated Elements
                     * @access protected
                     */
                    this.hyCount = 0;
                    /**
                     * @member {Object.<string, Array.<Element>>} Hyphenator~elements~ElementCollection~list
                     * @desc The collection of elements, where the key is a language code and the value is an array of elements
                     * @access protected
                     */
                    this.list = {};
                };
            /**
             * @member {Object} Hyphenator~elements~ElementCollection.prototype
             * @augments Hyphenator~elements~ElementCollection
             * @access protected
             */
            ElementCollection.prototype = {
                /**
                 * @method Hyphenator~elements~ElementCollection.prototype~add
                 * @augments Hyphenator~elements~ElementCollection
                 * @access protected
                 * @desc adds a DOM element to the collection
                 * @param {Object} el - The DOM element
                 * @param {string} lang - The language of the element
                 */
                add: function (el, lang) {
                    var elo = new Element(el);
                    if (!this.list.hasOwnProperty(lang)) {
                        this.list[lang] = [];
                    }
                    this.list[lang].push(elo);
                    this.count += 1;
                    return elo;
                },

                /**
                 * @method Hyphenator~elements~ElementCollection.prototype~remove
                 * @augments Hyphenator~elements~ElementCollection
                 * @access protected
                 * @desc removes a DOM element from the collection
                 * @param {Object} el - The DOM element
                 */
                remove: function (el) {
                    var lang, i, e, l;
                    for (lang in this.list) {
                        if (this.list.hasOwnProperty(lang)) {
                            for (i = 0; i < this.list[lang].length; i += 1) {
                                if (this.list[lang][i].element === el) {
                                    e = i;
                                    l = lang;
                                    break;
                                }
                            }
                        }
                    }
                    this.list[l].splice(e, 1);
                    this.count -= 1;
                    this.hyCount -= 1;
                },
                /**
                 * @callback Hyphenator~elements~ElementCollection.prototype~each~callback fn - The callback that is executed for each element
                 * @param {string} [k] The key (i.e. language) of the collection
                 * @param {Hyphenator~elements~ElementCollection~Element} element
                 */

                /**
                 * @method Hyphenator~elements~ElementCollection.prototype~each
                 * @augments Hyphenator~elements~ElementCollection
                 * @access protected
                 * @desc takes each element of the collection as an argument of fn
                 * @param {Hyphenator~elements~ElementCollection.prototype~each~callback} fn - A function that takes an element as an argument
                 */
                each: function (fn) {
                    var k;
                    for (k in this.list) {
                        if (this.list.hasOwnProperty(k)) {
                            if (fn.length === 2) {
                                fn(k, this.list[k]);
                            } else {
                                fn(this.list[k]);
                            }
                        }
                    }
                }
            };
            return new ElementCollection();
        }()),


        /**
         * @member {Object.<sting, string>} Hyphenator~exceptions
         * @desc
         * An object containing exceptions as comma separated strings for each language.
         * When the language-objects are loaded, their exceptions are processed, copied here and then deleted.
         * Exceptions can also be set by the user.
         * @see {@link Hyphenator~prepareLanguagesObj}
         * @access private
         */
        exceptions = {},

        /**
         * @member {Object.<string, boolean>} Hyphenator~docLanguages
         * @desc
         * An object holding all languages used in the document. This is filled by
         * {@link Hyphenator~gatherDocumentInfos}
         * @access private
         */
        docLanguages = {},

        /**
         * @member {string} Hyphenator~url
         * @desc
         * A string containing a insane RegularExpression to match URL's
         * @access private
         */
        url = '(?:\\w*:\/\/)?(?:(?:\\w*:)?(?:\\w*)@)?(?:(?:(?:[\\d]{1,3}\\.){3}(?:[\\d]{1,3}))|(?:(?:www\\.|[a-zA-Z]\\.)?[a-zA-Z0-9\\-\\.]+\\.(?:[a-z]{2,4})))(?::\\d*)?(?:\/[\\w#!:\\.?\\+=&%@!\\-]*)*',
        //      protocoll     usr     pwd                    ip               or                          host                 tld        port               path

        /**
         * @member {string} Hyphenator~mail
         * @desc
         * A string containing a RegularExpression to match mail-adresses
         * @access private
         */
        mail = '[\\w-\\.]+@[\\w\\.]+',

        /**
         * @member {string} Hyphenator~zeroWidthSpace
         * @desc
         * A string that holds a char.
         * Depending on the browser, this is the zero with space or an empty string.
         * zeroWidthSpace is used to break URLs
         * @access private
         */
        zeroWidthSpace = (function () {
            var zws, ua = window.navigator.userAgent.toLowerCase();
            zws = String.fromCharCode(8203); //Unicode zero width space
            if (ua.indexOf('msie 6') !== -1) {
                zws = ''; //IE6 doesn't support zws
            }
            if (ua.indexOf('opera') !== -1 && ua.indexOf('version/10.00') !== -1) {
                zws = ''; //opera 10 on XP doesn't support zws
            }
            return zws;
        }()),

        /**
         * @method Hyphenator~onBeforeWordHyphenation
         * @desc
         * This method is called just before a word is hyphenated.
         * It is called with two parameters: the word and its language.
         * The method must return a string (aka the word).
         * @see {@link Hyphenator.config}
         * @access private
         * @param {string} word
         * @param {string} lang
         * @return {string} The word that goes into hyphenation
         */
        onBeforeWordHyphenation = function (word) {
            return word;
        },

        /**
         * @method Hyphenator~onAfterWordHyphenation
         * @desc
         * This method is called for each word after it is hyphenated.
         * Takes the word as a first parameter and its language as a second parameter.
         * Returns a string that will replace the word that has been hyphenated.
         * @see {@link Hyphenator.config}
         * @access private
         * @param {string} word
         * @param {string} lang
         * @return {string} The word that goes into hyphenation
         */
        onAfterWordHyphenation = function (word) {
            return word;
        },

        /**
         * @method Hyphenator~onHyphenationDone
         * @desc
         * A method to be called, when the last element has been hyphenated.
         * If there are frames the method is called for each frame.
         * Therefore the location.href of the contextWindow calling this method is given as a parameter
         * @see {@link Hyphenator.config}
         * @param {string} context
         * @access private
         */
        onHyphenationDone = function (context) {
            return context;
        },

        /**
         * @name Hyphenator~selectorFunction
         * @desc
         * A function set by the user that has to return a HTMLNodeList or array of Elements to be hyphenated.
         * By default this is set to false so we can check if a selectorFunction is set…
         * @see {@link Hyphenator.config}
         * @see {@link Hyphenator~mySelectorFunction}
         * @default false
         * @type {function|boolean}
         * @access private
         */
        selectorFunction = false,

        /**
         * @name Hyphenator~flattenNodeList
         * @desc
         * Takes a nodeList and returns an array with all elements that are not contained by another element in the nodeList
         * By using this function the elements returned by selectElements can be 'flattened'.
         * @see {@link Hyphenator~selectElements}
         * @param {nodeList} nl
         * @return {Array} Array of 'parent'-elements
         * @access private
         */
        flattenNodeList = function (nl) {
            var parentElements = [],
                i = 0,
                j = 0,
                isParent = true;

            parentElements.push(nl[0]); //add the first item, since this is always an parent

            for (i = 1; i < nl.length; i += 1) { //cycle through nodeList
                for (j = 0; j < parentElements.length; j += 1) { //cycle through parentElements
                    if (parentElements[j].contains(nl[i])) {
                        isParent = false;
                        break;
                    }
                }
                if (isParent) {
                    parentElements.push(nl[i]);
                }
                isParent = true;
            }

            return parentElements;
        },

        /**
         * @method Hyphenator~mySelectorFunction
         * @desc
         * A function that returns a HTMLNodeList or array of Elements to be hyphenated.
         * By default it uses the classname ('hyphenate') to select the elements.
         * @access private
         */
        mySelectorFunction = function (hyphenateClass) {
            var tmp, el = [], i, l;
            if (window.document.getElementsByClassName) {
                el = contextWindow.document.getElementsByClassName(hyphenateClass);
            } else if (window.document.querySelectorAll) {
                el = contextWindow.document.querySelectorAll('.' + hyphenateClass);
            } else {
                tmp = contextWindow.document.getElementsByTagName('*');
                l = tmp.length;
                for (i = 0; i < l; i += 1) {
                    if (tmp[i].className.indexOf(hyphenateClass) !== -1 && tmp[i].className.indexOf(dontHyphenateClass) === -1) {
                        el.push(tmp[i]);
                    }
                }
            }
            return el;
        },

        /**
         * @method Hyphenator~selectElements
         * @desc
         * A function that uses either selectorFunction set by the user
         * or the default mySelectorFunction.
         * @access private
         */
        selectElements = function () {
            var elems;
            if (selectorFunction) {
                elems = selectorFunction();
            } else {
                elems = mySelectorFunction(hyphenateClass);
            }
            if (elems.length !== 0) {
                elems = flattenNodeList(elems);
            }
            return elems;
        },

        /**
         * @member {string} Hyphenator~intermediateState
         * @desc
         * The visibility of elements while they are hyphenated:
         * 'visible': unhyphenated text is visible and then redrawn when hyphenated.
         * 'hidden': unhyphenated text is made invisible as soon as possible and made visible after hyphenation.
         * @default 'hidden'
         * @see {@link Hyphenator.config}
         * @access private
         */
        intermediateState = 'hidden',

        /**
         * @member {string} Hyphenator~unhide
         * @desc
         * How hidden elements unhide: either simultaneous (default: 'wait') or progressively.
         * 'wait' makes Hyphenator.js to wait until all elements are hyphenated (one redraw)
         * With 'progressive' Hyphenator.js unhides elements as soon as they are hyphenated.
         * @see {@link Hyphenator.config}
         * @access private
         */
        unhide = 'wait',

        /**
         * @member {Array.<Hyphenator~CSSEdit>} Hyphenator~CSSEditors
         * @desc A container array that holds CSSEdit classes
         * For each window object one CSSEdit class is inserted
         * @access private
         */
        CSSEditors = [],

        /**
         * @constructor Hyphenator~CSSEdit
         * @desc
         * This class handles access and editing of StyleSheets.
         * Thanks to this styles (e.g. hiding and unhiding elements upon hyphenation)
         * can be changed in one place instead for each element.
         * @access private
         */
        CSSEdit = function (w) {
            w = w || window;
            var doc = w.document,
                /**
                 * @member {Object} Hyphenator~CSSEdit~sheet
                 * @desc
                 * A StyleSheet, where Hyphenator can write to.
                 * If no StyleSheet can be found, lets create one. 
                 * @access private
                 */
                sheet = (function () {
                    var i,
                        l = doc.styleSheets.length,
                        s,
                        element,
                        r = false;
                    for (i = 0; i < l; i += 1) {
                        s = doc.styleSheets[i];
                        try {
                            if (!!s.cssRules) {
                                r = s;
                                break;
                            }
                        } catch (ignore) {}
                    }
                    if (r === false) {
                        element = doc.createElement('style');
                        element.type = 'text/css';
                        doc.getElementsByTagName('head')[0].appendChild(element);
                        r = doc.styleSheets[doc.styleSheets.length - 1];
                    }
                    return r;
                }()),

                /**
                 * @typedef {Object} Hyphenator~CSSEdit~changes
                 * @property {Object} sheet - The StyleSheet where the change was made
                 * @property {number} index - The index of the changed rule
                 */

                /**
                 * @member {Array.<changes>} Hyphenator~CSSEdit~changes
                 * @desc
                 * Sets a CSS rule for a specified selector
                 * @access private
                 */
                changes = [],

                /**
                 * @typedef Hyphenator~CSSEdit~rule
                 * @property {number} index - The index of the rule
                 * @property {Object} rule - The style rule
                 */
                /**
                 * @method Hyphenator~CSSEdit~findRule
                 * @desc
                 * Searches the StyleSheets for a given selector and returns an object containing the rule.
                 * If nothing can be found, false is returned.
                 * @param {string} sel 
                 * @return {Hyphenator~CSSEdit~rule|false}
                 * @access private
                 */
                findRule = function (sel) {
                    var s, rule, sheets = w.document.styleSheets, rules, i, j, r = false;
                    for (i = 0; i < sheets.length; i += 1) {
                        s = sheets[i];
                        try { //FF has issues here with external CSS (s.o.p)
                            if (!!s.cssRules) {
                                rules = s.cssRules;
                            } else if (!!s.rules) {
                                // IE < 9
                                rules = s.rules;
                            }
                        } catch (ignore) {}
                        if (!!rules && !!rules.length) {
                            for (j = 0; j < rules.length; j += 1) {
                                rule = rules[j];
                                if (rule.selectorText === sel) {
                                    r = {
                                        index: j,
                                        rule: rule
                                    };
                                }
                            }
                        }
                    }
                    return r;
                },
                /**
                 * @method Hyphenator~CSSEdit~addRule
                 * @desc
                 * Adds a rule to the {@link Hyphenator~CSSEdit~sheet}
                 * @param {string} sel - The selector to be added
                 * @param {string} rulesStr - The rules for the specified selector
                 * @return {number} index of the new rule
                 * @access private
                 */
                addRule = function (sel, rulesStr) {
                    var i, r;
                    if (!!sheet.insertRule) {
                        if (!!sheet.cssRules) {
                            i = sheet.cssRules.length;
                        } else {
                            i = 0;
                        }
                        r = sheet.insertRule(sel + '{' + rulesStr + '}', i);
                    } else if (!!sheet.addRule) {
                        // IE < 9
                        if (!!sheet.rules) {
                            i = sheet.rules.length;
                        } else {
                            i = 0;
                        }
                        sheet.addRule(sel, rulesStr, i);
                        r = i;
                    }
                    return r;
                },
                /**
                 * @method Hyphenator~CSSEdit~removeRule
                 * @desc
                 * Removes a rule with the specified index from the specified sheet
                 * @param {Object} sheet - The style sheet
                 * @param {number} index - the index of the rule
                 * @access private
                 */
                removeRule = function (sheet, index) {
                    if (sheet.deleteRule) {
                        sheet.deleteRule(index);
                    } else {
                        // IE < 9
                        sheet.removeRule(index);
                    }
                };

            return {
                /**
                 * @method Hyphenator~CSSEdit.setRule
                 * @desc
                 * Sets a CSS rule for a specified selector
                 * @access public
                 * @param {string} sel - Selector
                 * @param {string} rulesString - CSS-Rules
                 */
                setRule: function (sel, rulesString) {
                    var i, existingRule, cssText;
                    existingRule = findRule(sel);
                    if (!!existingRule) {
                        if (!!existingRule.rule.cssText) {
                            cssText = existingRule.rule.cssText;
                        } else {
                            // IE < 9
                            cssText = existingRule.rule.style.cssText.toLowerCase();
                        }
                        if (cssText !== sel + ' { ' + rulesString + ' }') {
                            //cssText of the found rule is not uniquely selector + rulesString,
                            if (cssText.indexOf(rulesString) !== -1) {
                                //maybe there are other rules or IE < 9
                                //clear existing def
                                existingRule.rule.style.visibility = '';
                            }
                            //add rule and register for later removal
                            i = addRule(sel, rulesString);
                            changes.push({sheet: sheet, index: i});
                        }
                    } else {
                        i = addRule(sel, rulesString);
                        changes.push({sheet: sheet, index: i});
                    }
                },
                /**
                 * @method Hyphenator~CSSEdit.clearChanges
                 * @desc
                 * Removes all changes Hyphenator has made from the StyleSheets
                 * @access public
                 */
                clearChanges: function () {
                    var change = changes.pop();
                    while (!!change) {
                        removeRule(change.sheet, change.index);
                        change = changes.pop();
                    }
                }
            };
        },

        /**
         * @member {string} Hyphenator~hyphen
         * @desc
         * A string containing the character for in-word-hyphenation
         * @default the soft hyphen
         * @access private
         * @see {@link Hyphenator.config}
         */
        hyphen = String.fromCharCode(173),

        /**
         * @member {string} Hyphenator~urlhyphen
         * @desc
         * A string containing the character for url/mail-hyphenation
         * @default the zero width space
         * @access private
         * @see {@link Hyphenator.config}
         * @see {@link Hyphenator~zeroWidthSpace}
         */
        urlhyphen = zeroWidthSpace,

        /**
         * @method Hyphenator~hyphenateURL
         * @desc
         * Puts {@link Hyphenator~urlhyphen} (default: zero width space) after each no-alphanumeric char that my be in a URL.
         * @param {string} url to hyphenate
         * @returns string the hyphenated URL
         * @access public
         */
        hyphenateURL = function (url) {
            var tmp = url.replace(/([:\/\.\?#&\-_,;!@]+)/gi, '$&' + urlhyphen),
                parts = tmp.split(urlhyphen),
                i;
            for (i = 0; i < parts.length; i += 1) {
                if (parts[i].length > (2 * min)) {
                    parts[i] = parts[i].replace(/(\w{3})(\w)/gi, "$1" + urlhyphen + "$2");
                }
            }
            if (parts[parts.length - 1] === "") {
                parts.pop();
            }
            return parts.join(urlhyphen);
        },

        /**
         * @member {boolean} Hyphenator~safeCopy
         * @desc
         * Defines wether work-around for copy issues is active or not
         * @default true
         * @access private
         * @see {@link Hyphenator.config}
         * @see {@link Hyphenator~registerOnCopy}
         */
        safeCopy = true,

        /**
         * @method Hyphenator~zeroTimeOut
         * @desc
         * defer execution of a function on the call stack
         * Analog to window.setTimeout(fn, 0) but without a clamped delay if postMessage is supported
         * @access private
         * @see {@link http://dbaron.org/log/20100309-faster-timeouts}
         */
        zeroTimeOut = (function () {
            if (window.postMessage && window.addEventListener) {
                return (function () {
                    var timeouts = [],
                        msg = "Hyphenator_zeroTimeOut_message",
                        setZeroTimeOut = function (fn) {
                            timeouts.push(fn);
                            window.postMessage(msg, "*");
                        },
                        handleMessage = function (event) {
                            if (event.source === window && event.data === msg) {
                                event.stopPropagation();
                                if (timeouts.length > 0) {
                                    //var efn = timeouts.shift();
                                    //efn();
                                    timeouts.shift()();
                                }
                            }
                        };
                    window.addEventListener("message", handleMessage, true);
                    return setZeroTimeOut;
                }());
            }
            return function (fn) {
                window.setTimeout(fn, 0);
            };
        }()),

        /**
         * @member {Object} Hyphenator~hyphRunFor
         * @desc
         * stores location.href for documents where run() has been executed
         * to warn when Hyphenator.run() executed multiple times
         * @access private
         * @see {@link Hyphenator~runWhenLoaded}
         */
        hyphRunFor = {},

        /**
         * @method Hyphenator~runWhenLoaded
         * @desc
         * A crossbrowser solution for the DOMContentLoaded-Event based on
         * <a href = "http://jquery.com/">jQuery</a>
         * I added some functionality: e.g. support for frames and iframes…
         * @param {Object} w the window-object
         * @param {function()} f the function to call when the document is ready
         * @access private
         */
        runWhenLoaded = function (w, f) {
            var toplevel,
                add = window.document.addEventListener ? 'addEventListener' : 'attachEvent',
                rem = window.document.addEventListener ? 'removeEventListener' : 'detachEvent',
                pre = window.document.addEventListener ? '' : 'on',

                init = function (context) {
                    if (hyphRunFor[context.location.href]) {
                        onWarning(new Error("Warning: multiple execution of Hyphenator.run() – This may slow down the script!"));
                    }
                    contextWindow = context || window;
                    f();
                    hyphRunFor[contextWindow.location.href] = true;
                },

                doScrollCheck = function () {
                    try {
                        // If IE is used, use the trick by Diego Perini
                        // http://javascript.nwbox.com/IEContentLoaded/
                        w.document.documentElement.doScroll("left");
                    } catch (error) {
                        window.setTimeout(doScrollCheck, 1);
                        return;
                    }
                    //maybe modern IE fired DOMContentLoaded
                    if (!hyphRunFor[w.location.href]) {
                        documentLoaded = true;
                        init(w);
                    }
                },

                doOnEvent = function (e) {
                    var i, fl, haveAccess;
                    if (!!e && e.type === 'readystatechange' && w.document.readyState !== 'interactive' && w.document.readyState !== 'complete') {
                        return;
                    }

                    //DOM is ready/interactive, but frames may not be loaded yet!
                    //cleanup events
                    w.document[rem](pre + 'DOMContentLoaded', doOnEvent, false);
                    w.document[rem](pre + 'readystatechange', doOnEvent, false);

                    //check frames
                    fl = w.frames.length;
                    if (fl === 0 || !doFrames) {
                        //there are no frames!
                        //cleanup events
                        w[rem](pre + 'load', doOnEvent, false);
                        documentLoaded = true;
                        init(w);
                    } else if (doFrames && fl > 0) {
                        //we have frames, so wait for onload and then initiate runWhenLoaded recursevly for each frame:
                        if (!!e && e.type === 'load') {
                            //cleanup events
                            w[rem](pre + 'load', doOnEvent, false);
                            for (i = 0; i < fl; i += 1) {
                                haveAccess = undefined;
                                //try catch isn't enough for webkit
                                try {
                                    //opera throws only on document.toString-access
                                    haveAccess = w.frames[i].document.toString();
                                } catch (err) {
                                    haveAccess = undefined;
                                }
                                if (!!haveAccess) {
                                    runWhenLoaded(w.frames[i], f);
                                }
                            }
                            init(w);
                        }
                    }
                };
            if (documentLoaded || w.document.readyState === 'complete') {
                //Hyphenator has run already (documentLoaded is true) or
                //it has been loaded after onLoad
                documentLoaded = true;
                doOnEvent({type: 'load'});
            } else {
                //register events
                w.document[add](pre + 'DOMContentLoaded', doOnEvent, false);
                w.document[add](pre + 'readystatechange', doOnEvent, false);
                w[add](pre + 'load', doOnEvent, false);
                toplevel = false;
                try {
                    toplevel = !window.frameElement;
                } catch (ignore) {}
                if (toplevel && w.document.documentElement.doScroll) {
                    doScrollCheck(); //calls init()
                }
            }
        },

        /**
         * @method Hyphenator~getLang
         * @desc
         * Gets the language of an element. If no language is set, it may use the {@link Hyphenator~mainLanguage}.
         * @param {Object} el The first parameter is an DOM-Element-Object
         * @param {boolean} fallback The second parameter is a boolean to tell if the function should return the {@link Hyphenator~mainLanguage}
         * if there's no language found for the element.
         * @return {string} The language of the element
         * @access private
         */
        getLang = function (el, fallback) {
            try {
                return !!el.getAttribute('lang') ? el.getAttribute('lang').toLowerCase() :
                        !!el.getAttribute('xml:lang') ? el.getAttribute('xml:lang').toLowerCase() :
                                el.tagName.toLowerCase() !== 'html' ? getLang(el.parentNode, fallback) :
                                        fallback ? mainLanguage :
                                                null;
            } catch (ignore) {}
        },

        /**
         * @method Hyphenator~autoSetMainLanguage
         * @desc
         * Retrieves the language of the document from the DOM and sets the lang attribute of the html-tag.
         * The function looks in the following places:
         * <ul>
         * <li>lang-attribute in the html-tag</li>
         * <li>&lt;meta http-equiv = "content-language" content = "xy" /&gt;</li>
         * <li>&lt;meta name = "DC.Language" content = "xy" /&gt;</li>
         * <li>&lt;meta name = "language" content = "xy" /&gt;</li>
         * </li>
         * If nothing can be found a prompt using {@link Hyphenator~languageHint} and a prompt-string is displayed.
         * If the retrieved language is in the object {@link Hyphenator~supportedLangs} it is copied to {@link Hyphenator~mainLanguage}
         * @access private
         */
        autoSetMainLanguage = function (w) {
            w = w || contextWindow;
            var el = w.document.getElementsByTagName('html')[0],
                m = w.document.getElementsByTagName('meta'),
                i,
                getLangFromUser = function () {
                    var ml,
                        text = '',
                        dH = 300,
                        dW = 450,
                        dX = Math.floor((w.outerWidth - dW) / 2) + window.screenX,
                        dY = Math.floor((w.outerHeight - dH) / 2) + window.screenY,
                        ul = '',
                        languageHint;
                    if (!!window.showModalDialog && (w.location.href.indexOf(basePath) !== -1)) {
                        ml = window.showModalDialog(basePath + 'modalLangDialog.html', supportedLangs, "dialogWidth: " + dW + "px; dialogHeight: " + dH + "px; dialogtop: " + dY + "; dialogleft: " + dX + "; center: on; resizable: off; scroll: off;");
                    } else {
                        languageHint = (function () {
                            var k, r = '';
                            for (k in supportedLangs) {
                                if (supportedLangs.hasOwnProperty(k)) {
                                    r += k + ', ';
                                }
                            }
                            r = r.substring(0, r.length - 2);
                            return r;
                        }());
                        ul = window.navigator.language || window.navigator.userLanguage;
                        ul = ul.substring(0, 2);
                        if (!!supportedLangs[ul] && supportedLangs[ul].prompt !== '') {
                            text = supportedLangs[ul].prompt;
                        } else {
                            text = supportedLangs.en.prompt;
                        }
                        text += ' (ISO 639-1)\n\n' + languageHint;
                        ml = window.prompt(window.unescape(text), ul).toLowerCase();
                    }
                    return ml;
                };
            mainLanguage = getLang(el, false);
            if (!mainLanguage) {
                for (i = 0; i < m.length; i += 1) {
                    //<meta http-equiv = "content-language" content="xy">
                    if (!!m[i].getAttribute('http-equiv') && (m[i].getAttribute('http-equiv').toLowerCase() === 'content-language')) {
                        mainLanguage = m[i].getAttribute('content').toLowerCase();
                    }
                    //<meta name = "DC.Language" content="xy">
                    if (!!m[i].getAttribute('name') && (m[i].getAttribute('name').toLowerCase() === 'dc.language')) {
                        mainLanguage = m[i].getAttribute('content').toLowerCase();
                    }
                    //<meta name = "language" content = "xy">
                    if (!!m[i].getAttribute('name') && (m[i].getAttribute('name').toLowerCase() === 'language')) {
                        mainLanguage = m[i].getAttribute('content').toLowerCase();
                    }
                }
            }
            //get lang for frame from enclosing document
            if (!mainLanguage && doFrames && (!!contextWindow.frameElement)) {
                autoSetMainLanguage(window.parent);
            }
            //fallback to defaultLang if set
            if (!mainLanguage && defaultLanguage !== '') {
                mainLanguage = defaultLanguage;
            }
            //ask user for lang
            if (!mainLanguage) {
                mainLanguage = getLangFromUser();
            }
            el.lang = mainLanguage;
        },

        /**
         * @method Hyphenator~gatherDocumentInfos
         * @desc
         * This method runs through the DOM and executes the process()-function on:
         * - every node returned by the {@link Hyphenator~selectorFunction}.
         * @access private
         */
        gatherDocumentInfos = function () {
            var elToProcess, urlhyphenEls, tmp, i = 0,
                /**
                 * @method Hyphenator~gatherDocumentInfos
                 * @desc
                 * This method copies the element to the elements-variable, sets its visibility
                 * to intermediateState, retrieves its language and recursivly descends the DOM-tree until
                 * the child-Nodes aren't of type 1
                 * @param {Object} el a DOM element
                 * @param {string} plang the language of the parent element
                 * @param {boolean} isChild true, if the parent of el has been processed
                 */
                process = function (el, pLang, isChild) {
                    isChild = isChild || false;
                    var n, j = 0, hyphenate = true, eLang,
                        useCSS3 = function () {
                            css3hyphenateClassHandle =  new CSSEdit(contextWindow);
                            css3hyphenateClassHandle.setRule('.' + css3hyphenateClass, css3_h9n.property + ': auto;');
                            css3hyphenateClassHandle.setRule('.' + dontHyphenateClass, css3_h9n.property + ': manual;');
                            if ((eLang !== pLang) && css3_h9n.property.indexOf('webkit') !== -1) {
                                css3hyphenateClassHandle.setRule('.' + css3hyphenateClass, '-webkit-locale : ' + eLang + ';');
                            }
                            el.className = el.className + ' ' + css3hyphenateClass;
                        },
                        useHyphenator = function () {
                            //quick fix for test111.html
                            //better: weight elements
                            if (isBookmarklet && eLang !== mainLanguage) {
                                return;
                            }
                            if (supportedLangs.hasOwnProperty(eLang)) {
                                docLanguages[eLang] = true;
                            } else {
                                if (supportedLangs.hasOwnProperty(eLang.split('-')[0])) { //try subtag
                                    eLang = eLang.split('-')[0];
                                    docLanguages[eLang] = true;
                                } else if (!isBookmarklet) {
                                    hyphenate = false;
                                    onError(new Error('Language "' + eLang + '" is not yet supported.'));
                                }
                            }
                            if (hyphenate) {
                                if (intermediateState === 'hidden') {
                                    el.className = el.className + ' ' + hideClass;
                                }
                                elements.add(el, eLang);
                            }
                        };

                    if (el.lang && typeof (el.lang) === 'string') {
                        eLang = el.lang.toLowerCase(); //copy attribute-lang to internal eLang
                    } else if (!!pLang && pLang !== '') {
                        eLang = pLang.toLowerCase();
                    } else {
                        eLang = getLang(el, true);
                    }

                    if (!isChild) {
                        if (css3 && css3_h9n.support && !!css3_h9n.checkLangSupport(eLang)) {
                            useCSS3();
                        } else {
                            useHyphenator();
                        }
                    } else {
                        if (eLang !== pLang) {
                            if (css3 && css3_h9n.support && !!css3_h9n.checkLangSupport(eLang)) {
                                useCSS3();
                            } else {
                                useHyphenator();
                            }
                        } else {
                            if (!css3 || !css3_h9n.support || !css3_h9n.checkLangSupport(eLang)) {
                                useHyphenator();
                            } // else do nothing
                        }
                    }
                    n = el.childNodes[j];
                    while (!!n) {
                        if (n.nodeType === 1 && !dontHyphenate[n.nodeName.toLowerCase()] &&
                                n.className.indexOf(dontHyphenateClass) === -1 &&
                                n.className.indexOf(urlHyphenateClass) === -1 && !elToProcess[n]) {
                            process(n, eLang, true);
                        }
                        j += 1;
                        n = el.childNodes[j];
                    }
                },
                processUrlStyled = function (el) {
                    var n, j = 0;

                    n = el.childNodes[j];
                    while (!!n) {
                        if (n.nodeType === 1 && !dontHyphenate[n.nodeName.toLowerCase()] &&
                                n.className.indexOf(dontHyphenateClass) === -1 &&
                                n.className.indexOf(hyphenateClass) === -1 && !urlhyphenEls[n]) {
                            processUrlStyled(n);
                        } else if (n.nodeType === 3) {
                            n.data = hyphenateURL(n.data);
                        }
                        j += 1;
                        n = el.childNodes[j];
                    }
                };

            if (css3) {
                css3_gethsupport();
            }
            if (isBookmarklet) {
                elToProcess = contextWindow.document.getElementsByTagName('body')[0];
                process(elToProcess, mainLanguage, false);
            } else {
                if (!css3 && intermediateState === 'hidden') {
                    CSSEditors.push(new CSSEdit(contextWindow));
                    CSSEditors[CSSEditors.length - 1].setRule('.' + hyphenateClass, 'visibility: hidden;');
                    CSSEditors[CSSEditors.length - 1].setRule('.' + hideClass, 'visibility: hidden;');
                    CSSEditors[CSSEditors.length - 1].setRule('.' + unhideClass, 'visibility: visible;');
                }
                elToProcess = selectElements();
                tmp = elToProcess[i];
                while (!!tmp) {
                    process(tmp, '', false);
                    i += 1;
                    tmp = elToProcess[i];
                }

                urlhyphenEls = mySelectorFunction(urlHyphenateClass);
                i = 0;
                tmp = urlhyphenEls[i];
                while (!!tmp) {
                    processUrlStyled(tmp);
                    i += 1;
                    tmp = urlhyphenEls[i];
                }
            }
            if (elements.count === 0) {
                //nothing to hyphenate or all hyphenated by css3
                for (i = 0; i < CSSEditors.length; i += 1) {
                    CSSEditors[i].clearChanges();
                }
                onHyphenationDone(contextWindow.location.href);
            }
        },

        /**
         * @method Hyphenator~createCharMap
         * @desc
         * reads the charCodes from lo.characters and stores them in a bidi map:
         * charMap.int2code =  [0: 97, //a
         *                      1: 98, //b
         *                      2: 99] //c etc.
         * charMap.code2int = {"97": 0, //a
         *                     "98": 1, //b
         *                     "99": 2} //c etc.
         * @access private
         * @param {Object} language object
         */
        CharMap = function () {
            this.int2code = [];
            this.code2int = {};
            this.add = function (newValue) {
                if (!this.code2int[newValue]) {
                    this.int2code.push(newValue);
                    this.code2int[newValue] = this.int2code.length - 1;
                }
            };
        },

        /**
         * @constructor Hyphenator~ValueStore
         * @desc Storage-Object for storing hyphenation points (aka values)
         * @access private
         */
        ValueStore = function (len) {
            this.keys = (function () {
                var i, r;
                if (Object.prototype.hasOwnProperty.call(window, "Uint8Array")) { //IE<9 doesn't have window.hasOwnProperty (host object)
                    return new window.Uint8Array(len);
                }
                r = [];
                r.length = len;
                for (i = r.length - 1; i >= 0; i -= 1) {
                    r[i] = 0;
                }
                return r;
            }());
            this.startIndex = 1;
            this.actualIndex = 2;
            this.lastValueIndex = 2;
            this.add = function (p) {
                this.keys[this.actualIndex] = p;
                this.lastValueIndex = this.actualIndex;
                this.actualIndex += 1;
            };
            this.add0 = function () {
                //just do a step, since array is initialized with zeroes
                this.actualIndex += 1;
            };
            this.finalize = function () {
                var start = this.startIndex;
                this.keys[start] = this.lastValueIndex - start;
                this.startIndex = this.lastValueIndex + 1;
                this.actualIndex = this.lastValueIndex + 2;
                return start;
            };
        },

        /**
         * @method Hyphenator~convertPatternsToArray
         * @desc
         * converts the patterns to a (typed, if possible) array as described by Liang:
         *
         * 1. Create the CharMap: an alphabet of used character codes mapped to an int (e.g. a: "97" -> 0)
         *    This map is bidirectional:
         *    charMap.code2int is an object with charCodes as keys and corresponging ints as values
         *    charMao.int2code is an array of charCodes at int indizes
         *    the length of charMao.int2code is equal the length of the alphabet
         *
         * 2. Create a ValueStore: (typed) array that holds "values", i.e. the digits extracted from the patterns
         *    The first value starts at index 1 (since the trie is initialized with zeroes, starting at 0 would create errors)
         *    Each value starts with its length at index i, actual values are stored in i + n where n < length
         *    Trailing 0 are not stored. So pattern values like e.g. "010200" will become […,4,0,1,0,2,…]
         *    The ValueStore-Object manages handling of indizes automatically. Use ValueStore.add(p) to add a running value.
         *    Use ValueStore.finalize() when the last value of a pattern is added. It will set the length and return the starting index of the pattern.
         *    To prevent doubles we could temporarly store the values in a object {value: startIndex} and only add new values,
         *    but this object deoptimizes very fast (new hidden map for each entry); here we gain speed and pay memory
         *    
         * 3. Create and zero initialize a (typed) array to store the trie. The trie uses two slots for each entry/node:
         *    i: a link to another position in the array or -1 if the pattern ends here or more rows have to be added.
         *    i + 1: a link to a value in the ValueStore or 0 if there's no value for the path to this node.
         *    Although the array is one-dimensional it can be described as an array of "rows",
         *    where each "row" is an array of length trieRowLength (see below).
         *    The first entry of this "row" represents the first character of the alphabet, the second a possible link to value store,
         *    the third represents the second character of the alphabet and so on…
         *
         * 4. Initialize trieRowLength (length of the alphabet * 2)
         *
         * 5. Now we apply extract to each pattern collection (patterns of the same length are collected and concatenated to one string)
         *    extract goes through these pattern collections char by char and adds them either to the ValueStore (if they are digits) or
         *    to the trie (adding more "rows" if necessary, i.e. if the last link pointed to -1).
         *    So the first "row" holds all starting characters, where the subsequent rows hold the characters that follow the
         *    character that link to this row. Therefor the array is dense at the beginning and very sparse at the end.
         * 
         * 
         * @access private
         * @param {Object} language object
         */
        convertPatternsToArray = function (lo) {
            var trieNextEmptyRow = 0,
                i,
                charMapc2i,
                valueStore,
                indexedTrie,
                trieRowLength,

                extract = function (patternSizeInt, patterns) {
                    var charPos = 0,
                        charCode = 0,
                        mappedCharCode = 0,
                        rowStart = 0,
                        nextRowStart = 0,
                        prevWasDigit = false;
                    for (charPos = 0; charPos < patterns.length; charPos += 1) {
                        charCode = patterns.charCodeAt(charPos);
                        if ((charPos + 1) % patternSizeInt !== 0) {
                            //more to come…
                            if (charCode <= 57 && charCode >= 49) {
                                //charCode is a digit
                                valueStore.add(charCode - 48);
                                prevWasDigit = true;
                            } else {
                                //charCode is alphabetical
                                if (!prevWasDigit) {
                                    valueStore.add0();
                                }
                                prevWasDigit = false;
                                if (nextRowStart === -1) {
                                    nextRowStart = trieNextEmptyRow + trieRowLength;
                                    trieNextEmptyRow = nextRowStart;
                                    indexedTrie[rowStart + mappedCharCode * 2] = nextRowStart;
                                }
                                mappedCharCode = charMapc2i[charCode];
                                rowStart = nextRowStart;
                                nextRowStart = indexedTrie[rowStart + mappedCharCode * 2];
                                if (nextRowStart === 0) {
                                    indexedTrie[rowStart + mappedCharCode * 2] = -1;
                                    nextRowStart = -1;
                                }
                            }
                        } else {
                            //last part of pattern
                            if (charCode <= 57 && charCode >= 49) {
                                //the last charCode is a digit
                                valueStore.add(charCode - 48);
                                indexedTrie[rowStart + mappedCharCode * 2 + 1] = valueStore.finalize();
                            } else {
                                //the last charCode is alphabetical
                                if (!prevWasDigit) {
                                    valueStore.add0();
                                }
                                valueStore.add0();
                                if (nextRowStart === -1) {
                                    nextRowStart = trieNextEmptyRow + trieRowLength;
                                    trieNextEmptyRow = nextRowStart;
                                    indexedTrie[rowStart + mappedCharCode * 2] = nextRowStart;
                                }
                                mappedCharCode = charMapc2i[charCode];
                                rowStart = nextRowStart;
                                if (indexedTrie[rowStart + mappedCharCode * 2] === 0) {
                                    indexedTrie[rowStart + mappedCharCode * 2] = -1;
                                }
                                indexedTrie[rowStart + mappedCharCode * 2 + 1] = valueStore.finalize();
                            }
                            rowStart = 0;
                            nextRowStart = 0;
                            prevWasDigit = false;
                        }
                    }
                };/*,
                prettyPrintIndexedTrie = function (rowLength) {
                    var s = "0: ",
                        idx;
                    for (idx = 0; idx < indexedTrie.length; idx += 1) {
                        s += indexedTrie[idx];
                        s += ",";
                        if ((idx + 1) % rowLength === 0) {
                            s += "\n" + (idx + 1) + ": ";
                        }
                    }
                    console.log(s);
                };*/

            lo.charMap = new CharMap();
            for (i = 0; i < lo.patternChars.length; i += 1) {
                lo.charMap.add(lo.patternChars.charCodeAt(i));
            }
            charMapc2i = lo.charMap.code2int;

            lo.valueStore = valueStore = new ValueStore(lo.valueStoreLength);

            if (Object.prototype.hasOwnProperty.call(window, "Int32Array")) { //IE<9 doesn't have window.hasOwnProperty (host object)
                lo.indexedTrie = new window.Int32Array(lo.patternArrayLength * 2);
            } else {
                lo.indexedTrie = [];
                lo.indexedTrie.length = lo.patternArrayLength * 2;
                for (i = lo.indexedTrie.length - 1; i >= 0; i -= 1) {
                    lo.indexedTrie[i] = 0;
                }
            }
            indexedTrie = lo.indexedTrie;
            trieRowLength = lo.charMap.int2code.length * 2;

            for (i in lo.patterns) {
                if (lo.patterns.hasOwnProperty(i)) {
                    extract(parseInt(i, 10), lo.patterns[i]);
                }
            }
            //prettyPrintIndexedTrie(lo.charMap.int2code.length * 2);
        },

        /**
         * @method Hyphenator~recreatePattern
         * @desc
         * Recreates the pattern for the reducedPatternSet
         * @param {string} pattern The pattern (chars)
         * @param {string} nodePoints The nodePoints (integers)
         * @access private
         * @return {string} The pattern (chars and numbers)
         */
        recreatePattern = function (pattern, nodePoints) {
            var r = [], c = pattern.split(''), i;
            for (i = 0; i <= c.length; i += 1) {
                if (nodePoints[i] && nodePoints[i] !== 0) {
                    r.push(nodePoints[i]);
                }
                if (c[i]) {
                    r.push(c[i]);
                }
            }
            return r.join('');
        },

        /**
         * @method Hyphenator~convertExceptionsToObject
         * @desc
         * Converts a list of comma seprated exceptions to an object:
         * 'Fortran,Hy-phen-a-tion' -> {'Fortran':'Fortran','Hyphenation':'Hy-phen-a-tion'}
         * @access private
         * @param {string} exc a comma separated string of exceptions (without spaces)
         * @return {Object.<string, string>}
         */
        convertExceptionsToObject = function (exc) {
            var w = exc.split(', '),
                r = {},
                i,
                l,
                key;
            for (i = 0, l = w.length; i < l; i += 1) {
                key = w[i].replace(/-/g, '');
                if (!r.hasOwnProperty(key)) {
                    r[key] = w[i];
                }
            }
            return r;
        },

        /**
         * @method Hyphenator~loadPatterns
         * @desc
         * Checks if the requested file is available in the network.
         * Adds a &lt;script&gt;-Tag to the DOM to load an externeal .js-file containing patterns and settings for the given language.
         * If the given language is not in the {@link Hyphenator~supportedLangs}-Object it returns.
         * One may ask why we are not using AJAX to load the patterns. The XMLHttpRequest-Object 
         * has a same-origin-policy. This makes the Bookmarklet impossible.
         * @param {string} lang The language to load the patterns for
         * @access private
         * @see {@link Hyphenator~basePath}
         */
        loadPatterns = function (lang, cb) {
            var location, xhr, head, script, done = false;
            if (supportedLangs.hasOwnProperty(lang) && !Hyphenator.languages[lang]) {
                location = basePath + 'patterns/' + supportedLangs[lang].file;
            } else {
                return;
            }
            if (isLocal && !isBookmarklet) {
                //check if 'location' is available:
                xhr = null;
                try {
                    // Mozilla, Opera, Safari and Internet Explorer (ab v7)
                    xhr = new window.XMLHttpRequest();
                } catch (e) {
                    try {
                        //IE>=6
                        xhr  = new window.ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e2) {
                        try {
                            //IE>=5
                            xhr  = new window.ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e3) {
                            xhr  = null;
                        }
                    }
                }

                if (xhr) {
                    xhr.open('HEAD', location, true);
                    xhr.setRequestHeader('Cache-Control', 'no-cache');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 2) {
                            if (xhr.status >= 400) {
                                onError(new Error('Could not load\n' + location));
                                delete docLanguages[lang];
                                return;
                            }
                            xhr.abort();
                        }
                    };
                    xhr.send(null);
                }
            }
            if (createElem) {
                head = window.document.getElementsByTagName('head').item(0);
                script = createElem('script', window);
                script.src = location;
                script.type = 'text/javascript';
                script.charset = 'utf8';
                script.onload = script.onreadystatechange = function () {
                    if (!done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) {
                        done = true;

                        cb();

                        // Handle memory leak in IE
                        script.onload = script.onreadystatechange = null;
                        if (head && script.parentNode) {
                            head.removeChild(script);
                        }
                    }
                };
                head.appendChild(script);
            }
        },

        /**
         * @method Hyphenator~prepareLanguagesObj
         * @desc
         * Adds some feature to the language object:
         * - cache
         * - exceptions
         * Converts the patterns to a trie using {@link Hyphenator~convertPatterns}
         * @access private
         * @param {string} lang The language of the language object
         */
        prepareLanguagesObj = function (lang) {
            var lo = Hyphenator.languages[lang], wrd;

            if (!lo.prepared) {
                if (enableCache) {
                    lo.cache = {};
                    //Export
                    //lo['cache'] = lo.cache;
                }
                if (enableReducedPatternSet) {
                    lo.redPatSet = {};
                }
                //add exceptions from the pattern file to the local 'exceptions'-obj
                if (lo.hasOwnProperty('exceptions')) {
                    Hyphenator.addExceptions(lang, lo.exceptions);
                    delete lo.exceptions;
                }
                //copy global exceptions to the language specific exceptions
                if (exceptions.hasOwnProperty('global')) {
                    if (exceptions.hasOwnProperty(lang)) {
                        exceptions[lang] += ', ' + exceptions.global;
                    } else {
                        exceptions[lang] = exceptions.global;
                    }
                }
                //move exceptions from the the local 'exceptions'-obj to the 'language'-object
                if (exceptions.hasOwnProperty(lang)) {
                    lo.exceptions = convertExceptionsToObject(exceptions[lang]);
                    delete exceptions[lang];
                } else {
                    lo.exceptions = {};
                }
                convertPatternsToArray(lo);
                if (String.prototype.normalize) {
                    wrd = '[\\w' + lo.specialChars + lo.specialChars.normalize("NFD") + String.fromCharCode(173) + String.fromCharCode(8204) + '-]{' + min + ',}';
                } else {
                    wrd = '[\\w' + lo.specialChars + String.fromCharCode(173) + String.fromCharCode(8204) + '-]{' + min + ',}';
                }
                lo.genRegExp = new RegExp('(' + wrd + ')|(' + url + ')|(' + mail + ')', 'gi');
                lo.prepared = true;
            }
        },

        /****
         * @method Hyphenator~prepare
         * @desc
         * This funtion prepares the Hyphenator~Object: If RemoteLoading is turned off, it assumes
         * that the patternfiles are loaded, all conversions are made and the callback is called.
         * If storage is active the object is retrieved there.
         * If RemoteLoading is on (default), it loads the pattern files and repeatedly checks Hyphenator.languages.
         * If a patternfile is loaded the patterns are stored in storage (if enabled),
         * converted to their object style and the lang-object extended.
         * Finally the callback is called.
         * @access private
         */
        prepare = function (callback) {
            var lang, tmp1, tmp2,
                languagesLoaded = function () {
                    var l;
                    for (l in docLanguages) {
                        if (docLanguages.hasOwnProperty(l)) {
                            if (Hyphenator.languages.hasOwnProperty(l)) {
                                delete docLanguages[l];
                                if (!!storage) {
                                    storage.setItem(l, window.JSON.stringify(Hyphenator.languages[l]));
                                }
                                prepareLanguagesObj(l);
                                callback(l);
                            }
                        }
                    }
                };

            if (!enableRemoteLoading) {
                for (lang in Hyphenator.languages) {
                    if (Hyphenator.languages.hasOwnProperty(lang)) {
                        prepareLanguagesObj(lang);
                    }
                }
                callback('*');
                return;
            }
            // get all languages that are used and preload the patterns
            for (lang in docLanguages) {
                if (docLanguages.hasOwnProperty(lang)) {
                    if (!!storage && storage.test(lang)) {
                        Hyphenator.languages[lang] = window.JSON.parse(storage.getItem(lang));
                        prepareLanguagesObj(lang);
                        if (exceptions.hasOwnProperty('global')) {
                            tmp1 = convertExceptionsToObject(exceptions.global);
                            for (tmp2 in tmp1) {
                                if (tmp1.hasOwnProperty(tmp2)) {
                                    Hyphenator.languages[lang].exceptions[tmp2] = tmp1[tmp2];
                                }
                            }
                        }
                        //Replace exceptions since they may have been changed:
                        if (exceptions.hasOwnProperty(lang)) {
                            tmp1 = convertExceptionsToObject(exceptions[lang]);
                            for (tmp2 in tmp1) {
                                if (tmp1.hasOwnProperty(tmp2)) {
                                    Hyphenator.languages[lang].exceptions[tmp2] = tmp1[tmp2];
                                }
                            }
                            delete exceptions[lang];
                        }
                        //Replace genRegExp since it may have been changed:
                        if (String.prototype.normalize) {
                            tmp1 = '[\\w' + Hyphenator.languages[lang].specialChars + Hyphenator.languages[lang].specialChars.normalize("NFD") + String.fromCharCode(173) + String.fromCharCode(8204) + '-]{' + min + ',}';
                        } else {
                            tmp1 = '[\\w' + Hyphenator.languages[lang].specialChars + String.fromCharCode(173) + String.fromCharCode(8204) + '-]{' + min + ',}';
                        }
                        Hyphenator.languages[lang].genRegExp = new RegExp('(' + tmp1 + ')|(' + url + ')|(' + mail + ')', 'gi');
                        if (enableCache) {
                            if (!Hyphenator.languages[lang].cache) {
                                Hyphenator.languages[lang].cache = {};
                            }
                        }
                        delete docLanguages[lang];
                        callback(lang);
                    } else {
                        loadPatterns(lang, languagesLoaded);
                    }
                }
            }
            //call languagesLoaded in case language has been loaded manually
            //and remoteLoading is on (onload won't fire)
            languagesLoaded();
        },

        /**
         * @method Hyphenator~toggleBox
         * @desc
         * Creates the toggleBox: a small button to turn off/on hyphenation on a page.
         * @see {@link Hyphenator.config}
         * @access private
         */
        toggleBox = function () {
            var bdy, myTextNode,
                text = (Hyphenator.doHyphenation ? 'Hy-phen-a-tion' : 'Hyphenation'),
                myBox = contextWindow.document.getElementById('HyphenatorToggleBox');
            if (!!myBox) {
                myBox.firstChild.data = text;
            } else {
                bdy = contextWindow.document.getElementsByTagName('body')[0];
                myBox = createElem('div', contextWindow);
                myBox.setAttribute('id', 'HyphenatorToggleBox');
                myBox.setAttribute('class', dontHyphenateClass);
                myTextNode = contextWindow.document.createTextNode(text);
                myBox.appendChild(myTextNode);
                myBox.onclick =  Hyphenator.toggleHyphenation;
                myBox.style.position = 'absolute';
                myBox.style.top = '0px';
                myBox.style.right = '0px';
                myBox.style.zIndex = '1000';
                myBox.style.margin = '0';
                myBox.style.backgroundColor = '#AAAAAA';
                myBox.style.color = '#FFFFFF';
                myBox.style.font = '6pt Arial';
                myBox.style.letterSpacing = '0.2em';
                myBox.style.padding = '3px';
                myBox.style.cursor = 'pointer';
                myBox.style.WebkitBorderBottomLeftRadius = '4px';
                myBox.style.MozBorderRadiusBottomleft = '4px';
                myBox.style.borderBottomLeftRadius = '4px';
                bdy.appendChild(myBox);
            }
        },

        /**
         * @method Hyphenator~doCharSubst
         * @desc
         * Replace chars in a word
         *
         * @param {Object} loCharSubst Map of substitutions ({'ä': 'a', 'ü': 'u', …})
         * @param {string} w the word
         * @returns string The word with substituted characers
         * @access private
         */
        doCharSubst = function (loCharSubst, w) {
            var subst, r;
            for (subst in loCharSubst) {
                if (loCharSubst.hasOwnProperty(subst)) {
                    r = w.replace(new RegExp(subst, 'g'), loCharSubst[subst]);
                }
            }
            return r;
        },

        /**
         * @member {Array} Hyphenator~wwAsMappedCharCodeStore
         * @desc
         * Array (typed if supported) container for charCodes
         * @access private
         * @see {@link Hyphenator~hyphenateWord}
         */
        wwAsMappedCharCodeStore = (function () {
            if (Object.prototype.hasOwnProperty.call(window, "Int32Array")) {
                return new window.Int32Array(32);
            }
            return [];
        }()),

        /**
         * @member {Array} Hyphenator~wwhpStore
         * @desc
         * Array (typed if supported) container for hyphenation points
         * @access private
         * @see {@link Hyphenator~hyphenateWord}
         */
        wwhpStore = (function () {
            var r;
            if (Object.prototype.hasOwnProperty.call(window, "Uint8Array")) {
                r = new window.Uint8Array(32);
            } else {
                r = [];
            }
            return r;
        }()),

        /**
         * @method Hyphenator~hyphenateWord
         * @desc
         * This function is the heart of Hyphenator.js. It returns a hyphenated word.
         *
         * If there's already a {@link Hyphenator~hypen} in the word, the word is returned as it is.
         * If the word is in the exceptions list or in the cache, it is retrieved from it.
         * If there's a '-' hyphenate the parts.
         * The hyphenated word is returned and (if acivated) cached.
         * Both special Events onBeforeWordHyphenation and onAfterWordHyphenation are called for the word.
         * @param {Object} lo A language object (containing the patterns)
         * @param {string} lang The language of the word
         * @param {string} word The word
         * @returns string The hyphenated word
         * @access private
         */
        hyphenateWord = function (lo, lang, word) {
            var parts,
                i,
                pattern = "",
                ww,
                wwlen,
                wwhp = wwhpStore,
                pstart,
                plen,
                hp,
                wordLength = word.length,
                hw = '',
                charMap = lo.charMap.code2int,
                charCode,
                mappedCharCode,
                row = 0,
                link = 0,
                value = 0,
                values,
                indexedTrie = lo.indexedTrie,
                valueStore = lo.valueStore.keys,
                wwAsMappedCharCode = wwAsMappedCharCodeStore;

            word = onBeforeWordHyphenation(word, lang);
            if (word === '') {
                hw = '';
            } else if (enableCache && lo.cache && lo.cache.hasOwnProperty(word)) { //the word is in the cache
                hw = lo.cache[word];
            } else if (word.indexOf(hyphen) !== -1) {
                //word already contains shy; -> leave at it is!
                hw = word;
            } else if (lo.exceptions.hasOwnProperty(word)) { //the word is in the exceptions list
                hw = lo.exceptions[word].replace(/-/g, hyphen);
            } else if (word.indexOf('-') !== -1) {
                //word contains '-' -> hyphenate the parts separated with '-'
                parts = word.split('-');
                for (i = 0; i < parts.length; i += 1) {
                    parts[i] = hyphenateWord(lo, lang, parts[i]);
                }
                hw = parts.join('-');
            } else {
                ww = word.toLowerCase();
                if (String.prototype.normalize) {
                    ww = ww.normalize("NFC");
                }
                if (lo.hasOwnProperty("charSubstitution")) {
                    ww = doCharSubst(lo.charSubstitution, ww);
                }
                if (word.indexOf("'") !== -1) {
                    ww = ww.replace(/'/g, "’"); //replace APOSTROPHE with RIGHT SINGLE QUOTATION MARK (since the latter is used in the patterns)
                }
                ww = '_' + ww + '_';
                wwlen = ww.length;
                //prepare wwhp and wwAsMappedCharCode
                for (pstart = 0; pstart < wwlen; pstart += 1) {
                    wwhp[pstart] = 0;
                    charCode = ww.charCodeAt(pstart);
                    if (charMap[charCode] !== undefined) {
                        wwAsMappedCharCode[pstart] = charMap[charCode];
                    } else {
                        wwAsMappedCharCode[pstart] = -1;
                    }
                }
                //get hyphenation points for all substrings
                for (pstart = 0; pstart < wwlen; pstart += 1) {
                    row = 0;
                    pattern = '';
                    for (plen = pstart; plen < wwlen; plen += 1) {
                        mappedCharCode = wwAsMappedCharCode[plen];
                        if (mappedCharCode === -1) {
                            break;
                        }
                        if (enableReducedPatternSet) {
                            pattern += ww.charAt(plen);
                        }
                        link = indexedTrie[row + mappedCharCode * 2];
                        value = indexedTrie[row + mappedCharCode * 2 + 1];
                        if (value > 0) {
                            hp = valueStore[value];
                            while (hp) {
                                hp -= 1;
                                if (valueStore[value + 1 + hp] > wwhp[pstart + hp]) {
                                    wwhp[pstart + hp] = valueStore[value + 1 + hp];
                                }
                            }
                            if (enableReducedPatternSet) {
                                if (!lo.redPatSet) {
                                    lo.redPatSet = {};
                                }
                                if (valueStore.subarray) {
                                    values = valueStore.subarray(value + 1, value + 1 + valueStore[value]);
                                } else {
                                    values = valueStore.slice(value + 1, value + 1 + valueStore[value]);
                                }
                                lo.redPatSet[pattern] = recreatePattern(pattern, values);
                            }
                        }
                        if (link > 0) {
                            row = link;
                        } else {
                            break;
                        }
                    }
                }
                //create hyphenated word
                for (hp = 0; hp < wordLength; hp += 1) {
                    if (hp >= lo.leftmin && hp <= (wordLength - lo.rightmin) && (wwhp[hp + 1] % 2) !== 0) {
                        hw += hyphen + word.charAt(hp);
                    } else {
                        hw += word.charAt(hp);
                    }
                }
            }
            hw = onAfterWordHyphenation(hw, lang);
            if (enableCache) { //put the word in the cache
                lo.cache[word] = hw;
            }
            return hw;
        },

        /**
         * @method Hyphenator~removeHyphenationFromElement
         * @desc
         * Removes all hyphens from the element. If there are other elements, the function is
         * called recursively.
         * Removing hyphens is usefull if you like to copy text. Some browsers are buggy when the copy hyphenated texts.
         * @param {Object} el The element where to remove hyphenation.
         * @access public
         */
        removeHyphenationFromElement = function (el) {
            var h, u, i = 0, n;
            switch (hyphen) {
            case '|':
                h = '\\|';
                break;
            case '+':
                h = '\\+';
                break;
            case '*':
                h = '\\*';
                break;
            default:
                h = hyphen;
            }
            switch (urlhyphen) {
            case '|':
                u = '\\|';
                break;
            case '+':
                u = '\\+';
                break;
            case '*':
                u = '\\*';
                break;
            default:
                u = urlhyphen;
            }
            n = el.childNodes[i];
            while (!!n) {
                if (n.nodeType === 3) {
                    n.data = n.data.replace(new RegExp(h, 'g'), '');
                    n.data = n.data.replace(new RegExp(u, 'g'), '');
                } else if (n.nodeType === 1) {
                    removeHyphenationFromElement(n);
                }
                i += 1;
                n = el.childNodes[i];
            }
        },

        copy = (function () {
            var Copy = function () {

                this.oncopyHandler = function (e) {
                    e = e || window.event;
                    var shadow, selection, range, rangeShadow, restore,
                        target = e.target || e.srcElement,
                        currDoc = target.ownerDocument,
                        bdy = currDoc.getElementsByTagName('body')[0],
                        targetWindow = currDoc.defaultView || currDoc.parentWindow;
                    if (target.tagName && dontHyphenate[target.tagName.toLowerCase()]) {
                        //Safari needs this
                        return;
                    }
                    //create a hidden shadow element
                    shadow = currDoc.createElement('div');
                    //Moving the element out of the screen doesn't work for IE9 (https://connect.microsoft.com/IE/feedback/details/663981/)
                    //shadow.style.overflow = 'hidden';
                    //shadow.style.position = 'absolute';
                    //shadow.style.top = '-5000px';
                    //shadow.style.height = '1px';
                    //doing this instead:
                    shadow.style.color = window.getComputedStyle ? targetWindow.getComputedStyle(bdy, null).backgroundColor : '#FFFFFF';
                    shadow.style.fontSize = '0px';
                    bdy.appendChild(shadow);
                    if (!!window.getSelection) {
                        //FF3, Webkit, IE9
                        e.stopPropagation();
                        selection = targetWindow.getSelection();
                        range = selection.getRangeAt(0);
                        shadow.appendChild(range.cloneContents());
                        removeHyphenationFromElement(shadow);
                        selection.selectAllChildren(shadow);
                        restore = function () {
                            shadow.parentNode.removeChild(shadow);
                            selection.removeAllRanges(); //IE9 needs that
                            selection.addRange(range);
                        };
                    } else {
                        // IE<9
                        e.cancelBubble = true;
                        selection = targetWindow.document.selection;
                        range = selection.createRange();
                        shadow.innerHTML = range.htmlText;
                        removeHyphenationFromElement(shadow);
                        rangeShadow = bdy.createTextRange();
                        rangeShadow.moveToElementText(shadow);
                        rangeShadow.select();
                        restore = function () {
                            shadow.parentNode.removeChild(shadow);
                            if (range.text !== "") {
                                range.select();
                            }
                        };
                    }
                    zeroTimeOut(restore);
                };

                this.removeOnCopy = function (el) {
                    var body = el.ownerDocument.getElementsByTagName('body')[0];
                    if (!body) {
                        return;
                    }
                    el = el || body;
                    if (window.removeEventListener) {
                        el.removeEventListener("copy", this.oncopyHandler, true);
                    } else {
                        el.detachEvent("oncopy", this.oncopyHandler);
                    }
                };

                this.registerOnCopy = function (el) {
                    var body = el.ownerDocument.getElementsByTagName('body')[0];
                    if (!body) {
                        return;
                    }
                    el = el || body;
                    if (window.addEventListener) {
                        el.addEventListener("copy", this.oncopyHandler, true);
                    } else {
                        el.attachEvent("oncopy", this.oncopyHandler);
                    }
                };
            };

            return (safeCopy ? new Copy() : false);
        }()),


        /**
         * @method Hyphenator~checkIfAllDone
         * @desc
         * Checks if all elements in {@link Hyphenator~elements} are hyphenated, unhides them and fires onHyphenationDone()
         * @access private
         */
        checkIfAllDone = function () {
            var allDone = true, i, doclist = {}, doc;
            elements.each(function (ellist) {
                var j, l = ellist.length;
                for (j = 0; j < l; j += 1) {
                    allDone = allDone && ellist[j].hyphenated;
                    if (!doclist.hasOwnProperty(ellist[j].element.baseURI)) {
                        doclist[ellist[j].element.ownerDocument.location.href] = true;
                    }
                    doclist[ellist[j].element.ownerDocument.location.href] = doclist[ellist[j].element.ownerDocument.location.href] && ellist[j].hyphenated;
                }
            });
            if (allDone) {
                if (intermediateState === 'hidden' && unhide === 'progressive') {
                    elements.each(function (ellist) {
                        var j, l = ellist.length, el;
                        for (j = 0; j < l; j += 1) {
                            el = ellist[j].element;
                            el.className = el.className.replace(unhideClassRegExp, '');
                            if (el.className === '') {
                                el.removeAttribute('class');
                            }
                        }
                    });
                }
                for (i = 0; i < CSSEditors.length; i += 1) {
                    CSSEditors[i].clearChanges();
                }
                for (doc in doclist) {
                    if (doclist.hasOwnProperty(doc) && doc === contextWindow.location.href) {
                        onHyphenationDone(doc);
                    }
                }
                if (!!storage && storage.deferred.length > 0) {
                    for (i = 0; i < storage.deferred.length; i += 1) {
                        storage.deferred[i].call();
                    }
                    storage.deferred = [];
                }
            }
        },

        /**
         * @method Hyphenator~controlOrphans
         * @desc
         * removes orphans depending on the 'orphanControl'-setting:
         * orphanControl === 1: do nothing
         * orphanControl === 2: prevent last word to be hyphenated
         * orphanControl === 3: prevent one word on a last line (inserts a nobreaking space)
         * @param {string} part - The sring where orphans have to be removed
         * @access private
         */
        controlOrphans = function (part) {
            var h, r;
            switch (hyphen) {
            case '|':
                h = '\\|';
                break;
            case '+':
                h = '\\+';
                break;
            case '*':
                h = '\\*';
                break;
            default:
                h = hyphen;
            }
            //strip off blank space at the end (omitted closing tags)
            part = part.replace(/[\s]*$/, '');
            if (orphanControl >= 2) {
                //remove hyphen points from last word
                r = part.split(' ');
                r[1] = r[1].replace(new RegExp(h, 'g'), '');
                r[1] = r[1].replace(new RegExp(zeroWidthSpace, 'g'), '');
                r = r.join(' ');
            }
            if (orphanControl === 3) {
                //replace spaces by non breaking spaces
                r = r.replace(/[ ]+/g, String.fromCharCode(160));
            }
            return r;
        },

        /**
         * @method Hyphenator~hyphenateElement
         * @desc
         * Takes the content of the given element and - if there's text - replaces the words
         * by hyphenated words. If there's another element, the function is called recursively.
         * When all words are hyphenated, the visibility of the element is set to 'visible'.
         * @param {string} lang - The language-code of the element
         * @param {Element} elo - The element to hyphenate {@link Hyphenator~elements~ElementCollection~Element}
         * @access private
         */
        hyphenateElement = function (lang, elo) {
            var el = elo.element,
                hyphenate,
                n,
                i,
                lo;
            if (Hyphenator.languages.hasOwnProperty(lang) && Hyphenator.doHyphenation) {
                lo = Hyphenator.languages[lang];
                hyphenate = function (match, word, url, mail) {
                    var r;
                    if (!!url || !!mail) {
                        r = hyphenateURL(match);
                    } else {
                        r = hyphenateWord(lo, lang, word);
                    }
                    return r;
                };
                if (safeCopy && (el.tagName.toLowerCase() !== 'body')) {
                    copy.registerOnCopy(el);
                }
                i = 0;
                n = el.childNodes[i];
                while (!!n) {
                    if (n.nodeType === 3 //type 3 = #text
                            && /\S/.test(n.data) //not just white space
                            && n.data.length >= min) { //longer then min
                        n.data = n.data.replace(lo.genRegExp, hyphenate);
                        if (orphanControl !== 1) {
                            n.data = n.data.replace(/[\S]+ [\S]+[\s]*$/, controlOrphans);
                        }
                    }
                    i += 1;
                    n = el.childNodes[i];
                }
            }
            if (intermediateState === 'hidden' && unhide === 'wait') {
                el.className = el.className.replace(hideClassRegExp, '');
                if (el.className === '') {
                    el.removeAttribute('class');
                }
            }
            if (intermediateState === 'hidden' && unhide === 'progressive') {
                el.className = el.className.replace(hideClassRegExp, ' ' + unhideClass);
            }
            elo.hyphenated = true;
            elements.hyCount += 1;
            if (elements.count <= elements.hyCount) {
                checkIfAllDone();
            }
        },

        /**
         * @method Hyphenator~hyphenateLanguageElements
         * @desc
         * Calls hyphenateElement() for all elements of the specified language.
         * If the language is '*' then all elements are hyphenated.
         * This is done with a setTimout
         * to prevent a "long running Script"-alert when hyphenating large pages.
         * Therefore a tricky bind()-function was necessary.
         * @param {string} lang The language of the elements to hyphenate
         * @access private
         */

        hyphenateLanguageElements = function (lang) {
            /*function bind(fun, arg1, arg2) {
                return function () {
                    return fun(arg1, arg2);
                };
            }*/
            var i, l;
            if (lang === '*') {
                elements.each(function (lang, ellist) {
                    var j, le = ellist.length;
                    for (j = 0; j < le; j += 1) {
                        //zeroTimeOut(bind(hyphenateElement, lang, ellist[j]));
                        hyphenateElement(lang, ellist[j]);
                    }
                });
            } else {
                if (elements.list.hasOwnProperty(lang)) {
                    l = elements.list[lang].length;
                    for (i = 0; i < l; i += 1) {
                        //zeroTimeOut(bind(hyphenateElement, lang, elements.list[lang][i]));
                        hyphenateElement(lang, elements.list[lang][i]);
                    }
                }
            }
        },

        /**
         * @method Hyphenator~removeHyphenationFromDocument
         * @desc
         * Does what it says and unregisters the onCopyEvent from the elements
         * @access private
         */
        removeHyphenationFromDocument = function () {
            elements.each(function (ellist) {
                var i, l = ellist.length;
                for (i = 0; i < l; i += 1) {
                    removeHyphenationFromElement(ellist[i].element);
                    if (safeCopy) {
                        copy.removeOnCopy(ellist[i].element);
                    }
                    ellist[i].hyphenated = false;
                }
            });
        },

        /**
         * @method Hyphenator~createStorage
         * @desc
         * inits the private var {@link Hyphenator~storage) depending of the setting in {@link Hyphenator~storageType}
         * and the supported features of the system.
         * @access private
         */
        createStorage = function () {
            var s;
            try {
                if (storageType !== 'none' &&
                        window.JSON !== undefined &&
                        window.localStorage !== undefined &&
                        window.sessionStorage !== undefined &&
                        window.JSON.stringify !== undefined &&
                        window.JSON.parse !== undefined) {
                    switch (storageType) {
                    case 'session':
                        s = window.sessionStorage;
                        break;
                    case 'local':
                        s = window.localStorage;
                        break;
                    default:
                        s = undefined;
                        break;
                    }
                    //check for private mode
                    s.setItem('storageTest', '1');
                    s.removeItem('storageTest');
                }
            } catch (e) {
                //FF throws an error if DOM.storage.enabled is set to false
                s = undefined;
            }
            if (s) {
                storage = {
                    prefix: 'Hyphenator_' + Hyphenator.version + '_',
                    store: s,
                    deferred: [],
                    test: function (name) {
                        var val = this.store.getItem(this.prefix + name);
                        return (!!val) ? true : false;
                    },
                    getItem: function (name) {
                        return this.store.getItem(this.prefix + name);
                    },
                    setItem: function (name, value) {
                        try {
                            this.store.setItem(this.prefix + name, value);
                        } catch (e) {
                            onError(e);
                        }
                    }
                };
            } else {
                storage = undefined;
            }
        },

        /**
         * @method Hyphenator~storeConfiguration
         * @desc
         * Stores the current config-options in DOM-Storage
         * @access private
         */
        storeConfiguration = function () {
            if (!storage) {
                return;
            }
            var settings = {
                'STORED': true,
                'classname': hyphenateClass,
                'urlclassname': urlHyphenateClass,
                'donthyphenateclassname': dontHyphenateClass,
                'minwordlength': min,
                'hyphenchar': hyphen,
                'urlhyphenchar': urlhyphen,
                'togglebox': toggleBox,
                'displaytogglebox': displayToggleBox,
                'remoteloading': enableRemoteLoading,
                'enablecache': enableCache,
                'enablereducedpatternset': enableReducedPatternSet,
                'onhyphenationdonecallback': onHyphenationDone,
                'onerrorhandler': onError,
                'onwarninghandler': onWarning,
                'intermediatestate': intermediateState,
                'selectorfunction': selectorFunction || mySelectorFunction,
                'safecopy': safeCopy,
                'doframes': doFrames,
                'storagetype': storageType,
                'orphancontrol': orphanControl,
                'dohyphenation': Hyphenator.doHyphenation,
                'persistentconfig': persistentConfig,
                'defaultlanguage': defaultLanguage,
                'useCSS3hyphenation': css3,
                'unhide': unhide,
                'onbeforewordhyphenation': onBeforeWordHyphenation,
                'onafterwordhyphenation': onAfterWordHyphenation
            };
            storage.setItem('config', window.JSON.stringify(settings));
        },

        /**
         * @method Hyphenator~restoreConfiguration
         * @desc
         * Retrieves config-options from DOM-Storage and does configuration accordingly
         * @access private
         */
        restoreConfiguration = function () {
            var settings;
            if (storage.test('config')) {
                settings = window.JSON.parse(storage.getItem('config'));
                Hyphenator.config(settings);
            }
        };

    return {

        /**
         * @member {string} Hyphenator.version
         * @desc
         * String containing the actual version of Hyphenator.js
         * [major release].[minor releas].[bugfix release]
         * major release: new API, new Features, big changes
         * minor release: new languages, improvements
         * @access public
         */
        version: '5.1.0',

        /**
         * @member {boolean} Hyphenator.doHyphenation
         * @desc
         * If doHyphenation is set to false, hyphenateDocument() isn't called.
         * All other actions are performed.
         * @default true
         */
        doHyphenation: true,

        /**
         * @typedef {Object} Hyphenator.languages.language
         * @property {Number} leftmin - The minimum of chars to remain on the old line
         * @property {Number} rightmin - The minimum of chars to go on the new line
         * @property {string} specialChars - Non-ASCII chars in the alphabet.
         * @property {Object.<number, string>} patterns - the patterns in a compressed format. The key is the length of the patterns in the value string.
         * @property {Object.<string, string>} charSubstitution - optional: a hash table with chars that are replaced during hyphenation
         * @property {string | Object.<string, string>} exceptions - optional: a csv string containing exceptions
         */

        /**
         * @member {Object.<string, Hyphenator.languages.language>} Hyphenator.languages
         * @desc
         * Objects that holds key-value pairs, where key is the language and the value is the
         * language-object loaded from (and set by) the pattern file.
         * @namespace Hyphenator.languages
         * @access public
         */
        languages: {},


        /**
         * @method Hyphenator.config
         * @desc
         * The Hyphenator.config() function that takes an object as an argument. The object contains key-value-pairs
         * containig Hyphenator-settings.
         * @param {Hyphenator.config} obj
         * @access public
         * @example
         * &lt;script src = "Hyphenator.js" type = "text/javascript"&gt;&lt;/script&gt;
         * &lt;script type = "text/javascript"&gt;
         *     Hyphenator.config({'minwordlength':4,'hyphenchar':'|'});
         *     Hyphenator.run();
         * &lt;/script&gt;
         */
        config: function (obj) {
            var assert = function (name, type) {
                    var r, t;
                    t = typeof obj[name];
                    if (t === type) {
                        r = true;
                    } else {
                        onError(new Error('Config onError: ' + name + ' must be of type ' + type));
                        r = false;
                    }
                    return r;
                },
                key;

            if (obj.hasOwnProperty('storagetype')) {
                if (assert('storagetype', 'string')) {
                    storageType = obj.storagetype;
                }
                if (!storage) {
                    createStorage();
                }
            }
            if (!obj.hasOwnProperty('STORED') && storage && obj.hasOwnProperty('persistentconfig') && obj.persistentconfig === true) {
                restoreConfiguration();
            }

            for (key in obj) {
                if (obj.hasOwnProperty(key)) {
                    switch (key) {
                    case 'STORED':
                        break;
                    case 'classname':
                        if (assert('classname', 'string')) {
                            hyphenateClass = obj[key];
                        }
                        break;
                    case 'urlclassname':
                        if (assert('urlclassname', 'string')) {
                            urlHyphenateClass = obj[key];
                        }
                        break;
                    case 'donthyphenateclassname':
                        if (assert('donthyphenateclassname', 'string')) {
                            dontHyphenateClass = obj[key];
                        }
                        break;
                    case 'minwordlength':
                        if (assert('minwordlength', 'number')) {
                            min = obj[key];
                        }
                        break;
                    case 'hyphenchar':
                        if (assert('hyphenchar', 'string')) {
                            if (obj.hyphenchar === '&shy;') {
                                obj.hyphenchar = String.fromCharCode(173);
                            }
                            hyphen = obj[key];
                        }
                        break;
                    case 'urlhyphenchar':
                        if (obj.hasOwnProperty('urlhyphenchar')) {
                            if (assert('urlhyphenchar', 'string')) {
                                urlhyphen = obj[key];
                            }
                        }
                        break;
                    case 'togglebox':
                        if (assert('togglebox', 'function')) {
                            toggleBox = obj[key];
                        }
                        break;
                    case 'displaytogglebox':
                        if (assert('displaytogglebox', 'boolean')) {
                            displayToggleBox = obj[key];
                        }
                        break;
                    case 'remoteloading':
                        if (assert('remoteloading', 'boolean')) {
                            enableRemoteLoading = obj[key];
                        }
                        break;
                    case 'enablecache':
                        if (assert('enablecache', 'boolean')) {
                            enableCache = obj[key];
                        }
                        break;
                    case 'enablereducedpatternset':
                        if (assert('enablereducedpatternset', 'boolean')) {
                            enableReducedPatternSet = obj[key];
                        }
                        break;
                    case 'onhyphenationdonecallback':
                        if (assert('onhyphenationdonecallback', 'function')) {
                            onHyphenationDone = obj[key];
                        }
                        break;
                    case 'onerrorhandler':
                        if (assert('onerrorhandler', 'function')) {
                            onError = obj[key];
                        }
                        break;
                    case 'onwarninghandler':
                        if (assert('onwarninghandler', 'function')) {
                            onWarning = obj[key];
                        }
                        break;
                    case 'intermediatestate':
                        if (assert('intermediatestate', 'string')) {
                            intermediateState = obj[key];
                        }
                        break;
                    case 'selectorfunction':
                        if (assert('selectorfunction', 'function')) {
                            selectorFunction = obj[key];
                        }
                        break;
                    case 'safecopy':
                        if (assert('safecopy', 'boolean')) {
                            safeCopy = obj[key];
                        }
                        break;
                    case 'doframes':
                        if (assert('doframes', 'boolean')) {
                            doFrames = obj[key];
                        }
                        break;
                    case 'storagetype':
                        if (assert('storagetype', 'string')) {
                            storageType = obj[key];
                        }
                        break;
                    case 'orphancontrol':
                        if (assert('orphancontrol', 'number')) {
                            orphanControl = obj[key];
                        }
                        break;
                    case 'dohyphenation':
                        if (assert('dohyphenation', 'boolean')) {
                            Hyphenator.doHyphenation = obj[key];
                        }
                        break;
                    case 'persistentconfig':
                        if (assert('persistentconfig', 'boolean')) {
                            persistentConfig = obj[key];
                        }
                        break;
                    case 'defaultlanguage':
                        if (assert('defaultlanguage', 'string')) {
                            defaultLanguage = obj[key];
                        }
                        break;
                    case 'useCSS3hyphenation':
                        if (assert('useCSS3hyphenation', 'boolean')) {
                            css3 = obj[key];
                        }
                        break;
                    case 'unhide':
                        if (assert('unhide', 'string')) {
                            unhide = obj[key];
                        }
                        break;
                    case 'onbeforewordhyphenation':
                        if (assert('onbeforewordhyphenation', 'function')) {
                            onBeforeWordHyphenation = obj[key];
                        }
                        break;
                    case 'onafterwordhyphenation':
                        if (assert('onafterwordhyphenation', 'function')) {
                            onAfterWordHyphenation = obj[key];
                        }
                        break;
                    default:
                        onError(new Error('Hyphenator.config: property ' + key + ' not known.'));
                    }
                }
            }
            if (storage && persistentConfig) {
                storeConfiguration();
            }
        },

        /**
         * @method Hyphenator.run
         * @desc
         * Bootstrap function that starts all hyphenation processes when called:
         * Tries to create storage if required and calls {@link Hyphenator~runWhenLoaded} on 'window' handing over the callback 'process'
         * @access public
         * @example
         * &lt;script src = "Hyphenator.js" type = "text/javascript"&gt;&lt;/script&gt;
         * &lt;script type = "text/javascript"&gt;
         *   Hyphenator.run();
         * &lt;/script&gt;
         */
        run: function () {
                /**
                 *@callback Hyphenator.run~process process - The function is called when the DOM has loaded (or called for each frame)
                 */
            var process = function () {
                try {
                    if (contextWindow.document.getElementsByTagName('frameset').length > 0) {
                        return; //we are in a frameset
                    }
                    autoSetMainLanguage(undefined);
                    gatherDocumentInfos();
                    if (displayToggleBox) {
                        toggleBox();
                    }
                    prepare(hyphenateLanguageElements);
                } catch (e) {
                    onError(e);
                }
            };

            if (!storage) {
                createStorage();
            }
            runWhenLoaded(window, process);
        },

        /**
         * @method Hyphenator.addExceptions
             * @desc
         * Adds the exceptions from the string to the appropriate language in the 
         * {@link Hyphenator~languages}-object
         * @param {string} lang The language
         * @param {string} words A comma separated string of hyphenated words WITH spaces.
         * @access public
         * @example &lt;script src = "Hyphenator.js" type = "text/javascript"&gt;&lt;/script&gt;
         * &lt;script type = "text/javascript"&gt;
         *   Hyphenator.addExceptions('de','ziem-lich, Wach-stube');
         *   Hyphenator.run();
         * &lt;/script&gt;
         */
        addExceptions: function (lang, words) {
            if (lang === '') {
                lang = 'global';
            }
            if (exceptions.hasOwnProperty(lang)) {
                exceptions[lang] += ", " + words;
            } else {
                exceptions[lang] = words;
            }
        },

        /**
         * @method Hyphenator.hyphenate
         * @access public
         * @desc
         * Hyphenates the target. The language patterns must be loaded.
         * If the target is a string, the hyphenated string is returned,
         * if it's an object, the values are hyphenated directly and undefined (aka nothing) is returned
         * @param {string|Object} target the target to be hyphenated
         * @param {string} lang the language of the target
         * @returns {string|undefined}
         * @example &lt;script src = "Hyphenator.js" type = "text/javascript"&gt;&lt;/script&gt;
         * &lt;script src = "patterns/en.js" type = "text/javascript"&gt;&lt;/script&gt;
         * &lt;script type = "text/javascript"&gt;
         * var t = Hyphenator.hyphenate('Hyphenation', 'en'); //Hy|phen|ation
         * &lt;/script&gt;
         */
        hyphenate: function (target, lang) {
            var hyphenate, n, i, lo;
            lo = Hyphenator.languages[lang];
            if (Hyphenator.languages.hasOwnProperty(lang)) {
                if (!lo.prepared) {
                    prepareLanguagesObj(lang);
                }
                hyphenate = function (match, word, url, mail) {
                    var r;
                    if (!!url || !!mail) {
                        r = hyphenateURL(match);
                    } else {
                        r = hyphenateWord(lo, lang, word);
                    }
                    return r;
                };
                if (typeof target === 'object' && !(typeof target === 'string' || target.constructor === String)) {
                    i = 0;
                    n = target.childNodes[i];
                    while (!!n) {
                        if (n.nodeType === 3 //type 3 = #text
                                && /\S/.test(n.data) //not just white space
                                && n.data.length >= min) { //longer then min
                            n.data = n.data.replace(lo.genRegExp, hyphenate);
                        } else if (n.nodeType === 1) {
                            if (n.lang !== '') {
                                Hyphenator.hyphenate(n, n.lang);
                            } else {
                                Hyphenator.hyphenate(n, lang);
                            }
                        }
                        i += 1;
                        n = target.childNodes[i];
                    }
                } else if (typeof target === 'string' || target.constructor === String) {
                    return target.replace(lo.genRegExp, hyphenate);
                }
            } else {
                onError(new Error('Language "' + lang + '" is not loaded.'));
            }
        },

        /**
         * @method Hyphenator.getRedPatternSet
         * @desc
         * Returns the reduced pattern set: an object looking like: {'patk': pat}
         * @param {string} lang the language patterns are stored for
         * @returns {Object.<string, string>}
         * @access public
         */
        getRedPatternSet: function (lang) {
            return Hyphenator.languages[lang].redPatSet;
        },

        /**
         * @method Hyphenator.isBookmarklet
         * @desc
         * Returns {@link Hyphenator~isBookmarklet}.
         * @returns {boolean}
         * @access public
         */
        isBookmarklet: function () {
            return isBookmarklet;
        },

        /**
         * @method Hyphenator.getConfigFromURI
         * @desc
         * reads and sets configurations from GET parameters in the URI
         * @access public
         */
        getConfigFromURI: function () {
            /*jslint evil: true*/
            var loc = null, re = {}, jsArray = contextWindow.document.getElementsByTagName('script'), i, j, l, s, gp, option;
            for (i = 0, l = jsArray.length; i < l; i += 1) {
                if (!!jsArray[i].getAttribute('src')) {
                    loc = jsArray[i].getAttribute('src');
                }
                if (loc && (loc.indexOf('Hyphenator.js?') !== -1)) {
                    s = loc.indexOf('Hyphenator.js?');
                    gp = loc.substring(s + 14).split('&');
                    for (j = 0; j < gp.length; j += 1) {
                        option = gp[j].split('=');
                        if (option[0] !== 'bm') {
                            if (option[1] === 'true') {
                                option[1] = true;
                            } else if (option[1] === 'false') {
                                option[1] = false;
                            } else if (isFinite(option[1])) {
                                option[1] = parseInt(option[1], 10);
                            }
                            if (option[0] === 'togglebox' ||
                                    option[0] === 'onhyphenationdonecallback' ||
                                    option[0] === 'onerrorhandler' ||
                                    option[0] === 'selectorfunction' ||
                                    option[0] === 'onbeforewordhyphenation' ||
                                    option[0] === 'onafterwordhyphenation') {
                                option[1] = new Function('', option[1]);
                            }
                            re[option[0]] = option[1];
                        }
                    }
                    break;
                }
            }
            return re;
        },

        /**
         * @method Hyphenator.toggleHyphenation
         * @desc
         * Checks the current state of the ToggleBox and removes or does hyphenation.
         * @access public
         */
        toggleHyphenation: function () {
            if (Hyphenator.doHyphenation) {
                if (!!css3hyphenateClassHandle) {
                    css3hyphenateClassHandle.setRule('.' + css3hyphenateClass, css3_h9n.property + ': none;');
                }
                removeHyphenationFromDocument();
                Hyphenator.doHyphenation = false;
                storeConfiguration();
                toggleBox();
            } else {
                if (!!css3hyphenateClassHandle) {
                    css3hyphenateClassHandle.setRule('.' + css3hyphenateClass, css3_h9n.property + ': auto;');
                }
                Hyphenator.doHyphenation = true;
                hyphenateLanguageElements('*');
                storeConfiguration();
                toggleBox();
            }
        }
    };
}(window));

//Export properties/methods (for google closure compiler)
/**** to be moved to external file
Hyphenator['languages'] = Hyphenator.languages;
Hyphenator['config'] = Hyphenator.config;
Hyphenator['run'] = Hyphenator.run;
Hyphenator['addExceptions'] = Hyphenator.addExceptions;
Hyphenator['hyphenate'] = Hyphenator.hyphenate;
Hyphenator['getRedPatternSet'] = Hyphenator.getRedPatternSet;
Hyphenator['isBookmarklet'] = Hyphenator.isBookmarklet;
Hyphenator['getConfigFromURI'] = Hyphenator.getConfigFromURI;
Hyphenator['toggleHyphenation'] = Hyphenator.toggleHyphenation;
window['Hyphenator'] = Hyphenator;
*/

/*
 * call Hyphenator if it is a Bookmarklet
 */
if (Hyphenator.isBookmarklet()) {
    Hyphenator.config({displaytogglebox: true, intermediatestate: 'visible', storagetype: 'local', doframes: true, useCSS3hyphenation: true});
    Hyphenator.config(Hyphenator.getConfigFromURI());
    Hyphenator.run();
}
;/*
 * Copyright (c) 2014 Mike King (@micjamking)
 *
 * jQuery Succinct plugin
 * Version 1.1.0 (October 2014)
 *
 * Licensed under the MIT License
 */

 /*global jQuery*/
(function($) {
	'use strict';

	$.fn.succinct = function(options) {

		var settings = $.extend({
				size: 240,
				omission: '...',
				ignore: true,
                                splitByWord: false,
			}, options);

		return this.each(function() {

			var textDefault,
				textTruncated,
				elements = $(this),
				regex    = /[!-\/:-@\[-`{-~]$/,
				init     = function() {
					elements.each(function() {
						textDefault = $(this).html();

						if (textDefault.length > settings.size) {
							textTruncated = $.trim(textDefault)
											.substring(0, settings.size);
                                                        if (settings.splitByWord){
                                                            textTruncated = textTruncated.split(' ')
											.slice(0, -1)
											.join(' ');
                                                        }

							if (settings.ignore) {
								textTruncated = textTruncated.replace(regex, '');
							}

							$(this).html(textTruncated + settings.omission);
						}
					});
				};
			init();
		});
	};
})(jQuery);
;!function(){if(window.define)var a=window.define;if(window.require)var b=window.require;if(window.jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var a=jQuery.fn.select2.amd.define,b=jQuery.fn.select2.amd.require;var c,b,a;!function(d){function e(a,b){return u.call(a,b)}function f(a,b){var c,d,e,f,g,h,i,j,k,l,m,n=b&&b.split("/"),o=s.map,p=o&&o["*"]||{};if(a&&"."===a.charAt(0))if(b){for(n=n.slice(0,n.length-1),a=a.split("/"),g=a.length-1,s.nodeIdCompat&&w.test(a[g])&&(a[g]=a[g].replace(w,"")),a=n.concat(a),k=0;k<a.length;k+=1)if(m=a[k],"."===m)a.splice(k,1),k-=1;else if(".."===m){if(1===k&&(".."===a[2]||".."===a[0]))break;k>0&&(a.splice(k-1,2),k-=2)}a=a.join("/")}else 0===a.indexOf("./")&&(a=a.substring(2));if((n||p)&&o){for(c=a.split("/"),k=c.length;k>0;k-=1){if(d=c.slice(0,k).join("/"),n)for(l=n.length;l>0;l-=1)if(e=o[n.slice(0,l).join("/")],e&&(e=e[d])){f=e,h=k;break}if(f)break;!i&&p&&p[d]&&(i=p[d],j=k)}!f&&i&&(f=i,h=j),f&&(c.splice(0,h,f),a=c.join("/"))}return a}function g(a,b){return function(){return n.apply(d,v.call(arguments,0).concat([a,b]))}}function h(a){return function(b){return f(b,a)}}function i(a){return function(b){q[a]=b}}function j(a){if(e(r,a)){var b=r[a];delete r[a],t[a]=!0,m.apply(d,b)}if(!e(q,a)&&!e(t,a))throw new Error("No "+a);return q[a]}function k(a){var b,c=a?a.indexOf("!"):-1;return c>-1&&(b=a.substring(0,c),a=a.substring(c+1,a.length)),[b,a]}function l(a){return function(){return s&&s.config&&s.config[a]||{}}}var m,n,o,p,q={},r={},s={},t={},u=Object.prototype.hasOwnProperty,v=[].slice,w=/\.js$/;o=function(a,b){var c,d=k(a),e=d[0];return a=d[1],e&&(e=f(e,b),c=j(e)),e?a=c&&c.normalize?c.normalize(a,h(b)):f(a,b):(a=f(a,b),d=k(a),e=d[0],a=d[1],e&&(c=j(e))),{f:e?e+"!"+a:a,n:a,pr:e,p:c}},p={require:function(a){return g(a)},exports:function(a){var b=q[a];return"undefined"!=typeof b?b:q[a]={}},module:function(a){return{id:a,uri:"",exports:q[a],config:l(a)}}},m=function(a,b,c,f){var h,k,l,m,n,s,u=[],v=typeof c;if(f=f||a,"undefined"===v||"function"===v){for(b=!b.length&&c.length?["require","exports","module"]:b,n=0;n<b.length;n+=1)if(m=o(b[n],f),k=m.f,"require"===k)u[n]=p.require(a);else if("exports"===k)u[n]=p.exports(a),s=!0;else if("module"===k)h=u[n]=p.module(a);else if(e(q,k)||e(r,k)||e(t,k))u[n]=j(k);else{if(!m.p)throw new Error(a+" missing "+k);m.p.load(m.n,g(f,!0),i(k),{}),u[n]=q[k]}l=c?c.apply(q[a],u):void 0,a&&(h&&h.exports!==d&&h.exports!==q[a]?q[a]=h.exports:l===d&&s||(q[a]=l))}else a&&(q[a]=c)},c=b=n=function(a,b,c,e,f){if("string"==typeof a)return p[a]?p[a](b):j(o(a,b).f);if(!a.splice){if(s=a,s.deps&&n(s.deps,s.callback),!b)return;b.splice?(a=b,b=c,c=null):a=d}return b=b||function(){},"function"==typeof c&&(c=e,e=f),e?m(d,a,b,c):setTimeout(function(){m(d,a,b,c)},4),n},n.config=function(a){return n(a)},c._defined=q,a=function(a,b,c){b.splice||(c=b,b=[]),e(q,a)||e(r,a)||(r[a]=[a,b,c])},a.amd={jQuery:!0}}(),a("almond",function(){}),a("jquery",[],function(){var a=jQuery||$;return null==a&&console&&console.error&&console.error("Select2: An instance of jQuery or a jQuery-compatible library was not found. Make sure that you are including jQuery before Select2 on your web page."),a}),a("select2/utils",["jquery"],function(a){function b(a){var b=a.prototype,c=[];for(var d in b){var e=b[d];"function"==typeof e&&"constructor"!==d&&c.push(d)}return c}var c={};c.Extend=function(a,b){function c(){this.constructor=a}var d={}.hasOwnProperty;for(var e in b)d.call(b,e)&&(a[e]=b[e]);return c.prototype=b.prototype,a.prototype=new c,a.__super__=b.prototype,a},c.Decorate=function(a,c){function d(){var b=Array.prototype.unshift,d=c.prototype.constructor.length,e=a.prototype.constructor;d>0&&(b.call(arguments,a.prototype.constructor),e=c.prototype.constructor),e.apply(this,arguments)}function e(){this.constructor=d}var f=b(c),g=b(a);c.displayName=a.displayName,d.prototype=new e;for(var h=0;h<g.length;h++){var i=g[h];d.prototype[i]=a.prototype[i]}for(var j=(function(a){var b=function(){};a in d.prototype&&(b=d.prototype[a]);var e=c.prototype[a];return function(){var a=Array.prototype.unshift;return a.call(arguments,b),e.apply(this,arguments)}}),k=0;k<f.length;k++){var l=f[k];d.prototype[l]=j(l)}return d};var d=function(){this.listeners={}};return d.prototype.on=function(a,b){this.listeners=this.listeners||{},a in this.listeners?this.listeners[a].push(b):this.listeners[a]=[b]},d.prototype.trigger=function(a){var b=Array.prototype.slice;this.listeners=this.listeners||{},a in this.listeners&&this.invoke(this.listeners[a],b.call(arguments,1)),"*"in this.listeners&&this.invoke(this.listeners["*"],arguments)},d.prototype.invoke=function(a,b){for(var c=0,d=a.length;d>c;c++)a[c].apply(this,b)},c.Observable=d,c.generateChars=function(a){for(var b="",c=0;a>c;c++){var d=Math.floor(36*Math.random());b+=d.toString(36)}return b},c.bind=function(a,b){return function(){a.apply(b,arguments)}},c._convertData=function(a){for(var b in a){var c=b.split("-"),d=a;if(1!==c.length){for(var e=0;e<c.length;e++){var f=c[e];f=f.substring(0,1).toLowerCase()+f.substring(1),f in d||(d[f]={}),e==c.length-1&&(d[f]=a[b]),d=d[f]}delete a[b]}}return a},c.hasScroll=function(b,c){var d=a(c),e=c.style.overflowX,f=c.style.overflowY;return e!==f||"hidden"!==f&&"visible"!==f?"scroll"===e||"scroll"===f?!0:d.innerHeight()<c.scrollHeight||d.innerWidth()<c.scrollWidth:!1},c.escapeMarkup=function(a){var b={"\\":"&#92;","&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#47;"};return"string"!=typeof a?a:String(a).replace(/[&<>"'\/\\]/g,function(a){return b[a]})},c}),a("select2/results",["jquery","./utils"],function(a,b){function c(a,b,d){this.$element=a,this.data=d,this.options=b,c.__super__.constructor.call(this)}return b.Extend(c,b.Observable),c.prototype.render=function(){var b=a('<ul class="select2-results__options" role="tree"></ul>');return this.options.get("multiple")&&b.attr("aria-multiselectable","true"),this.$results=b,b},c.prototype.clear=function(){this.$results.empty()},c.prototype.displayMessage=function(b){var c=this.options.get("escapeMarkup");this.clear(),this.hideLoading();var d=a('<li role="treeitem" class="select2-results__option"></li>'),e=this.options.get("translations").get(b.message);d.append(c(e(b.args))),this.$results.append(d)},c.prototype.append=function(a){this.hideLoading();var b=[];if(null==a.results||0===a.results.length)return void(0===this.$results.children().length&&this.trigger("results:message",{message:"noResults"}));a.results=this.sort(a.results);for(var c=0;c<a.results.length;c++){var d=a.results[c],e=this.option(d);b.push(e)}this.$results.append(b)},c.prototype.position=function(a,b){var c=b.find(".select2-results");c.append(a)},c.prototype.sort=function(a){var b=this.options.get("sorter");return b(a)},c.prototype.setClasses=function(){var b=this;this.data.current(function(c){var d=a.map(c,function(a){return a.id.toString()}),e=b.$results.find(".select2-results__option[aria-selected]");e.each(function(){var b=a(this),c=a.data(this,"data");a.inArray(c.id,d)>-1?b.attr("aria-selected","true"):b.attr("aria-selected","false")});var f=e.filter("[aria-selected=true]");f.length>0?f.first().trigger("mouseenter"):e.first().trigger("mouseenter")})},c.prototype.showLoading=function(a){this.hideLoading();var b=this.options.get("translations").get("searching"),c={disabled:!0,loading:!0,text:b(a)},d=this.option(c);d.className+=" loading-results",this.$results.prepend(d)},c.prototype.hideLoading=function(){this.$results.find(".loading-results").remove()},c.prototype.option=function(b){var c=document.createElement("li");c.className="select2-results__option";var d={role:"treeitem","aria-selected":"false"};b.disabled&&(delete d["aria-selected"],d["aria-disabled"]="true"),null==b.id&&delete d["aria-selected"],null!=b._resultId&&(c.id=b._resultId),b.title&&(c.title=b.title),b.children&&(d.role="group",d["aria-label"]=b.text,delete d["aria-selected"]);for(var e in d){var f=d[e];c.setAttribute(e,f)}if(b.children){var g=a(c),h=document.createElement("strong");h.className="select2-results__group";{a(h)}this.template(b,h);for(var i=[],j=0;j<b.children.length;j++){var k=b.children[j],l=this.option(k);i.push(l)}var m=a("<ul></ul>",{"class":"select2-results__options select2-results__options--nested"});m.append(i),g.append(h),g.append(m)}else this.template(b,c);return a.data(c,"data",b),c},c.prototype.bind=function(b){var c=this,d=b.id+"-results";this.$results.attr("id",d),b.on("results:all",function(a){c.clear(),c.append(a.data),b.isOpen()&&c.setClasses()}),b.on("results:append",function(a){c.append(a.data),b.isOpen()&&c.setClasses()}),b.on("query",function(a){c.showLoading(a)}),b.on("select",function(){b.isOpen()&&c.setClasses()}),b.on("unselect",function(){b.isOpen()&&c.setClasses()}),b.on("open",function(){c.$results.attr("aria-expanded","true"),c.$results.attr("aria-hidden","false"),c.setClasses(),c.ensureHighlightVisible()}),b.on("close",function(){c.$results.attr("aria-expanded","false"),c.$results.attr("aria-hidden","true"),c.$results.removeAttr("aria-activedescendant")}),b.on("results:select",function(){var a=c.getHighlightedResults();if(0!==a.length){var b=a.data("data");"true"==a.attr("aria-selected")?c.options.get("multiple")?c.trigger("unselect",{data:b}):c.trigger("close"):c.trigger("select",{data:b})}}),b.on("results:previous",function(){var a=c.getHighlightedResults(),b=c.$results.find("[aria-selected]"),d=b.index(a);if(0!==d){var e=d-1;0===a.length&&(e=0);var f=b.eq(e);f.trigger("mouseenter");var g=c.$results.offset().top,h=f.offset().top,i=c.$results.scrollTop()+(h-g);0===e?c.$results.scrollTop(0):0>h-g&&c.$results.scrollTop(i)}}),b.on("results:next",function(){var a=c.getHighlightedResults(),b=c.$results.find("[aria-selected]"),d=b.index(a),e=d+1;if(!(e>=b.length)){var f=b.eq(e);f.trigger("mouseenter");var g=c.$results.offset().top+c.$results.outerHeight(!1),h=f.offset().top+f.outerHeight(!1),i=c.$results.scrollTop()+h-g;0===e?c.$results.scrollTop(0):h>g&&c.$results.scrollTop(i)}}),b.on("results:focus",function(a){a.element.addClass("select2-results__option--highlighted")}),b.on("results:message",function(a){c.displayMessage(a)}),a.fn.mousewheel&&this.$results.on("mousewheel",function(a){var b=c.$results.scrollTop(),d=c.$results.get(0).scrollHeight-c.$results.scrollTop()+a.deltaY,e=a.deltaY>0&&b-a.deltaY<=0,f=a.deltaY<0&&d<=c.$results.height();e?(c.$results.scrollTop(0),a.preventDefault(),a.stopPropagation()):f&&(c.$results.scrollTop(c.$results.get(0).scrollHeight-c.$results.height()),a.preventDefault(),a.stopPropagation())}),this.$results.on("mouseup",".select2-results__option[aria-selected]",function(b){var d=a(this),e=d.data("data");return"true"===d.attr("aria-selected")?void(c.options.get("multiple")?c.trigger("unselect",{originalEvent:b,data:e}):c.trigger("close")):void c.trigger("select",{originalEvent:b,data:e})}),this.$results.on("mouseenter",".select2-results__option[aria-selected]",function(){var b=a(this).data("data");c.getHighlightedResults().removeClass("select2-results__option--highlighted"),c.trigger("results:focus",{data:b,element:a(this)})})},c.prototype.getHighlightedResults=function(){var a=this.$results.find(".select2-results__option--highlighted");return a},c.prototype.destroy=function(){this.$results.remove()},c.prototype.ensureHighlightVisible=function(){var a=this.getHighlightedResults();if(0!==a.length){var b=this.$results.find("[aria-selected]"),c=b.index(a),d=this.$results.offset().top,e=a.offset().top,f=this.$results.scrollTop()+(e-d),g=e-d;f-=2*a.outerHeight(!1),2>=c?this.$results.scrollTop(0):(g>this.$results.outerHeight()||0>g)&&this.$results.scrollTop(f)}},c.prototype.template=function(b,c){var d=this.options.get("templateResult"),e=this.options.get("escapeMarkup"),f=d(b);null==f?c.style.display="none":"string"==typeof f?c.innerHTML=e(f):a(c).append(f)},c}),a("select2/keys",[],function(){var a={BACKSPACE:8,TAB:9,ENTER:13,SHIFT:16,CTRL:17,ALT:18,ESC:27,SPACE:32,PAGE_UP:33,PAGE_DOWN:34,END:35,HOME:36,LEFT:37,UP:38,RIGHT:39,DOWN:40,DELETE:46};return a}),a("select2/selection/base",["jquery","../utils","../keys"],function(a,b,c){function d(a,b){this.$element=a,this.options=b,d.__super__.constructor.call(this)}return b.Extend(d,b.Observable),d.prototype.render=function(){var b=a('<span class="select2-selection" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"></span>');return this._tabindex=0,null!=this.$element.data("old-tabindex")?this._tabindex=this.$element.data("old-tabindex"):null!=this.$element.attr("tabindex")&&(this._tabindex=this.$element.attr("tabindex")),b.attr("title",this.$element.attr("title")),b.attr("tabindex",this._tabindex),this.$selection=b,b},d.prototype.bind=function(a){var b=this,d=(a.id+"-container",a.id+"-results");this.container=a,this.$selection.attr("aria-owns",d),this.$selection.on("focus",function(a){b.trigger("focus",a)}),this.$selection.on("blur",function(a){b.trigger("blur",a)}),this.$selection.on("keydown",function(a){b.trigger("keypress",a),a.which===c.SPACE&&a.preventDefault()}),a.on("results:focus",function(a){b.$selection.attr("aria-activedescendant",a.data._resultId)}),a.on("selection:update",function(a){b.update(a.data)}),a.on("open",function(){b.$selection.attr("aria-expanded","true"),b._attachCloseHandler(a)}),a.on("close",function(){b.$selection.attr("aria-expanded","false"),b.$selection.removeAttr("aria-activedescendant"),b.$selection.focus(),b._detachCloseHandler(a)}),a.on("enable",function(){b.$selection.attr("tabindex",b._tabindex)}),a.on("disable",function(){b.$selection.attr("tabindex","-1")})},d.prototype._attachCloseHandler=function(b){a(document.body).on("mousedown.select2."+b.id,function(b){var c=a(b.target),d=c.closest(".select2"),e=a(".select2.select2-container--open");e.each(function(){var b=a(this);if(this!=d[0]){var c=b.data("element");c.select2("close")}})})},d.prototype._detachCloseHandler=function(b){a(document.body).off("mousedown.select2."+b.id)},d.prototype.position=function(a,b){var c=b.find(".selection");c.append(a)},d.prototype.destroy=function(){this._detachCloseHandler(this.container)},d.prototype.update=function(){throw new Error("The `update` method must be defined in child classes.")},d}),a("select2/selection/single",["jquery","./base","../utils","../keys"],function(a,b,c){function d(){d.__super__.constructor.apply(this,arguments)}return c.Extend(d,b),d.prototype.render=function(){var a=d.__super__.render.call(this);return a.addClass("select2-selection--single"),a.html('<span class="select2-selection__rendered"></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'),a},d.prototype.bind=function(a){var b=this;d.__super__.bind.apply(this,arguments);var c=a.id+"-container";this.$selection.find(".select2-selection__rendered").attr("id",c),this.$selection.attr("aria-labelledby",c),this.$selection.on("mousedown",function(a){1===a.which&&b.trigger("toggle",{originalEvent:a})}),this.$selection.on("focus",function(){}),this.$selection.on("blur",function(){}),a.on("selection:update",function(a){b.update(a.data)})},d.prototype.clear=function(){this.$selection.find(".select2-selection__rendered").empty()},d.prototype.display=function(a){var b=this.options.get("templateSelection"),c=this.options.get("escapeMarkup");return c(b(a))},d.prototype.selectionContainer=function(){return a("<span></span>")},d.prototype.update=function(a){if(0===a.length)return void this.clear();var b=a[0],c=this.display(b),d=this.$selection.find(".select2-selection__rendered");d.empty().append(c),d.prop("title",b.title||b.text)},d}),a("select2/selection/multiple",["jquery","./base","../utils"],function(a,b,c){function d(){d.__super__.constructor.apply(this,arguments)}return c.Extend(d,b),d.prototype.render=function(){var a=d.__super__.render.call(this);return a.addClass("select2-selection--multiple"),a.html('<ul class="select2-selection__rendered"></ul>'),a},d.prototype.bind=function(){var b=this;d.__super__.bind.apply(this,arguments),this.$selection.on("click",function(a){b.trigger("toggle",{originalEvent:a})}),this.$selection.on("click",".select2-selection__choice__remove",function(c){var d=a(this),e=d.parent(),f=e.data("data");b.trigger("unselect",{originalEvent:c,data:f})})},d.prototype.clear=function(){this.$selection.find(".select2-selection__rendered").empty()},d.prototype.display=function(a){var b=this.options.get("templateSelection"),c=this.options.get("escapeMarkup");return c(b(a))},d.prototype.selectionContainer=function(){var b=a('<li class="select2-selection__choice"><span class="select2-selection__choice__remove" role="presentation">&times;</span></li>');return b},d.prototype.update=function(a){if(this.clear(),0!==a.length){for(var b=[],c=0;c<a.length;c++){var d=a[c],e=this.display(d),f=this.selectionContainer();f.append(e),f.prop("title",d.title),f.data("data",d),b.push(f)}this.$selection.find(".select2-selection__rendered").append(b)}},d}),a("select2/selection/placeholder",["../utils"],function(){function a(a,b,c){this.placeholder=this.normalizePlaceholder(c.get("placeholder")),a.call(this,b,c)}return a.prototype.normalizePlaceholder=function(a,b){return"string"==typeof b&&(b={id:"",text:b}),b},a.prototype.createPlaceholder=function(a,b){var c=this.selectionContainer();return c.html(this.display(b)),c.addClass("select2-selection__placeholder").removeClass("select2-selection__choice"),c},a.prototype.update=function(a,b){var c=1==b.length&&b[0].id!=this.placeholder.id,d=b.length>1;if(d||c)return a.call(this,b);this.clear();var e=this.createPlaceholder(this.placeholder);this.$selection.find(".select2-selection__rendered").append(e)},a}),a("select2/selection/allowClear",["jquery"],function(a){function b(){}return b.prototype.bind=function(b,c,d){var e=this;b.call(this,c,d),null==e.placeholder&&window.console&&console.error&&console.error("Select2: The `allowClear` option should be used in combination with the `placeholder` option."),this.$selection.on("mousedown",".select2-selection__clear",function(b){if(!e.options.get("disabled")){b.stopPropagation();for(var c=a(this).data("data"),d=0;d<c.length;d++){var f={data:c[d]};if(e.trigger("unselect",f),f.prevented)return}e.$element.val(e.placeholder.id).trigger("change"),e.trigger("toggle")}})},b.prototype.update=function(b,c){if(b.call(this,c),!(this.$selection.find(".select2-selection__placeholder").length>0||0===c.length)){var d=a('<span class="select2-selection__clear">&times;</span>');d.data("data",c),this.$selection.find(".select2-selection__rendered").append(d)}},b}),a("select2/selection/search",["jquery","../utils","../keys"],function(a,b,c){function d(a,b,c){a.call(this,b,c)}return d.prototype.render=function(b){var c=a('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></li>');this.$searchContainer=c,this.$search=c.find("input");var d=b.call(this);return d},d.prototype.bind=function(a,b,d){var e=this;a.call(this,b,d),b.on("open",function(){e.$search.attr("tabindex",0),e.$search.focus()}),b.on("close",function(){e.$search.attr("tabindex",-1),e.$search.val(""),e.$search.focus()}),b.on("enable",function(){e.$search.prop("disabled",!1)}),b.on("disable",function(){e.$search.prop("disabled",!0)}),this.$selection.on("focusin",".select2-search--inline",function(a){e.trigger("focus",a)}),this.$selection.on("focusout",".select2-search--inline",function(a){e.trigger("blur",a)}),this.$selection.on("keydown",".select2-search--inline",function(a){a.stopPropagation(),e.trigger("keypress",a),e._keyUpPrevented=a.isDefaultPrevented();var b=a.which;if(b===c.BACKSPACE&&""===e.$search.val()){var d=e.$searchContainer.prev(".select2-selection__choice");if(d.length>0){var f=d.data("data");e.searchRemoveChoice(f)}}}),this.$selection.on("keyup",".select2-search--inline",function(a){e.handleSearch(a)})},d.prototype.createPlaceholder=function(a,b){this.$search.attr("placeholder",b.text)},d.prototype.update=function(a,b){this.$search.attr("placeholder",""),a.call(this,b),this.$selection.find(".select2-selection__rendered").append(this.$searchContainer),this.resizeSearch()},d.prototype.handleSearch=function(){if(this.resizeSearch(),!this._keyUpPrevented){var a=this.$search.val();this.trigger("query",{term:a})}this._keyUpPrevented=!1},d.prototype.searchRemoveChoice=function(a,b){this.trigger("unselect",{data:b}),this.trigger("open"),this.$search.val(b.text+" ")},d.prototype.resizeSearch=function(){this.$search.css("width","25px");var a="";if(""!==this.$search.attr("placeholder"))a=this.$selection.find(".select2-selection__rendered").innerWidth();else{var b=this.$search.val().length+1;a=.75*b+"em"}this.$search.css("width",a)},d}),a("select2/selection/eventRelay",["jquery"],function(a){function b(){}return b.prototype.bind=function(b,c,d){var e=this,f=["open","opening","close","closing","select","selecting","unselect","unselecting"],g=["opening","closing","selecting","unselecting"];b.call(this,c,d),c.on("*",function(b,c){if(-1!==a.inArray(b,f)){c=c||{};var d=a.Event("select2:"+b,{params:c});e.$element.trigger(d),-1!==a.inArray(b,g)&&(c.prevented=d.isDefaultPrevented())}})},b}),a("select2/translation",["jquery"],function(a){function c(a){this.dict=a||{}}return c.prototype.all=function(){return this.dict},c.prototype.get=function(a){return this.dict[a]},c.prototype.extend=function(b){this.dict=a.extend({},b.all(),this.dict)},c._cache={},c.loadPath=function(a){if(!(a in c._cache)){var d=b(a);c._cache[a]=d}return new c(c._cache[a])},c}),a("select2/diacritics",[],function(){var a={"Ⓐ":"A","Ａ":"A","À":"A","Á":"A","Â":"A","Ầ":"A","Ấ":"A","Ẫ":"A","Ẩ":"A","Ã":"A","Ā":"A","Ă":"A","Ằ":"A","Ắ":"A","Ẵ":"A","Ẳ":"A","Ȧ":"A","Ǡ":"A","Ä":"A","Ǟ":"A","Ả":"A","Å":"A","Ǻ":"A","Ǎ":"A","Ȁ":"A","Ȃ":"A","Ạ":"A","Ậ":"A","Ặ":"A","Ḁ":"A","Ą":"A","Ⱥ":"A","Ɐ":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ⓑ":"B","Ｂ":"B","Ḃ":"B","Ḅ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ɓ":"B","Ⓒ":"C","Ｃ":"C","Ć":"C","Ĉ":"C","Ċ":"C","Č":"C","Ç":"C","Ḉ":"C","Ƈ":"C","Ȼ":"C","Ꜿ":"C","Ⓓ":"D","Ｄ":"D","Ḋ":"D","Ď":"D","Ḍ":"D","Ḑ":"D","Ḓ":"D","Ḏ":"D","Đ":"D","Ƌ":"D","Ɗ":"D","Ɖ":"D","Ꝺ":"D","Ǳ":"DZ","Ǆ":"DZ","ǲ":"Dz","ǅ":"Dz","Ⓔ":"E","Ｅ":"E","È":"E","É":"E","Ê":"E","Ề":"E","Ế":"E","Ễ":"E","Ể":"E","Ẽ":"E","Ē":"E","Ḕ":"E","Ḗ":"E","Ĕ":"E","Ė":"E","Ë":"E","Ẻ":"E","Ě":"E","Ȅ":"E","Ȇ":"E","Ẹ":"E","Ệ":"E","Ȩ":"E","Ḝ":"E","Ę":"E","Ḙ":"E","Ḛ":"E","Ɛ":"E","Ǝ":"E","Ⓕ":"F","Ｆ":"F","Ḟ":"F","Ƒ":"F","Ꝼ":"F","Ⓖ":"G","Ｇ":"G","Ǵ":"G","Ĝ":"G","Ḡ":"G","Ğ":"G","Ġ":"G","Ǧ":"G","Ģ":"G","Ǥ":"G","Ɠ":"G","Ꞡ":"G","Ᵹ":"G","Ꝿ":"G","Ⓗ":"H","Ｈ":"H","Ĥ":"H","Ḣ":"H","Ḧ":"H","Ȟ":"H","Ḥ":"H","Ḩ":"H","Ḫ":"H","Ħ":"H","Ⱨ":"H","Ⱶ":"H","Ɥ":"H","Ⓘ":"I","Ｉ":"I","Ì":"I","Í":"I","Î":"I","Ĩ":"I","Ī":"I","Ĭ":"I","İ":"I","Ï":"I","Ḯ":"I","Ỉ":"I","Ǐ":"I","Ȉ":"I","Ȋ":"I","Ị":"I","Į":"I","Ḭ":"I","Ɨ":"I","Ⓙ":"J","Ｊ":"J","Ĵ":"J","Ɉ":"J","Ⓚ":"K","Ｋ":"K","Ḱ":"K","Ǩ":"K","Ḳ":"K","Ķ":"K","Ḵ":"K","Ƙ":"K","Ⱪ":"K","Ꝁ":"K","Ꝃ":"K","Ꝅ":"K","Ꞣ":"K","Ⓛ":"L","Ｌ":"L","Ŀ":"L","Ĺ":"L","Ľ":"L","Ḷ":"L","Ḹ":"L","Ļ":"L","Ḽ":"L","Ḻ":"L","Ł":"L","Ƚ":"L","Ɫ":"L","Ⱡ":"L","Ꝉ":"L","Ꝇ":"L","Ꞁ":"L","Ǉ":"LJ","ǈ":"Lj","Ⓜ":"M","Ｍ":"M","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ɯ":"M","Ⓝ":"N","Ｎ":"N","Ǹ":"N","Ń":"N","Ñ":"N","Ṅ":"N","Ň":"N","Ṇ":"N","Ņ":"N","Ṋ":"N","Ṉ":"N","Ƞ":"N","Ɲ":"N","Ꞑ":"N","Ꞥ":"N","Ǌ":"NJ","ǋ":"Nj","Ⓞ":"O","Ｏ":"O","Ò":"O","Ó":"O","Ô":"O","Ồ":"O","Ố":"O","Ỗ":"O","Ổ":"O","Õ":"O","Ṍ":"O","Ȭ":"O","Ṏ":"O","Ō":"O","Ṑ":"O","Ṓ":"O","Ŏ":"O","Ȯ":"O","Ȱ":"O","Ö":"O","Ȫ":"O","Ỏ":"O","Ő":"O","Ǒ":"O","Ȍ":"O","Ȏ":"O","Ơ":"O","Ờ":"O","Ớ":"O","Ỡ":"O","Ở":"O","Ợ":"O","Ọ":"O","Ộ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Ɔ":"O","Ɵ":"O","Ꝋ":"O","Ꝍ":"O","Ƣ":"OI","Ꝏ":"OO","Ȣ":"OU","Ⓟ":"P","Ｐ":"P","Ṕ":"P","Ṗ":"P","Ƥ":"P","Ᵽ":"P","Ꝑ":"P","Ꝓ":"P","Ꝕ":"P","Ⓠ":"Q","Ｑ":"Q","Ꝗ":"Q","Ꝙ":"Q","Ɋ":"Q","Ⓡ":"R","Ｒ":"R","Ŕ":"R","Ṙ":"R","Ř":"R","Ȑ":"R","Ȓ":"R","Ṛ":"R","Ṝ":"R","Ŗ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꝛ":"R","Ꞧ":"R","Ꞃ":"R","Ⓢ":"S","Ｓ":"S","ẞ":"S","Ś":"S","Ṥ":"S","Ŝ":"S","Ṡ":"S","Š":"S","Ṧ":"S","Ṣ":"S","Ṩ":"S","Ș":"S","Ş":"S","Ȿ":"S","Ꞩ":"S","Ꞅ":"S","Ⓣ":"T","Ｔ":"T","Ṫ":"T","Ť":"T","Ṭ":"T","Ț":"T","Ţ":"T","Ṱ":"T","Ṯ":"T","Ŧ":"T","Ƭ":"T","Ʈ":"T","Ⱦ":"T","Ꞇ":"T","Ꜩ":"TZ","Ⓤ":"U","Ｕ":"U","Ù":"U","Ú":"U","Û":"U","Ũ":"U","Ṹ":"U","Ū":"U","Ṻ":"U","Ŭ":"U","Ü":"U","Ǜ":"U","Ǘ":"U","Ǖ":"U","Ǚ":"U","Ủ":"U","Ů":"U","Ű":"U","Ǔ":"U","Ȕ":"U","Ȗ":"U","Ư":"U","Ừ":"U","Ứ":"U","Ữ":"U","Ử":"U","Ự":"U","Ụ":"U","Ṳ":"U","Ų":"U","Ṷ":"U","Ṵ":"U","Ʉ":"U","Ⓥ":"V","Ｖ":"V","Ṽ":"V","Ṿ":"V","Ʋ":"V","Ꝟ":"V","Ʌ":"V","Ꝡ":"VY","Ⓦ":"W","Ｗ":"W","Ẁ":"W","Ẃ":"W","Ŵ":"W","Ẇ":"W","Ẅ":"W","Ẉ":"W","Ⱳ":"W","Ⓧ":"X","Ｘ":"X","Ẋ":"X","Ẍ":"X","Ⓨ":"Y","Ｙ":"Y","Ỳ":"Y","Ý":"Y","Ŷ":"Y","Ỹ":"Y","Ȳ":"Y","Ẏ":"Y","Ÿ":"Y","Ỷ":"Y","Ỵ":"Y","Ƴ":"Y","Ɏ":"Y","Ỿ":"Y","Ⓩ":"Z","Ｚ":"Z","Ź":"Z","Ẑ":"Z","Ż":"Z","Ž":"Z","Ẓ":"Z","Ẕ":"Z","Ƶ":"Z","Ȥ":"Z","Ɀ":"Z","Ⱬ":"Z","Ꝣ":"Z","ⓐ":"a","ａ":"a","ẚ":"a","à":"a","á":"a","â":"a","ầ":"a","ấ":"a","ẫ":"a","ẩ":"a","ã":"a","ā":"a","ă":"a","ằ":"a","ắ":"a","ẵ":"a","ẳ":"a","ȧ":"a","ǡ":"a","ä":"a","ǟ":"a","ả":"a","å":"a","ǻ":"a","ǎ":"a","ȁ":"a","ȃ":"a","ạ":"a","ậ":"a","ặ":"a","ḁ":"a","ą":"a","ⱥ":"a","ɐ":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ⓑ":"b","ｂ":"b","ḃ":"b","ḅ":"b","ḇ":"b","ƀ":"b","ƃ":"b","ɓ":"b","ⓒ":"c","ｃ":"c","ć":"c","ĉ":"c","ċ":"c","č":"c","ç":"c","ḉ":"c","ƈ":"c","ȼ":"c","ꜿ":"c","ↄ":"c","ⓓ":"d","ｄ":"d","ḋ":"d","ď":"d","ḍ":"d","ḑ":"d","ḓ":"d","ḏ":"d","đ":"d","ƌ":"d","ɖ":"d","ɗ":"d","ꝺ":"d","ǳ":"dz","ǆ":"dz","ⓔ":"e","ｅ":"e","è":"e","é":"e","ê":"e","ề":"e","ế":"e","ễ":"e","ể":"e","ẽ":"e","ē":"e","ḕ":"e","ḗ":"e","ĕ":"e","ė":"e","ë":"e","ẻ":"e","ě":"e","ȅ":"e","ȇ":"e","ẹ":"e","ệ":"e","ȩ":"e","ḝ":"e","ę":"e","ḙ":"e","ḛ":"e","ɇ":"e","ɛ":"e","ǝ":"e","ⓕ":"f","ｆ":"f","ḟ":"f","ƒ":"f","ꝼ":"f","ⓖ":"g","ｇ":"g","ǵ":"g","ĝ":"g","ḡ":"g","ğ":"g","ġ":"g","ǧ":"g","ģ":"g","ǥ":"g","ɠ":"g","ꞡ":"g","ᵹ":"g","ꝿ":"g","ⓗ":"h","ｈ":"h","ĥ":"h","ḣ":"h","ḧ":"h","ȟ":"h","ḥ":"h","ḩ":"h","ḫ":"h","ẖ":"h","ħ":"h","ⱨ":"h","ⱶ":"h","ɥ":"h","ƕ":"hv","ⓘ":"i","ｉ":"i","ì":"i","í":"i","î":"i","ĩ":"i","ī":"i","ĭ":"i","ï":"i","ḯ":"i","ỉ":"i","ǐ":"i","ȉ":"i","ȋ":"i","ị":"i","į":"i","ḭ":"i","ɨ":"i","ı":"i","ⓙ":"j","ｊ":"j","ĵ":"j","ǰ":"j","ɉ":"j","ⓚ":"k","ｋ":"k","ḱ":"k","ǩ":"k","ḳ":"k","ķ":"k","ḵ":"k","ƙ":"k","ⱪ":"k","ꝁ":"k","ꝃ":"k","ꝅ":"k","ꞣ":"k","ⓛ":"l","ｌ":"l","ŀ":"l","ĺ":"l","ľ":"l","ḷ":"l","ḹ":"l","ļ":"l","ḽ":"l","ḻ":"l","ſ":"l","ł":"l","ƚ":"l","ɫ":"l","ⱡ":"l","ꝉ":"l","ꞁ":"l","ꝇ":"l","ǉ":"lj","ⓜ":"m","ｍ":"m","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ɯ":"m","ⓝ":"n","ｎ":"n","ǹ":"n","ń":"n","ñ":"n","ṅ":"n","ň":"n","ṇ":"n","ņ":"n","ṋ":"n","ṉ":"n","ƞ":"n","ɲ":"n","ŉ":"n","ꞑ":"n","ꞥ":"n","ǌ":"nj","ⓞ":"o","ｏ":"o","ò":"o","ó":"o","ô":"o","ồ":"o","ố":"o","ỗ":"o","ổ":"o","õ":"o","ṍ":"o","ȭ":"o","ṏ":"o","ō":"o","ṑ":"o","ṓ":"o","ŏ":"o","ȯ":"o","ȱ":"o","ö":"o","ȫ":"o","ỏ":"o","ő":"o","ǒ":"o","ȍ":"o","ȏ":"o","ơ":"o","ờ":"o","ớ":"o","ỡ":"o","ở":"o","ợ":"o","ọ":"o","ộ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","ɔ":"o","ꝋ":"o","ꝍ":"o","ɵ":"o","ƣ":"oi","ȣ":"ou","ꝏ":"oo","ⓟ":"p","ｐ":"p","ṕ":"p","ṗ":"p","ƥ":"p","ᵽ":"p","ꝑ":"p","ꝓ":"p","ꝕ":"p","ⓠ":"q","ｑ":"q","ɋ":"q","ꝗ":"q","ꝙ":"q","ⓡ":"r","ｒ":"r","ŕ":"r","ṙ":"r","ř":"r","ȑ":"r","ȓ":"r","ṛ":"r","ṝ":"r","ŗ":"r","ṟ":"r","ɍ":"r","ɽ":"r","ꝛ":"r","ꞧ":"r","ꞃ":"r","ⓢ":"s","ｓ":"s","ß":"s","ś":"s","ṥ":"s","ŝ":"s","ṡ":"s","š":"s","ṧ":"s","ṣ":"s","ṩ":"s","ș":"s","ş":"s","ȿ":"s","ꞩ":"s","ꞅ":"s","ẛ":"s","ⓣ":"t","ｔ":"t","ṫ":"t","ẗ":"t","ť":"t","ṭ":"t","ț":"t","ţ":"t","ṱ":"t","ṯ":"t","ŧ":"t","ƭ":"t","ʈ":"t","ⱦ":"t","ꞇ":"t","ꜩ":"tz","ⓤ":"u","ｕ":"u","ù":"u","ú":"u","û":"u","ũ":"u","ṹ":"u","ū":"u","ṻ":"u","ŭ":"u","ü":"u","ǜ":"u","ǘ":"u","ǖ":"u","ǚ":"u","ủ":"u","ů":"u","ű":"u","ǔ":"u","ȕ":"u","ȗ":"u","ư":"u","ừ":"u","ứ":"u","ữ":"u","ử":"u","ự":"u","ụ":"u","ṳ":"u","ų":"u","ṷ":"u","ṵ":"u","ʉ":"u","ⓥ":"v","ｖ":"v","ṽ":"v","ṿ":"v","ʋ":"v","ꝟ":"v","ʌ":"v","ꝡ":"vy","ⓦ":"w","ｗ":"w","ẁ":"w","ẃ":"w","ŵ":"w","ẇ":"w","ẅ":"w","ẘ":"w","ẉ":"w","ⱳ":"w","ⓧ":"x","ｘ":"x","ẋ":"x","ẍ":"x","ⓨ":"y","ｙ":"y","ỳ":"y","ý":"y","ŷ":"y","ỹ":"y","ȳ":"y","ẏ":"y","ÿ":"y","ỷ":"y","ẙ":"y","ỵ":"y","ƴ":"y","ɏ":"y","ỿ":"y","ⓩ":"z","ｚ":"z","ź":"z","ẑ":"z","ż":"z","ž":"z","ẓ":"z","ẕ":"z","ƶ":"z","ȥ":"z","ɀ":"z","ⱬ":"z","ꝣ":"z","Ά":"Α","Έ":"Ε","Ή":"Η","Ί":"Ι","Ϊ":"Ι","Ό":"Ο","Ύ":"Υ","Ϋ":"Υ","Ώ":"Ω","ά":"α","έ":"ε","ή":"η","ί":"ι","ϊ":"ι","ΐ":"ι","ό":"ο","ύ":"υ","ϋ":"υ","ΰ":"υ","ω":"ω","ς":"σ"};return a}),a("select2/data/base",["../utils"],function(a){function b(){b.__super__.constructor.call(this)}return a.Extend(b,a.Observable),b.prototype.current=function(){throw new Error("The `current` method must be defined in child classes.")},b.prototype.query=function(){throw new Error("The `query` method must be defined in child classes.")},b.prototype.bind=function(){},b.prototype.destroy=function(){},b.prototype.generateResultId=function(b,c){var d=b.id+"-result-";return d+=a.generateChars(4),d+=null!=c.id?"-"+c.id.toString():"-"+a.generateChars(4)},b}),a("select2/data/select",["./base","../utils","jquery"],function(a,b,c){function d(a,b){this.$element=a,this.options=b,d.__super__.constructor.call(this)}return b.Extend(d,a),d.prototype.current=function(a){var b=[],d=this;this.$element.find(":selected").each(function(){var a=c(this),e=d.item(a);b.push(e)}),a(b)},d.prototype.select=function(a){var b=this;if(c(a.element).is("option"))return a.element.selected=!0,void this.$element.trigger("change");if(this.$element.prop("multiple"))this.current(function(d){var e=[];a=[a],a.push.apply(a,d);for(var f=0;f<a.length;f++){var g=a[f].id;-1===c.inArray(g,e)&&e.push(g)}b.$element.val(e),b.$element.trigger("change")});else{var d=a.id;this.$element.val(d),this.$element.trigger("change")}},d.prototype.unselect=function(a){var b=this;if(this.$element.prop("multiple"))return c(a.element).is("option")?(a.element.selected=!1,void this.$element.trigger("change")):void this.current(function(d){for(var e=[],f=0;f<d.length;f++){var g=d[f].id;g!==a.id&&-1===c.inArray(g,e)&&e.push(g)}b.$element.val(e),b.$element.trigger("change")})},d.prototype.bind=function(a){var b=this;this.container=a,a.on("select",function(a){b.select(a.data)}),a.on("unselect",function(a){b.unselect(a.data)})},d.prototype.destroy=function(){this.$element.find("*").each(function(){c.removeData(this,"data")})},d.prototype.query=function(a,b){var d=[],e=this,f=this.$element.children();f.each(function(){var b=c(this);if(b.is("option")||b.is("optgroup")){var f=e.item(b),g=e.matches(a,f);null!==g&&d.push(g)}}),b({results:d})},d.prototype.option=function(a){var b;a.children?(b=document.createElement("optgroup"),b.label=a.text):(b=document.createElement("option"),void 0!==b.textContent?b.textContent=a.text:b.innerText=a.text),a.id&&(b.value=a.id),a.disabled&&(b.disabled=!0),a.selected&&(b.selected=!0),a.title&&(b.title=a.title);var d=c(b),e=this._normalizeItem(a);return e.element=b,c.data(b,"data",e),d},d.prototype.item=function(a){var b={};if(b=c.data(a[0],"data"),null!=b)return b;if(a.is("option"))b={id:a.val(),text:a.html(),disabled:a.prop("disabled"),selected:a.prop("selected"),title:a.prop("title")};else if(a.is("optgroup")){b={text:a.prop("label"),children:[],title:a.prop("title")};for(var d=a.children("option"),e=[],f=0;f<d.length;f++){var g=c(d[f]),h=this.item(g);e.push(h)}b.children=e}return b=this._normalizeItem(b),b.element=a[0],c.data(a[0],"data",b),b},d.prototype._normalizeItem=function(a){c.isPlainObject(a)||(a={id:a,text:a}),a=c.extend({},{text:""},a);var b={selected:!1,disabled:!1};return null!=a.id&&(a.id=a.id.toString()),null!=a.text&&(a.text=a.text.toString()),null==a._resultId&&a.id&&null!=this.container&&(a._resultId=this.generateResultId(this.container,a)),c.extend({},b,a)},d.prototype.matches=function(a,b){var c=this.options.get("matcher");return c(a,b)},d}),a("select2/data/array",["./select","../utils","jquery"],function(a,b,c){function d(a,b){var c=b.get("data")||[];d.__super__.constructor.call(this,a,b),a.append(this.convertToOptions(c))}return b.Extend(d,a),d.prototype.select=function(a){var b=this.$element.find('option[value="'+a.id+'"]');
0===b.length&&(b=this.option(a),this.$element.append(b)),d.__super__.select.call(this,a)},d.prototype.convertToOptions=function(a){function b(a){return function(){return c(this).val()==a.id}}for(var d=this,e=this.$element.find("option"),f=e.map(function(){return d.item(c(this)).id}).get(),g=[],h=0;h<a.length;h++){var i=this._normalizeItem(a[h]);if(c.inArray(i.id,f)>=0){var j=e.filter(b(i)),k=this.item(j),l=(c.extend(!0,{},k,i),this.option(k));j.replaceWith(l)}else{var m=this.option(i);if(i.children){var n=this.convertToOptions(i.children);m.append(n)}g.push(m)}}return g},d}),a("select2/data/ajax",["./array","../utils","jquery"],function(a,b,c){function d(b,c){this.ajaxOptions=this._applyDefaults(c.get("ajax")),null!=this.ajaxOptions.processResults&&(this.processResults=this.ajaxOptions.processResults),a.__super__.constructor.call(this,b,c)}return b.Extend(d,a),d.prototype._applyDefaults=function(a){var b={data:function(a){return{q:a.term}},transport:function(a,b,d){var e=c.ajax(a);return e.then(b),e.fail(d),e}};return c.extend({},b,a,!0)},d.prototype.processResults=function(a){return a},d.prototype.query=function(a,b){function d(){var d=f.transport(f,function(d){var f=e.processResults(d,a);window.console&&console.error&&(f&&f.results&&c.isArray(f.results)||console.error("Select2: The AJAX results did not return an array in the `results` key of the response.")),b(f)},function(){});e._request=d}var e=this;this._request&&(this._request.abort(),this._request=null);var f=c.extend({type:"GET"},this.ajaxOptions);"function"==typeof f.url&&(f.url=f.url(a)),"function"==typeof f.data&&(f.data=f.data(a)),this.ajaxOptions.delay&&""!==a.term?(this._queryTimeout&&window.clearTimeout(this._queryTimeout),this._queryTimeout=window.setTimeout(d,this.ajaxOptions.delay)):d()},d}),a("select2/data/tags",["jquery"],function(a){function b(b,c,d){var e=d.get("tags"),f=d.get("createTag");if(void 0!==f&&(this.createTag=f),b.call(this,c,d),a.isArray(e))for(var g=0;g<e.length;g++){var h=e[g],i=this._normalizeItem(h),j=this.option(i);this.$element.append(j)}}return b.prototype.query=function(a,b,c){function d(a,f){for(var g=a.results,h=0;h<g.length;h++){var i=g[h],j=null!=i.children&&!d({results:i.children},!0),k=i.text===b.term;if(k||j)return f?!1:(a.data=g,void c(a))}if(f)return!0;var l=e.createTag(b);if(null!=l){var m=e.option(l);m.attr("data-select2-tag",!0),e.$element.append(m),e.insertTag(g,l)}a.results=g,c(a)}var e=this;return this._removeOldTags(),null==b.term||""===b.term||null!=b.page?void a.call(this,b,c):void a.call(this,b,d)},b.prototype.createTag=function(a,b){return{id:b.term,text:b.term}},b.prototype.insertTag=function(a,b,c){b.unshift(c)},b.prototype._removeOldTags=function(){var b=(this._lastTag,this.$element.find("option[data-select2-tag]"));b.each(function(){this.selected||a(this).remove()})},b}),a("select2/data/tokenizer",["jquery"],function(a){function b(a,b,c){var d=c.get("tokenizer");void 0!==d&&(this.tokenizer=d),a.call(this,b,c)}return b.prototype.bind=function(a,b,c){a.call(this,b,c),this.$search=b.dropdown.$search||b.selection.$search||c.find(".select2-search__field")},b.prototype.query=function(a,b,c){function d(a){e.select(a)}var e=this;b.term=b.term||"";var f=this.tokenizer(b,this.options,d);f.term!==b.term&&(this.$search.length&&(this.$search.val(f.term),this.$search.focus()),b.term=f.term),a.call(this,b,c)},b.prototype.tokenizer=function(b,c,d,e){for(var f=d.get("tokenSeparators")||[],g=c.term,h=0,i=this.createTag||function(a){return{id:a.term,text:a.term}};h<g.length;){var j=g[h];if(-1!==a.inArray(j,f)){var k=g.substr(0,h),l=a.extend({},c,{term:k}),m=i(l);e(m),g=g.substr(h+1)||"",h=0}else h++}return{term:g}},b}),a("select2/data/minimumInputLength",[],function(){function a(a,b,c){this.minimumInputLength=c.get("minimumInputLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){return b.term=b.term||"",b.term.length<this.minimumInputLength?void this.trigger("results:message",{message:"inputTooShort",args:{minimum:this.minimumInputLength,input:b.term,params:b}}):void a.call(this,b,c)},a}),a("select2/data/maximumInputLength",[],function(){function a(a,b,c){this.maximumInputLength=c.get("maximumInputLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){return b.term=b.term||"",this.maximumInputLength>0&&b.term.length>this.maximumInputLength?void this.trigger("results:message",{message:"inputTooLong",args:{maximum:this.maximumInputLength,input:b.term,params:b}}):void a.call(this,b,c)},a}),a("select2/data/maximumSelectionLength",[],function(){function a(a,b,c){this.maximumSelectionLength=c.get("maximumSelectionLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){var d=this;this.current(function(e){var f=null!=e?e.length:0;return d.maximumSelectionLength>0&&f>=d.maximumSelectionLength?void d.trigger("results:message",{message:"maximumSelected",args:{maximum:d.maximumSelectionLength}}):void a.call(d,b,c)})},a}),a("select2/dropdown",["jquery","./utils"],function(a,b){function c(a,b){this.$element=a,this.options=b,c.__super__.constructor.call(this)}return b.Extend(c,b.Observable),c.prototype.render=function(){var b=a('<span class="select2-dropdown"><span class="select2-results"></span></span>');return b.attr("dir",this.options.get("dir")),this.$dropdown=b,b},c.prototype.position=function(){},c.prototype.destroy=function(){this.$dropdown.remove()},c}),a("select2/dropdown/search",["jquery","../utils"],function(a){function b(){}return b.prototype.render=function(b){var c=b.call(this),d=a('<span class="select2-search select2-search--dropdown"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></span>');return this.$searchContainer=d,this.$search=d.find("input"),c.prepend(d),c},b.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),this.$search.on("keydown",function(a){d.trigger("keypress",a),d._keyUpPrevented=a.isDefaultPrevented()}),this.$search.on("keyup",function(a){d.handleSearch(a)}),b.on("open",function(){d.$search.attr("tabindex",0),d.$search.focus(),window.setTimeout(function(){d.$search.focus()},0)}),b.on("close",function(){d.$search.attr("tabindex",-1),d.$search.val("")}),b.on("results:all",function(a){if(null==a.query.term||""===a.query.term){var b=d.showSearch(a);b?d.$searchContainer.removeClass("select2-search--hide"):d.$searchContainer.addClass("select2-search--hide")}})},b.prototype.handleSearch=function(){if(!this._keyUpPrevented){var a=this.$search.val();this.trigger("query",{term:a})}this._keyUpPrevented=!1},b.prototype.showSearch=function(){return!0},b}),a("select2/dropdown/hidePlaceholder",[],function(){function a(a,b,c,d){this.placeholder=this.normalizePlaceholder(c.get("placeholder")),a.call(this,b,c,d)}return a.prototype.append=function(a,b){b.results=this.removePlaceholder(b.results),a.call(this,b)},a.prototype.normalizePlaceholder=function(a,b){return"string"==typeof b&&(b={id:"",text:b}),b},a.prototype.removePlaceholder=function(a,b){for(var c=b.slice(0),d=b.length-1;d>=0;d--){var e=b[d];this.placeholder.id===e.id&&c.splice(d,1)}return c},a}),a("select2/dropdown/infiniteScroll",["jquery"],function(a){function b(a,b,c,d){this.lastParams={},a.call(this,b,c,d),this.$loadingMore=this.createLoadingMore(),this.loading=!1}return b.prototype.append=function(a,b){this.$loadingMore.remove(),this.loading=!1,a.call(this,b),this.showLoadingMore(b)&&this.$results.append(this.$loadingMore)},b.prototype.bind=function(b,c,d){var e=this;b.call(this,c,d),c.on("query",function(a){e.lastParams=a,e.loading=!0}),c.on("query:append",function(a){e.lastParams=a,e.loading=!0}),this.$results.on("scroll",function(){var b=a.contains(document.documentElement,e.$loadingMore[0]);if(!e.loading&&b){var c=e.$results.offset().top+e.$results.outerHeight(!1),d=e.$loadingMore.offset().top+e.$loadingMore.outerHeight(!1);c+50>=d&&e.loadMore()}})},b.prototype.loadMore=function(){this.loading=!0;var b=a.extend({},{page:1},this.lastParams);b.page++,this.trigger("query:append",b)},b.prototype.showLoadingMore=function(a,b){return b.pagination&&b.pagination.more},b.prototype.createLoadingMore=function(){var b=a('<li class="option load-more" role="treeitem"></li>'),c=this.options.get("translations").get("loadingMore");return b.html(c(this.lastParams)),b},b}),a("select2/dropdown/attachBody",["jquery","../utils"],function(a,b){function c(a,b,c){this.$dropdownParent=c.get("dropdownParent")||document.body,a.call(this,b,c)}return c.prototype.bind=function(a,b,c){var d=this,e=!1;a.call(this,b,c),b.on("open",function(){d._showDropdown(),d._attachPositioningHandler(b),e||(e=!0,b.on("results:all",function(){d._positionDropdown(),d._resizeDropdown()}),b.on("results:append",function(){d._positionDropdown(),d._resizeDropdown()}))}),b.on("close",function(){d._hideDropdown(),d._detachPositioningHandler(b)}),this.$dropdownContainer.on("mousedown",function(a){a.stopPropagation()})},c.prototype.position=function(a,b,c){b.attr("class",c.attr("class")),b.removeClass("select2"),b.addClass("select2-container--open"),b.css({position:"absolute",top:-999999}),this.$container=c},c.prototype.render=function(b){var c=a("<span></span>"),d=b.call(this);return c.append(d),this.$dropdownContainer=c,c},c.prototype._hideDropdown=function(){this.$dropdownContainer.detach()},c.prototype._attachPositioningHandler=function(c){var d=this,e="scroll.select2."+c.id,f="resize.select2."+c.id,g="orientationchange.select2."+c.id,h=this.$container.parents().filter(b.hasScroll);h.each(function(){a(this).data("select2-scroll-position",{x:a(this).scrollLeft(),y:a(this).scrollTop()})}),h.on(e,function(){var b=a(this).data("select2-scroll-position");a(this).scrollTop(b.y)}),a(window).on(e+" "+f+" "+g,function(){d._positionDropdown(),d._resizeDropdown()})},c.prototype._detachPositioningHandler=function(c){var d="scroll.select2."+c.id,e="resize.select2."+c.id,f="orientationchange.select2."+c.id,g=this.$container.parents().filter(b.hasScroll);g.off(d),a(window).off(d+" "+e+" "+f)},c.prototype._positionDropdown=function(){var b=a(window),c=this.$dropdown.hasClass("select2-dropdown--above"),d=this.$dropdown.hasClass("select2-dropdown--below"),e=null,f=(this.$container.position(),this.$container.offset());f.bottom=f.top+this.$container.outerHeight(!1);var g={height:this.$container.outerHeight(!1)};g.top=f.top,g.bottom=f.top+g.height;var h={height:this.$dropdown.outerHeight(!1)},i={top:b.scrollTop(),bottom:b.scrollTop()+b.height()},j=i.top<f.top-h.height,k=i.bottom>f.bottom+h.height,l={left:f.left,top:g.bottom};c||d||(e="below"),k||!j||c?!j&&k&&c&&(e="below"):e="above",("above"==e||c&&"below"!==e)&&(l.top=g.top-h.height),null!=e&&(this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--"+e),this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--"+e)),this.$dropdownContainer.css(l)},c.prototype._resizeDropdown=function(){this.$dropdownContainer.width(),this.$dropdown.css({width:this.$container.outerWidth(!1)+"px"})},c.prototype._showDropdown=function(){this.$dropdownContainer.appendTo(this.$dropdownParent),this._positionDropdown(),this._resizeDropdown()},c}),a("select2/dropdown/minimumResultsForSearch",[],function(){function a(b){for(var c=0,d=0;d<b.length;d++){var e=b[d];e.children?c+=a(e.children):c++}return c}function b(a,b,c,d){this.minimumResultsForSearch=c.get("minimumResultsForSearch"),this.minimumResultsForSearch<0&&(this.minimumResultsForSearch=1/0),a.call(this,b,c,d)}return b.prototype.showSearch=function(b,c){return a(c.data.results)<this.minimumResultsForSearch?!1:b.call(this,c)},b}),a("select2/dropdown/selectOnClose",[],function(){function a(){}return a.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),b.on("close",function(){d._handleSelectOnClose()})},a.prototype._handleSelectOnClose=function(){var a=this.getHighlightedResults();a.length<1||a.trigger("mouseup")},a}),a("select2/dropdown/closeOnSelect",[],function(){function a(){}return a.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),b.on("select",function(a){var b=a.originalEvent;b&&b.ctrlKey||d.trigger("close")})},a}),a("select2/i18n/en",[],function(){return{errorLoading:function(){return"The results could not be loaded."},inputTooLong:function(a){var b=a.input.length-a.maximum,c="Please delete "+b+" character";return 1!=b&&(c+="s"),c},inputTooShort:function(a){var b=a.minimum-a.input.length,c="Please enter "+b+" or more characters";return c},loadingMore:function(){return"Loading more results…"},maximumSelected:function(a){var b="You can only select "+a.maximum+" item";return 1!=a.maximum&&(b+="s"),b},noResults:function(){return"No results found"},searching:function(){return"Searching…"}}}),a("select2/defaults",["jquery","./results","./selection/single","./selection/multiple","./selection/placeholder","./selection/allowClear","./selection/search","./selection/eventRelay","./utils","./translation","./diacritics","./data/select","./data/array","./data/ajax","./data/tags","./data/tokenizer","./data/minimumInputLength","./data/maximumInputLength","./data/maximumSelectionLength","./dropdown","./dropdown/search","./dropdown/hidePlaceholder","./dropdown/infiniteScroll","./dropdown/attachBody","./dropdown/minimumResultsForSearch","./dropdown/selectOnClose","./dropdown/closeOnSelect","./i18n/en"],function(a,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C){function D(){this.reset()}D.prototype.apply=function(l){if(l=a.extend({},this.defaults,l),null==l.dataAdapter){if(l.dataAdapter=null!=l.ajax?o:null!=l.data?n:m,l.minimumInputLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,r)),l.maximumInputLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,s)),l.maximumSelectionLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,t)),null!=l.tags&&(l.dataAdapter=j.Decorate(l.dataAdapter,p)),(null!=l.tokenSeparators||null!=l.tokenizer)&&(l.dataAdapter=j.Decorate(l.dataAdapter,q)),null!=l.query){var C=b(l.amdBase+"compat/query");l.dataAdapter=j.Decorate(l.dataAdapter,C)}if(null!=l.initSelection){var D=b(l.amdBase+"compat/initSelection");l.dataAdapter=j.Decorate(l.dataAdapter,D)}}if(null==l.resultsAdapter&&(l.resultsAdapter=c,null!=l.ajax&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,x)),null!=l.placeholder&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,w)),l.selectOnClose&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,A))),null==l.dropdownAdapter){if(l.multiple)l.dropdownAdapter=u;else{var E=j.Decorate(u,v);l.dropdownAdapter=E}l.minimumResultsForSearch>0&&(l.dropdownAdapter=j.Decorate(l.dropdownAdapter,z)),l.closeOnSelect&&(l.dropdownAdapter=j.Decorate(l.dropdownAdapter,B)),l.dropdownAdapter=j.Decorate(l.dropdownAdapter,y)}if(null==l.selectionAdapter&&(l.selectionAdapter=l.multiple?e:d,null!=l.placeholder&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,f)),l.allowClear&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,g)),l.multiple&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,h)),l.selectionAdapter=j.Decorate(l.selectionAdapter,i)),"string"==typeof l.language)if(l.language.indexOf("-")>0){var F=l.language.split("-"),G=F[0];l.language=[l.language,G]}else l.language=[l.language];if(a.isArray(l.language)){var H=new k;l.language.push("en");for(var I=l.language,J=0;J<I.length;J++){var K=I[J],L={};try{L=k.loadPath(K)}catch(M){try{K=this.defaults.amdLanguageBase+K,L=k.loadPath(K)}catch(N){window.console&&console.warn&&console.warn('Select2: The lanugage file for "'+K+'" could not be automatically loaded. A fallback will be used instead.');continue}}H.extend(L)}l.translations=H}else l.translations=new k(l.language);return l},D.prototype.reset=function(){function b(a){function b(a){return l[a]||a}return a.replace(/[^\u0000-\u007E]/g,b)}function c(d,e){if(""===a.trim(d.term))return e;if(e.children&&e.children.length>0){for(var f=a.extend(!0,{},e),g=e.children.length-1;g>=0;g--){var h=e.children[g],i=c(d,h);null==i&&f.children.splice(g,1)}return f.children.length>0?f:c(d,f)}var j=b(e.text).toUpperCase(),k=b(d.term).toUpperCase();return j.indexOf(k)>-1?e:null}this.defaults={amdBase:"select2/",amdLanguageBase:"select2/i18n/",closeOnSelect:!0,escapeMarkup:j.escapeMarkup,language:C,matcher:c,minimumInputLength:0,maximumInputLength:0,maximumSelectionLength:0,minimumResultsForSearch:0,selectOnClose:!1,sorter:function(a){return a},templateResult:function(a){return a.text},templateSelection:function(a){return a.text},theme:"default",width:"resolve"}},D.prototype.set=function(b,c){var d=a.camelCase(b),e={};e[d]=c;var f=j._convertData(e);a.extend(this.defaults,f)};var E=new D;return E}),a("select2/options",["jquery","./defaults","./utils"],function(a,b,c){function d(a,c){this.options=a,null!=c&&this.fromElement(c),this.options=b.apply(this.options)}return d.prototype.fromElement=function(b){var d=["select2"];null==this.options.multiple&&(this.options.multiple=b.prop("multiple")),null==this.options.disabled&&(this.options.disabled=b.prop("disabled")),null==this.options.language&&(b.prop("lang")?this.options.language=b.prop("lang").toLowerCase():b.closest("[lang]").prop("lang")&&(this.options.language=b.closest("[lang]").prop("lang"))),null==this.options.dir&&(this.options.dir=b.prop("dir")?b.prop("dir"):b.closest("[dir]").prop("dir")?b.closest("[dir]").prop("dir"):"ltr"),b.prop("disabled",this.options.disabled),b.prop("multiple",this.options.multiple),b.data("select2-tags")&&(window.console&&console.warn&&console.warn('Select2: The `data-select2-tags` attribute has been changed to use the `data-data` and `data-tags="true"` attributes and will be removed in future versions of Select2.'),b.data("data",b.data("select2-tags")),b.data("tags",!0)),b.data("ajax-url")&&(window.console&&console.warn&&console.warn("Select2: The `data-ajax-url` attribute has been changed to `data-ajax--url` and support for the old attribute will be removed in future versions of Select2."),b.data("ajax--url",b.data("ajax-url")));var e=a.extend(!0,{},b[0].dataset||b.data());e=c._convertData(e);for(var f in e)a.inArray(f,d)>-1||(a.isPlainObject(this.options[f])?a.extend(this.options[f],e[f]):this.options[f]=e[f]);return this},d.prototype.get=function(a){return this.options[a]},d.prototype.set=function(a,b){this.options[a]=b},d}),a("select2/core",["jquery","./options","./utils","./keys"],function(a,b,c,d){var e=function(a,c){null!=a.data("select2")&&a.data("select2").destroy(),this.$element=a,this.id=this._generateId(a),c=c||{},this.options=new b(c,a),e.__super__.constructor.call(this);var d=a.attr("tabindex")||0;a.data("old-tabindex",d),a.attr("tabindex","-1");var f=this.options.get("dataAdapter");this.data=new f(a,this.options);var g=this.render();this._placeContainer(g);var h=this.options.get("selectionAdapter");this.selection=new h(a,this.options),this.$selection=this.selection.render(),this.selection.position(this.$selection,g);var i=this.options.get("dropdownAdapter");this.dropdown=new i(a,this.options),this.$dropdown=this.dropdown.render(),this.dropdown.position(this.$dropdown,g);var j=this.options.get("resultsAdapter");this.results=new j(a,this.options,this.data),this.$results=this.results.render(),this.results.position(this.$results,this.$dropdown);var k=this;this._bindAdapters(),this._registerDomEvents(),this._registerDataEvents(),this._registerSelectionEvents(),this._registerDropdownEvents(),this._registerResultsEvents(),this._registerEvents(),this.data.current(function(a){k.trigger("selection:update",{data:a})}),a.hide(),this._syncAttributes(),a.data("select2",this)};return c.Extend(e,c.Observable),e.prototype._generateId=function(a){var b="";return b=null!=a.attr("id")?a.attr("id"):null!=a.attr("name")?a.attr("name")+"-"+c.generateChars(2):c.generateChars(4),b="select2-"+b},e.prototype._placeContainer=function(a){a.insertAfter(this.$element);var b=this._resolveWidth(this.$element,this.options.get("width"));null!=b&&a.css("width",b)},e.prototype._resolveWidth=function(a,b){var c=/^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;if("resolve"==b){var d=this._resolveWidth(a,"style");return null!=d?d:this._resolveWidth(a,"element")}if("element"==b){var e=a.outerWidth(!1);return 0>=e?"auto":e+"px"}if("style"==b){var f=a.attr("style");if("string"!=typeof f)return null;for(var g=f.split(";"),h=0,i=g.length;i>h;h+=1){var j=g[h].replace(/\s/g,""),k=j.match(c);if(null!==k&&k.length>=1)return k[1]}return null}return b},e.prototype._bindAdapters=function(){this.data.bind(this,this.$container),this.selection.bind(this,this.$container),this.dropdown.bind(this,this.$container),this.results.bind(this,this.$container)},e.prototype._registerDomEvents=function(){var b=this;this.$element.on("change.select2",function(){b.data.current(function(a){b.trigger("selection:update",{data:a})})}),this._sync=c.bind(this._syncAttributes,this),this.$element[0].attachEvent&&this.$element[0].attachEvent("onpropertychange",this._sync);var d=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver;null!=d?(this._observer=new d(function(c){a.each(c,b._sync)}),this._observer.observe(this.$element[0],{attributes:!0,subtree:!1})):this.$element[0].addEventListener&&this.$element[0].addEventListener("DOMAttrModified",b._sync,!1)},e.prototype._registerDataEvents=function(){var a=this;this.data.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerSelectionEvents=function(){var b=this,c=["toggle"];this.selection.on("toggle",function(){b.toggleDropdown()}),this.selection.on("*",function(d,e){-1===a.inArray(d,c)&&b.trigger(d,e)})},e.prototype._registerDropdownEvents=function(){var a=this;this.dropdown.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerResultsEvents=function(){var a=this;this.results.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerEvents=function(){var a=this;this.on("open",function(){a.$container.addClass("select2-container--open")}),this.on("close",function(){a.$container.removeClass("select2-container--open")}),this.on("enable",function(){a.$container.removeClass("select2-container--disabled")}),this.on("disable",function(){a.$container.addClass("select2-container--disabled")}),this.on("focus",function(){a.$container.addClass("select2-container--focus")}),this.on("blur",function(){a.$container.removeClass("select2-container--focus")}),this.on("query",function(b){this.data.query(b,function(c){a.trigger("results:all",{data:c,query:b})})}),this.on("query:append",function(b){this.data.query(b,function(c){a.trigger("results:append",{data:c,query:b})})}),this.on("keypress",function(b){var c=b.which;a.isOpen()?c===d.ENTER?(a.trigger("results:select"),b.preventDefault()):c===d.UP?(a.trigger("results:previous"),b.preventDefault()):c===d.DOWN?(a.trigger("results:next"),b.preventDefault()):(c===d.ESC||c===d.TAB)&&(a.close(),b.preventDefault()):(c===d.ENTER||c===d.SPACE||(c===d.DOWN||c===d.UP)&&b.altKey)&&(a.open(),b.preventDefault())})},e.prototype._syncAttributes=function(){this.options.set("disabled",this.$element.prop("disabled")),this.options.get("disabled")?(this.isOpen()&&this.close(),this.trigger("disable")):this.trigger("enable")},e.prototype.trigger=function(a,b){var c=e.__super__.trigger,d={open:"opening",close:"closing",select:"selecting",unselect:"unselecting"};if(a in d){var f=d[a],g={prevented:!1,name:a,args:b};if(c.call(this,f,g),g.prevented)return void(b.prevented=!0)}c.call(this,a,b)},e.prototype.toggleDropdown=function(){this.options.get("disabled")||(this.isOpen()?this.close():this.open())},e.prototype.open=function(){this.isOpen()||(this.trigger("query",{}),this.trigger("open"))},e.prototype.close=function(){this.isOpen()&&this.trigger("close")},e.prototype.isOpen=function(){return this.$container.hasClass("select2-container--open")},e.prototype.enable=function(a){window.console&&console.warn&&console.warn('Select2: The `select2("enable")` method has been deprecated and will be removed in later Select2 versions. Use $element.prop("disabled") instead.'),0===a.length&&(a=[!0]);var b=!a[0];this.$element.prop("disabled",b)},e.prototype.data=function(){arguments.length>0&&window.console&&console.warn&&console.warn('Select2: Data can no longer be set using `select2("data")`. You should consider setting the value instead using `$element.val()`.');var a=[];return this.dataAdpater.current(function(b){a=b}),a},e.prototype.val=function(b){if(window.console&&console.warn&&console.warn('Select2: The `select2("val")` method has been deprecated and will be removed in later Select2 versions. Use $element.val() instead.'),0===b.length)return this.$element.val();var c=b[0];a.isArray(c)&&(c=a.map(c,function(a){return a.toString()})),this.$element.val(c).trigger("change")},e.prototype.destroy=function(){this.$container.remove(),this.$element[0].detachEvent&&this.$element[0].detachEvent("onpropertychange",this._sync),null!=this._observer?(this._observer.disconnect(),this._observer=null):this.$element[0].removeEventListener&&this.$element[0].removeEventListener("DOMAttrModified",this._sync,!1),this._sync=null,this.$element.off(".select2"),this.$element.attr("tabindex",this.$element.data("old-tabindex")),this.$element.show(),this.$element.removeData("select2"),this.data.destroy(),this.selection.destroy(),this.dropdown.destroy(),this.results.destroy(),this.data=null,this.selection=null,this.dropdown=null,this.results=null},e.prototype.render=function(){var b=a('<span class="select2 select2-container"><span class="selection"></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>');return b.attr("dir",this.options.get("dir")),this.$container=b,this.$container.addClass("select2-container--"+this.options.get("theme")),b.data("element",this.$element),b},e}),a("jquery.select2",["jquery","./select2/core","./select2/defaults"],function(a,c,d){try{b("jquery.mousewheel")}catch(e){}return null==a.fn.select2&&(a.fn.select2=function(b){if(b=b||{},"object"==typeof b)return this.each(function(){{var d=a.extend({},b,!0);new c(a(this),d)}}),this;if("string"==typeof b){var d=this.data("select2"),e=Array.prototype.slice.call(arguments,1);return d[b](e)}throw new Error("Invalid arguments for Select2: "+b)}),null==a.fn.select2.defaults&&(a.fn.select2.defaults=d),c}),b("jquery.select2"),jQuery.fn.select2.amd={define:a,require:b}}();;/**
 * OpenLab search dropdowns
 */

if (window.OpenLab === undefined) {
    var OpenLab = {};
}

var searchResizeTimer = {};

OpenLab.search = (function ($) {
    return{
        init: function () {

            //search
            if ($('.search-trigger-wrapper').length) {
                OpenLab.search.searchBarLoadActions();
                $('.search-trigger').on('click', function (e) {
                    e.preventDefault();
                    OpenLab.search.searchBarEventActions($(this));
                });
            }

        },
        searchBarLoadActions: function () {

            $('.search-form-wrapper').each(function () {
                var searchFormDim = OpenLab.search.invisibleDimensions($(this));
                $(this).data('thisheight', searchFormDim.height);
            });

        },
        searchBarEventActions: function (searchTrigger) {

            //var select = $('.search-form-wrapper .hidden-custom-select select');
            var adminBar = $('#wpadminbar');
            var mode = searchTrigger.data('mode');
            var location = searchTrigger.data('location');
            var searchForm = $('.search-form-wrapper.search-mode-' + mode + '.search-form-location-' + location);
            if (!searchTrigger.hasClass('in-action')) {
                searchTrigger.addClass('in-action');
                if (searchTrigger.parent().hasClass('search-live')) {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile' && searchTrigger.data('location') == 'header') {
                        adminBar.animate({
                            top: "-=" + searchForm.data('thisheight')
                        }, 700);
                        adminBar.removeClass('dropped');
                    }

                    searchForm.slideUp(800, function () {
                        searchTrigger.removeClass('in-action');
                    });


                } else {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile' && searchTrigger.data('location') == 'header') {
                        adminBar.addClass('dropped');
                        adminBar.animate({
                            top: "+=" + searchForm.data('thisheight')
                        }, 700);
                    }
                    searchForm.slideDown(700, function () {
                        searchTrigger.removeClass('in-action');
                    });
                }
                //select.customSelect();
            }

        },
        invisibleDimensions: function (el) {

            $(el).css({
                'display': 'block',
                'visibility': 'hidden'
            });
            var dim = {
                height: $(el).outerHeight(),
                width: $(el).outerWidth()
            };
            $(el).css({
                'display': 'none',
                'visibility': ''
            });
            return dim;
        },
        isBreakpoint: function (alias) {
            return $('.device-' + alias).is(':visible');
        },
    }
})(jQuery, OpenLab);

(function ($) {
    
    var legacyWidth = $(window).width();

    $(document).ready(function () {

        OpenLab.search.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(searchResizeTimer);
        searchResizeTimer = setTimeout(function () {

            if ($(this).width() != legacyWidth) {
                legacyWidth = $(this).width();
                if ($('.search-trigger-wrapper.search-live').length) {
                    OpenLab.search.searchBarEventActions($('.search-trigger-wrapper.search-live').find('.search-trigger'));
                }
            }

        }, 250);

    });

})(jQuery);;/**
 * OpenLab search dropdowns
 */


if (window.OpenLab === undefined) {
    var OpenLab = {};
}

var truncationResizeTimer = {};

OpenLab.truncation = (function ($) {

    return{
        init: function () {

            if ($('.truncate-on-the-fly').length) {
                setTimeout(function () {

                    OpenLab.truncation.truncateOnTheFly(true);

                }, 600);
            }

        },
        truncateOnTheFly: function (onInit, loadDelay) {

            if (onInit === undefined) {
                var onInit = false;
            }

            if (loadDelay === undefined) {
                var loadDelay = false;
            }

            $('.truncate-on-the-fly').each(function () {

                var thisElem = $(this);

                if (!loadDelay && thisElem.hasClass('load-delay')) {
                    return true;
                }

                var originalCopy = thisElem.parent().find('.original-copy').html();
                thisElem.html(originalCopy);

                var truncationBaseValue = thisElem.data('basevalue');
                var truncationBaseWidth = thisElem.data('basewidth');

                if (truncationBaseWidth === 'calculate') {

                    var sizerContainer = OpenLab.truncation.truncateSizerContainer(thisElem);
                    var static_w = 0;

                    sizerContainer.find('.truncate-static').each(function () {
                        static_w += $(this).width();
                    });

                    var available_w = sizerContainer.width() - static_w - 20;

                    if (available_w > 0) {
                        truncationBaseWidth = available_w;
                    } else {
                        truncationBaseWidth = 0;
                    }
                }

                var container_w = thisElem.parent().width();

                if (thisElem.data('link')) {

                    var omissionText = 'See More';

                    //for screen reader only append
                    //provides screen reader with addtional information in-link
                    if (thisElem.data('includename')) {

                        var nameTrunc = thisElem.data('includename');

                        //if the groupname is truncated, let's use that
                        var srprovider = thisElem.closest('.truncate-combo').find('[data-srprovider]');

                        if (srprovider.length) {
                            nameTrunc = srprovider.text();
                        }

                        omissionText = omissionText + ' <div class="sr-only sr-only-groupname">' + nameTrunc + '</div>';

                    }

                    var thisOmission = '<a href="' + thisElem.data('link') + '">' + omissionText + '</a>';
                } else {
                    var thisOmission = '';
                }

                if (container_w < truncationBaseWidth) {
                    var truncationValue = truncationBaseValue - (Math.round(((truncationBaseWidth - container_w) / truncationBaseWidth) * 100));
                    thisElem.find('.omission').remove();

                    if (!onInit) {
                        OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                    }

                } else {

                    if (thisElem.data('basewidth') === 'calculate') {

                        //thisElem.html('');

                        var sizerContainer_w = sizerContainer.width();
                        var sizerContainer_h = sizerContainer.height();

                        //thisElem.html(originalCopy);

                        sizerContainer.css({
                            'white-space': 'nowrap'
                        });

                        var sizerContainerNoWrap_w = sizerContainer.width();
                        var sizerContainerNoWrap_h = sizerContainer.height();

                        sizerContainer.css({
                            'white-space': 'normal'
                        });

                        if (sizerContainerNoWrap_w <= sizerContainer_w && sizerContainer_h === sizerContainerNoWrap_h) {
                            OpenLab.truncation.truncateReveal(thisElem);
                            return;
                        }

                        if (truncationBaseWidth < container_w) {

                            for (var looper = 0; looper < (truncationBaseValue + 1); looper++) {

                                if (thisElem.data('html')) {

                                    var truncationValue = looper;

                                    var myString = new HTMLString.String(thisElem.find('p').html());
                                    var sliceValue = Math.abs(myString.length() - truncationValue);
                                    var truncatedString = myString.slice(0, sliceValue);
                                    thisElem.find('p').html(truncatedString.html() + '<span class="omission">&hellip; ' + thisOmission + '</span>');

                                } else {
                                    var truncationValue = truncationBaseValue - looper;
                                    OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                                }

                                sizerContainer.css({
                                    'white-space': 'nowrap'
                                });

                                sizerContainerNoWrap_w = sizerContainer.width();

                                sizerContainer.css({
                                    'white-space': 'normal'
                                });

                                //recalculate sizes
                                sizerContainer_w = sizerContainer.width();
                                sizerContainer_h = sizerContainer.height();

                                if (sizerContainerNoWrap_w <= sizerContainer_w && sizerContainer_h === sizerContainerNoWrap_h) {

                                    break;

                                }

                            }

                        }

                    } else {

                        var truncationValue = truncationBaseValue;

                        if (!onInit) {
                            OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                        }

                    }

                }

                if (onInit) {
                    OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                }

                OpenLab.truncation.truncateReveal(thisElem);
            });
        },
        truncateMainAction: function (thisElem, truncationValue, thisOmission) {

            if (thisElem.data('minvalue')) {
                if (truncationValue < thisElem.data('minvalue')) {
                    truncationValue = thisElem.data('minvalue');
                }
            }

            if (truncationValue > 10) {
                thisElem.succinct({
                    size: truncationValue,
                    omission: '<span class="omission">&hellip; ' + thisOmission + '</span>'
                });

                //if we have an included groupname in the screen reader only link text
                //let's truncate it as well
                if (thisElem.data('srprovider')) {
                    var srLink = thisElem.closest('.truncate-combo').find('.sr-only-groupname');
                    srLink.text(thisElem.text());
                }

            } else {
                thisElem.html('<span class="omission">' + thisOmission + '</span>');
            }

        },
        truncateSizerContainer: function (thisElem) {

            var thisContainer = thisElem.closest('.truncate-sizer');
            var breakpoints = ['lg', 'md', 'sm', 'xs', 'xxs'];

            for (var i = 0; i < breakpoints.length; i++) {

                var breakpoint = breakpoints[i];
                var checkContainer = thisElem.closest('.truncate-sizer-' + breakpoint);

                if (checkContainer.length && OpenLab.truncation.isBreakpoint(breakpoint)) {
                    thisContainer = checkContainer;
                }

            }

            return thisContainer;

        },
        truncateReveal: function (thisElem) {
            thisElem.animate({
                opacity: '1.0'
            });

            $('.truncate-obfuscate')
                    .css({
                        'opacity': 0
                    })
                    .removeClass('invisible')
                    .animate({
                        'opacity': 1
                    }, 700);

            $(document).trigger('truncate-obfuscate-removed', thisElem);
        }
    }
})(jQuery, OpenLab);

(function ($) {
    $(document).ready(function () {

        OpenLab.truncation.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(truncationResizeTimer);
        truncationResizeTimer = setTimeout(function () {

            if ($('.truncate-on-the-fly').length) {

                $('.trucate-obfuscate').css('opacity', 0);
                OpenLab.truncation.truncateOnTheFly(false);
            }

        }, 250);

    });

    $(document).on('truncate-obfuscate-removed', function (e, thisElem) {

        $(thisElem).closest('.menu-loading').removeClass('menu-loading');

    });

})(jQuery);
;/**
 * OpenLab search dropdowns
 */

if (window.OpenLab === undefined) {
	var OpenLab = {};
}

var navResizeTimer = {};

OpenLab.nav = (function ($) {
	return{
		backgroundCont: {},
		backgroundTopStart: 0,
		plusHeight: 66,
		init: function () {

			OpenLab.nav.loginformInit();

			OpenLab.nav.backgroundCont = $( '#behind_menu_background' );

			//get starting position of mobile menu background
			OpenLab.nav.backgroundTopStart = OpenLab.nav.backgroundCont.css( 'top' );

			OpenLab.nav.removeDefaultScreenReaderShortcut();
			OpenLab.nav.directToggleAction();
			OpenLab.nav.backgroundAction();
			OpenLab.nav.mobileAnchorLinks();
			OpenLab.nav.hoverFixes();
			OpenLab.nav.tabindexNormalizer();
			OpenLab.nav.focusActions();
			OpenLab.nav.blurActions();

			OpenLab.nav.hyphenateInit();

						OpenLab.nav.adminToolbarPosition();
		},
		loginformInit: function () {

			var loginform = utilityVars.loginForm;

			$( "#wp-admin-bar-bp-login" ).append( loginform );

			$( "#wp-admin-bar-bp-login > a" ).click(
				function () {

					if ( ! $( this ).hasClass( 'login-click' )) {
						$( this ).closest( '#wp-admin-bar-bp-login' ).addClass( 'login-form-active' );
					}

					$( ".ab-submenu #sidebar-login-form" ).toggle(
						400,
						function () {
							$( ".ab-submenu #dropdown-user-login" ).focus();
							if ($( this ).hasClass( 'login-click' )) {
								$( this ).closest( '#wp-admin-bar-bp-login' ).removeClass( 'login-form-active' );
							}
							$( this ).toggleClass( "login-click" );
						}
					);

					OpenLab.nav.blurActions();
					return false;
				}
			);
		},
		hyphenateInit: function () {
			Hyphenator.config(
				{onhyphenationdonecallback: onHyphenationDone = function (context) {
							return undefined;
				},
					useCSS3hyphenation: true
					}
			);
			Hyphenator.run();
		},
		hoverFixes: function () {
			//fixing hover issues on mobile
			if (OpenLab.nav.isBreakpoint( 'xxs' ) || OpenLab.nav.isBreakpoint( 'xs' ) || OpenLab.nav.isBreakpoint( 'sm' )) {
				$( '.mobile-no-hover' ).bind(
					'touchend',
					function () {
						OpenLab.nav.fixHoverOnMobile( $( this ) );
					}
				)
			}
		},
		tabindexNormalizer: function () {

			//find tabindices in the adminbar greater than 1 and re-set
			$( '#wpadminbar [tabindex]' ).each(
				function () {

					var thisElem = $( this );
					if (parseInt( thisElem.attr( 'tabindex' ) ) > 0) {
						thisElem.attr( 'tabindex', 0 );
					}

				}
			);

			//add tabindex to mol icon menus
			$( '#wp-admin-bar-invites, #wp-admin-bar-messages, #wp-admin-bar-activity, #wp-admin-bar-my-account, #wp-admin-bar-top-logout, #wp-admin-bar-bp-register, #wp-admin-bar-bp-login' ).attr( 'tabindex', '0' );

		},
		focusActions: function () {

			//active menupop for keyboard users
			var adminbar = $( '#wpadminbar' );

			adminbar.find( 'li.menupop' ).on(
				'focus',
				function (e) {

					var el = $( this );

					if (el.parent().is( '#wp-admin-bar-root-default' ) && ! el.hasClass( 'hover' )) {
						e.preventDefault();
						adminbar.find( 'li.menupop.hover' ).removeClass( 'hover' );
						el.addClass( 'hover' );
					} else if ( ! el.hasClass( 'hover' )) {
						e.stopPropagation();
						e.preventDefault();
						el.addClass( 'hover' );
					} else if ( ! $( e.target ).closest( 'div' ).hasClass( 'ab-sub-wrapper' )) {
						e.stopPropagation();
						e.preventDefault();
						el.removeClass( 'hover' );
					}
				}
			);

			var skipToAdminbar = $( '#skipToAdminbar' );
			var skipTarget     = skipToAdminbar.attr( 'href' );

			skipToAdminbar.on(
				'click',
				function () {

					if (skipTarget === '#wp-admin-bar-bp-login') {
						$( skipTarget ).find( '> a' ).click();
					} else if (skipTarget === '#wp-admin-bar-my-openlab') {
						$( skipTarget ).closest( '.menupop' ).addClass( 'hover' );
						$( 'wp-admin-bar-my-openlab-default' ).focus();
					}

				}
			);

		},
		blurActions: function () {

			var adminbar = $( '#wpadminbar' );

			//make sure the menu closes when we leave
			adminbar.find( '.exit a' ).each(
				function () {

					var actionEl = $( this );

					actionEl.off( 'blur' ).on(
						'blur',
						function (e) {
							var el = $( this );

							el.closest( '.menupop' ).removeClass( 'hover' );

							//special case for login button
							if (el.closest( '#wp-admin-bar-bp-login' ).length) {
								el.closest( '#wp-admin-bar-bp-login' ).find( '> a' ).click();
							}

						}
					);

				}
			);

		},
		removeDefaultScreenReaderShortcut: function () {

			$( '#wpadminbar .screen-reader-shortcut' ).remove();

		},
		directToggleAction: function () {

			//if there is no direct toggle, we're done
			if ( ! $( '.direct-toggle' ).length) {
				return false;
			}

			var directToggle = $( '.direct-toggle' );

			directToggle.on(
				'click',
				function (e) {

					directToggle.removeClass( 'active' )
					e.stopImmediatePropagation();

					var thisElem = $( this );

					thisElem.addClass( 'active' );
					if ( ! thisElem.hasClass( 'in-action' )) {

						directToggle.removeClass( 'in-action' );
						thisElem.addClass( 'in-action' );

						var thisTarget     = thisElem.data( 'target' );
						var thisTargetElem = $( thisTarget );

						if (thisTargetElem.is( ':visible' )) {

							OpenLab.nav.hideNavMenu( thisElem, thisTargetElem );

						} else {

							directToggle.each(
								function () {
									var thisElem         = $( this );
									var thisToggleTarget = thisElem.data( 'target' );

									if ($( thisToggleTarget ).is( ':visible' )) {

										OpenLab.nav.hideNavMenu( thisElem, thisToggleTarget );

									}
								}
							);

							OpenLab.nav.showNavMenu( thisElem, thisTargetElem );

						}
					}
				}
			);
		},
		directToggleResizeHandler: function () {

			//if there is no direct toggle, we're done
			if ( ! $( '.direct-toggle' ).length) {
				return false;
			}

			//reset mobile menu background position
			OpenLab.nav.backgroundCont.css(
				{
					'top': OpenLab.nav.backgroundTopStart
				}
			)

			var directToggle = $( '.direct-toggle' );

			directToggle.each(
				function () {
					var thisElem         = $( this );
					var thisToggleTarget = thisElem.data( 'target' );

					if ( ! OpenLab.nav.isBreakpoint( 'xs' ) && ! OpenLab.nav.isBreakpoint( 'xxs' )) {
						//on background only elems, reset inline display value
						if (thisElem.data( 'backgroundonly' ) && (thisElem.data( 'backgroundonly' ) === true || thisElem.data( 'backgroundonly' ) === 1)) {
							$( thisToggleTarget ).css(
								{
									'display': ''
								}
							);
						}
					}

					if (thisElem.hasClass( 'active' )) {
						OpenLab.nav.hideNavMenu( thisElem, thisToggleTarget, false, true );

					}
				}
			);

		},
		hideNavMenu: function (thisElem, thisToggleTarget, thisAnchor, triggerBackgroundOnlyCheck) {
			var plusHeight     = OpenLab.nav.plusHeight;
			var backgroundOnly = false;

			//handle missing arguments
			if (typeof thisAnchor === 'undefined') {
				var thisAnchor = false;
			}
			if (typeof triggerBackgroundOnlyCheck === 'undefined') {
				var triggerBackgroundOnlyCheck = false;
			}

			//background only acheck
			if (thisElem.data( 'backgroundonly' ) && (thisElem.data( 'backgroundonly' ) === true || thisElem.data( 'backgroundonly' ) === 1)) {
				backgroundOnly = true;
			}

			if (thisElem.attr( 'data-plusheight' )) {
				plusHeight = parseInt( thisElem.data( 'plusheight' ) );
			}

			var thisTargetElem_h = $( thisToggleTarget ).height();
			thisTargetElem_h    += plusHeight;

			OpenLab.nav.backgroundCont.removeClass( 'active' ).animate(
				{
					'opacity': 0,
					'top': '-=' + thisTargetElem_h + 'px'
				},
				50,
				function () {
					$( this ).hide();
				}
			);

			//if background only, we're done
			if (backgroundOnly && triggerBackgroundOnlyCheck) {
				thisElem.removeClass( 'in-action' );
				thisElem.removeClass( 'active' );
				$( thisToggleTarget ).css(
					{
						'display': ''
					}
				);
				return false;
			}

			$( thisToggleTarget ).slideUp(
				700,
				function () {
					thisElem.removeClass( 'in-action' );
					thisElem.removeClass( 'active' );

					if (thisAnchor) {
						$.smoothScroll(
							{
								scrollTarget: thisAnchor
							}
						);
					}

				}
			);
		},
		showNavMenu: function (thisElem, thisTargetElem) {
			var plusHeight = OpenLab.nav.plusHeight;

			if (thisElem.attr( 'data-plusheight' )) {
				plusHeight = parseInt( thisElem.data( 'plusheight' ) );
			}

			thisTargetElem.slideDown(
				700,
				function () {

					var thisTargetElem_h = thisTargetElem.height();
					thisTargetElem_h    += plusHeight;

					thisElem.removeClass( 'in-action' );

					OpenLab.nav.backgroundCont.addClass( 'active' ).show()
						.css(
							{
								'top': '+=' + thisTargetElem_h + 'px'
							}
						)
						.animate(
							{
								'opacity': 0.42,
							},
							500
						);

					//for customSelect
					$( '.custom-select' ).each(
						function () {
							var customSelect_h = $( this ).find( '.customSelect' ).outerHeight();
							var customSelect_w = $( this ).find( '.customSelect' ).outerWidth();
							$( this ).find( 'select' ).css(
								{
									'height': customSelect_h + 'px',
									'width': customSelect_w + 'px'
								}
							);
						}
					)
				}
			);
		},
		backgroundAction: function () {

			OpenLab.nav.backgroundCont.on(
				'click',
				function () {

					var thisElem            = $( this );
					var currentActiveButton = $( '.direct-toggle.active' );
					var targetToClose       = currentActiveButton.data( 'target' );

					OpenLab.nav.hideNavMenu( currentActiveButton, targetToClose );

				}
			);

		},
		mobileAnchorLinks: function () {
			if ($( '.mobile-anchor-link' ).length) {
				$( '.mobile-anchor-link' ).find( 'a' ).on(
					'click',
					function (e) {
						e.preventDefault();
						var thisElem   = $( this );
						var thisAnchor = thisElem.attr( 'href' );

						var currentActiveButton = $( '.direct-toggle.active' );
						var background          = $( '#behind_menu_background' );
						var targetToClose       = currentActiveButton.data( 'target' );

						OpenLab.nav.hideNavMenu( currentActiveButton, targetToClose, thisAnchor );

					}
				);
			}
		},
		isBreakpoint: function (alias) {
			return $( '.device-' + alias ).is( ':visible' );
		},
		fixHoverOnMobile: function (thisElem) {
			thisElem.trigger( 'click' );
		},
		isBreakpoint: function (alias) {
			return $( '.device-' + alias ).is( ':visible' );
		},
		adminToolbarPosition: function() {
			if ( ! $( 'body' ).hasClass( 'block-editor-page' ) ) {
				return;
			}

			var mql = window.matchMedia( "screen and (max-width: 781px)" );
			if ( mql.matches ) {
				return;
			}

			setTimeout(
				function() {
					var bump = 40;
					var $editPostHeader = $( '.edit-post-header' );
					if ( $editPostHeader.length > 0 ) {
						editPostHeaderOffset = $editPostHeader.offset();
						$editPostHeader.css( 'top',(editPostHeaderOffset.top + bump) + 'px' );
					}
				},
				1000
			);
		}
	}
})( jQuery, OpenLab );

(function ($) {

	var windowWidth = $( window ).width();

	$( document ).ready(
		function () {

			OpenLab.nav.init();

		}
	);

	$( window ).on(
		'resize',
		function (e) {

			clearTimeout( navResizeTimer );
			navResizeTimer = setTimeout(
				function () {

					//checking to see if this is truly a resize event
					if ($( window ).width() != windowWidth) {

						windowWidth = $( window ).width();

						OpenLab.nav.hoverFixes();
						OpenLab.nav.directToggleResizeHandler();

					}

				},
				250
			);

		}
	);

})( jQuery );
;/**
 * Any client-side theme fixes go here (for group site themes; excludes OpenLab custom theme)
 */

/**
 * Twentyfourteen
 * Makes the header relative until scrolling, to fix issue with header going behind admin bar
 */

if (window.OpenLab === undefined) {
	var OpenLab = {};
}

OpenLab.fixes = (function ($) {
	return{
		init: function () {

			if ($( 'body' ).hasClass( 'masthead-fixed' )) {
				OpenLab.fixes.fixMasthead();
			}

			OpenLab.fixes.fixHemingwayEmptyButtons();
		},
		onLoad: function () {

			OpenLab.fixes.emptyHeaders();

		},
		fixMasthead: function () {

			//this is so that the on scroll function won't fire on themes that don't need it to
			if ( ! $( 'body' ).hasClass( 'masthead-fixing' )) {
				$( 'body' ).addClass( 'masthead-fixing' );
			}

			//get adminbar height
			var adminBar_h    = $( '#wpadminbar' ).outerHeight();
			var scrollTrigger = Math.ceil( adminBar_h / 2 );

			//if were below the scrollTrigger, remove the fixed class, otherwise make sure it's there
			if (OpenLab.fixes.getCurrentScroll() <= scrollTrigger) {
				$( 'body' ).removeClass( 'masthead-fixed' );
			} else {
				$( 'body' ).addClass( 'masthead-fixed' );
			}

		},
		getCurrentScroll: function () {
			var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

			return currentScroll;
		},
		/**
		 * If theme markup has header elements that could output as empty, but available filters only let you get inside the header tags,
		 * this fix can be applied by adding a span with class "empty-header"
		 * The span should be filled with some type of default tax in the event JS is disabled
		 */
		emptyHeaders: function () {

			if ($( '.empty-header' ).length === 0) {
				return false;
			}

			$( '.empty-header' ).each(
				function () {

					OpenLab.fixes.processEmptyHeader( $( this ) );

				}
			);
		},
		processEmptyHeader: function (thisElem) {

			var thisHeader = thisElem.closest( ':header' );

			if (thisHeader.length === 0) {
				return false;
			}

			/**
			 * The replacement span we're going to add we'll inherit all of the classes and ids
			 * from the empty header element in order to maintain vertical spacing
			 * A new class "empty-header-placeholder" will be added for additional style tweaking
			 */
			var headerClasses = thisHeader.attr( 'class' );

			if (typeof headerClasses === 'undefined') {
				headerClasses = '';
			} else {
				headerClasses = ' ';
			}

			headerClasses += 'empty-header-placeholder';

			var headerID = thisHeader.attr( 'id' );

			var replacement = $( '<span></span>' );
			replacement.attr( 'id', headerID );
			replacement.addClass( headerClasses );

			thisHeader.replaceWith( replacement[0].outerHTML );
		},
		fixHemingwayEmptyButtons: function() {
			$( '.navigation-inner.section-inner .toggle-container .nav-toggle' ).append( '<span class="sr-only">Toggle Navigation</span>' );
			$( '.navigation-inner.section-inner .toggle-container .search-toggle' ).append( '<span class="sr-only">Toggle Search</span>' );
		}
	}
})( jQuery, OpenLab );

(function ($) {

	$( document ).ready(
		function () {
			OpenLab.fixes.init();
		}
	);

	$( window ).load(
		function () {
			OpenLab.fixes.onLoad();
		}
	);

	$( window ).scroll(
		function () {

			if ($( 'body' ).hasClass( 'masthead-fixing' )) {
				OpenLab.fixes.fixMasthead();
			}
		}
	);

})( jQuery );
