import { Controller } from '@hotwired/stimulus';
import $ from "jquery";

export default class extends Controller {
    static targets = ['top'];


    connect() {
        $(this.topTarget).on('click', function() {
            $('html,body').animate({ scrollTop: 0 }, 'slow');
        });
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 300) {
                $('.scroll-to-top').removeClass('d-none');
            } else {
                $('.scroll-to-top').addClass('d-none');
            }
        });
    }
}
