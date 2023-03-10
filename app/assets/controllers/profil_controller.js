import {Controller} from '@hotwired/stimulus';
import $ from "jquery";
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
	static targets = ['interestDistance', 'restDistance', 'interestAge', 'restAge']

	interestDistanceTargetConnected(event) {
		let slider = $(event);
		let output = $(this.restDistanceTarget);
		output.html(slider.val()); // Display the default slider value

		// Update the current slider value (each time you drag the slider handle)
		slider.on('input', (e) => {
			output.html($(e.currentTarget).val());
		})
	}

	interestAgeTargetConnected(event) {
		let slider = $(event);
		let output = $(this.restAgeTarget);
		output.html(slider.val()); // Display the default slider value

		// Update the current slider value (each time you drag the slider handle)
		slider.on('input', (e) => {
			output.html($(e.currentTarget).val());
		})
	}
}
