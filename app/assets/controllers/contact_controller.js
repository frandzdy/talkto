import {Controller} from '@hotwired/stimulus';
import 'toastr'
/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
	static targets = ['contactForm']

	checkRecaptcha(event) {
		event.preventDefault();
		grecaptcha.ready(() => {
			grecaptcha.execute(googleRecaptchPkey, {action: 'submit'}).then((token) => {
				let $btn = $('#contact-submit-btn');
				$btn
					.html(
						'<img src="' + $btn.data('loading-img') + '" alt="Envoi en cours"> Envoi en cours'
					)
					.attr('disabled', 'disabled');
				// Add your logic to submit to your backend server here.
				$.post(
					Routing.generate('app_contact_recaptcha_check', {'token': token})
				).done(async (data) => {
					if (data.response) {
						const $form = $(this.contactFormTarget);
						$form.submit();
					} else {
						$btn.html('Envoyer la demande').removeAttr('disabled');
						toastr.error("Vous avez été identifié comme robot; si ce n'est pas le cas, veuillez réessayer.");
					}
				});
			});
		});
	}
}
