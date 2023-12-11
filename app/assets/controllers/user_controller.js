import {Controller} from '@hotwired/stimulus';
import {getAllAddresses} from "../js/front/pages/user";

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
    static targets = ['userForm']
    addresses = {}
    searching = false
    connect() {
        this.addressAutocomplete()
    }

    /**
     *
     * @param e
     */
    addressAutocomplete(e) {
        $(this.userFormTarget)
            .on('change', 'input#user_address', (event) => {
                if (!$(event.currentTarget).val().trim().length) {
                    $('.zone-address').find('.address').remove()
                }
                if ($(event.currentTarget).val() && $(event.currentTarget).val().trim().length < 3) {
                    return;
                }
                if (!this.searching) {
                    this.searching = true
                    debounce(getAllAddresses($(event.currentTarget).val()).then(
                        (response) => {
                            $('.zone-address').find('.address').remove()
                            let zoneAddress = $('.zone-address')

                            const ul = document.createElement('ul')
                            ul.classList.add('d-none')
                            ul.classList.add('address')
                            zoneAddress.append(ul)

                            let zoneAddressDiv = $('.zone-address').find('.address')
                            const li = document.createElement('li')
                            li.innerHTML = "-- Sélectionner votre adresse --"
                            zoneAddressDiv.append(li)

                            for(let index in response) {
                                const li = document.createElement('li')
                                li.setAttribute("data-street", response[index].street);
                                li.setAttribute("data-zipCode", response[index].postcode);
                                li.setAttribute("data-city", response[index].city);
                                li.setAttribute("class", "address-item");
                                li.innerHTML = `<b>` + response[index].label + `</b>`
                                zoneAddressDiv.append(li)
                                zoneAddressDiv.removeClass('d-none')
                            }
                        }
                    ).then(rest => this.searching = false), 1000)
                }
            })
            .on('click', 'li.address-item', (e) => {
                e.preventDefault()
                $('#user_address').val($(e.currentTarget).data('street'))
                $('#user_zipCode').val($(e.currentTarget).data('zipcode'))
                $('#user_city').val($(e.currentTarget).data('city'))
                $('.zone-address').find('.address').remove()
            })
    }

    /**
     *
     * @param event
     */
    checkRecaptcha(event) {
        event.preventDefault();
        grecaptcha.ready(() => {
            grecaptcha.execute(googleRecaptchPkey, {action: 'submit'}).then((token) => {
                let $btn = $('#user-submit-btn');
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
                        const $form = $(this.userFormTarget);
                        $form.submit();
                    } else {
                        $btn.html('Enregistrer').removeAttr('disabled');
                        toastr.error("Vous avez été identifié comme robot; si ce n'est pas le cas, veuillez réessayer.");
                    }
                });
            });
        });
    }
}