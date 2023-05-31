import {Controller} from '@hotwired/stimulus';
import flatpickr from "flatpickr";
import {French} from 'flatpickr/dist/l10n/fr'
import rangePlugin from 'flatpickr/dist/plugins/rangePlugin';

import $ from "jquery";

/**
 * Gestion des datepickers
 */
export default class extends Controller {
    /**
     * Initialise le datepicker
     */
    connect() {
        let options = {
            mode: "range",
            minDate: "today",
            altInput: true,
            altInputClass: '',
            dateFormat: "Y-m-d",
            altFormat: "d/m/Y",
            locale: French,
            disable: this.getDisabledDate(this.element.dataset.token),
        };
        console.log('"' + this.element.dataset.start + '"');
        console.log(this.element.dataset.end);
        let start = '"' + this.element.dataset.start + '"';
        let end ='"' + this.element.dataset.end + '"';
        console.log(start)
        console.log(end)
        Date.parse(start)
        console.log(Date.parse(end))
        if (this.element.dataset.defaultDate) {
            let options = {
                mode: "range",
                minDate: "today",
                altInput: true,
                defaultDate: ["02-06-2023", "22-06-2023"],
                altInputClass: '',
                dateFormat: "Y-m-d",
                altFormat: "d/m/Y",
                locale: French,
                disable: this.getDisabledDate(this.element.dataset.token),
            };
        }
        flatpickr(this.element, options)
    }

    getDisabledDate(token) {
        let result;
        $.get(
            Routing.generate('front_product_reservation_info', {'token': token}),
            null,
            function (data) {
                result = data;
            }
        )

        return [
            "2023-05-31",
            "2023-06-01",
            "2025-03-08",
            //new Date(2025, 4, 9),
            {
                from: "2023-06-05",
                to: "2023-06-10"
            }
        ]
    }
}
