jQuery(document).ready(function($) {
    if (typeof addComment != 'undefined') {
        addComment.moveForm = function (commId, parentId, respondId, postId) {
            var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');

            if (!comm || !respond || !cancel || !parent)
                return;

            t.respondId = respondId;
            postId = postId || false;

            if (!t.I('wp-temp-form-div')) {
                div = document.createElement('div');
                div.id = 'wp-temp-form-div';
                div.style.display = 'none';
                respond.parentNode.insertBefore(div, respond);
            }

            comm.parentNode.insertBefore(respond, comm.nextSibling);
            if (post && postId)
                post.value = postId;
            parent.value = parentId;
            cancel.style.display = '';

            cancel.onclick = function () {
                tinymce.activeEditor.setContent("");
                tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'comment');

                var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

                if (!temp || !respond) {
                    tinymce.EditorManager.execCommand('mceAddEditor', true, 'comment');
                    return;
                }

                t.I('comment_parent').value = '0';
                temp.parentNode.insertBefore(respond, temp);
                temp.parentNode.removeChild(temp);
                this.style.display = 'none';
                this.onclick = null;
                tinymce.EditorManager.execCommand('mceAddEditor', true, 'comment');
                return false;
            };

            try {
                t.I('comment').focus();
            }
            catch (e) {
            }

            return false;
        };
    }
});