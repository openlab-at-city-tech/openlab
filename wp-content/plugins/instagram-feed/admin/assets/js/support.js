var support_data = {
    genericText: sbi_support.genericText,
    articles: sbi_support.articles,
    system_info: sbi_support.system_info,
    system_info_n: sbi_support.system_info_n,
    exportFeed: 'none',
    stickyWidget: false,
    feeds: sbi_support.feeds,
    supportUrl: sbi_support.supportUrl,
    socialWallActivated: sbi_support.socialWallActivated,
    socialWallLinks: sbi_support.socialWallLinks,
    siteSearchUrl: sbi_support.siteSearchUrl,
    siteSearchUrlWithArgs: null,
    searchKeywords: null,
    buttons: sbi_support.buttons,
    links: sbi_support.links,
    supportPageUrl: sbi_support.supportPageUrl,
    systemInfoBtnStatus: 'collapsed',
    copyBtnStatus: null,
    ajax_handler: sbi_support.ajax_handler,
    nonce: sbi_support.nonce,
    icons: sbi_support.icons,
    images: sbi_support.images,
    svgIcons: sbi_svgs,
    notificationElement: {
        type: 'success', // success, error, warning, message
        text: '',
        shown: null
    },
    viewsActive: {
        tempLoginAboutPopup: false
    },
    //Tenmp User Account
    tempUser: sbi_support.tempUser,
    createStatus: null,
    deleteStatus: null
}

var sbisupport = new Vue({
    el: "#sbi-support",
    http: {
        emulateJSON: true,
        emulateHTTP: true
    },
    data: support_data,
    methods: {
        copySystemInfo: function () {
            let self = this;
            const el = document.createElement('textarea');
            el.className = 'sbi-fb-cp-clpboard';
            el.value = self.system_info_n;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            this.notificationElement = {
                type: 'success',
                text: this.genericText.copiedToClipboard,
                shown: "shown"
            };

            setTimeout(function () {
                this.notificationElement.shown = "hidden";
            }.bind(self), 3000);
        },
        /**
         * Copy text to clipboard
         *
         * @since 4.0
         */
        copyToClipBoard: function (value) {
            var self = this;
            const el = document.createElement('textarea');
            el.className = 'sbi-fb-cp-clpboard';
            el.value = value;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            self.notificationElement = {
                type: 'success',
                text: this.genericText.copiedToClipboard,
                shown: "shown"
            };
            setTimeout(function () {
                self.notificationElement.shown = "hidden";
            }, 3000);
        },
        expandSystemInfo: function () {
            this.systemInfoBtnStatus = (this.systemInfoBtnStatus == 'collapsed') ? 'expanded' : 'collapsed';
        },
        expandBtnText: function () {
            if (this.systemInfoBtnStatus == 'collapsed') {
                return this.buttons.expand;
            } else if (this.systemInfoBtnStatus == 'expanded') {
                return this.buttons.collapse;
            }
        },
        exportFeedSettings: function () {
            // return if no feed is selected
            if (this.exportFeed === 'none') {
                return;
            }

            let url = this.ajax_handler + '?action=sbi_export_settings_json&nonce=' + this.nonce + '&feed_id=' + this.exportFeed;
            window.location = url;
        },
        searchDoc: function () {
            let self = this;
            let searchInput = document.getElementById('sbi-search-doc-input');
            searchInput.addEventListener('keyup', function (event) {
                let url = new URL(self.siteSearchUrl);
                let search_params = url.searchParams;
                if (self.searchKeywords) {
                    search_params.set('search', self.searchKeywords);
                }
                search_params.set('plugin', 'instagram');
                url.search = search_params.toString();
                self.siteSearchUrlWithArgs = url.toString();

                if (event.key === 'Enter') {
                    window.open(self.siteSearchUrlWithArgs, '_blank');
                }
            })
        },
        searchDocStrings: function () {
            let self = this;
            let url = new URL(this.siteSearchUrl);
            let search_params = url.searchParams;
            setTimeout(function () {
                search_params.set('search', self.searchKeywords);
                search_params.set('plugin', 'instagram');
                url.search = search_params.toString();
                self.siteSearchUrlWithArgs = url.toString();
            }, 10);
        },
        goToSearchDocumentation: function () {
            if (this.searchKeywords !== null && this.siteSearchUrlWithArgs !== null) {
                window.open(this.siteSearchUrlWithArgs, '_blank');
            }
        },
        /**
         * Activate View
         *
         * @since 6.2.0
         */
        activateView: function (viewName, sourcePopupType = 'creation', ajaxAction = false) {
            var self = this;
            self.viewsActive[viewName] = (self.viewsActive[viewName] == false) ? true : false;
        },
        /**
         * Toggle Sticky Widget view
         *
         * @since 4.0
         */
        toggleStickyWidget: function () {
            this.stickyWidget = !this.stickyWidget;
        },

        /**
         * Create New Temp User
         *
         * @since 4.0
         */
        createTempUser: function () {
            const self = this;
            self.createStatus = 'loading';
            let data = new FormData();
            data.append('action', 'sbi_create_temp_user');
            data.append('nonce', sbi_admin.nonce);
            fetch(sbi_support.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    self.createStatus = null;
                    if (data.success) {
                        self.tempUser = data.user;
                    }
                    self.notificationElement = {
                        type: data.success === true ? 'success' : 'error',
                        text: data.message,
                        shown: "shown"
                    };
                    setTimeout(function () {
                        self.notificationElement.shown = "hidden";
                    }, 5000);
                });

        },

        /**
         * Delete Temp User
         *
         * @since 4.0
         */
        deleteTempUser: function () {
            const self = this;
            self.deleteStatus = 'loading';
            let data = new FormData();
            data.append('action', 'sbi_delete_temp_user');
            data.append('nonce', sbi_admin.nonce);
            data.append('userId', self.tempUser.id);
            fetch(sbi_support.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    self.deleteStatus = null;
                    if (data.success) {
                        self.tempUser = null;
                    }
                    self.notificationElement = {
                        type: data.success === true ? 'success' : 'error',
                        text: data.message,
                        shown: "shown"
                    };
                    setTimeout(function () {
                        self.notificationElement.shown = "hidden";
                    }, 5000);
                });
        }
    },
})
