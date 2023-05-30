import { Controller } from '@hotwired/stimulus';
import flatpickr from "flatpickr";
import { French } from 'flatpickr/dist/l10n/fr'

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
            dateFormat: "Y-m-d",
            altFormat: "d/m/Y",
            locale: French,
            disable: this.getDisabledDate(this.element.dataset.token)
        };

        flatpickr(this.element, options)
    }

    getDisabledDate(token) {
        console.log('OK stim' + token)
        // $.get(
        //     Routing.generate('front_product_info', {'token': token}),
        //     null,
        //     function(data) {
        //         console.log('OK ')
        //     }
        // )
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
