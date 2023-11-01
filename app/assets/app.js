/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'toastr/build/toastr.css';
import 'jquery-confirm/css/jquery-confirm.css';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

// ajout des fichiers js
// import Jquery
import $ from 'jquery';

global.$ = global.jQuery = $;

import * as Turbo from '@hotwired/turbo';

import toastr from 'toastr';

toastr.options.preventDuplicates = true;
toastr.options.positionClass = 'toast-bottom-left';

window.toastr = toastr;

const {debounce} = require('lodash')
window.debounce = debounce

import 'jquery-confirm';

import bsCustomFileInput from "bs-custom-file-input";

bsCustomFileInput.init();
//require('bootstrap/dist/js/bootstrap.bundle.min');

const routes = require('../public/js/fos_js_routes.json');
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
window.Routing = Routing;

//require('./js/pages/common')
require('./js/services/cookie')
require('./js/front/pages/product')

require('./js/services/vendor/modernizr-3.5.0.min.js')
require('./js/services/vendor/jquery-3.6.0.min.js')
require('./js/services/vendor/waypoints.min.js')
require('./js/services/bootstrap.bundle.min.js')
require('./js/services/metisMenu.min.js')
require('./js/services/slick.min.js')
require('./js/services/jquery.fancybox.min.js')
require('./js/services/isotope.pkgd.min.js')
require('./js/services/owl.carousel.min.js')
require('./js/services/jquery-ui.min.js')
import '@stripe/stripe-js';

import WOW from 'wow.js';

window.WOW = WOW

require('./js/services/imagesloaded.pkgd.min.js')
require('./js/services/main.js')
