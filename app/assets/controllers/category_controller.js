import { Controller } from '@hotwired/stimulus';
import $ from "jquery";
import bsCustomFileInput from "bs-custom-file-input";
/**
 * Gestion des justificatifs
 */
export default class extends Controller {
    /**
     * Initialise la liste des photos avec un element si elle est vide
     */
    connect() {

    }

    productFilter (event) {
        event.preventDefault()
        event.stopPropagation()

        const amount = $('#amount-value').val()
        const sortedBy = $('#sortedBy').val()
        const distance = $('#distance-value').val()
        const category = $('#category').val()

        let form = new FormData();
        form.append('amount', amount);
        form.append('sortedBy', sortedBy);
        form.append('distance', distance);
        $.ajax({
            type: "POST",
            url: Routing.generate('front_product_category', {'productCategory': category}),
            enctype: 'multipart/form-data',
            data: form,
            processData: false,
            contentType: false,
            cache: false,
            success: (response) => {
            }
        });
    }
}
