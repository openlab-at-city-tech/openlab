if (typeof wp.media !== "undefined" && wp.media) {
    wp.media.view.Attachment.Details.TwoColumn = wp.media.view.Attachment.Details.TwoColumn.extend({
        template: function (view) {
            const html = wp.media.template('attachment-details-two-column')(view);
            const dom = document.createElement('div');
            dom.innerHTML = html;
            if (this.model.attributes.type == "video") {
                var filesize = this.model.attributes.filesizeInBytes;

                var canShare = false;
                if (SCRIPT_DATA.B2S_PLUGIN_USER_VERSION > 0 && SCRIPT_DATA.canUseVideoAddon) {
                    if ((filesize / 1024) <= SCRIPT_DATA.volumeOpen) {
                        var btnText = SCRIPT_DATA.buttonTextShareable;
                        canShare = true;
                    } else {
                        var btnText = SCRIPT_DATA.buttonTextNotShareable;
                    }
                } else {
                    var btnText = SCRIPT_DATA.buttonTextUnlockModule;
                }

                const anchor = dom.querySelector('.attachment-info');
                const btn = document.createElement('button');
                if (!canShare) {
                    btn.setAttribute('disabled', true);
                }

                btn.setAttribute('id', this.model.attributes.id);
                btn.innerHTML = btnText;
                btn.classList.add('b2s-attachment-details-video-share-btn');
                btn.classList.add('b2s-btn');
                btn.classList.add('b2s-btn-primary');
                btn.classList.add('b2s-btn-sm');
                btn.classList.add('b2s-btn-margin-bottom-15');
                btn.classList.add('b2s-center-block');

                const title = document.createElement('h3');
                title.innerHTML = SCRIPT_DATA.blog2socialVideoTitle;
                title.classList.add("hndle");
                title.classList.add("ui-sortable-handle");

                const divider = document.createElement('hr');
                
                anchor.prepend(divider);
                anchor.prepend(btn);
                anchor.prepend(title);
            }


            return dom.innerHTML;
        },
        events: {
            ...wp.media.view.Attachment.Details.TwoColumn.prototype.events,
            'click .b2s-attachment-details-video-share-btn': 'shareVideo',
        },
        shareVideo: function (e)
        {
            const {id} = this.model.attributes;
            var filesize = this.model.attributes.filesizeInBytes;
            if ((filesize / 1024) <= SCRIPT_DATA.volumeOpen) {
                var canShare = true;
            } else {
                var canShare = false;
            }
            this.model.fetch({
                success: () => {
                    if (canShare) {
                        window.location.href = SCRIPT_DATA.url + id;
                    }
                },
                error: (collection, response, options) => {
                    console.log('could not open url to video share page');
                }
            });
        },
    });
}
