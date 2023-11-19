import {Controller} from '@hotwired/stimulus';
import BigPicture from "bigpicture";

/**
 * Gestion du plugin bigpicture
 */
export default class extends Controller {
    /**
     * Initialise le plugin bigpicture
     */
    connect() {
    }

    loadGallery(e) {
        console.log('toto')
        e.preventDefault()
        BigPicture({
            el: e.currentTarget,
            gallery: '#product-detailsContent',
            animationStart: function () {
                document.documentElement.style.overflowY = 'hidden'
                document.body.style.overflowY = 'scroll'
            },
            onClose: function () {
                document.documentElement.style.overflowY = 'auto'
                document.body.style.overflowY = 'auto'
            },
        })
    }

    loadGalleryModal(e) {
        console.log('toto Modal')
        e.preventDefault()
        BigPicture({
            el: e.currentTarget,
            gallery: '#nav-tabContent',
            animationStart: function () {
                document.documentElement.style.overflowY = 'hidden'
                document.body.style.overflowY = 'scroll'
            },
            onClose: function () {
                document.documentElement.style.overflowY = 'auto'
                document.body.style.overflowY = 'auto'
            },
        })
    }
}
