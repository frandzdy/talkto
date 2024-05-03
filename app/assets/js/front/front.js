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
import './../../styles/front/front.scss';
import 'slick-carousel/slick/slick-theme.scss';
// start the Stimulus application
import '../../bootstrap';

// ajout des fichiers js
// import Jquery
import $ from 'jquery';

global.$ = global.jQuery = $;

import '@hotwired/turbo';

const {debounce} = require('lodash')
window.debounce = debounce

import 'jquery-confirm';
jconfirm.defaults = {theme:'bootstrap'}

import bsCustomFileInput from "bs-custom-file-input";
global.bsCustomFile = bsCustomFileInput;

const routes = require('../../../public/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
window.Routing = Routing;

require('./services/cookie')
require('../../js/front/pages/product')

require('./services/vendor/modernizr-3.5.0.min.js')
require('./services/metisMenu.min.js')
require('./services/jquery.fancybox.min.js')
require('./services/isotope.pkgd.min.js')
require('./services/owl.carousel.min.js')
require('./services/jquery-ui.min.js')

import 'bootstrap'
import 'slick-carousel'
import WOW from 'wow.js';

window.WOW = WOW

require('./services/imagesloaded.pkgd.min.js')
