import {Controller} from '@hotwired/stimulus';
import flatpickr from "flatpickr";
import {French} from 'flatpickr/dist/l10n/fr'

/**
 * Gestion des datepickers
 */

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    /**
     * Initialise le datepicker
     */
    connect() {
        const disabledDates = JSON.parse(this.element.dataset.disabledDates)
        const token = this.element.dataset.token
        const dateNow = new Date();
        const minDate = new Date(dateNow.getFullYear(), dateNow.getMonth(), dateNow.getDate() + 1);
        const maxDate = new Date(dateNow.getFullYear() + 1, dateNow.getMonth(), dateNow.getDate());
        let options = {
            mode: "range",
            minDate: this.toIsoDate(minDate),
            maxDate: this.toIsoDate(maxDate),
            altInput: true,
            altInputClass: 'reservation-date',
            dateFormat: "Y-m-d",
            altFormat: "d/m/Y",
            locale: French,
            disable: [function (date) {
                // let dateString = this.toIsoDate(date)
                // let inRange = false;
                // console.log('dateString', dateString)
                // for (let i = 0; i < disabledDates.length; i++) {
                //     let range = disabledDates[i];
                //     console.log('range', range)
                //
                //     if (dateString >= range.from && dateString <= range.to) {
                //         inRange = true;
                //     } else {
                //         inRange = false;
                //     }
                // }
                // console.log('inRange', inRange)
                // return inRange;
            }],
            onChange: function (selectedDates, dateStr, instance) {
                $("#product_reservation_quantity option").each(function () {
                    $(this).remove();
                });

                let dates = dateStr.split('au')

                $('.indispo').addClass('d-none')
                $('.indispo').removeClass('d-lg-flex')
                $('.add').removeClass('d-none')
                $('.add').addClass('d-lg-flex')

                $.get(Routing.generate('front_product_check_dates', {
                    'startDate': dates[0],
                    'token': token
                }), null, (data) => {
                    if (data['quantity']) {
                        $("#product_reservation_quantity option").each(function () {
                            $(this).remove();
                        })
                        let option = $('<option value selected="selected"></option>').text('-- Sélectionnez une quantité --');
                        $('#product_reservation_quantity').append(option)

                        for (let i = 1; i <= data['quantity']; i++) {
                            let option = $("<option value='" + i + "'></option>").text(i);   // Create with jQuery
                            $('#product_reservation_quantity').append(option)
                        }
                    } else {
                        $('.add').addClass('d-none')
                        $('.add').removeClass('d-lg-flex')
                        $('.indispo').removeClass('d-none')
                        $('.indispo').addClass('d-lg-flex')

                        $("#product_reservation_quantity option").each(function () {
                            $(this).remove();
                        })

                        let option = $('<option value selected="selected"></option>').text('-- Sélectionnez une quantité --');
                        $('#product_reservation_quantity').append(option)
                    }
                    // générer une option a metre dans le select
                });
            },
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                // Utilize dayElem.dateObj, which is the corresponding Date
                for (let i = 0; i < disabledDates.length; i++) {
                    let range = disabledDates[i];
                    if (this.toIsoDate(dayElem.dateObj) >= range.from && this.toIsoDate(dayElem.dateObj) <= range.to) {
                        dayElem.innerHTML += "<span class='event'></span>";
                    }
                }
            }
        };

        flatpickr(this.element, options)
    }

    toIsoDate(date) {
        const z = n => ('0' + n).slice(-2)
        return date.getFullYear() + '-' + z(date.getMonth() + 1) + '-' + z(date.getDate())
    }
}
