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
        if ($('.picture-collection').children().length == 0) {
            this.onFileAdd();
        }
        this.reindex()
    }

    /**
     * Ajout d'un champ photo
     */
    onFileAdd(event) {
        const collectionHolder = $('.picture-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 5) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
        this.handleBsCustomFileInput(collectionHolder.find('[type="file"]'));
        collectionHolder.find('.file-index').removeClass("d-none");
    }

    /**
     * A la suppression d'un widget fichier
     */
    onFileDelete(e) {
        const elt = e.currentTarget;
        const token = $(e.currentTarget).data('token')
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
                            this.reindex()
                        }
                    },
                    close: {
                        text: "Annuler"
                    }
                }
            });

        } else {
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
                            elt.parentElement.remove();
                            this.reindex()
                        }
                    },
                    close: {
                        text: "Annuler"
                    }
                }
            });
        }
    }

    handleBsCustomFileInput(container) {
        if ($(container)) {
            bsCustomFileInput.init();
            $(container).change(() => {
                let fieldVal = $(this).val();
                if (fieldVal != undefined || fieldVal != "") {
                    $(this).closest('.file-elt').next(".custom-file-label").text(fieldVal);
                }
            });
        }
    }

    reindex() {
        let indexFile = 0;
        $('.picture').each( () => {
            ++indexFile
            $(this).find('.file-index').html(indexFile);
            $(this).find('.file-index').removeClass("d-none");
        });
    }
}
