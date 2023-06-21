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
        const token = this.element.dataset.token;
        const disabledDates = JSON.parse(this.element.dataset.disabledDates);
        const dateNow = new Date();
        const minDate = new Date(dateNow.getFullYear(), dateNow.getMonth(), dateNow.getDate() + 1);
        const maxDate = new Date(dateNow.getFullYear() + 1, dateNow.getMonth(), dateNow.getDate());
        console.log(minDate.toISOString().split('T')[0]);
        console.log(maxDate.toISOString().split('T')[0]);
        let options = {
            mode: "range",
            minDate: minDate.toISOString().split('T')[0],
            maxDate: maxDate.toISOString().split('T')[0],
            altInput: true,
            altInputClass: 'reservation-date',
            dateFormat: "Y-m-d",
            altFormat: "d/m/Y",
            locale: French,
            disable: [function (date) {
                let dateString = date.toISOString().split('T')[0]
                let inRange = false;
                for (let i = 0; i < disabledDates.length; i++) {
                    let range = disabledDates[i];
                    if (dateString >= range.from && dateString <= range.to) {
                        console.log(dateString + ' : IN : ' + range.from + ' - ' + range.to)
                        inRange = true;
                    } else if (dateString === range.from && range.to === undefined) {
                        inRange = true;
                        console.log(dateString + ' : IN : ' + range.from)
                    } else {
                        console.log(dateString + ' : NOT IN : ' + range.from + ' - ' + range.to)
                        inRange = false;
                    }
                }

                return inRange;
            }],
        };

        flatpickr(this.element, options)
    }
}
