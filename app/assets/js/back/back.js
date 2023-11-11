import '../../vendor/back/css/app.min.css';
import 'toastr/build/toastr.css';
import 'jquery-confirm/css/jquery-confirm.css';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
import '../../styles/back/back.scss'
// start the Stimulus application
import '../../bootstrap';

// ajout des fichiers js
// import Jquery
import $ from 'jquery';

global.$ = global.jQuery = $;

import toastr from 'toastr';

toastr.options.preventDuplicates = true;
toastr.options.positionClass = 'toast-bottom-left';

window.toastr = toastr;

import 'jquery-confirm';

import bsCustomFileInput from "bs-custom-file-input";

bsCustomFileInput.init();

require('jquery-confirm');
require('popper.js');
require('bootstrap/dist/js/bootstrap');
require('jquery-slimscroll');
require('js-cookie');

require('./services/common')

const routes = require('../../../public/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
window.Routing = Routing;

import '../../vendor/back/js/coloradmin'
import '../../vendor/back/js/coloradmin-theme-default'
