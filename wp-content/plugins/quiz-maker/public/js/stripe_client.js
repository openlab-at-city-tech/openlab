(function($){
    'use strict';

    $(document).ready(function(){
        var containerWrap = $(document).find('.ays-quiz-container .ays_stripe_wrap_div');
        containerWrap.each(function(){
            var stripeContainer = $(this);
            var quiz_id = $(this).parents('.ays-quiz-container').find('input[name="quiz_id"]').val();

            var quizMakerStripeOptions = JSON.parse( window.atob( window.quizMakerStripe[ quiz_id ] ) );
            // A reference to Stripe.js initialized with your real test publishable API key.
            var stripe = Stripe( quizMakerStripeOptions.apiKey );

            // Disable the button until we have Stripe set up on the page
            var wrap = $(document).find( quizMakerStripeOptions.wrapClass );

            wrap.show();

            wrap.find('.ays_quiz_stripe_submit').prop( 'disabled', true );

            fetch( quizMakerStripeOptions.fetchUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify( quizMakerStripeOptions )
            }).then(function (result) {
                return result.json();
            }).then(function (data) {
                var elements = stripe.elements();

                var style = {
                    base: {
                        color: "#32325d",
                        fontFamily: 'Arial, sans-serif',
                        fontSmoothing: "antialiased",
                        fontSize: "16px",
                        "::placeholder": {
                            color: "#32325d"
                        }
                    },
                    invalid: {
                        fontFamily: 'Arial, sans-serif',
                        color: "#fa755a",
                        iconColor: "#fa755a"
                    }
                };

                var card = elements.create("card", {
                    style: style,
                    hidePostalCode: true
                });
                // Stripe injects an iframe into the DOM
                card.mount( quizMakerStripeOptions.containerId );

                card.on("change", function (event) {
                    // Disable the Pay button if there are no card details in the Element
                    wrap.find(".ays_quiz_stripe_submit").prop( 'disabled', event.empty );
                    wrap.find(".ays_quiz_stripe_card_error").text( event.error ? event.error.message : "" );
                });

                var submitButton = wrap.find( ".ays_quiz_stripe_submit" );
                submitButton.on("click", function (event) {
                    event.preventDefault();
                    // Complete payment when the submit button is clicked
                    payWithCard(stripe, card, data.clientSecret, wrap, quizMakerStripeOptions);
                });
            });

        });

        // Calls stripe.confirmCardPayment
        // If the card requires authentication Stripe shows a pop-up modal to
        // prompt the user to enter authentication details without leaving your page.
        function payWithCard( stripe, card, clientSecret, wrap, quizMakerStripeOptions ) {
            loading(true, wrap);
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card
                }
            }).then(function (result) {
                if (result.error) {
                    // Show error to your customer
                    showError(result.error.message, wrap);
                } else {
                    // The payment succeeded!
                    orderComplete( result.paymentIntent, wrap, quizMakerStripeOptions );
                }
            });
        };

        /* ------- UI helpers ------- */

        // Shows a success message when the payment is complete
        function orderComplete( paymentIntent, wrap, quizMakerStripeOptions ) {
            loading(false, wrap);
            wrap.find(".ays_quiz_stripe_submit").prop( 'disabled', true );
            fetch( quizMakerStripeOptions.transactionCompleteUrl, {
                method: "post",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    data: paymentIntent,
                    quizId: quizMakerStripeOptions.quizId,
                    paymentType: quizMakerStripeOptions.paymentType
                }),
                credentials: "same-origin"
            }).then(response => response.json())
            .then(data => {
                Swal.fire({
                    title:"Your payment successfuly finished.",
                    type: "success",
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    width: "450px",
                }).then((result) => {
                    var stripeContainer = wrap.parents('.ays_stripe_wrap_div');
                    if( stripeContainer.attr('data-type') == 'postpay' ) {
                        var resId = stripeContainer.attr("data-result");
                        $.ajax({
                            url: window.quiz_maker_ajax_public.ajax_url,
                            method: "post",
                            dataType: "json",
                            crossDomain: true,
                            data: {
                                action: "ays_store_result_payed",
                                res_id: resId
                            },
                            success: function (response) {

                            }
                        });
                        var data = stripeContainer.parents(".ays-quiz-container").find("form").serializeFormJSON();
                        data.action = "ays_finish_quiz";
                        data.end_date = GetFullDateTime();
                        data.res_id = resId;
                        data["ays-paypal-paid"] = true;

                        $.ajax({
                            url: window.quiz_maker_ajax_public.ajax_url,
                            method: "post",
                            dataType: "json",
                            crossDomain: true,
                            data: data,
                            success: function (response) {
                            }
                        });

                        stripeContainer.hide(500);
                        stripeContainer.parents(".step").find(".ays_paypal_wrap_div").hide(500);
                        stripeContainer.parents(".step").find(".ays_quiz_results_page").html(window.aysResultsForQuiz);
                        stripeContainer.parents(".step").find(".ays_quiz_results_page").css("display", "block");
                        stripeContainer.parents(".step").find(".ays_quiz_results_page *").css("opacity", "1");
                        stripeContainer.parents("form").find(".ays_quiz_results").slideDown(1000);
                        setTimeout(function () {
                            stripeContainer.remove();
                        }, 1200);

                        var form = stripeContainer.parents("form");
                        var resCont = stripeContainer.parents(".step").find(".ays_quiz_results_page");
                        setTimeout(function () {
                            form.find("p.ays_score").addClass("tada");
                        }, 500);
                        var quizScore = form.find(".ays_score_percent").text();
                        quizScore = parseInt(quizScore);
                        var numberOfPercent = 0;
                        var percentAnimate = setInterval(function () {
                            form.find(".ays-progress-value").text(numberOfPercent + "%");
                            if (numberOfPercent == quizScore) {
                                clearInterval(percentAnimate);
                            }
                            numberOfPercent++;
                        }, 20);

                        var score = quizScore;
                        if (score > 0) {
                            form.find(".ays-progress-bar").css("padding-right", "7px");
                            var progressBarStyle = "first";
                            if (form.find(".ays-progress-bar").hasClass("second")) {
                                progressBarStyle = "second";
                            } else if (form.find(".ays-progress-bar").hasClass("third")) {
                                progressBarStyle = "third";
                            } else if (form.find(".ays-progress-bar").hasClass("fourth")) {
                                progressBarStyle = "fourth";
                            }
                            if (progressBarStyle == "first" || progressBarStyle == "second") {
                                form.find(".ays-progress-value").css("width", 0);
                                form.find(".ays-progress-value").css("transition", "width " + score * 25 + "ms linear");
                                setTimeout(function () {
                                    form.find(".ays-progress-value").css("width", score + "%");
                                }, 1);
                            }
                            form.find(".ays-progress-bar").css("transition", "width " + score * 25 + "ms linear");
                            setTimeout(function () {
                                form.find(".ays-progress-bar").css("width", score + "%");
                            }, 1);
                        }

                        form.find(".for_quiz_rate").rating({
                            onRate: function (res) {
                                $(this).rating("disable");
                                $(this).parent().find(".for_quiz_rate_reason").slideDown(500);
                                $(this).parents(".ays_quiz_rete").attr("data-rate_score", res);
                            }
                        });
                    }else{
                        location.reload();
                    }
                });
            }).catch(error => console.error(error));
        };

        // Show the customer the error from Stripe if their card fails to charge
        function showError(errorMsgText, wrap) {
            loading(false, wrap);
            var errorMsg = wrap.find(".ays_quiz_stripe_card_error");
            errorMsg.text( errorMsgText );
            setTimeout(function () {
                errorMsg.text( "" );
            }, 4000);
        };

        // Show a spinner on payment submission
        function loading(isLoading, wrap) {
            if (isLoading) {
                // Disable the button and show a spinner
                wrap.find(".ays_quiz_stripe_submit").prop( 'disabled', true );
                wrap.find(".ays_quiz_stripe_spinner").removeClass("ays_quiz_stripe_hidden");
                wrap.find(".ays_quiz_stripe_button_text").addClass("ays_quiz_stripe_hidden");
            } else {
                wrap.find(".ays_quiz_stripe_submit").prop( 'disabled', false );
                wrap.find(".ays_quiz_stripe_spinner").addClass("ays_quiz_stripe_hidden");
                wrap.find(".ays_quiz_stripe_button_text").removeClass("ays_quiz_stripe_hidden");
            }
        };
    });

})(jQuery);
