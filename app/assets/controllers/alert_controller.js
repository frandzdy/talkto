import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {

    connect(e) {
        if (this.element.dataset.type === 'success') {
            toastr.success(this.element.dataset.message);
        } else {
            toastr.error(this.element.dataset.message);
        }
    }
}
