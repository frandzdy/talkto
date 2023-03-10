
import {Controller} from '@hotwired/stimulus';
import * as Turbo from "@hotwired/turbo"
import InfiniteAjaxScroll from "@webcreate/infinite-ajax-scroll";

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
        let ias = new InfiniteAjaxScroll('.match-pagination', {
            item: '.match-user',
            next: '.page-link',
            pagination: '.navigation',
            spinner: {
                // element to show as spinner
                element: '.spinner-border',

                // delay in milliseconds
                // this is the minimal time the loader should be displayed. If loading takes longer, the spinner
                // will be shown for the duration of the loading. If the loading takes less then this duration,
                // say 300ms, then the spinner is still shown for 600ms.
                delay: 600,

                // this function is called when the button has to be shown
                show: function(element) {
                    element.style.opacity = '1'; // default behaviour
                },

                // this function is called when the button has to be hidden
                hide: function(element) {
                    element.style.opacity = '0'; // default behaviour
                }
            },
            logger: false
        });
    }

    showMatch(event) {
        Turbo.visit($(event.currentTarget).data('url'), {action: "replace"})
    }

    saveMatch(event) {
        $.post($(event.currentTarget).data('url'))
            .done((response) => {
                if (response.response === 1) {
                    Turbo.visit(Routing.generate('app_match_list'), {action: "replace"})
                } else if (response.response === 2) {
                    alert('Match')
                    setTimeout(() => {
                        Turbo.visit(Routing.generate('app_match_list'), {action: "replace"})
                    }, 5000);
                } else {
                    Turbo.visit(Routing.generate('app_match_list'), {action: "replace"})
                }
            });
    }
}
