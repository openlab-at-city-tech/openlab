(function () {
    if (typeof (advGb_CS) === 'undefined') return;
    var advGb_CStyles = advGb_CS.filter(function(cstyle) {return cstyle.id !== 0});

    tinyMCE.PluginManager.add('customstyles', function (editor) {
        var output = [{
            text: 'Paragraph',
            value: ' '
        }];
        for (var i = 0; i < advGb_CStyles.length; i++) {
            var style = {};
            classText = advGb_CStyles[i]['name'];
            style['text'] = advGb_CStyles[i]['title'];
            style['value'] = classText;
            output.push(style);
        }

        editor.addButton('customstyles', {
            type: 'listbox',
            text: 'Custom styles',
            icon: false,
            fixedWidth: true,
            onselect: function () {
                addCustomStyle(this.value());
            },
            onPostRender: createListBoxChangeHandler(),
            values: output
        });

        function addCustomStyle(classes) {
            var element = editor.selection.getNode();
            var par = editor.dom.getParent(element, 'div.advgbstyles');
            if (par) {
                if (par.className.indexOf(classes) > -1) {
                    var cont = document.createElement('p');
                    cont.id = par.id;
                    editor.dom.replace(cont, par, true);
                    editor.focus();
                    return false;
                } else {
                    par.className = 'advgbstyles ' + classes;
                    return false;
                }
            } else {
                if (element.nodeName === 'BODY') {
                    alert('Wrong element!');
                    return false;
                }
                var wrapper = document.createElement('div');
                wrapper.id = element.id;
                wrapper.className = 'advgbstyles ' + classes;
                editor.dom.replace(wrapper, element, true);
                editor.focus();
            }
        }

        function createListBoxChangeHandler() {
            return function () {
                var self = this;

                editor.on('nodeChange', function (e) {
                    var selected = editor.selection.getStart();
                    var classes = '';

                    if (selected.className && selected.className.indexOf('advgbstyles') > -1) {
                        classes = selected.className;
                        classes = classes.replace('advgbstyles ', '');
                    }

                    self.value(classes);
                })
            }
        }
    });
})();