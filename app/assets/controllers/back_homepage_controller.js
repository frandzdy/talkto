import {Controller} from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
    connect() {

    }

    onSlidersAdd(event) {
        const collectionHolder = $('.sliders-collection');
        const listIndex = collectionHolder.children().length + 1;
        if (listIndex <= 5) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
    }
}