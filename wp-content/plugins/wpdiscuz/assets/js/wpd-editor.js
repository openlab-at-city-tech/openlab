class wpdEditorCounter {
    constructor(quill, options) {
        this.quill = quill;
        this.options = options;
        this.commentmaxcount = options.commentmaxcount;
        this.replymaxcount = options.replymaxcount;
        this.commentmincount = options.commentmincount;
        this.replymincount = options.replymincount;
        this.container = document.getElementById('wpd-editor-char-counter-' + options.uniqueID);
        this.submit = document.getElementById('wpd-field-submit-' + options.uniqueID);
        quill.on('editor-change', this.update.bind(this));
        this.update();
    }

    calculate() {
        let length = this.quill.getText().length,
            editorid = this.quill.container.id,
            images = Array.from(document.querySelectorAll(`#${editorid} .ql-editor img`));
        if (images.length) {
            images.forEach(function (img) {
                if (img.src.match(/https\:\/\/s\.w\.org\/images\/core\/emoji/gi) !== null) {
                    length += img.alt.length;
                } else if (img.classList.contains('wpdem-sticker')) {
                    length += img.alt.length;
                } else {
                    length += img.src.length;
                }
            });
        }
        return length;
    }

    update() {
        let length = this.calculate(),
            _length = length - 1;
        let parentId = this.quill.container.id.substring(this.quill.container.id.lastIndexOf('_') + 1);
        let commentmaxcount = parseInt(parentId) ? this.replymaxcount : this.commentmaxcount;

        if (commentmaxcount > 0 && length >= commentmaxcount) {
            this.quill.deleteText(commentmaxcount, length);
        }
        if (commentmaxcount > 0) {
            let range = commentmaxcount - _length;
            this.container.innerText = range >= 0 ? range : 0;
            if (length + 10 > commentmaxcount) {
                this.container.classList.add("error");
            } else {
                this.container.classList.remove("error");
            }
        } else if (this.container) {
            this.container.remove();
        }
    }
}

Quill.register('modules/counter', wpdEditorCounter);

let Link = Quill.import('formats/link');

class wpdEditorLink extends Link {
    static create(value) {
        let node = super.create(value);
        value = this.sanitize(value);
        node.setAttribute('href', value);
        let siteUrl = location.protocol + '//' + location.hostname;
        if (value.startsWith(siteUrl) || value.charAt(0) === '#' || (value.charAt(0) === '/' && value.charAt(1) !== '/')) {
            node.removeAttribute('target');
        }
        return node;
    }

    static sanitize(url) {
        let s_url = super.sanitize(url);
        let protocol = s_url.slice(0, s_url.indexOf(':'));
        if (!(s_url.charAt(0) === '#' || s_url.charAt(0) === '/') && this.PROTOCOL_WHITELIST.indexOf(protocol) === -1) {
            s_url = 'http://' + url;
        }
        return s_url;
    }

}

Quill.register(wpdEditorLink, true);

class WpdEditor {
    constructor() {
        this.editorWraperPrefix = 'wpd-editor-wraper';
        this.textEditorContainer = 'ql-texteditor';
        this.textEditorPrefix = 'wc-textarea';
        this.editorToolbarPrefix = 'wpd-editor-toolbar';
        this.sourceCodeButtonName = 'sourcecode';
        this.spoiler = 'spoiler';
        this.spoilerPromtTitle = wpdiscuzAjaxObj.wc_spoiler_title;
        this._container = '';
        this._uniqueid = '';
        this.currentEditor = null;
        this._editors = new Map();
        this._handlers = new Map();
        this._initDefaults();
    }

    addButtonEventHandler(name, func) {
        this._handlers.set(name, func);
    }

    set uniqueid(value) {
        if (value !== '' && typeof value === 'string') {
            this._uniqueid = value;
        } else if (value === '') {
            this._uniqueid = this._findUniqueId();
        } else {
            console.error('Incorrect uniqueid.');
        }
    }

    get uniqueid() {
        return this._uniqueid;
    }

    set container(value) {
        if (value !== '' && typeof value === 'string') {
            this._container = value;
            this.uniqueid = this._findUniqueId();
        } else {
            console.error('Incorrect uniqueid.');
        }
    }

    get container() {
        return this._container;
    }

    createEditor(container) {
        this.container = container;
        if (!this._editors.has(this.uniqueid)) {
            let toolbar = `#${this.editorToolbarPrefix}-${this.uniqueid}`;
            wpdiscuzEditorOptions.modules.toolbar = toolbar;
            wpdiscuzEditorOptions.modules.counter.uniqueID = this.uniqueid;
            let editor = new Quill(this.container, wpdiscuzEditorOptions);
//            editor.setContents([{insert: '\n'}]);
            editor.on('editor-change', (eventName, ...args) => {
                if (args[0] !== null) {
                    this.currentEditor = editor;
                    this.container = editor.container.id;
                }
            });
//            editor.clipboard.addMatcher('PRE', (node, delta) => {
//                var Delta = Quill.import('delta');
//                return new Delta([{insert: this._htmlEntities(node.innerHTML), attributes: {'code-block': true}}]);
//            });
            editor.clipboard.addMatcher('a', (node, delta) => {
                if (node.getAttribute("href") === node.innerHTML) {
                    var Delta = Quill.import('delta');
                    return new Delta([{insert: node.innerHTML}]);
                } else {
                    return delta;
                }
            });

            editor.clipboard.addMatcher('img', (node, delta) => {
                let Delta = Quill.import('delta');
                let src = node.getAttribute("src");
                if (/^data:image\/.+;base64/.test(src)) {
                    return new Delta([{insert: ''}]);
                } else {
                    return new Delta([{insert: src}]);
                }
            });
            Array.from(document.querySelectorAll(`${toolbar} button`)).forEach((button) => {
                button.onclick = () => {
                    this.currentEditor = editor;
                    this.container = editor.container.id;
                    let buttonName = button.dataset.wpde_button_name;
                    if (buttonName !== undefined &&
                        typeof buttonName === 'string' &&
                        buttonName.trim() !== '' && this._handlers.has(buttonName)) {
                        this._handlers.get(buttonName)(this.currentEditor, this.uniqueid);
                    }
                };
            });
            this._bindTextEditor(editor);
            this._editors.set(this.uniqueid, editor);
            document.getElementById(`${this.editorWraperPrefix}-${this.uniqueid}`).style.display = "";
        } else {
            this.currentEditor = this._editors.get(this.uniqueid);
        }
        let commentsCount = 0;
        if (document.getElementsByClassName('wpd-thread-info').length) {
            commentsCount = parseInt(document.getElementsByClassName('wpd-thread-info')[0].getAttribute('data-comments-count'));
        }
        let phraseKey = commentsCount ? 'wc_comment_join_text' : 'wc_be_the_first_text';
        this.currentEditor.root.setAttribute('data-placeholder', wpdiscuzAjaxObj.applyFilterOnPhrase(wpdiscuzEditorOptions[phraseKey], phraseKey, jQuery(container)));
        return this.currentEditor;
    }

//    _htmlEntities(str) {
//        var txt = document.createElement('textarea');
//        txt.innerHTML = str;
//        return txt.value.replace(/<\!\-\-\?php/g, '<?php').replace(/\?\-\->/g, '?>').replace(/\-\-\->/g, '->');
//    }

    removeEditor(container) {
        this.container = container;
        if (this._editors.has(this.uniqueid)) {
            this._editors.delete(this.uniqueid);
        }
    }

    _bindTextEditor(editor) {
        let textEditorID = `${this.textEditorPrefix}-${this.uniqueid}`,
            textEditorHtml = document.getElementById(textEditorID);
        if (textEditorHtml) {
            textEditorHtml.style.cssText = "display: none;";
            editor.addContainer(this.textEditorContainer).appendChild(textEditorHtml);
        }
//        if (editor.container.id.indexOf('-edit_') > 0) {
//            console.log(textEditorHtml.value);
//            editor.clipboard.dangerouslyPasteHTML(0, textEditorHtml.value);
//            editor.update();
//        }
        /* editor.on('text-change', (delta, oldDelta, source) => {
         if (source === Quill.sources.USER) {
         textEditorHtml.value = editor.root.innerHTML;
         }
         });*/
        this.currentEditor = editor;
    }

    _findUniqueId() {
        return this.container.substring(this.container.lastIndexOf('-') + 1);
    }

    _initDefaults() {
        this.addButtonEventHandler(this.sourceCodeButtonName, (editor) => {
            let textEditor = document.getElementById(`${this.textEditorPrefix}-${this.uniqueid}`);
//            editor.deleteText(0, editor.getLength());
//            editor.clipboard.dangerouslyPasteHTML(0, textEditor.value);
            let sourceCodeWrapperBg = document.getElementById('wpd-editor-source-code-wrapper-bg');
            let sourceCodeWrapper = document.getElementById('wpd-editor-source-code-wrapper');
            let sourceCode = document.getElementById('wpd-editor-source-code');
            let editorUid = document.getElementById('wpd-editor-uid');
            sourceCodeWrapperBg.style.display = 'block';
            sourceCodeWrapper.style.display = 'block';
            editorUid.value = editor.container.id;
            sourceCode.value = editor.root.innerHTML;
        });
        this.addButtonEventHandler(this.spoiler, (editor) => {
            let spoilerTitle = prompt(this.spoilerPromtTitle);
            if (spoilerTitle === null) {
                return;
            }
            let spoilerShortCodeLeft = ` [spoiler title="${spoilerTitle}"] `,
                spoilerShortCodeRight = ' [/spoiler] ',
                reng = editor.getSelection();
            if (reng === null) {
                reng = {
                    index: editor.getLength() - 1,
                    length: 0
                };
            }
            if (reng.length === 0) {
                editor.insertText(reng.index, spoilerShortCodeLeft + spoilerShortCodeRight, Quill.sources.USER);
                editor.setSelection(reng.index + spoilerShortCodeLeft.length, Quill.sources.USER);
            } else {
                editor.insertText(reng.index, spoilerShortCodeLeft);
                editor.insertText(reng.index + spoilerShortCodeLeft.length + reng.length, spoilerShortCodeRight, Quill.sources.USER);
                editor.setSelection(reng.index + spoilerShortCodeLeft.length + reng.length + spoilerShortCodeRight.length, Quill.sources.USER);
            }
        });
    }
}
