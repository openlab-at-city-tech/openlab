! function () {
	tinymce.create('tinymce.plugins.shortcodesultimate', {
		init: function (e) {
			var t = this,
				n = 'su';
			// e.onKeyPress.add(function (e, o) {
			e.onBeforeSetContent.add(function (e, o) {
				// console.log(e);
				o.content = t["_shortcodes2html"](o.content);
			});
			e.onPostProcess.add(function (e, o) {
				o.set && (o.content = t["_shortcodes2html"](o.content));
				o.get && (o.content = t["_html2shortcodes"](o.content));
			});
		},
		getInfo: function () {
			return {
				longname: "BBCode Plugin",
				author: "Moxiecode Systems AB",
				authorurl: "http://www.tinymce.com",
				infourl: "http://www.tinymce.com/wiki.php/Plugin:bbcode"
			}
		},
		_html2shortcodes: function (content) {
			// Prepare data
			var shortcodes = ['row', 'column'],
				prefix = '';
			// Trim content
			content = tinymce.trim(content);
			content = content.replace(/<div.*?class=\"su-row (.*?)\".*?>(.*?)<\/div>/gi, '[row class="$1"]$2[/column]');
			content = content.replace(/<div.*?class=\"su-column su-column-size-(.*?) (.*?)\".*?>(.*?)<\/div>/gi, '[column size="$1" class="$2"]$3[/column]');
			return content;
			// function t(t, n) {
			// e = e.replace(t, n);
			// }
			// return e = tinymce.trim(e),
			// t(),
			// e;
			// t(/<a.*?href=\"(.*?)\".*?>(.*?)<\/a>/gi, "[url=$1]$2[/url]"),
			// t(/<font.*?color=\"(.*?)\".*?class=\"codeStyle\".*?>(.*?)<\/font>/gi, "[code][color=$1]$2[/color][/code]"),
			// t(/<font.*?color=\"(.*?)\".*?class=\"quoteStyle\".*?>(.*?)<\/font>/gi, "[quote][color=$1]$2[/color][/quote]"),
			// t(/<font.*?class=\"codeStyle\".*?color=\"(.*?)\".*?>(.*?)<\/font>/gi, "[code][color=$1]$2[/color][/code]"),
			// t(/<font.*?class=\"quoteStyle\".*?color=\"(.*?)\".*?>(.*?)<\/font>/gi, "[quote][color=$1]$2[/color][/quote]"),
			// t(/<span style=\"color: ?(.*?);\">(.*?)<\/span>/gi, "[color=$1]$2[/color]"),
			// t(/<font.*?color=\"(.*?)\".*?>(.*?)<\/font>/gi, "[color=$1]$2[/color]"),
			// t(/<span style=\"font-size:(.*?);\">(.*?)<\/span>/gi, "[size=$1]$2[/size]"),
			// t(/<font>(.*?)<\/font>/gi, "$1"),
			// t(/<img.*?src=\"(.*?)\".*?\/>/gi, "[img]$1[/img]"),
			// t(/<span class=\"codeStyle\">(.*?)<\/span>/gi, "[code]$1[/code]"),
			// t(/<span class=\"quoteStyle\">(.*?)<\/span>/gi, "[quote]$1[/quote]"),
			// t(/<strong class=\"codeStyle\">(.*?)<\/strong>/gi, "[code][b]$1[/b][/code]"),
			// t(/<strong class=\"quoteStyle\">(.*?)<\/strong>/gi, "[quote][b]$1[/b][/quote]"),
			// t(/<em class=\"codeStyle\">(.*?)<\/em>/gi, "[code][i]$1[/i][/code]"),
			// t(/<em class=\"quoteStyle\">(.*?)<\/em>/gi, "[quote][i]$1[/i][/quote]"),
			// t(/<u class=\"codeStyle\">(.*?)<\/u>/gi, "[code][u]$1[/u][/code]"),
			// t(/<u class=\"quoteStyle\">(.*?)<\/u>/gi, "[quote][u]$1[/u][/quote]"),
			// t(/<\/(strong|b)>/gi, "[/b]"),
			// t(/<(strong|b)>/gi, "[b]"),
			// t(/<\/(em|i)>/gi, "[/i]"),
			// t(/<(em|i)>/gi, "[i]"),
			// t(/<\/u>/gi, "[/u]"),
			// t(/<span style=\"text-decoration: ?underline;\">(.*?)<\/span>/gi, "[u]$1[/u]"),
			// t(/<u>/gi, "[u]"),
			// t(/<blockquote[^>]*>/gi, "[quote]"),
			// t(/<\/blockquote>/gi, "[/quote]"),
			// t(/<br \/>/gi, "\n"),
			// t(/<br\/>/gi, "\n"),
			// t(/<br>/gi, "\n"),
			// t(/<p>/gi, ""),
			// t(/<\/p>/gi, "\n"),
			// t(/&nbsp;|\u00a0/gi, " "),
			// t(/&quot;/gi, '"'),
			// t(/&lt;/gi, "<"),
			// t(/&gt;/gi, ">"),
			// t(/&amp;/gi, "&"),
		},
		_shortcodes2html: function (content) {
			// Prepare data
			var shortcodes = ['row', 'column'],
				prefix = '';
			// Trim content
			content = tinymce.trim(content);
			// Loop shortcodes
			for (var i = shortcodes.length - 1; i >= 0; i--) {
				content = wp.shortcode.replace(prefix + shortcodes[i], content, this._shortcode2html);
			};
			return content;
			// return e = tinymce.trim(e),
			// t(/\[column.*?size="(.*?)".*?class="(.*?)".*?\](.*?)\[\/column\]/gi, '<div class="su-column su-column-size-$1 $2">$3</div>'),
			// t(/\n/gi, "<br />"),
			// t(/\[b\]/gi, "<strong>"),
			// t(/\[\/b\]/gi, "</strong>"),
			// t(/\[i\]/gi, "<em>"),
			// t(/\[\/i\]/gi, "</em>"),
			// t(/\[u\]/gi, "<u>"),
			// t(/\[\/u\]/gi, "</u>"),
			// t(/\[url=([^\]]+)\](.*?)\[\/url\]/gi, '<a href="$1">$2</a>'),
			// t(/\[url\](.*?)\[\/url\]/gi, '<a href="$1">$1</a>'),
			// t(/\[img\](.*?)\[\/img\]/gi, '<img src="$1" />'),
			// t(/\[color=(.*?)\](.*?)\[\/color\]/gi, '<font color="$1">$2</font>'),
			// t(/\[code\](.*?)\[\/code\]/gi, '<span class="codeStyle">$1</span>&nbsp;'),
			// t(/\[quote.*?\](.*?)\[\/quote\]/gi, '<span class="quoteStyle">$1</span>&nbsp;'),
			// e
		},
		_shortcode2html: function (s) {
			// Prepare data
			var prefix = '';
			// Remove prefix from shortcode tag name
			s.tag = s.tag.replace(prefix, '');
			// Row
			if (s.tag === 'row') {
				var cssclass = (typeof s.attrs.named.class !== 'undefined') ? ' ' + s.attrs.named.class : '';
				return '<div class="su-row' + cssclass + '">' + s.content + '</div>';
			}
			// Columns
			else if (s.tag === 'column') {
				var size = (typeof s.attrs.named.size !== 'undefined') ? s.attrs.named.size.replace('/', '-') : '1-1',
					cssclass = (typeof s.attrs.named.class !== 'undefined') ? ' ' + s.attrs.named.class : '';
				return '<div class="su-column su-column-size-' + size + cssclass + '">' + s.content + '</div>';
			}
		}
	}),
	tinymce.PluginManager.add('shortcodesultimate', tinymce.plugins.shortcodesultimate);
}();