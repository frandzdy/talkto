import {Controller} from '@hotwired/stimulus';
import {getAllAddresses} from "../js/front/pages/user";

/* stimulusFetch: 'lazy' */
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
    addressAutocomplete(event) {
        $(this.userFormTarget).on('change', 'input#user_address', (event) => {
            const inputValue = $(event.currentTarget).val();
            const trimmedInput = inputValue && inputValue.trim();

            if (!trimmedInput.length) {
                this.removeAddress();
            }

            if (trimmedInput.length < 3) return;

            if (!this.searching) {
                this.searching = true;

                debounce(getAllAddresses(trimmedInput).then(
                    (response) => {
                        this.removeAddress();
                        this.createAddressList(response);
                    }
                ).then(() => this.searching = false), 1000);
            }
        }).on('click', 'li.address-item', this.handleClickOnAddress);
    }

    removeAddress() {
        $('.zone-address').find('.address').remove();
    }

    createAddressList(response) {
        const ul = document.createElement('ul');
        ul.classList.add('d-none', 'address');
        $('.zone-address').append(ul);
        const listContainer = $('.zone-address').find('.address');

        const defaultLi = document.createElement('li');
        defaultLi.innerHTML = "-- Sélectionner votre adresse --";
        listContainer.append(defaultLi);

        response.forEach(address => {
            const li = document.createElement('li');
            li.setAttribute('data-street', address.street);
            li.setAttribute('data-zipCode', address.postcode);
            li.setAttribute('data-city', address.city);
            li.setAttribute('class', 'address-item');
            li.innerHTML = `<b>${address.label}</b>`;
            listContainer.append(li);
        });
        listContainer.removeClass('d-none');
    }

    handleClickOnAddress(event) {
        event.preventDefault();
        const clickedData = $(event.currentTarget).data()
        $('#user_address').val(clickedData['street']);
        $('#user_zipCode').val(clickedData['zipcode']);
        $('#user_city').val(clickedData['city']);
        this.removeAddress();
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