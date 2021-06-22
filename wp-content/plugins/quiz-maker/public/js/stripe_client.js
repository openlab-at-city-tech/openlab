(function($){
    'use strict';

    $(document).ready(function(){

        // A reference to Stripe.js initialized with your real test publishable API key.
        var stripe = Stripe( quizMakerStripe.apiKey );

        // Disable the button until we have Stripe set up on the page
        var wrap = $(document).find( quizMakerStripe.wrapClass );
        wrap.show();

        wrap.find('.ays_quiz_stripe_submit').prop( 'disabled', true );

        fetch( quizMakerStripe.fetchUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify( quizMakerStripe )
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
            card.mount( quizMakerStripe.containerId );

            card.on("change", function (event) {
                // Disable the Pay button if there are no card details in the Element
                wrap.find(".ays_quiz_stripe_submit").prop( 'disabled', event.empty );
                wrap.find(".ays_quiz_stripe_card_error").text( event.error ? event.error.message : "" );
            });

            var submitButton = wrap.find( ".ays_quiz_stripe_submit" );
            submitButton.on("click", function (event) {
                event.preventDefault();
                // Complete payment when the submit button is clicked
                payWithCard(stripe, card, data.clientSecret);
            });
        });

        // Calls stripe.confirmCardPayment
        // If the card requires authentication Stripe shows a pop-up modal to
        // prompt the user to enter authentication details without leaving your page.
        function payWithCard( stripe, card, clientSecret ) {
            loading(true);
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card
                }
            }).then(function (result) {
                if (result.error) {
                    // Show error to your customer
                    showError(result.error.message);
                } else {
                    // The payment succeeded!
                    orderComplete( result.paymentIntent );
                }
            });
        };

        /* ------- UI helpers ------- */

        // Shows a success message when the payment is complete
        function orderComplete( paymentIntent ) {
            loading(false);
            wrap.find(".ays_quiz_stripe_submit").prop( 'disabled', true );
            fetch( quizMakerStripe.transactionCompleteUrl, {
                method: "post",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    data: paymentIntent,
                    quizId: quizMakerStripe.quizId
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
                    location.reload();
                });
            }).catch(error => console.error(error));
        };

        // Show the customer the error from Stripe if their card fails to charge
        function showError(errorMsgText) {
            loading(false);
            var errorMsg = wrap.find(".ays_quiz_stripe_card_error");
            errorMsg.text( errorMsgText );
            setTimeout(function () {
                errorMsg.text( "" );
            }, 4000);
        };

        // Show a spinner on payment submission
        function loading(isLoading) {
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
