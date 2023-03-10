(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

/***/ "./assets/controllers sync recursive ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \\.(j|t)sx?$":
/*!*****************************************************************************************************************!*\
  !*** ./assets/controllers/ sync ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \.(j|t)sx?$ ***!
  \*****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var map = {
	"./hello_controller.js": "./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js!./assets/controllers/hello_controller.js"
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = "./assets/controllers sync recursive ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \\.(j|t)sx?$";

/***/ }),

/***/ "./node_modules/@symfony/stimulus-bridge/dist/webpack/loader.js!./assets/controllers.json":
/*!************************************************************************************************!*\
  !*** ./node_modules/@symfony/stimulus-bridge/dist/webpack/loader.js!./assets/controllers.json ***!
  \************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
});

/***/ }),

/***/ "./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js!./assets/controllers/hello_controller.js":
/*!******************************************************************************************************************!*\
  !*** ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js!./assets/controllers/hello_controller.js ***!
  \******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _default)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_object_set_prototype_of_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.set-prototype-of.js */ "./node_modules/core-js/modules/es.object.set-prototype-of.js");
/* harmony import */ var core_js_modules_es_object_set_prototype_of_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_set_prototype_of_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_get_prototype_of_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.get-prototype-of.js */ "./node_modules/core-js/modules/es.object.get-prototype-of.js");
/* harmony import */ var core_js_modules_es_object_get_prototype_of_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_get_prototype_of_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_reflect_construct_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.reflect.construct.js */ "./node_modules/core-js/modules/es.reflect.construct.js");
/* harmony import */ var core_js_modules_es_reflect_construct_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_reflect_construct_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.define-property.js */ "./node_modules/core-js/modules/es.object.define-property.js");
/* harmony import */ var core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_object_create_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.object.create.js */ "./node_modules/core-js/modules/es.object.create.js");
/* harmony import */ var core_js_modules_es_object_create_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_create_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");
/* harmony import */ var core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");
/* harmony import */ var core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");
/* harmony import */ var core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _hotwired_stimulus__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @hotwired/stimulus */ "./node_modules/@hotwired/stimulus/dist/stimulus.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }














function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } Object.defineProperty(subClass, "prototype", { value: Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }), writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

var _default = /*#__PURE__*/function (_Controller) {
  _inherits(_default, _Controller);

  var _super = _createSuper(_default);

  function _default() {
    _classCallCheck(this, _default);

    return _super.apply(this, arguments);
  }

  _createClass(_default, [{
    key: "connect",
    value: function connect() {
      this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
    }
  }]);

  return _default;
}(_hotwired_stimulus__WEBPACK_IMPORTED_MODULE_12__.Controller);



/***/ }),

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var toastr_build_toastr_min_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! toastr/build/toastr.min.css */ "./node_modules/toastr/build/toastr.min.css");
/* harmony import */ var _styles_app_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles/app.scss */ "./assets/styles/app.scss");
/* harmony import */ var _bootstrap__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./bootstrap */ "./assets/bootstrap.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hotwired_turbo__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @hotwired/turbo */ "./node_modules/@hotwired/turbo/dist/turbo.es2017-esm.js");
/* harmony import */ var toastr__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! toastr */ "./node_modules/toastr/toastr.js");
/* harmony import */ var toastr__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(toastr__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var simple_parallax_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! simple-parallax-js */ "./node_modules/simple-parallax-js/dist/simpleParallax.min.js");
/* harmony import */ var simple_parallax_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(simple_parallax_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var bootstrap_dist_js_bootstrap_bundle_min__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! bootstrap/dist/js/bootstrap.bundle.min */ "./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js");
/* harmony import */ var bootstrap_dist_js_bootstrap_bundle_min__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(bootstrap_dist_js_bootstrap_bundle_min__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _vendor_friendsofsymfony_jsrouting_bundle_Resources_public_js_router_min_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js */ "./vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js");
/* harmony import */ var _vendor_friendsofsymfony_jsrouting_bundle_Resources_public_js_router_min_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_vendor_friendsofsymfony_jsrouting_bundle_Resources_public_js_router_min_js__WEBPACK_IMPORTED_MODULE_8__);
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)

 // start the Stimulus application

 // impor Jquery

 //window.$ = window.jQuery = $;

__webpack_require__.g.$ = __webpack_require__.g.jQuery = (jquery__WEBPACK_IMPORTED_MODULE_3___default());


window.toastr = (toastr__WEBPACK_IMPORTED_MODULE_5___default());
 // ajout des fichiers js



var routes = __webpack_require__(/*! ../public/js/fos_js_routes.json */ "./public/js/fos_js_routes.json");


_vendor_friendsofsymfony_jsrouting_bundle_Resources_public_js_router_min_js__WEBPACK_IMPORTED_MODULE_8___default().setRoutingData(routes);
window.Routing = (_vendor_friendsofsymfony_jsrouting_bundle_Resources_public_js_router_min_js__WEBPACK_IMPORTED_MODULE_8___default());

__webpack_require__(/*! ./js/pages/common */ "./assets/js/pages/common.js");

__webpack_require__(/*! ./js/services/cookie */ "./assets/js/services/cookie.js");

var velo = document.querySelector('#velo');
var etape = document.querySelector('#etape');
new (simple_parallax_js__WEBPACK_IMPORTED_MODULE_6___default())(velo, {
  scale: 1.5,
  overflow: true
});
new (simple_parallax_js__WEBPACK_IMPORTED_MODULE_6___default())(etape, {
  scale: 1.5,
  overflow: true
});

/***/ }),

/***/ "./assets/bootstrap.js":
/*!*****************************!*\
  !*** ./assets/bootstrap.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "app": () => (/* binding */ app)
/* harmony export */ });
/* harmony import */ var _symfony_stimulus_bridge__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @symfony/stimulus-bridge */ "./node_modules/@symfony/stimulus-bridge/dist/index.js");
 // Registers Stimulus controllers from controllers.json and in the controllers/ directory

var app = (0,_symfony_stimulus_bridge__WEBPACK_IMPORTED_MODULE_0__.startStimulusApp)(__webpack_require__("./assets/controllers sync recursive ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \\.(j|t)sx?$")); // register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

/***/ }),

/***/ "./assets/js/pages/common.js":
/*!***********************************!*\
  !*** ./assets/js/pages/common.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  jquery__WEBPACK_IMPORTED_MODULE_0___default()('.navbar-toggler').on('click', function () {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#navbarSupportedContent').toggle('slow');
  });
});

/***/ }),

/***/ "./assets/js/services/cookie.js":
/*!**************************************!*\
  !*** ./assets/js/services/cookie.js ***!
  \**************************************/
/***/ (() => {

tarteaucitron.init({
  "privacyUrl": "",

  /* Privacy policy url */
  "cookieName": "tacsportmeetup",

  /* Cookie name */
  "orientation": "bottom",

  /* Banner position (top - bottom) */
  "showAlertSmall": false,

  /* Show the small banner on bottom right */
  "cookieslist": true,

  /* Show the cookie list */
  "adblocker": true,

  /* Show a Warning if an adblocker is detected */
  "AcceptAllCta": true,

  /* Show the accept all button when highPrivacy on */
  "highPrivacy": true,

  /* Disable auto consent */
  "handleBrowserDNTRequest": false,

  /* If Do Not Track == 1, disallow all */
  "removeCredit": false,

  /* Remove credit link */
  "moreInfoLink": true,

  /* Show more info link */
  "useExternalCss": false,

  /* If false, the tarteaucitron.css file will be loaded */
  //"cookieDomain": ".my-multisite-domaine.fr", /* Shared cookie for multisite */
  "readmoreLink": "/cookiespolicy"
  /* Change the default readmore link */

});
tarteaucitron.user.gtagUa = googleGtm;

tarteaucitron.user.gtagMore = function () {
  dataLayer;
  /* add here your optionnal gtag() */
};

(tarteaucitron.job = tarteaucitron.job || []).push('gtag');
tarteaucitron.user.googletagmanagerId = googleTagUa;
(tarteaucitron.job = tarteaucitron.job || []).push('googletagmanager');

/***/ }),

/***/ "./vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js":
/*!************************************************************************************!*\
  !*** ./vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js ***!
  \************************************************************************************/
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;__webpack_require__(/*! core-js/modules/es.object.assign.js */ "./node_modules/core-js/modules/es.object.assign.js");

__webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");

__webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");

__webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");

__webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");

__webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");

__webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");

__webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");

__webpack_require__(/*! core-js/modules/es.object.define-property.js */ "./node_modules/core-js/modules/es.object.define-property.js");

__webpack_require__(/*! core-js/modules/es.object.freeze.js */ "./node_modules/core-js/modules/es.object.freeze.js");

__webpack_require__(/*! core-js/modules/es.regexp.constructor.js */ "./node_modules/core-js/modules/es.regexp.constructor.js");

__webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");

__webpack_require__(/*! core-js/modules/es.regexp.to-string.js */ "./node_modules/core-js/modules/es.regexp.to-string.js");

__webpack_require__(/*! core-js/modules/es.array.for-each.js */ "./node_modules/core-js/modules/es.array.for-each.js");

__webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");

__webpack_require__(/*! core-js/modules/es.array.is-array.js */ "./node_modules/core-js/modules/es.array.is-array.js");

__webpack_require__(/*! core-js/modules/es.array.index-of.js */ "./node_modules/core-js/modules/es.array.index-of.js");

__webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");

__webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");

__webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

!function (e, t) {
  var n = t();
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (n.Routing),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : 0;
}(this, function () {
  function e() {
    return e = Object.assign || function (e) {
      for (var t = 1; t < arguments.length; t++) {
        var n = arguments[t];

        for (var o in n) {
          Object.prototype.hasOwnProperty.call(n, o) && (e[o] = n[o]);
        }
      }

      return e;
    }, e.apply(this, arguments);
  }

  function t(e) {
    "@babel/helpers - typeof";

    return (t = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
      return typeof e;
    } : function (e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
    })(e);
  }

  function n(e, t) {
    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
  }

  function o(e, t) {
    for (var n = 0; n < t.length; n++) {
      var o = t[n];
      o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o);
    }
  }

  function r(e, t, n) {
    return t && o(e.prototype, t), n && o(e, n), e;
  }

  var i = {};
  Object.defineProperty(i, "__esModule", {
    value: !0
  }), i["default"] = i.Routing = i.Router = void 0;

  var s = function () {
    function o(e, t) {
      n(this, o), this.context_ = e || {
        base_url: "",
        prefix: "",
        host: "",
        port: "",
        scheme: "",
        locale: ""
      }, this.setRoutes(t || {});
    }

    return r(o, [{
      key: "setRoutingData",
      value: function value(e) {
        this.setBaseUrl(e.base_url), this.setRoutes(e.routes), "undefined" != typeof e.prefix && this.setPrefix(e.prefix), "undefined" != typeof e.port && this.setPort(e.port), "undefined" != typeof e.locale && this.setLocale(e.locale), this.setHost(e.host), "undefined" != typeof e.scheme && this.setScheme(e.scheme);
      }
    }, {
      key: "setRoutes",
      value: function value(e) {
        this.routes_ = Object.freeze(e);
      }
    }, {
      key: "getRoutes",
      value: function value() {
        return this.routes_;
      }
    }, {
      key: "setBaseUrl",
      value: function value(e) {
        this.context_.base_url = e;
      }
    }, {
      key: "getBaseUrl",
      value: function value() {
        return this.context_.base_url;
      }
    }, {
      key: "setPrefix",
      value: function value(e) {
        this.context_.prefix = e;
      }
    }, {
      key: "setScheme",
      value: function value(e) {
        this.context_.scheme = e;
      }
    }, {
      key: "getScheme",
      value: function value() {
        return this.context_.scheme;
      }
    }, {
      key: "setHost",
      value: function value(e) {
        this.context_.host = e;
      }
    }, {
      key: "getHost",
      value: function value() {
        return this.context_.host;
      }
    }, {
      key: "setPort",
      value: function value(e) {
        this.context_.port = e;
      }
    }, {
      key: "getPort",
      value: function value() {
        return this.context_.port;
      }
    }, {
      key: "setLocale",
      value: function value(e) {
        this.context_.locale = e;
      }
    }, {
      key: "getLocale",
      value: function value() {
        return this.context_.locale;
      }
    }, {
      key: "buildQueryParams",
      value: function value(e, n, o) {
        var r,
            i = this,
            s = new RegExp(/\[\]$/);
        if (n instanceof Array) n.forEach(function (n, r) {
          s.test(e) ? o(e, n) : i.buildQueryParams(e + "[" + ("object" === t(n) ? r : "") + "]", n, o);
        });else if ("object" === t(n)) for (r in n) {
          this.buildQueryParams(e + "[" + r + "]", n[r], o);
        } else o(e, n);
      }
    }, {
      key: "getRoute",
      value: function value(e) {
        var t = this.context_.prefix + e,
            n = e + "." + this.context_.locale,
            o = this.context_.prefix + e + "." + this.context_.locale,
            r = [t, n, o, e];

        for (var i in r) {
          if (r[i] in this.routes_) return this.routes_[r[i]];
        }

        throw new Error('The route "' + e + '" does not exist.');
      }
    }, {
      key: "generate",
      value: function value(t, n, r) {
        var i = this.getRoute(t),
            s = n || {},
            u = e({}, s),
            a = "",
            c = !0,
            f = "",
            l = "undefined" == typeof this.getPort() || null === this.getPort() ? "" : this.getPort();

        if (i.tokens.forEach(function (e) {
          if ("text" === e[0] && "string" == typeof e[1]) return a = o.encodePathComponent(e[1]) + a, void (c = !1);
          {
            if ("variable" !== e[0]) throw new Error('The token type "' + e[0] + '" is not supported.');
            var n = i.defaults && !Array.isArray(i.defaults) && "string" == typeof e[3] && e[3] in i.defaults;

            if (!1 === c || !n || "string" == typeof e[3] && e[3] in s && !Array.isArray(i.defaults) && s[e[3]] != i.defaults[e[3]]) {
              var r;
              if ("string" == typeof e[3] && e[3] in s) r = s[e[3]], delete u[e[3]];else {
                if ("string" != typeof e[3] || !n || Array.isArray(i.defaults)) {
                  if (c) return;
                  throw new Error('The route "' + t + '" requires the parameter "' + e[3] + '".');
                }

                r = i.defaults[e[3]];
              }
              var f = !0 === r || !1 === r || "" === r;

              if (!f || !c) {
                var l = o.encodePathComponent(r);
                "null" === l && null === r && (l = ""), a = e[1] + l + a;
              }

              c = !1;
            } else n && "string" == typeof e[3] && e[3] in u && delete u[e[3]];
          }
        }), "" === a && (a = "/"), i.hosttokens.forEach(function (e) {
          var t;
          return "text" === e[0] ? void (f = e[1] + f) : void ("variable" === e[0] && (e[3] in s ? (t = s[e[3]], delete u[e[3]]) : i.defaults && !Array.isArray(i.defaults) && e[3] in i.defaults && (t = i.defaults[e[3]]), f = e[1] + t + f));
        }), a = this.context_.base_url + a, i.requirements && "_scheme" in i.requirements && this.getScheme() != i.requirements._scheme) {
          var h = f || this.getHost();
          a = i.requirements._scheme + "://" + h + (h.indexOf(":" + l) > -1 || "" === l ? "" : ":" + l) + a;
        } else if ("undefined" != typeof i.schemes && "undefined" != typeof i.schemes[0] && this.getScheme() !== i.schemes[0]) {
          var p = f || this.getHost();
          a = i.schemes[0] + "://" + p + (p.indexOf(":" + l) > -1 || "" === l ? "" : ":" + l) + a;
        } else f && this.getHost() !== f + (f.indexOf(":" + l) > -1 || "" === l ? "" : ":" + l) ? a = this.getScheme() + "://" + f + (f.indexOf(":" + l) > -1 || "" === l ? "" : ":" + l) + a : r === !0 && (a = this.getScheme() + "://" + this.getHost() + (this.getHost().indexOf(":" + l) > -1 || "" === l ? "" : ":" + l) + a);

        if (Object.keys(u).length > 0) {
          var y = [],
              d = function d(e, t) {
            t = "function" == typeof t ? t() : t, t = null === t ? "" : t, y.push(o.encodeQueryComponent(e) + "=" + o.encodeQueryComponent(t));
          };

          for (var g in u) {
            u.hasOwnProperty(g) && this.buildQueryParams(g, u[g], d);
          }

          a = a + "?" + y.join("&");
        }

        return a;
      }
    }], [{
      key: "getInstance",
      value: function value() {
        return u;
      }
    }, {
      key: "setData",
      value: function value(e) {
        var t = o.getInstance();
        t.setRoutingData(e);
      }
    }, {
      key: "customEncodeURIComponent",
      value: function value(e) {
        return encodeURIComponent(e).replace(/%2F/g, "/").replace(/%40/g, "@").replace(/%3A/g, ":").replace(/%21/g, "!").replace(/%3B/g, ";").replace(/%2C/g, ",").replace(/%2A/g, "*").replace(/\(/g, "%28").replace(/\)/g, "%29").replace(/'/g, "%27");
      }
    }, {
      key: "encodePathComponent",
      value: function value(e) {
        return o.customEncodeURIComponent(e).replace(/%3D/g, "=").replace(/%2B/g, "+").replace(/%21/g, "!").replace(/%7C/g, "|");
      }
    }, {
      key: "encodeQueryComponent",
      value: function value(e) {
        return o.customEncodeURIComponent(e).replace(/%3F/g, "?");
      }
    }]), o;
  }();

  i.Router = s;
  var u = new s();
  i.Routing = u;
  var a = u;
  return i["default"] = a, {
    Router: s,
    Routing: u
  };
});

/***/ }),

/***/ "./assets/styles/app.scss":
/*!********************************!*\
  !*** ./assets/styles/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./public/js/fos_js_routes.json":
/*!**************************************!*\
  !*** ./public/js/fos_js_routes.json ***!
  \**************************************/
/***/ ((module) => {

"use strict";
module.exports = JSON.parse('{"base_url":"","routes":{"chat_save_message":{"tokens":[["variable","/","[^/]++","tokenId",true],["variable","/","[^/]++","discussion",true],["text","/chat/save_message"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"chat_load_message":{"tokens":[["text","/chat/load_message"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]}},"prefix":"","host":"localhost","port":"","scheme":"http","locale":""}');

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_jquery_dist_jquery_js","vendors-node_modules_core-js_internals_export_js-node_modules_core-js_internals_to-string_js","vendors-node_modules_hotwired_turbo_dist_turbo_es2017-esm_js-node_modules_symfony_stimulus-br-67fb4b"], () => (__webpack_exec__("./assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUFDLHNCQUFMO0FBQTRCRyxNQUFBQSxLQUFLLEVBQUMsZUFBUzdCLENBQVQsRUFBVztBQUFDLGVBQU9hLENBQUMsQ0FBQ3FFLHdCQUFGLENBQTJCbEYsQ0FBM0IsRUFBOEJpRixPQUE5QixDQUFzQyxNQUF0QyxFQUE2QyxHQUE3QyxDQUFQO0FBQXlEO0FBQXZHLEtBQTVqQixDQUE3dUgsQ0FBRCxFQUFxNUlwRSxDQUE1NUk7QUFBODVJLEdBQXRpSixFQUFOOztBQUEraUplLEVBQUFBLENBQUMsQ0FBQ3BCLE1BQUYsR0FBU3NCLENBQVQ7QUFBVyxNQUFJd0IsQ0FBQyxHQUFDLElBQUl4QixDQUFKLEVBQU47QUFBWUYsRUFBQUEsQ0FBQyxDQUFDcEQsT0FBRixHQUFVOEUsQ0FBVjtBQUFZLE1BQUlDLENBQUMsR0FBQ0QsQ0FBTjtBQUFRLFNBQU8xQixDQUFDLENBQUMsU0FBRCxDQUFELEdBQWEyQixDQUFiLEVBQWU7QUFBQy9DLElBQUFBLE1BQU0sRUFBQ3NCLENBQVI7QUFBVXRELElBQUFBLE9BQU8sRUFBQzhFO0FBQWxCLEdBQXRCO0FBQTJDLENBQXJzTCxDQUFEOzs7Ozs7Ozs7Ozs7QUNBQSIsInNvdXJjZXMiOlsid2VicGFjazovLy98L1xcLihqfHQpc3giLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2NvbnRyb2xsZXJzLmpzb24iLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2NvbnRyb2xsZXJzL2hlbGxvX2NvbnRyb2xsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2FwcC5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvYm9vdHN0cmFwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qcy9wYWdlcy9jb21tb24uanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL3NlcnZpY2VzL2Nvb2tpZS5qcyIsIndlYnBhY2s6Ly8vLi92ZW5kb3IvZnJpZW5kc29mc3ltZm9ueS9qc3JvdXRpbmctYnVuZGxlL1Jlc291cmNlcy9wdWJsaWMvanMvcm91dGVyLm1pbi5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvc3R5bGVzL2FwcC5zY3NzIl0sInNvdXJjZXNDb250ZW50IjpbInZhciBtYXAgPSB7XG5cdFwiLi9oZWxsb19jb250cm9sbGVyLmpzXCI6IFwiLi9ub2RlX21vZHVsZXMvQHN5bWZvbnkvc3RpbXVsdXMtYnJpZGdlL2xhenktY29udHJvbGxlci1sb2FkZXIuanMhLi9hc3NldHMvY29udHJvbGxlcnMvaGVsbG9fY29udHJvbGxlci5qc1wiXG59O1xuXG5cbmZ1bmN0aW9uIHdlYnBhY2tDb250ZXh0KHJlcSkge1xuXHR2YXIgaWQgPSB3ZWJwYWNrQ29udGV4dFJlc29sdmUocmVxKTtcblx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oaWQpO1xufVxuZnVuY3Rpb24gd2VicGFja0NvbnRleHRSZXNvbHZlKHJlcSkge1xuXHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKG1hcCwgcmVxKSkge1xuXHRcdHZhciBlID0gbmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIiArIHJlcSArIFwiJ1wiKTtcblx0XHRlLmNvZGUgPSAnTU9EVUxFX05PVF9GT1VORCc7XG5cdFx0dGhyb3cgZTtcblx0fVxuXHRyZXR1cm4gbWFwW3JlcV07XG59XG53ZWJwYWNrQ29udGV4dC5rZXlzID0gZnVuY3Rpb24gd2VicGFja0NvbnRleHRLZXlzKCkge1xuXHRyZXR1cm4gT2JqZWN0LmtleXMobWFwKTtcbn07XG53ZWJwYWNrQ29udGV4dC5yZXNvbHZlID0gd2VicGFja0NvbnRleHRSZXNvbHZlO1xubW9kdWxlLmV4cG9ydHMgPSB3ZWJwYWNrQ29udGV4dDtcbndlYnBhY2tDb250ZXh0LmlkID0gXCIuL2Fzc2V0cy9jb250cm9sbGVycyBzeW5jIHJlY3Vyc2l2ZSAuL25vZGVfbW9kdWxlcy9Ac3ltZm9ueS9zdGltdWx1cy1icmlkZ2UvbGF6eS1jb250cm9sbGVyLWxvYWRlci5qcyEgXFxcXC4oanx0KXN4PyRcIjsiLCJleHBvcnQgZGVmYXVsdCB7XG59OyIsImltcG9ydCB7IENvbnRyb2xsZXIgfSBmcm9tICdAaG90d2lyZWQvc3RpbXVsdXMnO1xuXG4vKlxuICogVGhpcyBpcyBhbiBleGFtcGxlIFN0aW11bHVzIGNvbnRyb2xsZXIhXG4gKlxuICogQW55IGVsZW1lbnQgd2l0aCBhIGRhdGEtY29udHJvbGxlcj1cImhlbGxvXCIgYXR0cmlidXRlIHdpbGwgY2F1c2VcbiAqIHRoaXMgY29udHJvbGxlciB0byBiZSBleGVjdXRlZC4gVGhlIG5hbWUgXCJoZWxsb1wiIGNvbWVzIGZyb20gdGhlIGZpbGVuYW1lOlxuICogaGVsbG9fY29udHJvbGxlci5qcyAtPiBcImhlbGxvXCJcbiAqXG4gKiBEZWxldGUgdGhpcyBmaWxlIG9yIGFkYXB0IGl0IGZvciB5b3VyIHVzZSFcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgZXh0ZW5kcyBDb250cm9sbGVyIHtcbiAgICBjb25uZWN0KCkge1xuICAgICAgICB0aGlzLmVsZW1lbnQudGV4dENvbnRlbnQgPSAnSGVsbG8gU3RpbXVsdXMhIEVkaXQgbWUgaW4gYXNzZXRzL2NvbnRyb2xsZXJzL2hlbGxvX2NvbnRyb2xsZXIuanMnO1xuICAgIH1cbn1cbiIsIi8qXG4gKiBXZWxjb21lIHRvIHlvdXIgYXBwJ3MgbWFpbiBKYXZhU2NyaXB0IGZpbGUhXG4gKlxuICogV2UgcmVjb21tZW5kIGluY2x1ZGluZyB0aGUgYnVpbHQgdmVyc2lvbiBvZiB0aGlzIEphdmFTY3JpcHQgZmlsZVxuICogKGFuZCBpdHMgQ1NTIGZpbGUpIGluIHlvdXIgYmFzZSBsYXlvdXQgKGJhc2UuaHRtbC50d2lnKS5cbiAqL1xuXG4vLyBhbnkgQ1NTIHlvdSBpbXBvcnQgd2lsbCBvdXRwdXQgaW50byBhIHNpbmdsZSBjc3MgZmlsZSAoYXBwLmNzcyBpbiB0aGlzIGNhc2UpXG5pbXBvcnQgJ3RvYXN0ci9idWlsZC90b2FzdHIubWluLmNzcyc7XG5pbXBvcnQgJy4vc3R5bGVzL2FwcC5zY3NzJztcblxuLy8gc3RhcnQgdGhlIFN0aW11bHVzIGFwcGxpY2F0aW9uXG5pbXBvcnQgJy4vYm9vdHN0cmFwJztcblxuLy8gaW1wb3IgSnF1ZXJ5XG5pbXBvcnQgJCBmcm9tICdqcXVlcnknO1xuLy93aW5kb3cuJCA9IHdpbmRvdy5qUXVlcnkgPSAkO1xuZ2xvYmFsLiQgPSBnbG9iYWwualF1ZXJ5ID0gJDtcblxuaW1wb3J0ICogYXMgVHVyYm8gZnJvbSAnQGhvdHdpcmVkL3R1cmJvJztcblxuaW1wb3J0IHRvYXN0ciBmcm9tICd0b2FzdHInO1xuXG53aW5kb3cudG9hc3RyID0gdG9hc3RyO1xuXG5pbXBvcnQgc2ltcGxlUGFyYWxsYXggZnJvbSAnc2ltcGxlLXBhcmFsbGF4LWpzJztcblxuLy8gYWpvdXQgZGVzIGZpY2hpZXJzIGpzXG5pbXBvcnQgJ2Jvb3RzdHJhcC9kaXN0L2pzL2Jvb3RzdHJhcC5idW5kbGUubWluJztcblxuY29uc3Qgcm91dGVzID0gcmVxdWlyZSgnLi4vcHVibGljL2pzL2Zvc19qc19yb3V0ZXMuanNvbicpO1xuaW1wb3J0IFJvdXRpbmcgZnJvbSAnLi4vdmVuZG9yL2ZyaWVuZHNvZnN5bWZvbnkvanNyb3V0aW5nLWJ1bmRsZS9SZXNvdXJjZXMvcHVibGljL2pzL3JvdXRlci5taW4uanMnO1xuUm91dGluZy5zZXRSb3V0aW5nRGF0YShyb3V0ZXMpO1xud2luZG93LlJvdXRpbmcgPSBSb3V0aW5nO1xuXG5yZXF1aXJlKCcuL2pzL3BhZ2VzL2NvbW1vbicpXG5yZXF1aXJlKCcuL2pzL3NlcnZpY2VzL2Nvb2tpZScpXG5cbnZhciB2ZWxvID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3ZlbG8nKTtcbnZhciBldGFwZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNldGFwZScpO1xubmV3IHNpbXBsZVBhcmFsbGF4KFxuXHR2ZWxvLFxuXHR7XG5cdFx0c2NhbGU6IDEuNSxcblx0XHRvdmVyZmxvdzogdHJ1ZVxuXHR9XG4pO1xubmV3IHNpbXBsZVBhcmFsbGF4KFxuXHRldGFwZSxcblx0e1xuXHRcdHNjYWxlOiAxLjUsXG5cdFx0b3ZlcmZsb3c6IHRydWVcblx0fVxuKTtcbiIsImltcG9ydCB7IHN0YXJ0U3RpbXVsdXNBcHAgfSBmcm9tICdAc3ltZm9ueS9zdGltdWx1cy1icmlkZ2UnO1xuXG4vLyBSZWdpc3RlcnMgU3RpbXVsdXMgY29udHJvbGxlcnMgZnJvbSBjb250cm9sbGVycy5qc29uIGFuZCBpbiB0aGUgY29udHJvbGxlcnMvIGRpcmVjdG9yeVxuZXhwb3J0IGNvbnN0IGFwcCA9IHN0YXJ0U3RpbXVsdXNBcHAocmVxdWlyZS5jb250ZXh0KFxuICAgICdAc3ltZm9ueS9zdGltdWx1cy1icmlkZ2UvbGF6eS1jb250cm9sbGVyLWxvYWRlciEuL2NvbnRyb2xsZXJzJyxcbiAgICB0cnVlLFxuICAgIC9cXC4oanx0KXN4PyQvXG4pKTtcblxuLy8gcmVnaXN0ZXIgYW55IGN1c3RvbSwgM3JkIHBhcnR5IGNvbnRyb2xsZXJzIGhlcmVcbi8vIGFwcC5yZWdpc3Rlcignc29tZV9jb250cm9sbGVyX25hbWUnLCBTb21lSW1wb3J0ZWRDb250cm9sbGVyKTtcbiIsImltcG9ydCAkIGZyb20gXCJqcXVlcnlcIjtcclxuXHJcbiQoZnVuY3Rpb24gKCl7XHJcblx0JCgnLm5hdmJhci10b2dnbGVyJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKXtcclxuXHRcdCQoJyNuYXZiYXJTdXBwb3J0ZWRDb250ZW50JykudG9nZ2xlKCdzbG93Jyk7XHJcblx0fSk7XHJcbn0pO1xyXG5cclxuIiwidGFydGVhdWNpdHJvbi5pbml0KHtcclxuXHRcInByaXZhY3lVcmxcIjogXCJcIiwgLyogUHJpdmFjeSBwb2xpY3kgdXJsICovXHJcblxyXG5cdFwiY29va2llTmFtZVwiOiBcInRhY3Nwb3J0bWVldHVwXCIsIC8qIENvb2tpZSBuYW1lICovXHJcblxyXG5cdFwib3JpZW50YXRpb25cIjogXCJib3R0b21cIiwgLyogQmFubmVyIHBvc2l0aW9uICh0b3AgLSBib3R0b20pICovXHJcblx0XCJzaG93QWxlcnRTbWFsbFwiOiBmYWxzZSwgLyogU2hvdyB0aGUgc21hbGwgYmFubmVyIG9uIGJvdHRvbSByaWdodCAqL1xyXG5cdFwiY29va2llc2xpc3RcIjogdHJ1ZSwgLyogU2hvdyB0aGUgY29va2llIGxpc3QgKi9cclxuXHJcblx0XCJhZGJsb2NrZXJcIjogdHJ1ZSwgLyogU2hvdyBhIFdhcm5pbmcgaWYgYW4gYWRibG9ja2VyIGlzIGRldGVjdGVkICovXHJcblx0XCJBY2NlcHRBbGxDdGFcIjogdHJ1ZSwgLyogU2hvdyB0aGUgYWNjZXB0IGFsbCBidXR0b24gd2hlbiBoaWdoUHJpdmFjeSBvbiAqL1xyXG5cdFwiaGlnaFByaXZhY3lcIjogdHJ1ZSwgLyogRGlzYWJsZSBhdXRvIGNvbnNlbnQgKi9cclxuXHRcImhhbmRsZUJyb3dzZXJETlRSZXF1ZXN0XCI6IGZhbHNlLCAvKiBJZiBEbyBOb3QgVHJhY2sgPT0gMSwgZGlzYWxsb3cgYWxsICovXHJcblxyXG5cdFwicmVtb3ZlQ3JlZGl0XCI6IGZhbHNlLCAvKiBSZW1vdmUgY3JlZGl0IGxpbmsgKi9cclxuXHRcIm1vcmVJbmZvTGlua1wiOiB0cnVlLCAvKiBTaG93IG1vcmUgaW5mbyBsaW5rICovXHJcblx0XCJ1c2VFeHRlcm5hbENzc1wiOiBmYWxzZSwgLyogSWYgZmFsc2UsIHRoZSB0YXJ0ZWF1Y2l0cm9uLmNzcyBmaWxlIHdpbGwgYmUgbG9hZGVkICovXHJcblxyXG5cdC8vXCJjb29raWVEb21haW5cIjogXCIubXktbXVsdGlzaXRlLWRvbWFpbmUuZnJcIiwgLyogU2hhcmVkIGNvb2tpZSBmb3IgbXVsdGlzaXRlICovXHJcblxyXG5cdFwicmVhZG1vcmVMaW5rXCI6IFwiL2Nvb2tpZXNwb2xpY3lcIiAvKiBDaGFuZ2UgdGhlIGRlZmF1bHQgcmVhZG1vcmUgbGluayAqL1xyXG59KTtcclxudGFydGVhdWNpdHJvbi51c2VyLmd0YWdVYSA9IGdvb2dsZUd0bTtcclxudGFydGVhdWNpdHJvbi51c2VyLmd0YWdNb3JlID0gZnVuY3Rpb24gKCkge1xyXG5cdGRhdGFMYXllci8qIGFkZCBoZXJlIHlvdXIgb3B0aW9ubmFsIGd0YWcoKSAqL1xyXG59O1xyXG4odGFydGVhdWNpdHJvbi5qb2IgPSB0YXJ0ZWF1Y2l0cm9uLmpvYiB8fCBbXSkucHVzaCgnZ3RhZycpO1xyXG50YXJ0ZWF1Y2l0cm9uLnVzZXIuZ29vZ2xldGFnbWFuYWdlcklkID0gZ29vZ2xlVGFnVWE7XHJcbih0YXJ0ZWF1Y2l0cm9uLmpvYiA9IHRhcnRlYXVjaXRyb24uam9iIHx8IFtdKS5wdXNoKCdnb29nbGV0YWdtYW5hZ2VyJyk7XHJcbiIsIiFmdW5jdGlvbihlLHQpe3ZhciBuPXQoKTtcImZ1bmN0aW9uXCI9PXR5cGVvZiBkZWZpbmUmJmRlZmluZS5hbWQ/ZGVmaW5lKFtdLG4uUm91dGluZyk6XCJvYmplY3RcIj09dHlwZW9mIG1vZHVsZSYmbW9kdWxlLmV4cG9ydHM/bW9kdWxlLmV4cG9ydHM9bi5Sb3V0aW5nOihlLlJvdXRpbmc9bi5Sb3V0aW5nLGUuZm9zPXtSb3V0ZXI6bi5Sb3V0ZXJ9KX0odGhpcyxmdW5jdGlvbigpe2Z1bmN0aW9uIGUoKXtyZXR1cm4gZT1PYmplY3QuYXNzaWdufHxmdW5jdGlvbihlKXtmb3IodmFyIHQ9MTt0PGFyZ3VtZW50cy5sZW5ndGg7dCsrKXt2YXIgbj1hcmd1bWVudHNbdF07Zm9yKHZhciBvIGluIG4pT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG4sbykmJihlW29dPW5bb10pfXJldHVybiBlfSxlLmFwcGx5KHRoaXMsYXJndW1lbnRzKX1mdW5jdGlvbiB0KGUpe1wiQGJhYmVsL2hlbHBlcnMgLSB0eXBlb2ZcIjtyZXR1cm4odD1cImZ1bmN0aW9uXCI9PXR5cGVvZiBTeW1ib2wmJlwic3ltYm9sXCI9PXR5cGVvZiBTeW1ib2wuaXRlcmF0b3I/ZnVuY3Rpb24oZSl7cmV0dXJuIHR5cGVvZiBlfTpmdW5jdGlvbihlKXtyZXR1cm4gZSYmXCJmdW5jdGlvblwiPT10eXBlb2YgU3ltYm9sJiZlLmNvbnN0cnVjdG9yPT09U3ltYm9sJiZlIT09U3ltYm9sLnByb3RvdHlwZT9cInN5bWJvbFwiOnR5cGVvZiBlfSkoZSl9ZnVuY3Rpb24gbihlLHQpe2lmKCEoZSBpbnN0YW5jZW9mIHQpKXRocm93IG5ldyBUeXBlRXJyb3IoXCJDYW5ub3QgY2FsbCBhIGNsYXNzIGFzIGEgZnVuY3Rpb25cIil9ZnVuY3Rpb24gbyhlLHQpe2Zvcih2YXIgbj0wO248dC5sZW5ndGg7bisrKXt2YXIgbz10W25dO28uZW51bWVyYWJsZT1vLmVudW1lcmFibGV8fCExLG8uY29uZmlndXJhYmxlPSEwLFwidmFsdWVcImluIG8mJihvLndyaXRhYmxlPSEwKSxPYmplY3QuZGVmaW5lUHJvcGVydHkoZSxvLmtleSxvKX19ZnVuY3Rpb24gcihlLHQsbil7cmV0dXJuIHQmJm8oZS5wcm90b3R5cGUsdCksbiYmbyhlLG4pLGV9dmFyIGk9e307T2JqZWN0LmRlZmluZVByb3BlcnR5KGksXCJfX2VzTW9kdWxlXCIse3ZhbHVlOiEwfSksaVtcImRlZmF1bHRcIl09aS5Sb3V0aW5nPWkuUm91dGVyPXZvaWQgMDt2YXIgcz1mdW5jdGlvbigpe2Z1bmN0aW9uIG8oZSx0KXtuKHRoaXMsbyksdGhpcy5jb250ZXh0Xz1lfHx7YmFzZV91cmw6XCJcIixwcmVmaXg6XCJcIixob3N0OlwiXCIscG9ydDpcIlwiLHNjaGVtZTpcIlwiLGxvY2FsZTpcIlwifSx0aGlzLnNldFJvdXRlcyh0fHx7fSl9cmV0dXJuIHIobyxbe2tleTpcInNldFJvdXRpbmdEYXRhXCIsdmFsdWU6ZnVuY3Rpb24oZSl7dGhpcy5zZXRCYXNlVXJsKGUuYmFzZV91cmwpLHRoaXMuc2V0Um91dGVzKGUucm91dGVzKSxcInVuZGVmaW5lZFwiIT10eXBlb2YgZS5wcmVmaXgmJnRoaXMuc2V0UHJlZml4KGUucHJlZml4KSxcInVuZGVmaW5lZFwiIT10eXBlb2YgZS5wb3J0JiZ0aGlzLnNldFBvcnQoZS5wb3J0KSxcInVuZGVmaW5lZFwiIT10eXBlb2YgZS5sb2NhbGUmJnRoaXMuc2V0TG9jYWxlKGUubG9jYWxlKSx0aGlzLnNldEhvc3QoZS5ob3N0KSxcInVuZGVmaW5lZFwiIT10eXBlb2YgZS5zY2hlbWUmJnRoaXMuc2V0U2NoZW1lKGUuc2NoZW1lKX19LHtrZXk6XCJzZXRSb3V0ZXNcIix2YWx1ZTpmdW5jdGlvbihlKXt0aGlzLnJvdXRlc189T2JqZWN0LmZyZWV6ZShlKX19LHtrZXk6XCJnZXRSb3V0ZXNcIix2YWx1ZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLnJvdXRlc199fSx7a2V5Olwic2V0QmFzZVVybFwiLHZhbHVlOmZ1bmN0aW9uKGUpe3RoaXMuY29udGV4dF8uYmFzZV91cmw9ZX19LHtrZXk6XCJnZXRCYXNlVXJsXCIsdmFsdWU6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb250ZXh0Xy5iYXNlX3VybH19LHtrZXk6XCJzZXRQcmVmaXhcIix2YWx1ZTpmdW5jdGlvbihlKXt0aGlzLmNvbnRleHRfLnByZWZpeD1lfX0se2tleTpcInNldFNjaGVtZVwiLHZhbHVlOmZ1bmN0aW9uKGUpe3RoaXMuY29udGV4dF8uc2NoZW1lPWV9fSx7a2V5OlwiZ2V0U2NoZW1lXCIsdmFsdWU6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb250ZXh0Xy5zY2hlbWV9fSx7a2V5Olwic2V0SG9zdFwiLHZhbHVlOmZ1bmN0aW9uKGUpe3RoaXMuY29udGV4dF8uaG9zdD1lfX0se2tleTpcImdldEhvc3RcIix2YWx1ZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmNvbnRleHRfLmhvc3R9fSx7a2V5Olwic2V0UG9ydFwiLHZhbHVlOmZ1bmN0aW9uKGUpe3RoaXMuY29udGV4dF8ucG9ydD1lfX0se2tleTpcImdldFBvcnRcIix2YWx1ZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmNvbnRleHRfLnBvcnR9fSx7a2V5Olwic2V0TG9jYWxlXCIsdmFsdWU6ZnVuY3Rpb24oZSl7dGhpcy5jb250ZXh0Xy5sb2NhbGU9ZX19LHtrZXk6XCJnZXRMb2NhbGVcIix2YWx1ZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmNvbnRleHRfLmxvY2FsZX19LHtrZXk6XCJidWlsZFF1ZXJ5UGFyYW1zXCIsdmFsdWU6ZnVuY3Rpb24oZSxuLG8pe3ZhciByLGk9dGhpcyxzPW5ldyBSZWdFeHAoL1xcW1xcXSQvKTtpZihuIGluc3RhbmNlb2YgQXJyYXkpbi5mb3JFYWNoKGZ1bmN0aW9uKG4scil7cy50ZXN0KGUpP28oZSxuKTppLmJ1aWxkUXVlcnlQYXJhbXMoZStcIltcIisoXCJvYmplY3RcIj09PXQobik/cjpcIlwiKStcIl1cIixuLG8pfSk7ZWxzZSBpZihcIm9iamVjdFwiPT09dChuKSlmb3IociBpbiBuKXRoaXMuYnVpbGRRdWVyeVBhcmFtcyhlK1wiW1wiK3IrXCJdXCIsbltyXSxvKTtlbHNlIG8oZSxuKX19LHtrZXk6XCJnZXRSb3V0ZVwiLHZhbHVlOmZ1bmN0aW9uKGUpe3ZhciB0PXRoaXMuY29udGV4dF8ucHJlZml4K2Usbj1lK1wiLlwiK3RoaXMuY29udGV4dF8ubG9jYWxlLG89dGhpcy5jb250ZXh0Xy5wcmVmaXgrZStcIi5cIit0aGlzLmNvbnRleHRfLmxvY2FsZSxyPVt0LG4sbyxlXTtmb3IodmFyIGkgaW4gcilpZihyW2ldaW4gdGhpcy5yb3V0ZXNfKXJldHVybiB0aGlzLnJvdXRlc19bcltpXV07dGhyb3cgbmV3IEVycm9yKCdUaGUgcm91dGUgXCInK2UrJ1wiIGRvZXMgbm90IGV4aXN0LicpfX0se2tleTpcImdlbmVyYXRlXCIsdmFsdWU6ZnVuY3Rpb24odCxuLHIpe3ZhciBpPXRoaXMuZ2V0Um91dGUodCkscz1ufHx7fSx1PWUoe30scyksYT1cIlwiLGM9ITAsZj1cIlwiLGw9XCJ1bmRlZmluZWRcIj09dHlwZW9mIHRoaXMuZ2V0UG9ydCgpfHxudWxsPT09dGhpcy5nZXRQb3J0KCk/XCJcIjp0aGlzLmdldFBvcnQoKTtpZihpLnRva2Vucy5mb3JFYWNoKGZ1bmN0aW9uKGUpe2lmKFwidGV4dFwiPT09ZVswXSYmXCJzdHJpbmdcIj09dHlwZW9mIGVbMV0pcmV0dXJuIGE9by5lbmNvZGVQYXRoQ29tcG9uZW50KGVbMV0pK2Esdm9pZChjPSExKTt7aWYoXCJ2YXJpYWJsZVwiIT09ZVswXSl0aHJvdyBuZXcgRXJyb3IoJ1RoZSB0b2tlbiB0eXBlIFwiJytlWzBdKydcIiBpcyBub3Qgc3VwcG9ydGVkLicpO3ZhciBuPWkuZGVmYXVsdHMmJiFBcnJheS5pc0FycmF5KGkuZGVmYXVsdHMpJiZcInN0cmluZ1wiPT10eXBlb2YgZVszXSYmZVszXWluIGkuZGVmYXVsdHM7aWYoITE9PT1jfHwhbnx8XCJzdHJpbmdcIj09dHlwZW9mIGVbM10mJmVbM11pbiBzJiYhQXJyYXkuaXNBcnJheShpLmRlZmF1bHRzKSYmc1tlWzNdXSE9aS5kZWZhdWx0c1tlWzNdXSl7dmFyIHI7aWYoXCJzdHJpbmdcIj09dHlwZW9mIGVbM10mJmVbM11pbiBzKXI9c1tlWzNdXSxkZWxldGUgdVtlWzNdXTtlbHNle2lmKFwic3RyaW5nXCIhPXR5cGVvZiBlWzNdfHwhbnx8QXJyYXkuaXNBcnJheShpLmRlZmF1bHRzKSl7aWYoYylyZXR1cm47dGhyb3cgbmV3IEVycm9yKCdUaGUgcm91dGUgXCInK3QrJ1wiIHJlcXVpcmVzIHRoZSBwYXJhbWV0ZXIgXCInK2VbM10rJ1wiLicpfXI9aS5kZWZhdWx0c1tlWzNdXX12YXIgZj0hMD09PXJ8fCExPT09cnx8XCJcIj09PXI7aWYoIWZ8fCFjKXt2YXIgbD1vLmVuY29kZVBhdGhDb21wb25lbnQocik7XCJudWxsXCI9PT1sJiZudWxsPT09ciYmKGw9XCJcIiksYT1lWzFdK2wrYX1jPSExfWVsc2UgbiYmXCJzdHJpbmdcIj09dHlwZW9mIGVbM10mJmVbM11pbiB1JiZkZWxldGUgdVtlWzNdXX19KSxcIlwiPT09YSYmKGE9XCIvXCIpLGkuaG9zdHRva2Vucy5mb3JFYWNoKGZ1bmN0aW9uKGUpe3ZhciB0O3JldHVyblwidGV4dFwiPT09ZVswXT92b2lkKGY9ZVsxXStmKTp2b2lkKFwidmFyaWFibGVcIj09PWVbMF0mJihlWzNdaW4gcz8odD1zW2VbM11dLGRlbGV0ZSB1W2VbM11dKTppLmRlZmF1bHRzJiYhQXJyYXkuaXNBcnJheShpLmRlZmF1bHRzKSYmZVszXWluIGkuZGVmYXVsdHMmJih0PWkuZGVmYXVsdHNbZVszXV0pLGY9ZVsxXSt0K2YpKX0pLGE9dGhpcy5jb250ZXh0Xy5iYXNlX3VybCthLGkucmVxdWlyZW1lbnRzJiZcIl9zY2hlbWVcImluIGkucmVxdWlyZW1lbnRzJiZ0aGlzLmdldFNjaGVtZSgpIT1pLnJlcXVpcmVtZW50cy5fc2NoZW1lKXt2YXIgaD1mfHx0aGlzLmdldEhvc3QoKTthPWkucmVxdWlyZW1lbnRzLl9zY2hlbWUrXCI6Ly9cIitoKyhoLmluZGV4T2YoXCI6XCIrbCk+LTF8fFwiXCI9PT1sP1wiXCI6XCI6XCIrbCkrYX1lbHNlIGlmKFwidW5kZWZpbmVkXCIhPXR5cGVvZiBpLnNjaGVtZXMmJlwidW5kZWZpbmVkXCIhPXR5cGVvZiBpLnNjaGVtZXNbMF0mJnRoaXMuZ2V0U2NoZW1lKCkhPT1pLnNjaGVtZXNbMF0pe3ZhciBwPWZ8fHRoaXMuZ2V0SG9zdCgpO2E9aS5zY2hlbWVzWzBdK1wiOi8vXCIrcCsocC5pbmRleE9mKFwiOlwiK2wpPi0xfHxcIlwiPT09bD9cIlwiOlwiOlwiK2wpK2F9ZWxzZSBmJiZ0aGlzLmdldEhvc3QoKSE9PWYrKGYuaW5kZXhPZihcIjpcIitsKT4tMXx8XCJcIj09PWw/XCJcIjpcIjpcIitsKT9hPXRoaXMuZ2V0U2NoZW1lKCkrXCI6Ly9cIitmKyhmLmluZGV4T2YoXCI6XCIrbCk+LTF8fFwiXCI9PT1sP1wiXCI6XCI6XCIrbCkrYTpyPT09ITAmJihhPXRoaXMuZ2V0U2NoZW1lKCkrXCI6Ly9cIit0aGlzLmdldEhvc3QoKSsodGhpcy5nZXRIb3N0KCkuaW5kZXhPZihcIjpcIitsKT4tMXx8XCJcIj09PWw/XCJcIjpcIjpcIitsKSthKTtpZihPYmplY3Qua2V5cyh1KS5sZW5ndGg+MCl7dmFyIHk9W10sZD1mdW5jdGlvbihlLHQpe3Q9XCJmdW5jdGlvblwiPT10eXBlb2YgdD90KCk6dCx0PW51bGw9PT10P1wiXCI6dCx5LnB1c2goby5lbmNvZGVRdWVyeUNvbXBvbmVudChlKStcIj1cIitvLmVuY29kZVF1ZXJ5Q29tcG9uZW50KHQpKX07Zm9yKHZhciBnIGluIHUpdS5oYXNPd25Qcm9wZXJ0eShnKSYmdGhpcy5idWlsZFF1ZXJ5UGFyYW1zKGcsdVtnXSxkKTthPWErXCI/XCIreS5qb2luKFwiJlwiKX1yZXR1cm4gYX19XSxbe2tleTpcImdldEluc3RhbmNlXCIsdmFsdWU6ZnVuY3Rpb24oKXtyZXR1cm4gdX19LHtrZXk6XCJzZXREYXRhXCIsdmFsdWU6ZnVuY3Rpb24oZSl7dmFyIHQ9by5nZXRJbnN0YW5jZSgpO3Quc2V0Um91dGluZ0RhdGEoZSl9fSx7a2V5OlwiY3VzdG9tRW5jb2RlVVJJQ29tcG9uZW50XCIsdmFsdWU6ZnVuY3Rpb24oZSl7cmV0dXJuIGVuY29kZVVSSUNvbXBvbmVudChlKS5yZXBsYWNlKC8lMkYvZyxcIi9cIikucmVwbGFjZSgvJTQwL2csXCJAXCIpLnJlcGxhY2UoLyUzQS9nLFwiOlwiKS5yZXBsYWNlKC8lMjEvZyxcIiFcIikucmVwbGFjZSgvJTNCL2csXCI7XCIpLnJlcGxhY2UoLyUyQy9nLFwiLFwiKS5yZXBsYWNlKC8lMkEvZyxcIipcIikucmVwbGFjZSgvXFwoL2csXCIlMjhcIikucmVwbGFjZSgvXFwpL2csXCIlMjlcIikucmVwbGFjZSgvJy9nLFwiJTI3XCIpfX0se2tleTpcImVuY29kZVBhdGhDb21wb25lbnRcIix2YWx1ZTpmdW5jdGlvbihlKXtyZXR1cm4gby5jdXN0b21FbmNvZGVVUklDb21wb25lbnQoZSkucmVwbGFjZSgvJTNEL2csXCI9XCIpLnJlcGxhY2UoLyUyQi9nLFwiK1wiKS5yZXBsYWNlKC8lMjEvZyxcIiFcIikucmVwbGFjZSgvJTdDL2csXCJ8XCIpfX0se2tleTpcImVuY29kZVF1ZXJ5Q29tcG9uZW50XCIsdmFsdWU6ZnVuY3Rpb24oZSl7cmV0dXJuIG8uY3VzdG9tRW5jb2RlVVJJQ29tcG9uZW50KGUpLnJlcGxhY2UoLyUzRi9nLFwiP1wiKX19XSksb30oKTtpLlJvdXRlcj1zO3ZhciB1PW5ldyBzO2kuUm91dGluZz11O3ZhciBhPXU7cmV0dXJuIGlbXCJkZWZhdWx0XCJdPWEse1JvdXRlcjpzLFJvdXRpbmc6dX19KTsiLCIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW5cbmV4cG9ydCB7fTsiXSwibmFtZXMiOlsiQ29udHJvbGxlciIsImVsZW1lbnQiLCJ0ZXh0Q29udGVudCIsIiQiLCJnbG9iYWwiLCJqUXVlcnkiLCJUdXJibyIsInRvYXN0ciIsIndpbmRvdyIsInNpbXBsZVBhcmFsbGF4Iiwicm91dGVzIiwicmVxdWlyZSIsIlJvdXRpbmciLCJzZXRSb3V0aW5nRGF0YSIsInZlbG8iLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3IiLCJldGFwZSIsInNjYWxlIiwib3ZlcmZsb3ciLCJzdGFydFN0aW11bHVzQXBwIiwiYXBwIiwiY29udGV4dCIsIm9uIiwidG9nZ2xlIiwidGFydGVhdWNpdHJvbiIsImluaXQiLCJ1c2VyIiwiZ3RhZ1VhIiwiZ29vZ2xlR3RtIiwiZ3RhZ01vcmUiLCJkYXRhTGF5ZXIiLCJqb2IiLCJwdXNoIiwiZ29vZ2xldGFnbWFuYWdlcklkIiwiZ29vZ2xlVGFnVWEiLCJlIiwidCIsIm4iLCJkZWZpbmUiLCJhbWQiLCJtb2R1bGUiLCJleHBvcnRzIiwiZm9zIiwiUm91dGVyIiwiT2JqZWN0IiwiYXNzaWduIiwiYXJndW1lbnRzIiwibGVuZ3RoIiwibyIsInByb3RvdHlwZSIsImhhc093blByb3BlcnR5IiwiY2FsbCIsImFwcGx5IiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJjb25zdHJ1Y3RvciIsIlR5cGVFcnJvciIsImVudW1lcmFibGUiLCJjb25maWd1cmFibGUiLCJ3cml0YWJsZSIsImRlZmluZVByb3BlcnR5Iiwia2V5IiwiciIsImkiLCJ2YWx1ZSIsInMiLCJjb250ZXh0XyIsImJhc2VfdXJsIiwicHJlZml4IiwiaG9zdCIsInBvcnQiLCJzY2hlbWUiLCJsb2NhbGUiLCJzZXRSb3V0ZXMiLCJzZXRCYXNlVXJsIiwic2V0UHJlZml4Iiwic2V0UG9ydCIsInNldExvY2FsZSIsInNldEhvc3QiLCJzZXRTY2hlbWUiLCJyb3V0ZXNfIiwiZnJlZXplIiwiUmVnRXhwIiwiQXJyYXkiLCJmb3JFYWNoIiwidGVzdCIsImJ1aWxkUXVlcnlQYXJhbXMiLCJFcnJvciIsImdldFJvdXRlIiwidSIsImEiLCJjIiwiZiIsImwiLCJnZXRQb3J0IiwidG9rZW5zIiwiZW5jb2RlUGF0aENvbXBvbmVudCIsImRlZmF1bHRzIiwiaXNBcnJheSIsImhvc3R0b2tlbnMiLCJyZXF1aXJlbWVudHMiLCJnZXRTY2hlbWUiLCJfc2NoZW1lIiwiaCIsImdldEhvc3QiLCJpbmRleE9mIiwic2NoZW1lcyIsInAiLCJrZXlzIiwieSIsImQiLCJlbmNvZGVRdWVyeUNvbXBvbmVudCIsImciLCJqb2luIiwiZ2V0SW5zdGFuY2UiLCJlbmNvZGVVUklDb21wb25lbnQiLCJyZXBsYWNlIiwiY3VzdG9tRW5jb2RlVVJJQ29tcG9uZW50Il0sInNvdXJjZVJvb3QiOiIifQ==