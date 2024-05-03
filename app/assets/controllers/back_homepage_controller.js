import {Controller} from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        this.handleBsCustomInputFile('[type="file"]');
    }

    onWebsiteContentsAdd(event) {
        const collectionHolder = $('.websiteContent-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 3) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
        this.handleBsCustomInputFile(collectionHolder.find('[type="file"]'));
    }

    onSlidersAdd(event) {
        const collectionHolder = $('.sliders-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 3) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }

    onUnderSlidersAdd(event) {
        const collectionHolder = $('.under-sliders-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 3) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }

    onMidsAdd(event) {
        const collectionHolder = $('.mids-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 2) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }

    handleBsCustomInputFile(container) {
        if (container) {
            bsCustomFile.init();
        }
    };
}