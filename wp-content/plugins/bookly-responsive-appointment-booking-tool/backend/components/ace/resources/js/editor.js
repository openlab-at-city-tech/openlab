/**
 * jQuery booklyAceEditor.
 */
(function ($) {
    let editor, staticWordCompleter, langTools;
    let methods = {
        init: function () {
            // Init editor
            editor = ace.edit(this.attr('id'));
            editor.renderer.setShowGutter(false);
            editor.setShowPrintMargin(false);
            editor.setHighlightActiveLine(false);
            editor.setOptions({
                enableBasicAutocompletion: true,
                enableLiveAutocompletion: true,
                enableSnippets: true,
                wrap: true,
                indentedSoftWrap: false,
                fontSize: '16px'
            });
            editor.getSession().setMode('ace/mode/bookly');
            staticWordCompleter = ace.require('ace/mode/bookly_completer').BooklyCompleter;
            langTools = ace.require('ace/ext/language_tools');

            $(this).keydown(function(e) {
                e.stopPropagation();
            });

            $(this).data('booklyEditor', {
                editor: editor,
                langTools: langTools,
                staticWordCompleter: staticWordCompleter
            });

            if (this.data('codes') !== undefined) {
                editor.completers = [staticWordCompleter(this.data('codes'))];
            }

            if (this.data('value') !== undefined) {
                editor.session.setValue('' + this.data('value'));
            }

            return this;
        },
        setValue: function (value) {
            $(this).data('booklyEditor').editor.session.setValue(value);
        },
        getValue: function () {
            return $(this).data('booklyEditor').editor.session.getValue();
        },
        setCodes: function (codes) {
            $(this).data('booklyEditor').langTools.setCompleters([$(this).data('booklyEditor').staticWordCompleter(codes)]);
        },
        focus: function () {
            let editor = $(this).data('booklyEditor').editor;
            editor.focus();
            const session = editor.getSession();
            const count = session.getLength();
            editor.gotoLine(count, session.getLine(count - 1).length);
        },
        onChange: function (callback) {
            if ( $(this).data('booklyEditor') ) {
                $(this).data('booklyEditor').editor.getSession().on('change', function () {
                    callback();
                });
            }
        }
    };

    $.fn.booklyAceEditor = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('No method ' + method + ' for jQuery.booklyAceEditor');
        }
    };
})(jQuery);