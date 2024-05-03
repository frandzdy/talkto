import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
    }

    /**
     * Redirection sur un tr de la fiche client
     */
    confirmPictureDelete(event) {
        event.preventDefault()
        const elt = e.currentTarget;
        const token = $(event.currentTarget).data('token')
        const productToken = $(event.currentTarget).data('productToken')
        if (token && productToken) {
            $.confirm({
                title: 'Suppression d\'une photo',
                content: 'Souhaitez-vous supprimer cette photo de ce produit ?',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: 'Supprimer',
                        btnClass: 'btn-red',
                        action: () => {
                            $.post(Routing.generate('back_product_picture_delete', {'id': token, 'product': productToken}), null, function (data) {
                                $('#nav-' + token + '-tab').remove();
                                $('#nav-' + token).remove();
                                elt.parentElement.remove();
                                toastr.success('Image supprimé !')
                            });
                        }
                    },
                    close: {
                        text: "Annuler"
                    }
                }
            })
        }
    }

    onCollectionDelete(event) {
        const elt = e.currentTarget;
        $.confirm({
            title: 'Suppression d\'une élément',
            content: 'Souhaitez-vous supprimer cette élément ?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Supprimer',
                    btnClass: 'btn-red',
                    action: () => {
                        elt.parentElement.remove();
                        toastr.success('Element supprimé !')
                    }
                },
                close: {
                    text: "Annuler"
                }
            }
        })
    }
}
