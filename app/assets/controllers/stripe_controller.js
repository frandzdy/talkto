import {Controller} from '@hotwired/stimulus';
import {loadStripe} from '@stripe/stripe-js';

/**
 * Gestion des paiements
 */

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    rentedPolicyCheckbox = $('#rented-policy')
    cautionCheckbox = $('#caution-policy')
    /**
     * Initialise la liste des photos avec un element si elle est vide
     */
    connect() {
        this.handleStripe();
    }

    async handleStripe() {

        const stripe = await loadStripe(stripePublicKey)
        const loader = 'auto'
        let linkAuthenticationElement = null
        const options = {
            clientSecret: this.element.dataset.clientSecret,
        }

        // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in step 3
        const elements = stripe.elements(options)
        if (isLogged) {
            linkAuthenticationElement = elements.create('linkAuthentication', {defaultValues: {email: email}});
        }
        // Create and mount the Payment Element
        const paymentElement = elements.create('payment', {
            layout: {
                type: 'accordion',
                defaultCollapsed: false,
                radios: false,
                spacedAccordionItems: true
            },
            business: {"name": "Reented"}
        });
        paymentElement.mount('#payment-element')
        if (isLogged) {
            linkAuthenticationElement.mount('#link-authentication-element')
        }
        const buttonSubmit = $('#stripe_submit')

        buttonSubmit.on('click', async (event) => {
            event.preventDefault();
            event.stopPropagation();
            buttonSubmit.attr('disabled');
            let policy = false
            if ($('#rented-policy').is(':checked')) {
                policy = true
            }
            let caution = false
            if ($('#caution-policy').is(':checked')) {
                caution = true
            }
            if (!caution || !policy) {
                if (!caution) {
                    this.cautionCheckbox.addClass('is-invalid');
                }
                if (!policy) {
                    this.rentedPolicyCheckbox.addClass('is-invalid');
                }
                toastr.error('Vous devez accepter les conditions de location et la caution.')
                return;
            } else {
                this.cautionCheckbox.removeClass('is-invalid')
                this.rentedPolicyCheckbox.removeClass('is-invalid')
            }

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
            buttonSubmit.removeAttr('disabled');
        });
    }
}
