import { Controller } from '@hotwired/stimulus';
import 'toastr'
import $ from "jquery";
import simpleParallax from 'simple-parallax-js';
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
    connect () {
        new simpleParallax(this.element, {
            delay: this.data.get('delay') || 0,
            orientation: this.data.get('orientation') || 'left',
            scale: this.data.get('scale') || 1.3,
            transition: this.data.get('transition') || 'cubic-bezier(0,0,0,1)',
            overflow: this.data.get('overflow') === 'true',
            customContainer: this.data.get('customContainer') || '',
            customWrapper: this.data.get('customWrapper') || ''
        })
    }
}
