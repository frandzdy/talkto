import {Controller} from '@hotwired/stimulus';

/**
 * Gestion des checkins
 */

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    /**
     * Initialise la liste des photos avec un element si elle est vide
     */
    connect() {
        if ($('.checkin-collection').children().length == 0) {
            this.onFileAdd();
        }

        this.reindex()
    }

    /**
     * Ajout d'un champ photo
     */
    onFileAdd(event) {
        const collectionHolder = $('.checkin-collection');
        const listIndex = collectionHolder.find('input').length + 1;
        if (listIndex <= 5) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
        this.handleBsCustomFileInput(collectionHolder.find('[type="file"]'))
        collectionHolder.find('.file-index').removeClass("d-none");
        this.reindex()
    }

    handleBsCustomFileInput(container) {
        if ($(container)) {
            bsCustomFile.init();
        }
    }
    /**
     * A la suppression d'un widget fichier
     */
    onFileDelete(e) {
        const elt = e.currentTarget;
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

    reindex() {
        let indexFile = 0;
        $('.file-elt').each(function () {
            ++indexFile
            $(this).find('.file-index').html(indexFile);
            $(this).find('.file-index').removeClass("d-none");
        });
    }
}
