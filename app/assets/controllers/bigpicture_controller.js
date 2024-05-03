import {Controller} from '@hotwired/stimulus';
import BigPicture from "bigpicture";

/**
 * Gestion du plugin bigpicture
 */

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    /**
     * Initialise le plugin bigpicture
     */
    connect() {
    }

    loadBackGallery(e) {
        e.preventDefault()
        BigPicture({
            el: e.currentTarget,
            gallery: '#websiteContent',
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

    loadGallery(e) {
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

    loadGalleryCheckin(e) {
        e.preventDefault()
        BigPicture({
            el: e.currentTarget,
            gallery: '#nav-tabContent-checkin',
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
