import {Controller} from '@hotwired/stimulus';

/**
 * Pagination par fleche des blocs sur les fiches
 */

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    /**
     * Page précédente
     */
    prev() {
        let page = parseInt(this.element.dataset.page, 10);
        if (page > 1) {
            this.element.dataset.page = (page - 1).toString();
        }
        this.checkButtons();
        this.call();
    }

    /**
     * Page suivante
     */
    next() {
        let page = parseInt(this.element.dataset.page, 10);
        const totalPage = parseInt(this.element.dataset.totalPage, 10);
        if (page < totalPage) {
            this.element.dataset.page = (page + 1).toString();
        }
        this.checkButtons();
        this.call();
    }

    /**
     * Desactivation / Activation des boutons de fleches
     */
    checkButtons() {
        this.element.querySelector('.btn-prev').disabled = (this.element.dataset.page === "1")
        this.element.querySelector('.btn-next').disabled = (this.element.dataset.page === this.element.dataset.totalPage)
    }

    /**
     * Remplace le contenu du bloc par le résultat de la pagination
     */
    call() {
        $.get(
            this.element.dataset.url.replace('_PAGE_', this.element.dataset.page),
            null,
            (data) => {
                this.element.closest('.tab-pane').querySelector('.table-responsive').innerHTML = data
            }
        )
    }
}
