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
        if ($('.manager-collection').children().length == 0) {
            this.onFileAdd();
        }
    }

    /**
     * Ajout d'un champ photo
     */
    onFileAdd(event) {
        const collectionHolder = $('.manager-collection');
        const listIndex = collectionHolder.find('input').length + 1;
        if (listIndex <= 5) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
        this.handleBsCustomFileInput(collectionHolder.find('input[type=file]'));
    }

    /**
     * A la suppression d'un widget fichier
     */
    onFileDelete(e) {
        const elt = e.target;
        $.confirm({
            title: 'Suppression d\'une photo',
            content: 'Souhaitez-vous supprimer ce photo ?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Supprimer',
                    btnClass: 'btn-red',
                    action: () => {
                         elt.closest('.file-elt').parentElement.remove();
                    }
                },
                close: {
                    text: "Annuler"
                }
            }
        });
    }

    handleBsCustomFileInput(container) {
        if ($(container)) {
            bsCustomFileInput.init();
            $(container).change(function () {
                var fieldVal = $(this).val();
                if (fieldVal != undefined || fieldVal != "") {
                    $(this).next(".custom-file-label").text(fieldVal);
                }
            });
        }
    }
}
