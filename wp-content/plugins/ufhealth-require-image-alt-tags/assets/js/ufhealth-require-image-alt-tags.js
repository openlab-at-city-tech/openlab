jQuery(document).ready(function(f) {
    "use strict";
    var e = function(t) {
        var e = void 0 !== t && t, d = f(".media-frame-toolbar .media-toolbar-primary"), a = f('.media-frame-content ul.attachments li[aria-checked="true"]'), r = !0, o = [];
        if (f(".ufh-needs-alt-text").each(function(t, e) {
            f(e).removeClass("ufh-needs-alt-text");
        }), 0 === a.length) {
            var n, l = f(".attachment-details").attr("data-id");
            if (void 0 !== l) {
                n = wp.media.model.Attachment.get(l).get("alt");
            } else {
                var i = f('.media-modal-content label[data-setting="alt"] input'), s = f('.media-frame-content input[data-setting="alt"]');
                n = i.length && 0 < i.length ? i.val() : s.val();
            }
            return 0 === f(".media-sidebar.visible").length || n.length && 0 < n.length ? (d.addClass("ufh-has-alt-text"), 
            !0) : (d.removeClass("ufh-has-alt-text"), e && alert(ufhTagsCopy.editTxt), !1);
        }
        if (a.each(function(t, e) {
            var a = f(e), n = a.attr("data-id"), l = wp.media.model.Attachment.get(n), i = l.get("alt");
            void 0 !== n && (i.length || "image" !== l.get("type") ? (d.addClass("ufh-has-alt-text"), 
            a.removeClass("ufh-needs-alt-text")) : (a.addClass("ufh-needs-alt-text"), o.push(l.get("title")), 
            r = !1));
        }), !1 === r) {
            if (d.removeClass("ufh-has-alt-text"), e) {
                for (var m = "\n\n", u = 0, h = o.length; u < h; u++) m = m + o[u] + "\n\n";
                alert(ufhTagsCopy.disclaimer + "\n\n" + ufhTagsCopy.txt + ":" + m);
            }
            return !1;
        }
        return !0;
    }, t = f("body");
    t.on("keyup", '.media-modal-content label[data-setting="alt"] input, .media-frame-content input[data-setting="alt"]', function() {
        e();
    }), t.on("mouseenter mouseleave click", ".media-frame-toolbar .media-toolbar-primary", function(t) {
        e("click" === t.type);
    });
});
//# sourceMappingURL=ufhealth-require-image-alt-tags.js.map