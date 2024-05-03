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

import '@hotwired/turbo';

import toastr from 'toastr';

toastr.options.preventDuplicates = true;
toastr.options.positionClass = 'toast-bottom-left';

global.toastr = toastr

import 'jquery-confirm'
jconfirm.defaults = {theme:'bootstrap'}

import bsCustomFileInput from "bs-custom-file-input"
global.bsCustomFile = bsCustomFileInput

require('./../front/services/jquery-ui.min.js')

require('bootstrap')
require('jquery-slimscroll');
require('js-cookie');

require('./pages/common')

const routes = require('../../../public/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
window.Routing = Routing;

import '../../vendor/back/js/coloradmin'
import '../../vendor/back/js/coloradmin-theme-default'
