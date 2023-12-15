import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
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
