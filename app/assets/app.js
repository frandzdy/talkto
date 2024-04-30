/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import 'toastr/build/toastr.css';
import 'jquery-confirm/css/jquery-confirm.css';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
// start the Stimulus application
import './bootstrap';

import 'jquery-confirm';
jconfirm.defaults = {theme:'material'}

import toastr from 'toastr';

toastr.options.preventDuplicates = true;
toastr.options.positionClass = 'toast-bottom-left';

window.toastr = toastr;
