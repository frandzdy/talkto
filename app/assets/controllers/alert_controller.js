import {Controller} from '@hotwired/stimulus';
import $ from "jquery";

export default class extends Controller {
	static targets = ['alertSuccess'];

	/**
	 * Callback button target alertSuccess
	 */
	alertSuccessTargetConnected() {
		setTimeout(() => {
			if ($(this.alertSuccessTarget).css('display') == "block") {
				$(this.alertSuccessTarget).hide('slideUp');
			}
		}, 5000);
	}
}
