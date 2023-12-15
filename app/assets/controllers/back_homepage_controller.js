import {Controller} from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {

    }

    onSlidersAdd(event) {
        const collectionHolder = $('.sliders-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 3) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }

    onUnderSlidersAdd(event) {
        const collectionHolder = $('.under_sliders-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 3) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }

    onMidsAdd(event) {
        const collectionHolder = $('.mids-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 3) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }

    onDeleteProduct(event) {
        const elt = e.currentTarget;
        $.confirm({
            title: 'Suppression d\'un produit',
            content: 'Souhaitez-vous supprimer ce produit ?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Supprimer',
                    btnClass: 'btn-red',
                    action: () => {
                        elt.parentElement.remove()
                    }
                },
                close: {
                    text: "Annuler"
                }
            }
        })
    }
}