import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['contactForm']

    checkRecaptcha(event) {
        event.preventDefault();
        grecaptcha.ready(() => {
            grecaptcha.execute(googleRecaptchPkey, {action: 'submit'}).then((token) => {
                let $btn = $('#contact-submit-btn');
                $btn
                    .html(
                        '<img style="width: 50px;" src="' + $btn.data('loading-img') + '" alt="Envoi en cours"> Envoi en cours'
                    )
                    .attr('disabled', 'disabled');
                // Add your logic to submit to your backend server here.
                $.post(
                    Routing.generate('front_recaptcha_check', {'token': token})
                ).done(async (data) => {
                    if (data.response) {
                        const $form = $(this.contactFormTarget);
                        $form.submit();
                    } else {
                        $btn.html('Envoyer').removeAttr('disabled');
                        toastr.error("Vous avez été identifié comme robot; si ce n'est pas le cas, veuillez réessayer.");
                    }
                });
            });
        });
    }
}