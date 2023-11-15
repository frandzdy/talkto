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
        this.confirmPictureDelete()
    }

    /**
     * Redirection sur un tr de la fiche client
     */
    confirmPictureDelete() {
        $("img.closing-picture").each(function (index) {
            $(this).on("click", function (e) {
                e.stopPropagation()
                e.preventDefault()
                window.location.href = $(this).closest('tr').data('href');
                $.confirm({
                    title: 'Suppression d\'une photo',
                    content: 'Souhaitez-vous supprimer cette photo ?',
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                        confirm: {
                            text: 'Supprimer',
                            btnClass: 'btn-red',
                            action: () => {
                                toastr.success('Suppression effectué.')
                                window.location.reload(true)
                            }
                        },
                        close: {
                            text: "Annuler"
                        }
                    }
                });
            });
        });
    }
}
