/*
 Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @file Sample plugin for CKEditor.
 */
(function () {
    CKEDITOR.plugins.add('cmtt_glossary',
            {
                init: function (editor)
                {
                    var a = {
                        exec: function (editor) {
                            var selection = editor.getSelection();
                            var text = selection.getSelectedText();
                            var nodeHtml = selection.getStartElement();
                            console.log(nodeHtml);
                            console.log(nodeHtml.innerHTML);
                            nodeHtml.setText(nodeHtml.getText().replace(text, '[glossary_exclude]' + text + '[/glossary_exclude]'));
                        }
                    };

                    editor.addCommand('cmtt_exclude_cmd', a);

                    editor.ui.addButton('cmtt_exclude',
                            {
                                label: 'Exclude from CM Tooltip Glossary',
                                command: 'cmtt_exclude_cmd',
                                toolbar: 'links',
                                icon: this.path + '../icon.png'
                            });

                    var b = {
                        exec: function (editor) {
                            var selection = editor.getSelection();
                            var text = selection.getSelectedText();
                            var nodeHtml = selection.getStartElement();
                            console.log(nodeHtml);
                            console.log(nodeHtml.innerHTML);
                            nodeHtml.setText(nodeHtml.getText().replace(text, '[cm_tooltip_parse]' + text + '[/cm_tooltip_parse]'));
                        }
                    };

                    editor.addCommand('cmtt_parse_cmd', b);

                    editor.ui.addButton('cmtt_parse',
                            {
                                label: 'Parse with CM Tooltip Glossary',
                                command: 'cmtt_parse_cmd',
                                toolbar: 'links',
                                icon: this.path + '../icon.png'
                            });
                }
            });
})();
