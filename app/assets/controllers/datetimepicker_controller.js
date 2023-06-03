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
            altInputClass: 'reservation-date',
            dateFormat: "Y-m-d",
            altFormat: "d/m/Y",
            locale: French,
            disable: this.getDisabledDate(this.element.dataset.token),
        };

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
