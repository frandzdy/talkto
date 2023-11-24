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
    }

    /**
     * Redirection sur un tr de la fiche client
     */
    confirmPictureDelete(event) {
        event.preventDefault()
        const token = $(event.currentTarget).data('token')
        if (token) {
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
                            $.post(Routing.generate('front_product_picture_delete', {'token': token}), null, function (data) {
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
}
