(function () {
  var oftb_toolbarActive = false;

  tinymce.create('tinymce.plugins.outofthebox', {
    init: function (ed, url) {

      var t = this;
      t.url = url;

      ed.addCommand('mceOutoftheBox', function (query) {
        ed.windowManager.open({
          file: ajaxurl + '?action=outofthebox-getpopup&' + query,
          width: 1000,
          height: 600,
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addCommand('mceOutoftheBox_links', function () {
        ed.windowManager.open({
          file: ajaxurl + '?action=outofthebox-getpopup&type=links',
          width: 1000,
          height: 600,
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addCommand('mceOutoftheBox_embedded', function () {
        ed.windowManager.open({
          file: ajaxurl + '?action=outofthebox-getpopup&type=embedded',
          width: 1000,
          height: 600,
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addButton('outofthebox', {
        title: 'Out-of-the-Box shortcode',
        image: url + '/../../css/images/dropbox_logo.png',
        cmd: 'mceOutoftheBox'
      });
      ed.addButton('outofthebox_links', {
        title: 'Out-of-the-Box links',
        image: url + '/../../css/images/dropbox_logo_link.png',
        cmd: 'mceOutoftheBox_links'
      });
      ed.addButton('outofthebox_embedded', {
        title: 'Embed files from Dropbox',
        image: url + '/../../css/images/dropbox_logo_embedded.png',
        cmd: 'mceOutoftheBox_embedded'
      });

      ed.on('mousedown', function (event) {
        if (ed.dom.getParent(event.target, '#wp-oftb-toolbar')) {
          if (tinymce.Env.ie) {
            // Stop IE > 8 from making the wrapper resizable on mousedown
            event.preventDefault();
          }
        } else {
          removeOftBToolbar(ed);
        }
      });

      ed.on('mouseup', function (event) {
        var image,
                node = event.target,
                dom = ed.dom;

        // Don't trigger on right-click
        if (event.button && event.button > 1) {
          return;
        }

        if (node.nodeName === 'DIV' && dom.getParent(node, '#wp-oftb-toolbar')) {
          image = dom.select('img[data-wp-oftbselect]')[0];

          if (image) {
            ed.selection.select(image);

            if (dom.hasClass(node, 'remove')) {
              removeOftBToolbar(ed);
              removeOftBImage(image, ed);
            } else if (dom.hasClass(node, 'edit')) {
              var shortcode = ed.selection.getContent();
              shortcode = shortcode.replace('</p>', '').replace('<p>', '').replace('[outofthebox ', '').replace('"]', '');
              var query = encodeURIComponent(shortcode).split('%3D%22').join('=').split('%22%20').join('&');
              removeOftBToolbar(ed);
              ed.execCommand('mceOutoftheBox', query);
            }
          }
        } else if (node.nodeName === 'IMG' && !ed.dom.getAttrib(node, 'data-wp-oftbselect') && isOftBPlaceholder(node, ed)) {
          addOftBToolbar(node, ed);
        } else if (node.nodeName !== 'IMG') {
          removeOftBToolbar(ed);
        }
      });

      ed.on('keydown', function (event) {
        var keyCode = event.keyCode
        // Key presses will replace the image so we need to remove the toolbar
        if (oftb_toolbarActive) {
          if (event.ctrlKey || event.metaKey || event.altKey ||
                  (keyCode < 48 && keyCode > 90) || keyCode > 186) {
            return;
          }

          removeOftBToolbar(ed);
        }
      });

      ed.on('cut', function () {
        removeOftBToolbar(ed);
      });

      ed.on('BeforeSetcontent', function (ed) {
        ed.content = t._do_oftb_shortcode(ed.content, t.url);
      });
      ed.on('PostProcess', function (ed) {
        if (ed.get)
          ed.content = t._get_oftb_shortcode(ed.content);
      });
    },
    _do_oftb_shortcode: function (co, url) {
      return co.replace(/\[outofthebox([^\]]*)\]/g, function (a, b) {
        return '<img src="' + url + '/../../css/images/transparant.png" class="wp_oftb_shortcode mceItem" title="Out-of-the-Box" data-mce-placeholder="1" data-code="' + Base64.encode(b) + '"/>';
      });
    },
    _get_oftb_shortcode: function (co) {

      function getAttr(s, n) {
        n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
        return n ? n[1] : '';
      }
      ;

      return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function (a, im) {
        var cls = getAttr(im, 'class');

        if (cls.indexOf('wp_oftb_shortcode') != -1)
          return '<p>[outofthebox ' + tinymce.trim(Base64.decode(getAttr(im, 'data-code'))) + ']</p>';

        return a;
      });
    },
    createControl: function (n, cm) {
      return null;
    }
  });

  tinymce.PluginManager.add('outofthebox', tinymce.plugins.outofthebox);


  function removeOftBImage(node, editor) {
    editor.dom.remove(node);
    removeToolbar();
  }

  function addOftBToolbar(node, editor) {
    var toolbarHtml, toolbar,
            dom = editor.dom;

    removeOftBToolbar(editor);

    // Don't add to placeholders
    if (!node || node.nodeName !== 'IMG' || !isOftBPlaceholder(node, editor)) {
      return;
    }

    dom.setAttrib(node, 'data-wp-oftbselect', 1);

    toolbarHtml = '<div class="dashicons dashicons-edit edit" data-mce-bogus="1"></div>' +
            '<div class="dashicons dashicons-no-alt remove" data-mce-bogus="1"></div>';

    toolbar = dom.create('div', {
      'id': 'wp-oftb-toolbar',
      'data-mce-bogus': '1',
      'contenteditable': false
    }, toolbarHtml);

    var parentDiv = node.parentNode;
    parentDiv.insertBefore(toolbar, node);

    oftb_toolbarActive = true;
  }

  function removeOftBToolbar(editor) {
    var toolbar = editor.dom.get('wp-oftb-toolbar');

    if (toolbar) {
      editor.dom.remove(toolbar);
    }

    editor.dom.setAttrib(editor.dom.select('img[data-wp-oftbselect]'), 'data-wp-oftbselect', null);

    oftb_toolbarActive = false;
  }

  function isOftBPlaceholder(node, editor) {
    var dom = editor.dom;

    if (dom.hasClass(node, 'wp_oftb_shortcode')) {

      return true;
    }

    return false;
  }

  /* Create Base64 Object */
  var Base64 = {_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) {
      var t = "";
      var n, r, i, s, o, u, a;
      var f = 0;
      e = Base64._utf8_encode(e);
      while (f < e.length) {
        n = e.charCodeAt(f++);
        r = e.charCodeAt(f++);
        i = e.charCodeAt(f++);
        s = n >> 2;
        o = (n & 3) << 4 | r >> 4;
        u = (r & 15) << 2 | i >> 6;
        a = i & 63;
        if (isNaN(r)) {
          u = a = 64
        } else if (isNaN(i)) {
          a = 64
        }
        t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
      }
      return t
    }, decode: function (e) {
      var t = "";
      var n, r, i;
      var s, o, u, a;
      var f = 0;
      e = e.replace(/[^A-Za-z0-9\+\/\=]/g, "");
      while (f < e.length) {
        s = this._keyStr.indexOf(e.charAt(f++));
        o = this._keyStr.indexOf(e.charAt(f++));
        u = this._keyStr.indexOf(e.charAt(f++));
        a = this._keyStr.indexOf(e.charAt(f++));
        n = s << 2 | o >> 4;
        r = (o & 15) << 4 | u >> 2;
        i = (u & 3) << 6 | a;
        t = t + String.fromCharCode(n);
        if (u != 64) {
          t = t + String.fromCharCode(r)
        }
        if (a != 64) {
          t = t + String.fromCharCode(i)
        }
      }
      t = Base64._utf8_decode(t);
      return t
    }, _utf8_encode: function (e) {
      e = e.replace(/\r\n/g, "\n");
      var t = "";
      for (var n = 0; n < e.length; n++) {
        var r = e.charCodeAt(n);
        if (r < 128) {
          t += String.fromCharCode(r)
        } else if (r > 127 && r < 2048) {
          t += String.fromCharCode(r >> 6 | 192);
          t += String.fromCharCode(r & 63 | 128)
        } else {
          t += String.fromCharCode(r >> 12 | 224);
          t += String.fromCharCode(r >> 6 & 63 | 128);
          t += String.fromCharCode(r & 63 | 128)
        }
      }
      return t
    }, _utf8_decode: function (e) {
      var t = "";
      var n = 0;
      var r = c1 = c2 = 0;
      while (n < e.length) {
        r = e.charCodeAt(n);
        if (r < 128) {
          t += String.fromCharCode(r);
          n++
        } else if (r > 191 && r < 224) {
          c2 = e.charCodeAt(n + 1);
          t += String.fromCharCode((r & 31) << 6 | c2 & 63);
          n += 2
        } else {
          c2 = e.charCodeAt(n + 1);
          c3 = e.charCodeAt(n + 2);
          t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
          n += 3
        }
      }
      return t
    }}

})();