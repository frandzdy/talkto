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
    connect() {
        this.redirectUser()
        this.resetFilter()
    }

    /**
     * Redirection sur un tr de la fiche client
     */
    redirectUser() {
        $("tr.customer-row").each(function (index) {
            $(this).on("click", function (e) {
                window.location.href = $(this).closest('tr').data('href');
            });
        });
    }

    /**
     * Reinitialise le formulaire de recherche
     */
    resetFilter() {
        $('.reinitFilter').on('click', function () {
            $('#customer_account_filter_term').val('');
            $.post(Routing('back_customer_index'));
        });
    }
}
