(function ($) {
    if (typeof ajaxurl === "undefined") {
        ajaxurl = ju_feedback.ajaxurl;
    }

    var JUFeedbackApp = {
        listFreePlugins: function () {
            return [
                "wp-speed-of-light",
                "wp-meta-seo",
                "wp-latest-posts",
                "advanced-gutenberg",
                "imagerecycle-pdf-image-compression"
            ];
        },
        getElements: function (slug) {
            var reasonKey = [];

            $(".ju-feedback-dialog." + slug).find("input.reason-deactive:checked").each(function (index, ele) {
                reasonKey.push(this.name);
            });

            return {
                deactiveElement: $("#the-list").find('[data-slug="' + slug + '"] span.deactivate a'),
                modalElement: $(".ju-feedback-dialog." + slug),
                disableButton: $(".ju-feedback-dialog." + slug).find(".feedback-button a.disable-only"),
                sendFeedbackButton: $(".ju-feedback-dialog." + slug).find(".feedback-button a.send-message"),
                reasonKey: reasonKey,
                technicalElement: $(".ju-feedback-dialog." + slug).find(".technical-information")
            };
        },
        deactivateAction: function (slug) {
            location.href = this.getElements(slug).deactiveElement.attr('href');
        },
        modal: function () {
            var self = this;
            self.initModal = function (slug) {
                var modal;
                if (!modal) {
                    modal = self.getElements(slug).modalElement.dialog({
                        width: 500,
                        maxHeight: 400,
                        autoOpen: false,
                        closeOnEscape: true,
                        draggable: false,
                        resizable: false,
                        position: {my: "center", at: "center", of: window},
                        modal: true,
                        dialogClass: 'noTitle juDialogFeedback',
                        show: {
                            effect: "fade",
                            duration: 500
                        },
                        hide: {
                            effect: "fade",
                            duration: 300
                        },
                        open: function (event, ui) {
                            $('.ui-widget-overlay').bind('click', function () {
                                self.getElements(slug).modalElement.dialog('close');
                            });
                        },
                        create: function (event, ui) {
                            $(".choose-reason").click(function () {
                                if (self.getElements(slug).reasonKey.length > 0) {
                                    var id = $(this).attr('id');
                                    if ($('input#' + id + ':checked').length > 0) {
                                        $('textarea#comment-' + id).show().focus();
                                    } else {
                                        $('textarea#comment-' + id).hide();
                                    }

                                    positionDialog(self.getElements(slug).modalElement);
                                } else {
                                    $('textarea.feedback-text').hide();
                                }
                            });

                            $(".technical-information .more").toggle(function () {
                                $(this).html('arrow_drop_up');
                                $(".technical-information textarea[name='technical']").show();

                                positionDialog(self.getElements(slug).modalElement);
                            }, function () {
                                $(this).html('arrow_drop_down');
                                $(".technical-information textarea[name='technical']").hide();

                                positionDialog(self.getElements(slug).modalElement);
                            });

                            self.getElements(slug).disableButton.click(function (e) {
                                e.preventDefault();
                                $.ajax({
                                    url: ajaxurl,
                                    dataType: 'json',
                                    method: 'POST',
                                    data: {
                                        action: 'ju_disable_feedback_' + slug,
                                        ajax_nonce: ju_feedback.token
                                    },
                                    success: function () {
                                        self.deactivateAction(slug);
                                    }
                                })
                            });

                            self.getElements(slug).sendFeedbackButton.click(function (e) {
                                var technical_val = '';
                                var allow_send_technical = $('input[name="allow_send_technical"]:checked').length > 0;
                                if (allow_send_technical) {
                                    technical_val = self.getElements(slug).technicalElement.find('textarea[name="technical"]').attr('data-info');
                                }

                                var reasons = [];
                                if (self.getElements(slug).reasonKey.length > 0) {
                                    self.getElements(slug).reasonKey.forEach(function (ele) {
                                        reasons.push({
                                            'reason': ele,
                                            'comment': $('textarea#comment-' + ele).val()
                                        });
                                    });
                                }

                                e.preventDefault();
                                self.getElements(slug).modalElement.find('.ju-loading').show();
                                $.ajax({
                                    url: ajaxurl,
                                    dataType: 'json',
                                    method: 'POST',
                                    data: {
                                        action: 'ju_send_feedback_deactive_' + slug,
                                        reasons: JSON.stringify(reasons),
                                        feedbackTechnical: technical_val,
                                        ajax_nonce: ju_feedback.token
                                    },
                                    success: function (res) {
                                        setTimeout(function () {
                                            self.getElements(slug).modalElement.find('.feedback-button').hide();
                                            self.getElements(slug).modalElement.find('.content').hide();
                                            self.getElements(slug).modalElement.find('.feedback-result-notice').html(res.message).show();
                                            if (res.send_status) {
                                                self.getElements(slug).modalElement.find('.feedback-result-notice').addClass('send-success');
                                            } else {
                                                self.getElements(slug).modalElement.find('.feedback-result-notice').addClass('send-error');
                                            }
                                            setTimeout(function () {
                                                self.deactivateAction(slug);
                                            }, 1000);
                                        }, 1000);
                                    }
                                });
                            });
                        }
                    });
                }

                return modal;
            };

            self.showModal = function (slug) {
                self.initModal(slug).dialog("open");
                self.renderTechnicalData(slug);
            };
        },
        blindEvents: function blindEvents() {
            var self = this;
            var deactive_button = $("#the-list").find('tr.active span.deactivate');

            deactive_button.on('click', 'a', function (e) {
                var slug = $(e.target).parents('tr.active').attr("data-slug");

                if ($.inArray(slug, self.listFreePlugins()) !== -1 && self.getElements(slug).modalElement.length > 0) {
                    e.preventDefault();
                    self.showModal(slug);
                }
            });
        },
        renderTechnicalData: function renderTechnicalData(slug) {
            var self = this;

            self.getElements(slug).technicalElement.find('textarea[name="technical"]').val('Loading...');
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                method: 'POST',
                data: {
                    action: 'ju_feedback_get_technical_data_' + slug,
                    ajax_nonce: ju_feedback.token
                },
                success: function (res) {
                    if (res.get_status) {
                        pretty_str = JSON.stringify(res.data, undefined, 4);
                        self.getElements(slug).technicalElement.find('textarea[name="technical"]').attr('data-info', JSON.stringify(res.data));
                    } else {
                        pretty_str = 'We could not find technical information !';
                    }

                    self.getElements(slug).technicalElement.find('textarea[name="technical"]').val(pretty_str);
                }
            })
        },
        init: function () {
            this.modal();
            this.blindEvents();
        },
    };

    function positionDialog(element) {
        var screen_height = $(window).height();
        var dialog_height = element.outerHeight();

        if (dialog_height + 70 >= screen_height) {
            element.dialog("option", "maxHeight", screen_height);
        } else {
            element.dialog("option", "maxHeight", screen_height - 70);
        }
    }

    $(function () {
        JUFeedbackApp.init();
    });
})(jQuery);