(function($) {
    nggProgressBar = {

        settings: {
            header:  '',
            id:      'progressbar',
            init:    false,
            maxStep: 100,
            wait:    false,

            finishedNote: 'Done!',
            submitNGGFormOnFinished: true,
            includeTinyMCEHackForIncrements: true
        },

        width: 0,

        init: function(settings) {
            const self = this;

            settings = this.settings = $.extend({}, this.settings, {}, settings || {});

            this.adjust_max_step(settings.maxStep);

            if ($("#" + settings.id + "_dialog").length === 0) {

                settings.header = (settings.header.length > 0) ? settings.header : '' ;
                $("body").append('<div id="' + settings.id + '_dialog"><div id="' + settings.id + '" class="progressborder"><div class="' + settings.id + '"><span>0%</span></div></div></div>');

                $("#" + settings.id + "_dialog").dialog({
                    width: 640,
                    resizable: true,
                    modal: true,
                    title: settings.header,
                    position: {
                        my: 'center',
                        at: 'center',
                        of: this.find_parent(window)
                    },
                    close: function(event, ui) {
                        self.remove_dialog(0, 0);
                    }
                });
            }

            this.div = $('#' + settings.id + '_dialog');
            settings.init = true;

            return this.div;
        },

        adjust_max_step: function(maxStep) {
            this.settings.maxStep = maxStep;
            this.width = Math.round(( 100 / maxStep) * 100 ) /100;
        },

        find_parent: function(child) {
            try {
                if (child && child.parent) {
                    child = child.parent;
                }
            } catch (Exception) {}

            return child;
        },

        addMessage: function(message) {
            const settings = this.settings;
            if (!settings.init) {
                this.init();
            }

            const div = this.div;
            if (div.find("#" + settings.id + "_message").length === 0) {
                div.append('<div class="' + settings.id + '_message"><span style="display:block" id="' + settings.id + '_message">' + message + '</span></div>');
            } else {
                $("#" + settings.id + "_message").html(message);
            }
        },

        addNote: function(note, detail) {
            const settings = this.settings;
            if (!settings.init) {
                this.init();
            }

            const div = this.div;
            settings.wait = true;

            if (div.find("#" + settings.id + "_note").length === 0) {
                div.append('<ul id="' + settings.id + '_note">&nbsp;</ul>');
            }

            if (detail) {
                $("#" + settings.id + "_note").append("<li>" + note + "<div class='show_details'><span>[more]</span><br />" + detail + "</div></li>");
            } else {
                $("#" + settings.id + "_note").append("<li>" + note + "</li>");
            }

            // increase the height to show the note
            div.dialog("option", "height", 220);
        },

        increase: function(step) {
            const settings = this.settings;

            const value  = step * this.width + "%";
            const rvalue = Math.round(step * this.width) + "%";

            $("#" + settings.id + " div").width(value);
            $("#" + settings.id + " span").html(rvalue);

            if (settings.includeTinyMCEHackForIncrements) {
                // Try to restore ATP tabs
                $(this.find_parent(window).document).scrollTop(0);

                const tinymce_frame = $(this.find_parent(window).frameElement).parent();
                const css_top = tinymce_frame.css('top');

                setTimeout(function() {
                    tinymce_frame.css('top', 0);
                }, 1);

                setTimeout(function() {
                    tinymce_frame.css('top', css_top);
                }, 3);
            }
        },

        finished: function(message) {
            const settings = this.settings;

            message = (message === undefined) ? settings.finishedNote : message;

            $("#" + settings.id + " div").width('100%');
            $("#" + settings.id + " span").html('100%');

            // In the case we add a note, we should wait for a click
            const div = this.div;
            const self = this;

            if (settings.wait) {
                $("#" + settings.id).delay(1000).hide("slow");

                self.addNote(message);

                div.on('click', function() {
                    self.remove_dialog(false, 0);
                });

            } else {
                window.setTimeout(function() {
                    self.remove_dialog(true, 1);
                }, 1000);
            }
        },

        remove_dialog: function(delay, value) {
            const dialog = $("#" + this.settings.id + "_dialog");

            if (delay) {
                dialog.delay(4000).dialog("destroy");
            } else {
                dialog.dialog("destroy");
            }

            dialog.remove();

            // In the case it's the manage page, force a submit
            if (this.settings.submitNGGFormOnFinished) {
                const form = $('.nggform');
                form.prepend("<input type=\"hidden\" name=\"ajax_callback\" value=\""+value+"\">");
                if (delay)
                    form.delay(4000).trigger('submit');
                else
                    form.trigger('submit')
            }
        }
    };
})(jQuery);

const nggProgressBarManager = {

    // The initial count of images to be processed; necessary for keeping track of our progress
    starting_count: 0,

    // The nggProgressBar element itself
    progress_bar: null,

    // The current status: in case the jQueryUI dialog is closed by the user we must stop polling
    polling_status: false,

    // The button that triggers this popup will be disabled while the operation is in progress
    button: null,

    messages: {
        no_images_found: 'No images were found to operate on',
        operation_finished: 'Operation has completed',
        header: 'Working...'
    },

    initialize: function(id, messages, primary_url, secondary_url) {
        const self = this;

        this.messages      = messages;
        this.primary_url   = primary_url;
        this.secondary_url = (secondary_url === undefined) ? primary_url : secondary_url;

        this.button = document.getElementById(id);
        this.button.addEventListener('click', function(event) {
            event.preventDefault();
            self.button.setAttribute('disabled', 'true');
            self.first_fetch();
        });
    },

    initialize_progress_bar: function() {
        if (this.progress_bar) {
            return this.progress_bar;
        } else {
            const self = this;

            // nggProgressBar is found in ngg.progressbar.js
            this.progress_bar = nggProgressBar.init({
                header: this.messages.header,
                wait: true,
                finishedNote: this.messages.operation_finished,
                submitNGGFormOnFinished: false,
                includeTinyMCEHackForIncrements: false
            });

            // Disable polling and destroy our reference
            this.progress_bar.on('dialogclose', function() {
                self.polling_status = false;
                self.progress_bar = null;
                self.button.removeAttribute('disabled');
            });
        }
    },

    first_fetch: function() {
        this.initialize_progress_bar();
        const self = this;

        fetch(this.primary_url, {
            method: 'post',
            cache: 'no-cache'
        }).then(function(result) { return result.json(); }).then(function(data) {
            /** @property {int} data.remaining */
            if (data.remaining === 0) {
                self.button.removeAttribute('disabled');
                nggProgressBar.finished(self.messages.no_images_found);
            } else {
                self.polling_status = true;
                self.starting_count = data.remaining;
                nggProgressBar.adjust_max_step(self.starting_count);
                self.continuing_fetch();
            }
        });
    },

    continuing_fetch: function() {
        const self = this;
        fetch(this.secondary_url, {
            method: 'post',
            cache: 'no-cache'
        }).then(function(result) { return result.json(); }).then(function(data) {
            /** @property {int} data.remaining */
            const percent = Math.round(((self.starting_count - data.remaining) / self.starting_count) * 100);

            if (percent < 100) {
                nggProgressBar.increase(percent);

                if (self.polling_status) {
                    self.continuing_fetch();
                }
            } else {
                nggProgressBar.increase(100);
                nggProgressBar.finished();

                self.button.removeAttribute('disabled');
            }
        });
    }
};