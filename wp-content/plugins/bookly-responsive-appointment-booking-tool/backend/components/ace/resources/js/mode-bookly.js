define('ace/mode/bookly', function(require, exports, module) {

    var oop = require("ace/lib/oop");
    var TextMode = require("ace/mode/text").Mode;
    var BooklyHighlightRules = require("ace/mode/bookly_highlight_rules").BooklyHighlightRules;

    var Mode = function() {
        this.HighlightRules = BooklyHighlightRules;
    };
    oop.inherits(Mode, TextMode);

    exports.Mode = Mode;
});

define('ace/mode/bookly_highlight_rules', function(require, exports, module) {

    var oop = require("ace/lib/oop");
    var TextHighlightRules = require("ace/mode/text_highlight_rules").TextHighlightRules;

    var BooklyHighlightRules = function() {

        this.$rules = new TextHighlightRules().getRules();

        // Generate bookly highlight rules
        this.$rules['start'] = this.$rules['start'] || [];
        // Start loop
        this.$rules['start'].push({
            token: 'bookly_each',
            regex: '{#each (\\w+(?:\\.\\w+)*)\\s+as\\s+(\\w+)(?:\\s+delimited\\s+by\\s+"(.+?)")?\\s*}',
            merge: false,
        });
        // End loop
        this.$rules['start'].push({
            token: 'bookly_endeach',
            regex: '{/each}',
            merge: false,
        });
        // Start if
        this.$rules['start'].push({
            token: 'bookly_if',
            regex: '{#if (\\w+(?:\\.\\w+)*)}',
            merge: false,
        });
        // End if
        this.$rules['start'].push({
            token: 'bookly_endif',
            regex: '{/if}',
            merge: false,
        });
        // Code
        this.$rules['start'].push({
            token: 'bookly_code',
            regex: '{(\\w+(?:\\.\\w+)*(?:\\#\\w+)*)}',
            merge: false,
        });
    }

    oop.inherits(BooklyHighlightRules, TextHighlightRules);

    exports.BooklyHighlightRules = BooklyHighlightRules;
});

define('ace/mode/bookly_completer', function(require, exports, module) {
    exports.BooklyCompleter = function( codes ) {
        var TokenIterator = ace.require('ace/token_iterator').TokenIterator;
        return {
            // Add required symbols to ace completions finder
            identifierRegexps: [/[a-zA-Z_0-9\.\$\{\#\-\u00A2-\u2000\u2070-\uFFFF]/],
            // Rewrite autocomplete rules
            getCompletions: function (state, session, pos, prefix, callback) {
                let iterator    = new TokenIterator(session, pos.row, pos.column),
                    // Get token for current cursor position (e.g. 'bookly_code' from highlight rules)
                    token       = iterator.getCurrentToken(),
                    completions = [];
                // Disable completions inside {#each ...}
                const line = session.getLine(pos.row);
                if (token && token.type === 'bookly_each' && line[pos.column - 1] !== '}' && line[pos.column] !== '{') {
                    callback(null, []);
                }
                // Fix issue when each or endeach starts at new line
                if (pos.column === 0 && token && (token.type === 'bookly_endeach' || token.type === 'bookly_each')) {
                    token = iterator.stepBackward();
                }
                if (token === undefined) {
                    token = iterator.stepBackward();
                }
                if (codes) {
                    // Check if cursor inside loop
                    let level = 0;
                    // Get previous token while it available to find loop token
                    while (token && !(token.type === 'bookly_each' && level === 0)) {
                        if (token.type === 'bookly_endeach') {
                            level++;
                        }
                        if (token.type === 'bookly_each') {
                            level--;
                        }
                        token = iterator.stepBackward();
                    }

                    const nested = !!token;

                    if (nested) {
                        // Cursor inside a loop
                        level = 0;
                        let path = [];
                        // Build nested loops path
                        while (token) {
                            if (token.type === 'bookly_endeach') {
                                level++;
                            }
                            if (token.type === 'bookly_each') {
                                level--;
                                if (level < 1) {
                                    let _loop = token.value.match(/{(#each (\w+(?:\.\w+)*)\s+as\s+(\w+)(?:\s+delimited\s+by\s+"(.+?)")?\s*)}/);
                                    path.push({loop: _loop[2].split('.').pop(), name: _loop[3]});
                                }
                            }
                            token = iterator.stepBackward();
                        }

                        path.reverse();

                        function getLoopCodes(codes, path) {
                            for (let i = 0; i < path.length; i++) {
                                if (!codes.hasOwnProperty(path[i]['loop'])) {
                                    return false;
                                }
                                codes = codes[path[i]['loop']]['loop']['codes'];
                            }
                            return codes;
                        }

                        // Add codes to completion for all nested loops
                        let top = true;
                        while (path.length) {
                            const name = path[path.length - 1]['name'];
                            let loop_codes = getLoopCodes(codes, path);
                            if (loop_codes !== false) {
                                Object.keys(loop_codes).forEach(function (code) {
                                    if (!loop_codes[code].hasOwnProperty('loop') && (!loop_codes[code].hasOwnProperty('code') || loop_codes[code]['code'])) {
                                        completions.push({
                                            caption: '{' + name + '.' + code + '}',
                                            value: '{' + name + '.' + code + '}',
                                            score: 500,
                                            docHTML: escapeHtml(loop_codes[code]['description'])
                                        });
                                    } else if (top) {
                                        // Add top level loops to completions
                                        completions.push({
                                            caption: '{#each ' + name + '.' + code + ' as ' + loop_codes[code]['loop']['item'] + '}{/each}',
                                            value: '{#each ' + name + '.' + code + ' as ' + loop_codes[code]['loop']['item'] + '}{/each}',
                                            snippet: '{#each ' + name + '.' + code + ' as ' + loop_codes[code]['loop']['item'] + '}$0{/each}',
                                            score: 400,
                                            docHTML: escapeHtml(loop_codes[code]['description'][0])
                                        });
                                        completions.push({
                                            caption: '{#each ' + name + '.' + code + ' as ' + loop_codes[code]['loop']['item'] + ' delimited by ", "}{/each}',
                                            value: '{#each ' + name + '.' + code + ' as ' + loop_codes[code]['loop']['item'] + ' delimited by ", "}{/each}',
                                            snippet: '{#each ' + name + '.' + code + ' as ' + loop_codes[code]['loop']['item'] + ' delimited by ", "}$0{/each}',
                                            score: 300,
                                            docHTML: escapeHtml(loop_codes[code]['description'][1])
                                        });
                                    }
                                    if (loop_codes[code].hasOwnProperty('if') && loop_codes[code]['if']) {
                                        completions.push({
                                            caption: '{#if ' + name + '.' + code + '}{/if}',
                                            value: '{#if ' + name + '.' + code + '}{/if}',
                                            snippet: '{#if ' + name + '.' + code + '}$0{/if}',
                                            score: 200,
                                            docHTML: escapeHtml(loop_codes[code]['description'])
                                        });
                                    }
                                });
                            }
                            top = false;
                            path = path.slice(0, path.length - 1)
                        }
                    }
                    // Add first level codes to completions
                    Object.keys(codes).forEach(function (code) {
                        if (typeof codes[code] === 'object' && codes[code].hasOwnProperty('loop')) {
                            if (!nested) {
                                completions.push({
                                    caption: '{#each ' + code + ' as ' + codes[code]['loop']['item'] + '}{/each}',
                                    value: '{#each ' + code + ' as ' + codes[code]['loop']['item'] + '}{/each}',
                                    snippet: '{#each ' + code + ' as ' + codes[code]['loop']['item'] + '}$0{/each}',
                                    score: 400,
                                    docHTML: escapeHtml(codes[code]['description'][0])
                                });
                                completions.push({
                                    caption: '{#each ' + code + ' as ' + codes[code]['loop']['item'] + ' delimited by ", "}{/each}',
                                    value: '{#each ' + code + ' as ' + codes[code]['loop']['item'] + ' delimited by ", "}{/each}',
                                    snippet: '{#each ' + code + ' as ' + codes[code]['loop']['item'] + ' delimited by ", "}$0{/each}',
                                    score: 300,
                                    docHTML: escapeHtml(codes[code]['description'][1])
                                });
                            }
                        } else if(!codes[code].hasOwnProperty('code') || codes[code]['code']) {
                            completions.push({
                                caption: '{' + code + '}',
                                value: '{' + code + '}',
                                score: 500,
                                docHTML: escapeHtml(codes[code]['description'])
                            });
                        }
                        if (codes[code].hasOwnProperty('if') && codes[code]['if']) {
                            completions.push({
                                caption: '{#if ' + code + '}{/if}',
                                value: '{#if ' + code + '}{/if}',
                                snippet: '{#if ' + code + '}$0{/if}',
                                score: 100,
                                docHTML: escapeHtml(codes[code]['description'])
                            });
                        }
                    });
                }
                callback(null, completions);

                function escapeHtml(description) {
                    if (Array.isArray(description)) {
                        return description;
                    } else {
                        return description
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                    }
                }
            }
        }
    }
});
