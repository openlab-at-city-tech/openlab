/* globals define, module, jQuery */

/*
 * Mailcheck https://github.com/mailcheck/mailcheck
 * Author
 * Derrick Ko (@derrickko)
 *
 * Released under the MIT License.
 *
 * v 1.1.2
 */

var Mailcheck = {
    domainThreshold: 2,
    secondLevelThreshold: 2,
    topLevelThreshold: 2,

    defaultDomains: ['msn.com', 'bellsouth.net',
        'telus.net', 'comcast.net', 'optusnet.com.au',
        'earthlink.net', 'qq.com', 'sky.com', 'icloud.com',
        'mac.com', 'sympatico.ca', 'googlemail.com',
        'att.net', 'xtra.co.nz', 'web.de',
        'cox.net', 'gmail.com', 'ymail.com',
        'aim.com', 'rogers.com', 'verizon.net',
        'rocketmail.com', 'google.com', 'optonline.net',
        'sbcglobal.net', 'aol.com', 'me.com', 'btinternet.com',
        'charter.net', 'shaw.ca', 'protonmail.com'],

    defaultSecondLevelDomains: ["yahoo", "hotmail", "mail", "live", "outlook", "gmx", "ymail", "gmail", "protonmail"],

    defaultTopLevelDomains: ["com", "com.au", "com.tw", "ca", "co.nz", "co.uk", "de",
        "fr", "it", "ru", "net", "org", "edu", "gov", "jp", "nl", "kr", "se", "eu",
        "ie", "co.il", "us", "at", "be", "dk", "hk", "es", "gr", "ch", "no", "cz",
        "in", "net", "net.au", "info", "biz", "mil", "co.jp", "sg", "hu", "uk", "io", "ai", "co.in"],

    run: function(opts) {
        opts.domains = opts.domains || Mailcheck.defaultDomains;
        opts.secondLevelDomains = opts.secondLevelDomains || Mailcheck.defaultSecondLevelDomains;
        opts.topLevelDomains = opts.topLevelDomains || Mailcheck.defaultTopLevelDomains;
        opts.distanceFunction = opts.distanceFunction || Mailcheck.sift4Distance;

        var defaultCallback = function(result){ return result; };
        var suggestedCallback = opts.suggested || defaultCallback;
        var emptyCallback = opts.empty || defaultCallback;

        var result = Mailcheck.suggest(Mailcheck.encodeEmail(opts.email), opts.domains, opts.secondLevelDomains, opts.topLevelDomains, opts.distanceFunction);

        return result ? suggestedCallback(result) : emptyCallback();
    },

    suggest: function(email, domains, secondLevelDomains, topLevelDomains, distanceFunction) {
        email = email.toLowerCase();

        var emailParts = this.splitEmail(email);

        if (secondLevelDomains && topLevelDomains) {
            // If the email is a valid 2nd-level + top-level, do not suggest anything.
            if (secondLevelDomains.indexOf(emailParts.secondLevelDomain) !== -1 && topLevelDomains.indexOf(emailParts.topLevelDomain) !== -1) {
                return false;
            }
        }

        var closestDomain = this.findClosestDomain(emailParts.domain, domains, distanceFunction, this.domainThreshold);

        if (closestDomain) {
            if (closestDomain == emailParts.domain) {
                // The email address exactly matches one of the supplied domains; do not return a suggestion.
                return false;
            } else {
                // The email address closely matches one of the supplied domains; return a suggestion
                return { address: emailParts.address, domain: closestDomain, full: emailParts.address + "@" + closestDomain };
            }
        }

        // The email address does not closely match one of the supplied domains
        var closestSecondLevelDomain = this.findClosestDomain(emailParts.secondLevelDomain, secondLevelDomains, distanceFunction, this.secondLevelThreshold);
        var closestTopLevelDomain    = this.findClosestDomain(emailParts.topLevelDomain, topLevelDomains, distanceFunction, this.topLevelThreshold);

        if (emailParts.domain) {
            closestDomain = emailParts.domain;
            var rtrn = false;

            if(closestSecondLevelDomain && closestSecondLevelDomain != emailParts.secondLevelDomain) {
                // The email address may have a mispelled second-level domain; return a suggestion
                closestDomain = closestDomain.replace(emailParts.secondLevelDomain, closestSecondLevelDomain);
                rtrn = true;
            }

            if(closestTopLevelDomain && closestTopLevelDomain != emailParts.topLevelDomain && emailParts.secondLevelDomain !== '') {
                // The email address may have a mispelled top-level domain; return a suggestion
                closestDomain = closestDomain.replace(new RegExp(emailParts.topLevelDomain + "$"), closestTopLevelDomain);
                rtrn = true;
            }

            if (rtrn) {
                return { address: emailParts.address, domain: closestDomain, full: emailParts.address + "@" + closestDomain };
            }
        }

        /* The email address exactly matches one of the supplied domains, does not closely
         * match any domain and does not appear to simply have a mispelled top-level domain,
         * or is an invalid email address; do not return a suggestion.
         */
        return false;
    },

    findClosestDomain: function(domain, domains, distanceFunction, threshold) {
        threshold = threshold || this.topLevelThreshold;
        var dist;
        var minDist = Infinity;
        var closestDomain = null;

        if (!domain || !domains) {
            return false;
        }
        if(!distanceFunction) {
            distanceFunction = this.sift4Distance;
        }

        for (var i = 0; i < domains.length; i++) {
            if (domain === domains[i]) {
                return domain;
            }
            dist = distanceFunction(domain, domains[i]);
            if (dist < minDist) {
                minDist = dist;
                closestDomain = domains[i];
            }
        }

        if (minDist <= threshold && closestDomain !== null) {
            return closestDomain;
        } else {
            return false;
        }
    },

    sift4Distance: function(s1, s2, maxOffset) {
        // sift4: https://siderite.blogspot.com/2014/11/super-fast-and-accurate-string-distance.html
        if (maxOffset === undefined) {
            maxOffset = 5; //default
        }

        if (!s1||!s1.length) {
            if (!s2) {
                return 0;
            }
            return s2.length;
        }

        if (!s2||!s2.length) {
            return s1.length;
        }

        var l1=s1.length;
        var l2=s2.length;

        var c1 = 0;  //cursor for string 1
        var c2 = 0;  //cursor for string 2
        var lcss = 0;  //largest common subsequence
        var local_cs = 0; //local common substring
        var trans = 0;  //number of transpositions ('ab' vs 'ba')
        var offset_arr=[];  //offset pair array, for computing the transpositions

        while ((c1 < l1) && (c2 < l2)) {
            if (s1.charAt(c1) == s2.charAt(c2)) {
                local_cs++;
                var isTrans=false;
                //see if current match is a transposition
                var i=0;
                while (i<offset_arr.length) {
                    var ofs=offset_arr[i];
                    if (c1<=ofs.c1 || c2 <= ofs.c2) {
                        // when two matches cross, the one considered a transposition is the one with the largest difference in offsets
                        isTrans=Math.abs(c2-c1)>=Math.abs(ofs.c2-ofs.c1);
                        if (isTrans)
                        {
                            trans++;
                        } else
                        {
                            if (!ofs.trans) {
                                ofs.trans=true;
                                trans++;
                            }
                        }
                        break;
                    } else {
                        if (c1>ofs.c2 && c2>ofs.c1) {
                            offset_arr.splice(i,1);
                        } else {
                            i++;
                        }
                    }
                }
                offset_arr.push({
                    c1:c1,
                    c2:c2,
                    trans:isTrans
                });
            } else {
                lcss+=local_cs;
                local_cs=0;
                if (c1!=c2) {
                    c1=c2=Math.min(c1,c2);  //using min allows the computation of transpositions
                }
                //if matching characters are found, remove 1 from both cursors (they get incremented at the end of the loop)
                //so that we can have only one code block handling matches
                for (var j = 0; j < maxOffset && (c1+j<l1 || c2+j<l2); j++) {
                    if ((c1 + j < l1) && (s1.charAt(c1 + j) == s2.charAt(c2))) {
                        c1+= j-1;
                        c2--;
                        break;
                    }
                    if ((c2 + j < l2) && (s1.charAt(c1) == s2.charAt(c2 + j))) {
                        c1--;
                        c2+= j-1;
                        break;
                    }
                }
            }
            c1++;
            c2++;
            // this covers the case where the last match is on the last token in list, so that it can compute transpositions correctly
            if ((c1 >= l1) || (c2 >= l2)) {
                lcss+=local_cs;
                local_cs=0;
                c1=c2=Math.min(c1,c2);
            }
        }
        lcss+=local_cs;
        return Math.round(Math.max(l1,l2)- lcss +trans); //add the cost of transpositions to the final result
    },

    splitEmail: function(email) {
        email = email !== null ? (email.replace(/^\s*/, '').replace(/\s*$/, '')) : null; // trim() not exist in old IE!
        var parts = email.split('@');

        if (parts.length < 2) {
            return false;
        }

        for (var i = 0; i < parts.length; i++) {
            if (parts[i] === '') {
                return false;
            }
        }

        var domain = parts.pop();
        var domainParts = domain.split('.');
        var sld = '';
        var tld = '';

        if (domainParts.length === 0) {
            // The address does not have a top-level domain
            return false;
        } else if (domainParts.length == 1) {
            // The address has only a top-level domain (valid under RFC)
            tld = domainParts[0];
        } else {
            // The address has a domain and a top-level domain
            sld = domainParts[0];
            for (var j = 1; j < domainParts.length; j++) {
                tld += domainParts[j] + '.';
            }
            tld = tld.substring(0, tld.length - 1);
        }

        return {
            topLevelDomain: tld,
            secondLevelDomain: sld,
            domain: domain,
            address: parts.join('@')
        };
    },

    // Encode the email address to prevent XSS but leave in valid
    // characters, following this official spec:
    // http://en.wikipedia.org/wiki/Email_address#Syntax
    encodeEmail: function(email) {
        var result = encodeURI(email);
        result = result.replace('%20', ' ').replace('%25', '%').replace('%5E', '^')
            .replace('%60', '`').replace('%7B', '{').replace('%7C', '|')
            .replace('%7D', '}');
        return result;
    }
};

// Export the mailcheck object if we're in a CommonJS env (e.g. Node).
// Modeled off of Underscore.js.
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Mailcheck;
}

// Support AMD style definitions
// Based on jQuery (see http://stackoverflow.com/a/17954882/1322410)
if (typeof define === "function" && define.amd) {
    define("mailcheck", [], function() {
        return Mailcheck;
    });
}

if (typeof window !== 'undefined' && window.jQuery) {
    (function($){
        $.fn.mailcheck = function(opts) {
            var self = this;
            if (opts.suggested) {
                var oldSuggested = opts.suggested;
                opts.suggested = function(result) {
                    oldSuggested(self, result);
                };
            }

            if (opts.empty) {
                var oldEmpty = opts.empty;
                opts.empty = function() {
                    oldEmpty.call(null, self);
                };
            }

            opts.email = this.val();
            Mailcheck.run(opts);
        };
    })(jQuery);
}

/*
 *  email-autocomplete - 0.1.3
 *  jQuery plugin that displays in-place autocomplete suggestions for email input fields.
 *
 *
 *  Made by Low Yong Zhen <yz@stargate.io>
 */
"use strict";

(function ($, window, document, undefined) {

    var pluginName = "emailautocomplete";
    var defaults = {
        suggClass: "eac-sugg",
        domains: ["yahoo.com" ,"hotmail.com" ,"gmail.com" ,"me.com" ,"aol.com" ,"mac.com" ,"live.com" ,"comcast.net" ,"googlemail.com" ,"msn.com" ,"hotmail.co.uk" ,"yahoo.com" ,"facebook.com" ,"verizon.net" ,"sbcglobal.net" ,"att.net" ,"gmx.com" ,"outlook.com" ,"icloud.com", "premio.io", "protonmail.com"]
    };

    function EmailAutocomplete(elem, options) {
        this.$field = $(elem);
        this.options = $.extend(true, {}, defaults, options); //we want deep extend
        this._defaults = defaults;
        this._domains = this.options.domains;
        this.init();
    }

    EmailAutocomplete.prototype = {
        init: function () {

            //shim indexOf
            if (!Array.prototype.indexOf) {
                this.doIndexOf();
            }

            //this will be calculated upon keyup
            this.fieldLeftOffset = null;

            //wrap our field
            var $wrap = $("<div class='eac-input-wrap' />").css({
                display: this.$field.css("display"),
                position: this.$field.css("position") === 'static' ? 'relative' : this.$field.css("position"),
                fontSize: this.$field.css("fontSize")
            });
            this.$field.wrap($wrap);

            //create container to test width of current val
            this.$cval = $("<span class='eac-cval' />").css({
                visibility: "hidden",
                position: "absolute",
                display: "inline-block",
                fontFamily: this.$field.css("fontFamily"),
                fontWeight: this.$field.css("fontWeight"),
                letterSpacing: this.$field.css("letterSpacing")
            }).insertAfter(this.$field);

            //create the suggestion overlay
            /* touchstart jquery 1.7+ */
            var heightPad = (this.$field.outerHeight(true) - this.$field.height()) / 2; //padding+border
            this.$suggOverlay = $("<span class='"+this.options.suggClass+"' />").css({
                display: "block",
                "box-sizing": "content-box", //standardize
                lineHeight: this.$field.css('lineHeight'),
                paddingTop: heightPad + "px",
                paddingBottom: heightPad + "px",
                fontFamily: this.$field.css("fontFamily"),
                fontWeight: this.$field.css("fontWeight"),
                letterSpacing: this.$field.css("letterSpacing"),
                position: "absolute",
                top: 0,
                left: 0
            }).insertAfter(this.$field);

            //bind events and handlers
            this.$field.on("keyup.eac", $.proxy(this.displaySuggestion, this));

            this.$field.on("blur.eac", $.proxy(this.autocomplete, this));

            this.$field.on("keydown.eac", $.proxy(function(e){
                if(e.which === 39 || e.which === 9 || e.which === 32 || e.which === 13){
                    this.autocomplete();
                }
                if ( e.which === 9 && !this.$field.hasClass('email-focus')) {
                    this.$field.addClass('email-focus');
                    e.preventDefault();
                }else {
                    if ( e.which === 32 ){
                        e.preventDefault();
                    }
                    this.$field.removeClass('email-focus');
                }
            }, this));
            this.$field.on("click", $.proxy(function(e){
                this.autocomplete();
            }, this));

            this.$suggOverlay.on("mousedown.eac touchstart.eac", $.proxy(this.autocomplete, this));
        },

        suggest: function (str) {
            str = $.trim(str.toLowerCase());
            var str_arr = str.split("@");
            if (str_arr.length > 1) {
                str = str_arr.pop();
                if (!str.length) {
                    return "";
                }
            } else {
                return "";
            }
            var match = this._domains.filter(function (domain) {
                return domain.indexOf(str) === 0;
            }).shift() || "";

            return match.replace(str, "");
        },

        autocomplete: function () {
            if(typeof this.suggestion === "undefined" || this.suggestion.length < 1){
                return false;
            }

            this.$field.val(this.val + this.suggestion);
            this.$suggOverlay.text("");
            this.$cval.text("");
        },

        /**
         * Displays the suggestion, handler for field keyup event
         */
        displaySuggestion: function (e) {
            this.val = this.$field.val();
            this.suggestion = this.suggest(this.val);

            if (!this.suggestion.length) {
                this.$suggOverlay.text("");
            } else {
                e.preventDefault();
            }

            //update with new suggestion
            this.$suggOverlay.text(this.suggestion);
            this.$cval.text(this.val);

            // get input padding, border and margin to offset text
            if(this.fieldLeftOffset === null){
                this.fieldLeftOffset = (this.$field.outerWidth(true) - this.$field.width()) / 2;
            }

            //find width of current input val so we can offset the suggestion text
            var cvalWidth = this.$cval.width();

            if(this.$field.outerWidth() > cvalWidth){
                //offset our suggestion container
                this.$suggOverlay.css('left', this.fieldLeftOffset + cvalWidth + "px");
            }
        },

        /**
         * indexof polyfill
         * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/indexOf#Polyfill
         */
        doIndexOf: function(){

            Array.prototype.indexOf = function (searchElement, fromIndex) {
                if ( this === undefined || this === null ) {
                    throw new TypeError( '"this" is null or not defined' );
                }

                var length = this.length >>> 0; // Hack to convert object.length to a UInt32

                fromIndex = +fromIndex || 0;

                if (Math.abs(fromIndex) === Infinity) {
                    fromIndex = 0;
                }

                if (fromIndex < 0) {
                    fromIndex += length;
                    if (fromIndex < 0) {
                        fromIndex = 0;
                    }
                }

                for (;fromIndex < length; fromIndex++) {
                    if (this[fromIndex] === searchElement) {
                        return fromIndex;
                    }
                }

                return -1;
            };
        }
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "yz_" + pluginName)) {
                $.data(this, "yz_" + pluginName, new EmailAutocomplete(this, options));
            }
        });
    };

})(jQuery, window, document);
