/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'toastr/build/toastr.min.css';
import 'startbootstrap-new-age/dist/css/styles.css';
import './styles/app.scss';
import 'intro.js/introjs.css';
import 'jquery-confirm/dist/jquery-confirm.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

// start the Stimulus application
import './bootstrap';

// ajout des fichiers js
// import Jquery
import $ from 'jquery';
global.$ = global.jQuery = $;
import 'jquery-ui-bundle';

import * as Turbo from '@hotwired/turbo';

import toastr from 'toastr';

window.toastr = toastr;

import 'jquery-confirm';

require('bootstrap/dist/js/bootstrap.bundle.min');

const routes = require('../public/js/fos_js_routes.json');
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
window.Routing = Routing;

require('./js/pages/common')
require('./js/services/cookie')
import introJS from 'intro.js';

window.introJS = introJS;

$(function (){

})