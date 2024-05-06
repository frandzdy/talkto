import {Controller } from '@hotwired/stimulus';
import {getAllAddresses} from "../js/front/pages/user";
import {useDebounce} from 'stimulus-use';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['userForm', 'profileFile', 'imgFile']
    addresses = {}
    searching = false
    connect() {
        this.addressAutocomplete()
    }

    /**
     *
     */
    addressAutocomplete(event) {
        $(this.userFormTarget).on('keyup', 'input#user_address', async (event) => {
            const inputValue = $(event.currentTarget).val();
            const trimmedInput = inputValue && inputValue.trim();

            if (!trimmedInput.length) {
                $('.zone-address').find('.address').remove();
            }

            if (trimmedInput.length < 3) return;

            if (!this.searching) {
                this.searching = true;

                debounce(await getAllAddresses(trimmedInput).then(
                    (response) => {
                        $('.zone-address').find('.address').remove();
                        this.createAddressList(response);
                    }
                ).then(() => this.searching = false), 2000);
            }
        })
        .on('keyup', 'input#user_payment_address', async (event) => {
            const inputValue = $(event.currentTarget).val();
            const trimmedInput = inputValue && inputValue.trim();

            if (!trimmedInput.length) {
                $('.zone-address').find('.address').remove();
            }

            if (trimmedInput.length < 3) return;

            if (!this.searching) {
                this.searching = true;

                debounce(await getAllAddresses(trimmedInput).then(
                    (response) => {
                        $('.zone-address').find('.address').remove();
                        this.createPaymentAddressList(response);
                    }
                ), 2000);
                this.searching = false
            }
        })
            .on('click', 'li.address-item', this.handleClickOnAddress)// user_payment_address
            .on('click', 'li.payment-address-item', this.handleClickOnPaymentAddress);// user_payment_address
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

    createPaymentAddressList(response) {
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
            li.setAttribute('class', 'payment-address-item');
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
        $('.zone-address').find('.address').remove();
    }

    handleClickOnPaymentAddress(event) {
        event.preventDefault();
        const clickedData = $(event.currentTarget).data()
        $('#user_payment_address').val(clickedData['street']);
        $('#user_payment_zipCode').val(clickedData['zipcode']);
        $('#user_payment_city').val(clickedData['city']);
        $('.zone-address').find('.address').remove();
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

    /**
     * Permet de custom les input file bootstrap
     */

    imagesPreview(event) {
        console.log('files : ', this.profileFileTarget.files)
        if (this.profileFileTarget.files) {
            const filesAmount = this.profileFileTarget.files.length;
            const filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
            for (let i = 0; i < filesAmount; i++) {
                const reader = new FileReader();

                reader.onload = (event) => {
                    const image = new Image();

                    image.onload = () => {
                        const canvas = document.createElement("canvas");
                        const context = canvas.getContext("2d");
                        const max_size = 150;
                        let width = image.width;
                        let height = image.height;
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                        canvas.width = width;
                        canvas.height = height;
                        context.drawImage(image,
                            0,
                            0,
                            image.width,
                            image.height,
                            0,
                            0,
                            canvas.width,
                            canvas.height
                        );
                        this.imgFileTarget.src = canvas.toDataURL()
                    }
                    image.src = event.target.result;
                    this.imgFileTarget = image
                }
                if (!filterType.test(this.profileFileTarget.files[i].type)) {
                    toastr.error("Format fichier (PNG/JPEG/JPG).")
                    return;
                }
                reader.readAsDataURL(this.profileFileTarget.files[i])
            }
        }
    }
}