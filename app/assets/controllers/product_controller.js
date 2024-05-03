import { Controller } from '@hotwired/stimulus';

/**
 * Gestion des produits
 */

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    /**
     * Initialise la liste des photos avec un element si elle est vide
     */
    connect() {

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
        this.reindex()
    }

    /**
     * A la suppression d'un widget fichier
     */
    onFileDelete(e) {
        const elt = e.currentTarget;
        const token = $(e.currentTarget).data('token')
        const productToken = $(e.currentTarget).data('productToken')
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
                            $.post(Routing.generate('front_product_picture_delete', {'token': token, 'productToken': productToken}), null, function (data) {
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
            bsCustomFile.init();
        }
    }

    reindex() {
        let indexFile = 0;
        $('.picture').each( () => {
            ++indexFile
            $(this).find('.file-index').html(indexFile);
            $(this).find('.file-index').text(indexFile);
            $(this).find('.file-index').innerText = indexFile;
            $(this).find('.file-index').innerHTML = indexFile;
            $(this).find('.file-index').removeClass("d-none");
        });
    }
}
