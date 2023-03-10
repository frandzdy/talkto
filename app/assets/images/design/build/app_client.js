(self["webpackChunk"] = self["webpackChunk"] || []).push([["app_client"],{

/***/ "./assets/js/pages/04-client.js":
/*!**************************************!*\
  !*** ./assets/js/pages/04-client.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
__webpack_require__(/*! core-js/modules/es.parse-int.js */ "./node_modules/core-js/modules/es.parse-int.js");

var limit = 10;
var step = 0;
var receive = 0;
var load = false;
var loadFriend = true;
var loadGroup = true;
var selectedId = 0; //

$(function () {
  var socket = io.connect('http://tennis-meetup.fr:1337');
  /**
   * on se connecte
   */

  if (isLogged) {
    /**
     *
     * @param data
     */
    var addTemplate = function addTemplate(data, append) {
      if (append) {
        if (data.token != userId) {
          // pas mon message
          $('#receiver').append('<div class="d-flex justify-content-start mb-4" id="' + data.id + '">' + '    <div class="img_cont_msg">' + '        <img src="/uploads/profile_picture/' + data.avatar + '" class="rounded-circle user_img_msg" alt="' + data.alt + '"/>' + '    </div>' + '    <div class="msg_cotainer">' + data.message + '        <span class="msg_time">' + data.createdAt + '</span>' + '    </div>' + '</div>');
        } else {
          // mon message
          $('#receiver').append('<div class="d-flex justify-content-end mb-4" id="' + data.id + '">' + '<div class="msg_cotainer_send">' + data.message + '       <span class="msg_time_send">' + data.createdAt + '</span>' + '   </div>' + '   <div class="img_cont_msg">' + '       <img src="/uploads/profile_picture/' + data.avatar + '" alt="' + data.alt + '"class="rounded-circle user_img_msg">' + '   </div>' + '</div>');
        }
      } else {
        if (data.token != userId) {
          // pas mon message
          $('#receiver').prepend('<div class="d-flex justify-content-start mb-4" id="' + data.id + '">' + '    <div class="img_cont_msg">' + '        <img src="/uploads/profile_picture/' + data.avatar + '" class="rounded-circle user_img_msg"  style="max-width: 50px" alt="' + data.alt + '"/>' + '    </div>' + '    <div class="msg_cotainer">' + data.message + '        <span class="msg_time">' + data.createdAt + '</span>' + '    </div>' + '</div>');
        } else {
          // mon message
          $('#receiver').prepend('<div class="d-flex justify-content-end mb-4" id="' + data.id + '">' + '<div class="msg_cotainer_send">' + data.message + '       <span class="msg_time_send">' + data.createdAt + '</span>' + '   </div>' + '   <div class="img_cont_msg">' + '       <img src="/uploads/profile_picture/' + data.avatar + '" alt="' + data.alt + '"class="rounded-circle user_img_msg" style="max-width: 50px">' + '   </div>' + '</div>');
        }
      }
    };
    /**
     * retourne le loader
     * @returns {string}
     */


    var loader = function loader() {
      $("#receiver").prepend('<div class="d-flex justify-content-center loader">\n' + '  <div class="spinner-border" role="status">\n' + '    <span class="visually-hidden">Loading...</span>\n' + '  </div>\n' + '</div>');
    };
    /**
     * destroy loader
     * @returns {string}
     */


    var removeLoader = function removeLoader() {
      $('.loader').remove();
    };

    var enabled = function enabled() {
      $('#message').removeAttr('disabled');
      $('#chat-file').removeAttr('disabled');
      $('.send_btn').removeAttr('disabled');
    };
    /**
     *
     * @param interlocuteur
     */


    var headerChat = function headerChat(interlocuteur) {
      $('#interl').fadeIn("slow");
      $('#interlAvatar').attr('src', '/' + interlocuteur.interlocuteurAvatar);
      $('#interlProfile').attr('href', interlocuteur.interlocuteurProfile);
      $('#interlName').html('');
      $('#interlName').html(interlocuteur.interlocuteurName);
    };

    socket.emit('login', {
      userId: userId,
      firstname: firstname,
      lastname: lastname,
      avatar: 'avatar'
    });
    socket.on('login', function (data) {
      $('#connected_' + data.token).removeClass('offline');
    });
    socket.on('disconnect', function (data) {
      $('#connected_' + data.token).addClass('offline');
    });
    /**
     * message recu
     */

    socket.on('message', function (data) {
      console.log(data);

      if (data.groupId == $('#groupeId').val()) {
        addTemplate(data, 1);
        $('#receiver').animate({
          scrollTop: $('#receiver').prop('scrollHeight')
        }, 500);
      } else {
        $("#badgeNotif").html(parseInt($("#badgeNotif").html()) + 1); // il faut dire a socket de mettre à jour la base en marquant le message comme non lue
      }
    });
    /**
     * Envoie Message
     */

    $('#FormMessenger').submit(function (event) {
      event.preventDefault();
      var message = $('#message').val();

      if (message == "") {
        $('#message').css('border', '1px red solid');
        return true;
      }

      var to = $('#receiverId').val();
      var groupe = $('#groupeId').val();
      $.ajax({
        url: Routing.generate('chat_save_message', {
          'discussion': groupe,
          'tokenId': userId
        }),
        data: {
          message: message
        },
        method: "POST",
        beforeSend: function beforeSend() {
          $('#message').val('');
        },
        success: function success(data) {
          socket.emit('message', data.resultat);
        }
      });
    });
    $('.send_btn').click(function (event) {
      event.preventDefault();
      var messageSanitaze = $('#message').val();
      var message = $('#message').mentionsInput('getValue');
      var media = $('#chat-file').prop("files");
      var to = $('#receiverId').val();
      var groupe = $('#groupeId').val();

      if (message == "" && media.length == 0) {
        $('#message').css('border', '1px red solid');
        return false;
      }

      $('#message').css('border', 'none');
      var formData = new FormData();

      for (i = 0; i < media.length; i++) {
        formData.append("media[]", media[i]);
      }

      formData.append("message", message);
      formData.append("messageSanitaze", messageSanitaze);
      $.ajax({
        url: Routing.generate('chat_save_message', {
          'discussion': groupe,
          'tokenId': userId
        }),
        contentType: false,
        processData: false,
        type: 'post',
        data: formData,
        beforeSend: function beforeSend() {
          $('#message').val('');
        },
        success: function success(data) {
          socket.emit('message', data.resultat);
        }
      });
    });
    /**
     * Liste d'amis
     */

    $('.friendLists').click(function () {
      step = 0;
      $('#receiver').html('');
      selectedId = $(this).prop('id');

      if (selectedId != $('#groupeId').val() && loadFriend) {
        $('#groupeId').val(selectedId);
        $.ajax({
          url: Routing.generate('chat_load_message'),
          data: {
            groupe: selectedId
          },
          method: "POST",
          beforeSend: function beforeSend() {
            $('#receiver').html('');
            loadFriend = false;
            loader();
          },
          success: function success(data) {
            for (var k in data.resultat) {
              addTemplate(data.resultat[k]);
            }

            $('#receiver').animate({
              scrollTop: $('#receiver').prop('scrollHeight')
            }, 500);
            loadFriend = true;
            headerChat(data.interlocuteur); // il faut dire au scoket de mettre à jour tous les messages non en lu

            removeLoader();
            enabled();
          }
        });
      }
    });
    $('.contacts .groupeLists').click(function () {
      step = 0;
      $('#receiver').html('');
      selectedId = $(this).prop('id');

      if (selectedId != $('#groupeId').val() && loadGroup) {
        $('#groupeId').val(selectedId);
        $.ajax({
          url: Routing.generate('chat_load_message'),
          data: {
            groupe: selectedId
          },
          method: "POST",
          beforeSend: function beforeSend() {
            $('#receiver').html('');
            loadGroup = false;
          },
          success: function success(data) {
            for (var k in data.resultat) {
              addTemplate(data.resultat[k]);
            }

            $('#receiver').animate({
              scrollTop: $('#receiver').prop('scrollHeight')
            }, 500);
            loadGroup = true;
            removeLoader();
            enabled();
          }
        });
      }
    });
    $('#receiver').scroll(function () {
      if ($(this).scrollTop() == 0 && receive == false) {
        if (!receive) {
          var lastChat = $('#groupeId').val();
          step = step + limit;
          $.ajax({
            url: Routing.generate('chat_load_message'),
            data: {
              groupe: lastChat,
              step: step
            },
            method: "POST",
            beforeSend: function beforeSend() {
              receive = true;
              loader();
            },
            success: function success(data) {
              for (var k in data.resultat) {
                addTemplate(data.resultat[k]);
              }

              $('#groupeId').val(data.groupeId);
              receive = false;
              enabled();
            },
            complete: function complete() {
              receive = false;
              removeLoader();
            }
          });
        }
      }
    });
  } else {
    alert('not logged');
  }
});

/***/ }),

/***/ "./node_modules/core-js/internals/number-parse-int.js":
/*!************************************************************!*\
  !*** ./node_modules/core-js/internals/number-parse-int.js ***!
  \************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var global = __webpack_require__(/*! ../internals/global */ "./node_modules/core-js/internals/global.js");
var fails = __webpack_require__(/*! ../internals/fails */ "./node_modules/core-js/internals/fails.js");
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "./node_modules/core-js/internals/function-uncurry-this.js");
var toString = __webpack_require__(/*! ../internals/to-string */ "./node_modules/core-js/internals/to-string.js");
var trim = (__webpack_require__(/*! ../internals/string-trim */ "./node_modules/core-js/internals/string-trim.js").trim);
var whitespaces = __webpack_require__(/*! ../internals/whitespaces */ "./node_modules/core-js/internals/whitespaces.js");

var $parseInt = global.parseInt;
var Symbol = global.Symbol;
var ITERATOR = Symbol && Symbol.iterator;
var hex = /^[+-]?0x/i;
var exec = uncurryThis(hex.exec);
var FORCED = $parseInt(whitespaces + '08') !== 8 || $parseInt(whitespaces + '0x16') !== 22
  // MS Edge 18- broken with boxed symbols
  || (ITERATOR && !fails(function () { $parseInt(Object(ITERATOR)); }));

// `parseInt` method
// https://tc39.es/ecma262/#sec-parseint-string-radix
module.exports = FORCED ? function parseInt(string, radix) {
  var S = trim(toString(string));
  return $parseInt(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
} : $parseInt;


/***/ }),

/***/ "./node_modules/core-js/internals/string-trim.js":
/*!*******************************************************!*\
  !*** ./node_modules/core-js/internals/string-trim.js ***!
  \*******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "./node_modules/core-js/internals/function-uncurry-this.js");
var requireObjectCoercible = __webpack_require__(/*! ../internals/require-object-coercible */ "./node_modules/core-js/internals/require-object-coercible.js");
var toString = __webpack_require__(/*! ../internals/to-string */ "./node_modules/core-js/internals/to-string.js");
var whitespaces = __webpack_require__(/*! ../internals/whitespaces */ "./node_modules/core-js/internals/whitespaces.js");

var replace = uncurryThis(''.replace);
var whitespace = '[' + whitespaces + ']';
var ltrim = RegExp('^' + whitespace + whitespace + '*');
var rtrim = RegExp(whitespace + whitespace + '*$');

// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
var createMethod = function (TYPE) {
  return function ($this) {
    var string = toString(requireObjectCoercible($this));
    if (TYPE & 1) string = replace(string, ltrim, '');
    if (TYPE & 2) string = replace(string, rtrim, '');
    return string;
  };
};

module.exports = {
  // `String.prototype.{ trimLeft, trimStart }` methods
  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
  start: createMethod(1),
  // `String.prototype.{ trimRight, trimEnd }` methods
  // https://tc39.es/ecma262/#sec-string.prototype.trimend
  end: createMethod(2),
  // `String.prototype.trim` method
  // https://tc39.es/ecma262/#sec-string.prototype.trim
  trim: createMethod(3)
};


/***/ }),

/***/ "./node_modules/core-js/internals/whitespaces.js":
/*!*******************************************************!*\
  !*** ./node_modules/core-js/internals/whitespaces.js ***!
  \*******************************************************/
/***/ ((module) => {

// a string of all valid unicode whitespaces
module.exports = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';


/***/ }),

/***/ "./node_modules/core-js/modules/es.parse-int.js":
/*!******************************************************!*\
  !*** ./node_modules/core-js/modules/es.parse-int.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var $parseInt = __webpack_require__(/*! ../internals/number-parse-int */ "./node_modules/core-js/internals/number-parse-int.js");

// `parseInt` method
// https://tc39.es/ecma262/#sec-parseint-string-radix
$({ global: true, forced: parseInt != $parseInt }, {
  parseInt: $parseInt
});


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_jquery_dist_jquery_js","vendors-node_modules_core-js_internals_export_js-node_modules_core-js_internals_to-string_js"], () => (__webpack_exec__("./assets/js/pages/04-client.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QkFBeUIscUJBQXFCO0FBQzlDO0FBQ0E7QUFDQSx5QkFBeUIsb0JBQW9CO0FBQzdDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7Ozs7Ozs7QUM5QkE7QUFDQTtBQUNBOzs7Ozs7Ozs7OztBQ0ZBLFFBQVEsbUJBQU8sQ0FBQyx1RUFBcUI7QUFDckMsZ0JBQWdCLG1CQUFPLENBQUMsMkZBQStCOztBQUV2RDtBQUNBO0FBQ0EsSUFBSSw2Q0FBNkM7QUFDakQ7QUFDQSxDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL3BhZ2VzLzA0LWNsaWVudC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9pbnRlcm5hbHMvbnVtYmVyLXBhcnNlLWludC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9pbnRlcm5hbHMvc3RyaW5nLXRyaW0uanMiLCJ3ZWJwYWNrOi8vLy4vbm9kZV9tb2R1bGVzL2NvcmUtanMvaW50ZXJuYWxzL3doaXRlc3BhY2VzLmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9jb3JlLWpzL21vZHVsZXMvZXMucGFyc2UtaW50LmpzIl0sInNvdXJjZXNDb250ZW50IjpbInZhciBsaW1pdCA9IDEwO1xyXG52YXIgc3RlcCA9IDA7XHJcbnZhciByZWNlaXZlID0gMDtcclxudmFyIGxvYWQgPSBmYWxzZTtcclxudmFyIGxvYWRGcmllbmQgPSB0cnVlO1xyXG52YXIgbG9hZEdyb3VwID0gdHJ1ZTtcclxudmFyIHNlbGVjdGVkSWQgPSAwO1xyXG4vL1xyXG4kKGZ1bmN0aW9uICgpIHtcclxuXHR2YXIgc29ja2V0ID0gaW8uY29ubmVjdCgnaHR0cDovL3Rlbm5pcy1tZWV0dXAuZnI6MTMzNycpO1xyXG5cdC8qKlxyXG5cdCAqIG9uIHNlIGNvbm5lY3RlXHJcblx0ICovXHJcblx0aWYgKGlzTG9nZ2VkKSB7XHJcblx0XHRzb2NrZXQuZW1pdCgnbG9naW4nLCB7XHJcblx0XHRcdHVzZXJJZDogdXNlcklkLFxyXG5cdFx0XHRmaXJzdG5hbWU6IGZpcnN0bmFtZSxcclxuXHRcdFx0bGFzdG5hbWU6IGxhc3RuYW1lLFxyXG5cdFx0XHRhdmF0YXI6ICdhdmF0YXInLFxyXG5cdFx0fSk7XHJcblx0XHRzb2NrZXQub24oJ2xvZ2luJywgZnVuY3Rpb24gKGRhdGEpIHtcclxuXHRcdFx0JCgnI2Nvbm5lY3RlZF8nICsgZGF0YS50b2tlbikucmVtb3ZlQ2xhc3MoJ29mZmxpbmUnKTtcclxuXHRcdH0pO1xyXG5cdFx0c29ja2V0Lm9uKCdkaXNjb25uZWN0JywgZnVuY3Rpb24gKGRhdGEpIHtcclxuXHRcdFx0JCgnI2Nvbm5lY3RlZF8nICsgZGF0YS50b2tlbikuYWRkQ2xhc3MoJ29mZmxpbmUnKTtcclxuXHRcdH0pO1xyXG5cdFx0LyoqXHJcblx0XHQgKiBtZXNzYWdlIHJlY3VcclxuXHRcdCAqL1xyXG5cdFx0c29ja2V0Lm9uKCdtZXNzYWdlJywgZnVuY3Rpb24gKGRhdGEpIHtcclxuXHRcdFx0Y29uc29sZS5sb2coZGF0YSlcclxuXHRcdFx0aWYgKGRhdGEuZ3JvdXBJZCA9PSAkKCcjZ3JvdXBlSWQnKS52YWwoKSkge1xyXG5cdFx0XHRcdGFkZFRlbXBsYXRlKGRhdGEsIDEpO1xyXG5cdFx0XHRcdCQoJyNyZWNlaXZlcicpLmFuaW1hdGUoe3Njcm9sbFRvcDogJCgnI3JlY2VpdmVyJykucHJvcCgnc2Nyb2xsSGVpZ2h0Jyl9LCA1MDApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdCQoXCIjYmFkZ2VOb3RpZlwiKS5odG1sKHBhcnNlSW50KCQoXCIjYmFkZ2VOb3RpZlwiKS5odG1sKCkpICsgMSk7XHJcblx0XHRcdFx0Ly8gaWwgZmF1dCBkaXJlIGEgc29ja2V0IGRlIG1ldHRyZSDDoCBqb3VyIGxhIGJhc2UgZW4gbWFycXVhbnQgbGUgbWVzc2FnZSBjb21tZSBub24gbHVlXHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdFx0LyoqXHJcblx0XHQgKiBFbnZvaWUgTWVzc2FnZVxyXG5cdFx0ICovXHJcblx0XHQkKCcjRm9ybU1lc3NlbmdlcicpLnN1Ym1pdChmdW5jdGlvbiAoZXZlbnQpIHtcclxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuXHRcdFx0dmFyIG1lc3NhZ2UgPSAkKCcjbWVzc2FnZScpLnZhbCgpO1xyXG5cdFx0XHRpZiAobWVzc2FnZSA9PSBcIlwiKSB7XHJcblx0XHRcdFx0JCgnI21lc3NhZ2UnKS5jc3MoJ2JvcmRlcicsICcxcHggcmVkIHNvbGlkJyk7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdFx0dmFyIHRvID0gJCgnI3JlY2VpdmVySWQnKS52YWwoKTtcclxuXHRcdFx0dmFyIGdyb3VwZSA9ICQoJyNncm91cGVJZCcpLnZhbCgpO1xyXG5cclxuXHRcdFx0JC5hamF4KHtcclxuXHRcdFx0XHR1cmw6IFJvdXRpbmcuZ2VuZXJhdGUoJ2NoYXRfc2F2ZV9tZXNzYWdlJyxcclxuXHRcdFx0XHRcdHtcclxuXHRcdFx0XHRcdFx0J2Rpc2N1c3Npb24nOiBncm91cGUsXHJcblx0XHRcdFx0XHRcdCd0b2tlbklkJzogdXNlcklkXHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0KSxcclxuXHRcdFx0XHRkYXRhOiB7XHJcblx0XHRcdFx0XHRtZXNzYWdlOiBtZXNzYWdlXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHRtZXRob2Q6IFwiUE9TVFwiLFxyXG5cdFx0XHRcdGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdCQoJyNtZXNzYWdlJykudmFsKCcnKTtcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdHN1Y2Nlc3M6IGZ1bmN0aW9uIChkYXRhKSB7XHJcblx0XHRcdFx0XHRzb2NrZXQuZW1pdCgnbWVzc2FnZScsIGRhdGEucmVzdWx0YXQpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSk7XHJcblx0XHR9KTtcclxuXHRcdCQoJy5zZW5kX2J0bicpLmNsaWNrKGZ1bmN0aW9uIChldmVudCkge1xyXG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHR2YXIgbWVzc2FnZVNhbml0YXplID0gJCgnI21lc3NhZ2UnKS52YWwoKTtcclxuXHRcdFx0dmFyIG1lc3NhZ2UgPSAkKCcjbWVzc2FnZScpLm1lbnRpb25zSW5wdXQoJ2dldFZhbHVlJyk7XHJcblx0XHRcdHZhciBtZWRpYSA9ICQoJyNjaGF0LWZpbGUnKS5wcm9wKFwiZmlsZXNcIik7XHJcblx0XHRcdHZhciB0byA9ICQoJyNyZWNlaXZlcklkJykudmFsKCk7XHJcblx0XHRcdHZhciBncm91cGUgPSAkKCcjZ3JvdXBlSWQnKS52YWwoKTtcclxuXHRcdFx0aWYgKG1lc3NhZ2UgPT0gXCJcIiAmJiBtZWRpYS5sZW5ndGggPT0gMCkge1xyXG5cdFx0XHRcdCQoJyNtZXNzYWdlJykuY3NzKCdib3JkZXInLCAnMXB4IHJlZCBzb2xpZCcpO1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cdFx0XHQkKCcjbWVzc2FnZScpLmNzcygnYm9yZGVyJywgJ25vbmUnKTtcclxuXHRcdFx0dmFyIGZvcm1EYXRhID0gbmV3IEZvcm1EYXRhKCk7XHJcblx0XHRcdGZvciAoaSA9IDA7IGkgPCBtZWRpYS5sZW5ndGg7IGkrKykge1xyXG5cdFx0XHRcdGZvcm1EYXRhLmFwcGVuZChcIm1lZGlhW11cIiwgbWVkaWFbaV0pO1xyXG5cdFx0XHR9XHJcblx0XHRcdGZvcm1EYXRhLmFwcGVuZChcIm1lc3NhZ2VcIiwgbWVzc2FnZSk7XHJcblx0XHRcdGZvcm1EYXRhLmFwcGVuZChcIm1lc3NhZ2VTYW5pdGF6ZVwiLCBtZXNzYWdlU2FuaXRhemUpO1xyXG5cdFx0XHQkLmFqYXgoe1xyXG5cdFx0XHRcdHVybDogUm91dGluZy5nZW5lcmF0ZSgnY2hhdF9zYXZlX21lc3NhZ2UnLFxyXG5cdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHQnZGlzY3Vzc2lvbic6IGdyb3VwZSxcclxuXHRcdFx0XHRcdFx0J3Rva2VuSWQnOiB1c2VySWRcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHQpLFxyXG5cdFx0XHRcdGNvbnRlbnRUeXBlOiBmYWxzZSxcclxuXHRcdFx0XHRwcm9jZXNzRGF0YTogZmFsc2UsXHJcblx0XHRcdFx0dHlwZTogJ3Bvc3QnLFxyXG5cdFx0XHRcdGRhdGE6IGZvcm1EYXRhLFxyXG5cdFx0XHRcdGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdCQoJyNtZXNzYWdlJykudmFsKCcnKTtcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdHN1Y2Nlc3M6IGZ1bmN0aW9uIChkYXRhKSB7XHJcblx0XHRcdFx0XHRzb2NrZXQuZW1pdCgnbWVzc2FnZScsIGRhdGEucmVzdWx0YXQpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSk7XHJcblx0XHR9KTtcclxuXHRcdC8qKlxyXG5cdFx0ICogTGlzdGUgZCdhbWlzXHJcblx0XHQgKi9cclxuXHRcdCQoJy5mcmllbmRMaXN0cycpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0c3RlcCA9IDA7XHJcblx0XHRcdCQoJyNyZWNlaXZlcicpLmh0bWwoJycpO1xyXG5cdFx0XHRzZWxlY3RlZElkID0gJCh0aGlzKS5wcm9wKCdpZCcpO1xyXG5cdFx0XHRpZiAoc2VsZWN0ZWRJZCAhPSAkKCcjZ3JvdXBlSWQnKS52YWwoKSAmJiBsb2FkRnJpZW5kKSB7XHJcblx0XHRcdFx0JCgnI2dyb3VwZUlkJykudmFsKHNlbGVjdGVkSWQpO1xyXG5cdFx0XHRcdCQuYWpheCh7XHJcblx0XHRcdFx0XHR1cmw6IFJvdXRpbmcuZ2VuZXJhdGUoJ2NoYXRfbG9hZF9tZXNzYWdlJyksXHJcblx0XHRcdFx0XHRkYXRhOiB7XHJcblx0XHRcdFx0XHRcdGdyb3VwZTogc2VsZWN0ZWRJZCxcclxuXHRcdFx0XHRcdH0sXHJcblx0XHRcdFx0XHRtZXRob2Q6IFwiUE9TVFwiLFxyXG5cdFx0XHRcdFx0YmVmb3JlU2VuZDogZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdFx0XHQkKCcjcmVjZWl2ZXInKS5odG1sKCcnKTtcclxuXHRcdFx0XHRcdFx0bG9hZEZyaWVuZCA9IGZhbHNlO1xyXG5cdFx0XHRcdFx0XHRsb2FkZXIoKTtcclxuXHRcdFx0XHRcdH0sXHJcblx0XHRcdFx0XHRzdWNjZXNzOiBmdW5jdGlvbiAoZGF0YSkge1xyXG5cdFx0XHRcdFx0XHRmb3IgKHZhciBrIGluIGRhdGEucmVzdWx0YXQpIHtcclxuXHRcdFx0XHRcdFx0XHRhZGRUZW1wbGF0ZShkYXRhLnJlc3VsdGF0W2tdKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHQkKCcjcmVjZWl2ZXInKS5hbmltYXRlKHtzY3JvbGxUb3A6ICQoJyNyZWNlaXZlcicpLnByb3AoJ3Njcm9sbEhlaWdodCcpfSwgNTAwKTtcclxuXHRcdFx0XHRcdFx0bG9hZEZyaWVuZCA9IHRydWU7XHJcblx0XHRcdFx0XHRcdGhlYWRlckNoYXQoZGF0YS5pbnRlcmxvY3V0ZXVyKTtcclxuXHRcdFx0XHRcdFx0Ly8gaWwgZmF1dCBkaXJlIGF1IHNjb2tldCBkZSBtZXR0cmUgw6Agam91ciB0b3VzIGxlcyBtZXNzYWdlcyBub24gZW4gbHVcclxuXHRcdFx0XHRcdFx0cmVtb3ZlTG9hZGVyKCk7XHJcblx0XHRcdFx0XHRcdGVuYWJsZWQoKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9KVxyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHJcblx0XHQkKCcuY29udGFjdHMgLmdyb3VwZUxpc3RzJykuY2xpY2soZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRzdGVwID0gMDtcclxuXHRcdFx0JCgnI3JlY2VpdmVyJykuaHRtbCgnJyk7XHJcblx0XHRcdHNlbGVjdGVkSWQgPSAkKHRoaXMpLnByb3AoJ2lkJyk7XHJcblx0XHRcdGlmIChzZWxlY3RlZElkICE9ICQoJyNncm91cGVJZCcpLnZhbCgpICYmIGxvYWRHcm91cCkge1xyXG5cdFx0XHRcdCQoJyNncm91cGVJZCcpLnZhbChzZWxlY3RlZElkKTtcclxuXHRcdFx0XHQkLmFqYXgoe1xyXG5cdFx0XHRcdFx0dXJsOiBSb3V0aW5nLmdlbmVyYXRlKCdjaGF0X2xvYWRfbWVzc2FnZScpLFxyXG5cdFx0XHRcdFx0ZGF0YToge1xyXG5cdFx0XHRcdFx0XHRncm91cGU6IHNlbGVjdGVkSWQsXHJcblx0XHRcdFx0XHR9LFxyXG5cdFx0XHRcdFx0bWV0aG9kOiBcIlBPU1RcIixcclxuXHRcdFx0XHRcdGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0JCgnI3JlY2VpdmVyJykuaHRtbCgnJyk7XHJcblx0XHRcdFx0XHRcdGxvYWRHcm91cCA9IGZhbHNlO1xyXG5cdFx0XHRcdFx0fSxcclxuXHRcdFx0XHRcdHN1Y2Nlc3M6IGZ1bmN0aW9uIChkYXRhKSB7XHJcblx0XHRcdFx0XHRcdGZvciAodmFyIGsgaW4gZGF0YS5yZXN1bHRhdCkge1xyXG5cdFx0XHRcdFx0XHRcdGFkZFRlbXBsYXRlKGRhdGEucmVzdWx0YXRba10pO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdCQoJyNyZWNlaXZlcicpLmFuaW1hdGUoe3Njcm9sbFRvcDogJCgnI3JlY2VpdmVyJykucHJvcCgnc2Nyb2xsSGVpZ2h0Jyl9LCA1MDApO1xyXG5cdFx0XHRcdFx0XHRsb2FkR3JvdXAgPSB0cnVlO1xyXG5cdFx0XHRcdFx0XHRyZW1vdmVMb2FkZXIoKTtcclxuXHRcdFx0XHRcdFx0ZW5hYmxlZCgpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pXHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cclxuXHRcdCQoJyNyZWNlaXZlcicpLnNjcm9sbChmdW5jdGlvbiAoKSB7XHJcblx0XHRcdGlmICgkKHRoaXMpLnNjcm9sbFRvcCgpID09IDAgJiYgcmVjZWl2ZSA9PSBmYWxzZSkge1xyXG5cdFx0XHRcdGlmICghcmVjZWl2ZSkge1xyXG5cdFx0XHRcdFx0dmFyIGxhc3RDaGF0ID0gJCgnI2dyb3VwZUlkJykudmFsKCk7XHJcblx0XHRcdFx0XHRzdGVwID0gc3RlcCArIGxpbWl0O1xyXG5cdFx0XHRcdFx0JC5hamF4KHtcclxuXHRcdFx0XHRcdFx0dXJsOiBSb3V0aW5nLmdlbmVyYXRlKCdjaGF0X2xvYWRfbWVzc2FnZScpLFxyXG5cdFx0XHRcdFx0XHRkYXRhOiB7XHJcblx0XHRcdFx0XHRcdFx0Z3JvdXBlOiBsYXN0Q2hhdCxcclxuXHRcdFx0XHRcdFx0XHRzdGVwOiBzdGVwXHJcblx0XHRcdFx0XHRcdH0sXHJcblx0XHRcdFx0XHRcdG1ldGhvZDogXCJQT1NUXCIsXHJcblx0XHRcdFx0XHRcdGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0XHRyZWNlaXZlID0gdHJ1ZTtcclxuXHRcdFx0XHRcdFx0XHRsb2FkZXIoKTtcclxuXHRcdFx0XHRcdFx0fSxcclxuXHRcdFx0XHRcdFx0c3VjY2VzczogZnVuY3Rpb24gKGRhdGEpIHtcclxuXHRcdFx0XHRcdFx0XHRmb3IgKHZhciBrIGluIGRhdGEucmVzdWx0YXQpIHtcclxuXHRcdFx0XHRcdFx0XHRcdGFkZFRlbXBsYXRlKGRhdGEucmVzdWx0YXRba10pO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHQkKCcjZ3JvdXBlSWQnKS52YWwoZGF0YS5ncm91cGVJZCk7XHJcblx0XHRcdFx0XHRcdFx0cmVjZWl2ZSA9IGZhbHNlO1xyXG5cdFx0XHRcdFx0XHRcdGVuYWJsZWQoKTtcclxuXHRcdFx0XHRcdFx0fSxcclxuXHRcdFx0XHRcdFx0Y29tcGxldGU6IGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0XHRyZWNlaXZlID0gZmFsc2U7XHJcblx0XHRcdFx0XHRcdFx0cmVtb3ZlTG9hZGVyKCk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH0pO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIGRhdGFcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gYWRkVGVtcGxhdGUoZGF0YSwgYXBwZW5kKSB7XHJcblx0XHRcdGlmIChhcHBlbmQpIHtcclxuXHRcdFx0XHRpZiAoZGF0YS50b2tlbiAhPSB1c2VySWQpIHtcclxuXHRcdFx0XHRcdC8vIHBhcyBtb24gbWVzc2FnZVxyXG5cdFx0XHRcdFx0JCgnI3JlY2VpdmVyJykuYXBwZW5kKFxyXG5cdFx0XHRcdFx0XHQnPGRpdiBjbGFzcz1cImQtZmxleCBqdXN0aWZ5LWNvbnRlbnQtc3RhcnQgbWItNFwiIGlkPVwiJyArIGRhdGEuaWQgKyAnXCI+J1xyXG5cdFx0XHRcdFx0XHQrICcgICAgPGRpdiBjbGFzcz1cImltZ19jb250X21zZ1wiPidcclxuXHRcdFx0XHRcdFx0KyAnICAgICAgICA8aW1nIHNyYz1cIi91cGxvYWRzL3Byb2ZpbGVfcGljdHVyZS8nICsgZGF0YS5hdmF0YXIgKyAnXCIgY2xhc3M9XCJyb3VuZGVkLWNpcmNsZSB1c2VyX2ltZ19tc2dcIiBhbHQ9XCInICsgZGF0YS5hbHQgKyAnXCIvPidcclxuXHRcdFx0XHRcdFx0KyAnICAgIDwvZGl2PidcclxuXHRcdFx0XHRcdFx0KyAnICAgIDxkaXYgY2xhc3M9XCJtc2dfY290YWluZXJcIj4nICsgZGF0YS5tZXNzYWdlXHJcblx0XHRcdFx0XHRcdCsgJyAgICAgICAgPHNwYW4gY2xhc3M9XCJtc2dfdGltZVwiPicgKyBkYXRhLmNyZWF0ZWRBdCArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHQrICcgICAgPC9kaXY+J1xyXG5cdFx0XHRcdFx0XHQrICc8L2Rpdj4nXHJcblx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHQvLyBtb24gbWVzc2FnZVxyXG5cdFx0XHRcdFx0JCgnI3JlY2VpdmVyJykuYXBwZW5kKFxyXG5cdFx0XHRcdFx0XHQnPGRpdiBjbGFzcz1cImQtZmxleCBqdXN0aWZ5LWNvbnRlbnQtZW5kIG1iLTRcIiBpZD1cIicgKyBkYXRhLmlkICsgJ1wiPicgK1xyXG5cdFx0XHRcdFx0XHQnPGRpdiBjbGFzcz1cIm1zZ19jb3RhaW5lcl9zZW5kXCI+JyArIGRhdGEubWVzc2FnZSArXHJcblx0XHRcdFx0XHRcdCcgICAgICAgPHNwYW4gY2xhc3M9XCJtc2dfdGltZV9zZW5kXCI+JyArIGRhdGEuY3JlYXRlZEF0ICsgJzwvc3Bhbj4nICtcclxuXHRcdFx0XHRcdFx0JyAgIDwvZGl2PicgK1xyXG5cdFx0XHRcdFx0XHQnICAgPGRpdiBjbGFzcz1cImltZ19jb250X21zZ1wiPicgK1xyXG5cdFx0XHRcdFx0XHQnICAgICAgIDxpbWcgc3JjPVwiL3VwbG9hZHMvcHJvZmlsZV9waWN0dXJlLycgKyBkYXRhLmF2YXRhciArICdcIiBhbHQ9XCInICsgZGF0YS5hbHQgKyAnXCJjbGFzcz1cInJvdW5kZWQtY2lyY2xlIHVzZXJfaW1nX21zZ1wiPicgK1xyXG5cdFx0XHRcdFx0XHQnICAgPC9kaXY+JyArXHJcblx0XHRcdFx0XHRcdCc8L2Rpdj4nXHJcblx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRpZiAoZGF0YS50b2tlbiAhPSB1c2VySWQpIHtcclxuXHRcdFx0XHRcdC8vIHBhcyBtb24gbWVzc2FnZVxyXG5cdFx0XHRcdFx0JCgnI3JlY2VpdmVyJykucHJlcGVuZChcclxuXHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJkLWZsZXgganVzdGlmeS1jb250ZW50LXN0YXJ0IG1iLTRcIiBpZD1cIicgKyBkYXRhLmlkICsgJ1wiPidcclxuXHRcdFx0XHRcdFx0KyAnICAgIDxkaXYgY2xhc3M9XCJpbWdfY29udF9tc2dcIj4nXHJcblx0XHRcdFx0XHRcdCsgJyAgICAgICAgPGltZyBzcmM9XCIvdXBsb2Fkcy9wcm9maWxlX3BpY3R1cmUvJyArIGRhdGEuYXZhdGFyICsgJ1wiIGNsYXNzPVwicm91bmRlZC1jaXJjbGUgdXNlcl9pbWdfbXNnXCIgIHN0eWxlPVwibWF4LXdpZHRoOiA1MHB4XCIgYWx0PVwiJyArIGRhdGEuYWx0ICsgJ1wiLz4nXHJcblx0XHRcdFx0XHRcdCsgJyAgICA8L2Rpdj4nXHJcblx0XHRcdFx0XHRcdCsgJyAgICA8ZGl2IGNsYXNzPVwibXNnX2NvdGFpbmVyXCI+JyArIGRhdGEubWVzc2FnZVxyXG5cdFx0XHRcdFx0XHQrICcgICAgICAgIDxzcGFuIGNsYXNzPVwibXNnX3RpbWVcIj4nICsgZGF0YS5jcmVhdGVkQXQgKyAnPC9zcGFuPidcclxuXHRcdFx0XHRcdFx0KyAnICAgIDwvZGl2PidcclxuXHRcdFx0XHRcdFx0KyAnPC9kaXY+J1xyXG5cdFx0XHRcdFx0KTtcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0Ly8gbW9uIG1lc3NhZ2VcclxuXHRcdFx0XHRcdCQoJyNyZWNlaXZlcicpLnByZXBlbmQoXHJcblx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwiZC1mbGV4IGp1c3RpZnktY29udGVudC1lbmQgbWItNFwiIGlkPVwiJyArIGRhdGEuaWQgKyAnXCI+JyArXHJcblx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwibXNnX2NvdGFpbmVyX3NlbmRcIj4nICsgZGF0YS5tZXNzYWdlICtcclxuXHRcdFx0XHRcdFx0JyAgICAgICA8c3BhbiBjbGFzcz1cIm1zZ190aW1lX3NlbmRcIj4nICsgZGF0YS5jcmVhdGVkQXQgKyAnPC9zcGFuPicgK1xyXG5cdFx0XHRcdFx0XHQnICAgPC9kaXY+JyArXHJcblx0XHRcdFx0XHRcdCcgICA8ZGl2IGNsYXNzPVwiaW1nX2NvbnRfbXNnXCI+JyArXHJcblx0XHRcdFx0XHRcdCcgICAgICAgPGltZyBzcmM9XCIvdXBsb2Fkcy9wcm9maWxlX3BpY3R1cmUvJyArIGRhdGEuYXZhdGFyICsgJ1wiIGFsdD1cIicgKyBkYXRhLmFsdCArICdcImNsYXNzPVwicm91bmRlZC1jaXJjbGUgdXNlcl9pbWdfbXNnXCIgc3R5bGU9XCJtYXgtd2lkdGg6IDUwcHhcIj4nICtcclxuXHRcdFx0XHRcdFx0JyAgIDwvZGl2PicgK1xyXG5cdFx0XHRcdFx0XHQnPC9kaXY+J1xyXG5cdFx0XHRcdFx0KTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiByZXRvdXJuZSBsZSBsb2FkZXJcclxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIGxvYWRlcigpIHtcclxuXHRcdFx0JChcIiNyZWNlaXZlclwiKS5wcmVwZW5kKFxyXG5cdFx0XHRcdCc8ZGl2IGNsYXNzPVwiZC1mbGV4IGp1c3RpZnktY29udGVudC1jZW50ZXIgbG9hZGVyXCI+XFxuJyArXHJcblx0XHRcdFx0JyAgPGRpdiBjbGFzcz1cInNwaW5uZXItYm9yZGVyXCIgcm9sZT1cInN0YXR1c1wiPlxcbicgK1xyXG5cdFx0XHRcdCcgICAgPHNwYW4gY2xhc3M9XCJ2aXN1YWxseS1oaWRkZW5cIj5Mb2FkaW5nLi4uPC9zcGFuPlxcbicgK1xyXG5cdFx0XHRcdCcgIDwvZGl2PlxcbicgK1xyXG5cdFx0XHRcdCc8L2Rpdj4nKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIGRlc3Ryb3kgbG9hZGVyXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiByZW1vdmVMb2FkZXIoKSB7XHJcblxyXG5cdFx0XHQkKCcubG9hZGVyJykucmVtb3ZlKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0ZnVuY3Rpb24gZW5hYmxlZCgpIHtcclxuXHRcdFx0JCgnI21lc3NhZ2UnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xyXG5cdFx0XHQkKCcjY2hhdC1maWxlJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuXHRcdFx0JCgnLnNlbmRfYnRuJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gaW50ZXJsb2N1dGV1clxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBoZWFkZXJDaGF0KGludGVybG9jdXRldXIpIHtcclxuXHRcdFx0JCgnI2ludGVybCcpLmZhZGVJbihcInNsb3dcIik7XHJcblx0XHRcdCQoJyNpbnRlcmxBdmF0YXInKS5hdHRyKCdzcmMnLCAnLycgKyBpbnRlcmxvY3V0ZXVyLmludGVybG9jdXRldXJBdmF0YXIpO1xyXG5cdFx0XHQkKCcjaW50ZXJsUHJvZmlsZScpLmF0dHIoJ2hyZWYnLCBpbnRlcmxvY3V0ZXVyLmludGVybG9jdXRldXJQcm9maWxlKTtcclxuXHRcdFx0JCgnI2ludGVybE5hbWUnKS5odG1sKCcnKTtcclxuXHRcdFx0JCgnI2ludGVybE5hbWUnKS5odG1sKGludGVybG9jdXRldXIuaW50ZXJsb2N1dGV1ck5hbWUpO1xyXG5cdFx0fVxyXG5cdH0gZWxzZSB7XHJcblx0XHRhbGVydCgnbm90IGxvZ2dlZCcpXHJcblx0fVxyXG59KTtcclxuIiwidmFyIGdsb2JhbCA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9nbG9iYWwnKTtcbnZhciBmYWlscyA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9mYWlscycpO1xudmFyIHVuY3VycnlUaGlzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2Z1bmN0aW9uLXVuY3VycnktdGhpcycpO1xudmFyIHRvU3RyaW5nID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3RvLXN0cmluZycpO1xudmFyIHRyaW0gPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvc3RyaW5nLXRyaW0nKS50cmltO1xudmFyIHdoaXRlc3BhY2VzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3doaXRlc3BhY2VzJyk7XG5cbnZhciAkcGFyc2VJbnQgPSBnbG9iYWwucGFyc2VJbnQ7XG52YXIgU3ltYm9sID0gZ2xvYmFsLlN5bWJvbDtcbnZhciBJVEVSQVRPUiA9IFN5bWJvbCAmJiBTeW1ib2wuaXRlcmF0b3I7XG52YXIgaGV4ID0gL15bKy1dPzB4L2k7XG52YXIgZXhlYyA9IHVuY3VycnlUaGlzKGhleC5leGVjKTtcbnZhciBGT1JDRUQgPSAkcGFyc2VJbnQod2hpdGVzcGFjZXMgKyAnMDgnKSAhPT0gOCB8fCAkcGFyc2VJbnQod2hpdGVzcGFjZXMgKyAnMHgxNicpICE9PSAyMlxuICAvLyBNUyBFZGdlIDE4LSBicm9rZW4gd2l0aCBib3hlZCBzeW1ib2xzXG4gIHx8IChJVEVSQVRPUiAmJiAhZmFpbHMoZnVuY3Rpb24gKCkgeyAkcGFyc2VJbnQoT2JqZWN0KElURVJBVE9SKSk7IH0pKTtcblxuLy8gYHBhcnNlSW50YCBtZXRob2Rcbi8vIGh0dHBzOi8vdGMzOS5lcy9lY21hMjYyLyNzZWMtcGFyc2VpbnQtc3RyaW5nLXJhZGl4XG5tb2R1bGUuZXhwb3J0cyA9IEZPUkNFRCA/IGZ1bmN0aW9uIHBhcnNlSW50KHN0cmluZywgcmFkaXgpIHtcbiAgdmFyIFMgPSB0cmltKHRvU3RyaW5nKHN0cmluZykpO1xuICByZXR1cm4gJHBhcnNlSW50KFMsIChyYWRpeCA+Pj4gMCkgfHwgKGV4ZWMoaGV4LCBTKSA/IDE2IDogMTApKTtcbn0gOiAkcGFyc2VJbnQ7XG4iLCJ2YXIgdW5jdXJyeVRoaXMgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZnVuY3Rpb24tdW5jdXJyeS10aGlzJyk7XG52YXIgcmVxdWlyZU9iamVjdENvZXJjaWJsZSA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9yZXF1aXJlLW9iamVjdC1jb2VyY2libGUnKTtcbnZhciB0b1N0cmluZyA9IHJlcXVpcmUoJy4uL2ludGVybmFscy90by1zdHJpbmcnKTtcbnZhciB3aGl0ZXNwYWNlcyA9IHJlcXVpcmUoJy4uL2ludGVybmFscy93aGl0ZXNwYWNlcycpO1xuXG52YXIgcmVwbGFjZSA9IHVuY3VycnlUaGlzKCcnLnJlcGxhY2UpO1xudmFyIHdoaXRlc3BhY2UgPSAnWycgKyB3aGl0ZXNwYWNlcyArICddJztcbnZhciBsdHJpbSA9IFJlZ0V4cCgnXicgKyB3aGl0ZXNwYWNlICsgd2hpdGVzcGFjZSArICcqJyk7XG52YXIgcnRyaW0gPSBSZWdFeHAod2hpdGVzcGFjZSArIHdoaXRlc3BhY2UgKyAnKiQnKTtcblxuLy8gYFN0cmluZy5wcm90b3R5cGUueyB0cmltLCB0cmltU3RhcnQsIHRyaW1FbmQsIHRyaW1MZWZ0LCB0cmltUmlnaHQgfWAgbWV0aG9kcyBpbXBsZW1lbnRhdGlvblxudmFyIGNyZWF0ZU1ldGhvZCA9IGZ1bmN0aW9uIChUWVBFKSB7XG4gIHJldHVybiBmdW5jdGlvbiAoJHRoaXMpIHtcbiAgICB2YXIgc3RyaW5nID0gdG9TdHJpbmcocmVxdWlyZU9iamVjdENvZXJjaWJsZSgkdGhpcykpO1xuICAgIGlmIChUWVBFICYgMSkgc3RyaW5nID0gcmVwbGFjZShzdHJpbmcsIGx0cmltLCAnJyk7XG4gICAgaWYgKFRZUEUgJiAyKSBzdHJpbmcgPSByZXBsYWNlKHN0cmluZywgcnRyaW0sICcnKTtcbiAgICByZXR1cm4gc3RyaW5nO1xuICB9O1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSB7XG4gIC8vIGBTdHJpbmcucHJvdG90eXBlLnsgdHJpbUxlZnQsIHRyaW1TdGFydCB9YCBtZXRob2RzXG4gIC8vIGh0dHBzOi8vdGMzOS5lcy9lY21hMjYyLyNzZWMtc3RyaW5nLnByb3RvdHlwZS50cmltc3RhcnRcbiAgc3RhcnQ6IGNyZWF0ZU1ldGhvZCgxKSxcbiAgLy8gYFN0cmluZy5wcm90b3R5cGUueyB0cmltUmlnaHQsIHRyaW1FbmQgfWAgbWV0aG9kc1xuICAvLyBodHRwczovL3RjMzkuZXMvZWNtYTI2Mi8jc2VjLXN0cmluZy5wcm90b3R5cGUudHJpbWVuZFxuICBlbmQ6IGNyZWF0ZU1ldGhvZCgyKSxcbiAgLy8gYFN0cmluZy5wcm90b3R5cGUudHJpbWAgbWV0aG9kXG4gIC8vIGh0dHBzOi8vdGMzOS5lcy9lY21hMjYyLyNzZWMtc3RyaW5nLnByb3RvdHlwZS50cmltXG4gIHRyaW06IGNyZWF0ZU1ldGhvZCgzKVxufTtcbiIsIi8vIGEgc3RyaW5nIG9mIGFsbCB2YWxpZCB1bmljb2RlIHdoaXRlc3BhY2VzXG5tb2R1bGUuZXhwb3J0cyA9ICdcXHUwMDA5XFx1MDAwQVxcdTAwMEJcXHUwMDBDXFx1MDAwRFxcdTAwMjBcXHUwMEEwXFx1MTY4MFxcdTIwMDBcXHUyMDAxXFx1MjAwMicgK1xuICAnXFx1MjAwM1xcdTIwMDRcXHUyMDA1XFx1MjAwNlxcdTIwMDdcXHUyMDA4XFx1MjAwOVxcdTIwMEFcXHUyMDJGXFx1MjA1RlxcdTMwMDBcXHUyMDI4XFx1MjAyOVxcdUZFRkYnO1xuIiwidmFyICQgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZXhwb3J0Jyk7XG52YXIgJHBhcnNlSW50ID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL251bWJlci1wYXJzZS1pbnQnKTtcblxuLy8gYHBhcnNlSW50YCBtZXRob2Rcbi8vIGh0dHBzOi8vdGMzOS5lcy9lY21hMjYyLyNzZWMtcGFyc2VpbnQtc3RyaW5nLXJhZGl4XG4kKHsgZ2xvYmFsOiB0cnVlLCBmb3JjZWQ6IHBhcnNlSW50ICE9ICRwYXJzZUludCB9LCB7XG4gIHBhcnNlSW50OiAkcGFyc2VJbnRcbn0pO1xuIl0sIm5hbWVzIjpbImxpbWl0Iiwic3RlcCIsInJlY2VpdmUiLCJsb2FkIiwibG9hZEZyaWVuZCIsImxvYWRHcm91cCIsInNlbGVjdGVkSWQiLCIkIiwic29ja2V0IiwiaW8iLCJjb25uZWN0IiwiaXNMb2dnZWQiLCJhZGRUZW1wbGF0ZSIsImRhdGEiLCJhcHBlbmQiLCJ0b2tlbiIsInVzZXJJZCIsImlkIiwiYXZhdGFyIiwiYWx0IiwibWVzc2FnZSIsImNyZWF0ZWRBdCIsInByZXBlbmQiLCJsb2FkZXIiLCJyZW1vdmVMb2FkZXIiLCJyZW1vdmUiLCJlbmFibGVkIiwicmVtb3ZlQXR0ciIsImhlYWRlckNoYXQiLCJpbnRlcmxvY3V0ZXVyIiwiZmFkZUluIiwiYXR0ciIsImludGVybG9jdXRldXJBdmF0YXIiLCJpbnRlcmxvY3V0ZXVyUHJvZmlsZSIsImh0bWwiLCJpbnRlcmxvY3V0ZXVyTmFtZSIsImVtaXQiLCJmaXJzdG5hbWUiLCJsYXN0bmFtZSIsIm9uIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImNvbnNvbGUiLCJsb2ciLCJncm91cElkIiwidmFsIiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsInByb3AiLCJwYXJzZUludCIsInN1Ym1pdCIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJjc3MiLCJ0byIsImdyb3VwZSIsImFqYXgiLCJ1cmwiLCJSb3V0aW5nIiwiZ2VuZXJhdGUiLCJtZXRob2QiLCJiZWZvcmVTZW5kIiwic3VjY2VzcyIsInJlc3VsdGF0IiwiY2xpY2siLCJtZXNzYWdlU2FuaXRhemUiLCJtZW50aW9uc0lucHV0IiwibWVkaWEiLCJsZW5ndGgiLCJmb3JtRGF0YSIsIkZvcm1EYXRhIiwiaSIsImNvbnRlbnRUeXBlIiwicHJvY2Vzc0RhdGEiLCJ0eXBlIiwiayIsInNjcm9sbCIsImxhc3RDaGF0IiwiZ3JvdXBlSWQiLCJjb21wbGV0ZSIsImFsZXJ0Il0sInNvdXJjZVJvb3QiOiIifQ==