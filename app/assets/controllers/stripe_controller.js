import { Controller } from '@hotwired/stimulus';
import $ from "jquery";

import {loadStripe} from '@stripe/stripe-js';

/**
 * Gestion des paiements
 */
export default class extends Controller {
    /**
     * Initialise la liste des photos avec un element si elle est vide
     */
    connect() {
        this.handleStripe();
    }

    async handleStripe() {
        const stripe = await loadStripe('pk_test_51HD51PFRcMdepTxq4JsEEuihDjkOnftzJCpyxkZHpHX9aLvWuxviSpQCqH9GvszGqtfwMXcOS12jl11g3yyfSpWW0072zp4ZEd');

        const options = {
            clientSecret: this.element.dataset.clientSecret,
            // Fully customizable with appearance API.
            //appearance: {/*...*/}
        };

        // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in step 3
        const elements = stripe.elements(options);

        // Create and mount the Payment Element
        const paymentElement = elements.create('payment', {
            layout: {
                type: 'accordion',
                defaultCollapsed: false,
                radios: false,
                spacedAccordionItems: true
            },
            business: {"name":"Rented"}
        });
        paymentElement.mount('#payment-element');

        const buttonSubmit = $('#stripe_submit');

        buttonSubmit.on('click', async (event) => {
            buttonSubmit.attr('disabled');
            event.preventDefault();
            event.stopPropagation();
            let form = new FormData($('#user-reservation-form')[0]);
            await $.post({
                url: Routing.generate('front_stripe_payment_intent'),
                method: 'POST',
                data: form
            })
                .then((data) => {
                    console.log(data);
                })
            return;
            const {error} = await stripe.confirmPayment({
                //`Elements` instance that was used to create the Payment Element
                elements,
                confirmParams: {
                    return_url: Routing.generate('front_stripe_success', [], true),
                },
            });

            if (error) {
                // This point will only be reached if there is an immediate error when
                // confirming the payment. Show error to your customer (for example, payment
                // details incomplete)
                const messageContainer = $('#error-message');
                messageContainer.textContent = error.message;
            } else {
                // Your customer will be redirected to your `return_url`. For some payment
                // methods like iDEAL, your customer will be redirected to an intermediate
                // site first to authorize the payment, then redirected to the `return_url`.
            }
            buttonSubmit.attrRemove('disabled');
        });
    }

    checkForm() {

    }
}
