import {Controller} from '@hotwired/stimulus';
import 'toastr'
import $ from "jquery";
import bsCustomFileInput from "bs-custom-file-input";

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
    static targets = ['container', 'modal', 'modalProduct', 'alertSuccess'];

    connect() {
        let cart = []
        this.handleBsCustomFileInput($('[type=file]'))
        this.initPlugins();
        this.updateWidgetCart();
        $(this.containerTarget)
            .on('click', 'a.open-front-modal', (event) => {
                event.preventDefault();
                const item = $(event.currentTarget);
                const href = item.attr('href');
                const title = item.data('modal-title');
                const size = item.data('lg-size');

                this.openModal(title, href, size);
            })
            .on('click', 'a.open-product-modal', (event) => {
                event.preventDefault();
                const item = $(event.currentTarget);
                const href = item.attr('href');
                const title = item.data('modal-title');
                const size = item.data('lg-size');

                this.openProductModal(title, href, size);
            })
            .on('click', 'a.post-confirm', (event) => {
                // Liens d'actions avec confirmation
                event.preventDefault();
                const item = $(event.currentTarget);
                $.confirm({
                    title: item.data('title'),
                    content: item.data('confirm-message'),
                    type: item.data('type') || 'red',
                    typeAnimated: true,
                    buttons: {
                        confirm: {
                            text: item.data('button-text'),
                            btnClass: item.data('btn-class') || 'btn-red',
                            action: () => {
                                this.postUrl(item.attr('href'))
                            }
                        },
                        close: {
                            text: "Annuler"
                        }
                    }
                });
            })
            .on('click', 'a.add-product-cart', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.addToCart();
                this.updateWidgetCart();
                this.closeProductModal();
            })
            .on('click', 'div.update-product-cart-minus', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.updateCart(event);
                this.updateWidgetCart();
            })
            .on('click', 'div.update-product-cart-plus', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.updateCart(event);
                this.updateWidgetCart();
            })
            .on('blur', 'input.reservation-date', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.updateCart(event);
            })
            .on('click', 'a.update-cart', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.submitUpdateCart(event);
                this.updateWidgetCart();
            })
            .on('click', 'a.checkout-login', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.login();
            });

    }
    login() {

    }
    addToCart() {
        const productForm = $('#productForm');
        const token = productForm.find('#token').val();
        const quantity = productForm.find('#quantity').val()
        const startDate = productForm.find('#startDate').val()
        if (!quantity || !startDate) {
            toastr.error('Vérifiez vos quantités et vos dates de réservation !');
        } else {
            $.post({
                url: Routing.generate('front_product_add_cart'),
                type: 'post',
                data: {'token': token, 'quantity': quantity, 'startDate': startDate},
                success: function (data) {
                    $('#cart-length').html(data.totalQuantity);
                    toastr.success('Produit(s) ajouté(s) !');
                }
            });
            this.cart = {'token': token, 'quantity': quantity, 'startDate': startDate}
        }
    }
    updateCart (event) {
        let quantity = $(event.currentTarget).closest('tr').find('#quantity').val()
        let token = $(event.currentTarget).closest('tr').find('#token').val()
        let startDate = $(event.currentTarget).closest('tr').find('#startDate').val()
        if (quantity) {
            this.cart = {'token': token, 'quantity': quantity, 'startDate': startDate}
        } else {
            //this.cart = this.cart.filter(item => item.token !== token)
        }
        console.log(this.cart)
    }

    submitUpdateCart()
    {
        $.post({
            url: Routing.generate('front_cart_update'),
            data: {'carts': this.cart}
        }).done(function (data) {
            Turbo.visit(Routing.generate('front_cart_index'), { action: "replace" })
        });
    }
    removeFromCart() {
        const productForm = $('#productForm');
        const token = productForm.find('#token').val();
        const quantity = productForm.find('#quantity').val()
        const startDate = productForm.find('#startDate').val()
        if (!quantity || !startDate) {
            toastr.error('Vérifiez vos quantités et vos dates de réservation !');
        } else {
            $.post({
                url: Routing.generate('front_product_add_cart'),
                type: 'post',
                data: {'token': token, 'quantity': quantity, 'startDate': startDate}
            }).done(function (data) {
                toastr.success('Produit(s) supprimé(s) !');
                $('#cart-length').html(data.totalQuantity)
            });
            this.updateWidgetCart();
        }
    }

    updateWidgetCart () {
        $.get(Routing.generate('front_cart_widget'))
            .done(function(data) {
            $('.mini-cart').html(data.response);
            $('#cart-length').html(data.totalQuantity);
        });
    }

    /**
     * Permet de simuler un POST sur une URL
     */
    postUrl(url) {
        $('<form></form>')
            .attr('action', url)
            .attr('id', 'form-confirm')
            .attr('method', 'POST')
            .appendTo('body');

        $('#form-confirm').submit();
    }

    /**
     * Gestion des fomulaires ajax
     */
    handleAjaxForm(target, data, action) {
        try {
            $.ajax({
                type: "POST",
                url: action,
                enctype: 'multipart/form-data',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success: (response) => {
                    if (response.template) {
                        $(target).html($(response.template));
                    }

                    if (response.error) {
                        toastr.error(response.error);

                        return false;
                    }

                    if (!response.success) {
                        if ($(target).hasClass('modal')) {
                            $(target).find('.wrapper').html($(response));
                            this.handleModalForm(target);
                            this.handleBsCustomFileInput($(target).find('.custom-file-input[type="file"]'));
                        } else if (!response.template) {
                            $(target).html($(response));
                        }

                        return false;
                    }

                    if (response.success && response.redirectUrl) {
                        document.location = response.redirectUrl;
                        document.location.reload();
                        return false;
                    }

                    if (response.success && response.callback) {
                        if (response.callbackData) {
                            window[response.callback](response.callbackData)
                        } else {
                            window[response.callback]();
                        }
                        $(this.modalTarget).modal('hide');
                        $(this.modalProductTarget).modal('hide');
                    }

                    if (response.message) {
                        toastr.success(response.message);
                    }
                },
                error: function (response) {
                    console.error(response.responseText);
                    if (response.status === 422) {
                        if ($(target).hasClass('modal')) {
                            $(target).find('.wrapper').html(response.responseText);
                            this.handleModalForm(target);
                        } else if (!response.template) {
                            $(target).html($(response));
                        }
                    } else {
                        toastr.error("Une erreur est survenue.");
                    }
                }
            });
        } catch (e) {
            console.log(e);
            //$(target).html();
        }
    }

    openModal(title, href, size) {
        $.get(href).done((response) => {
            if (title) {
                $(this.modalTarget).find('.modal-title').html(title);
            }
            if (size == true) {
                $(this.modalTarget).find('.modal-dialog').addClass('modal-lg');
            }
            $(this.modalTarget).find('.wrapper').html(response);
            this.handleModalForm(this.modalTarget);
            this.handleBsCustomFileInput($(this.modalTarget).find('.custom-file-input[type="file"]'));
            $(this.modalTarget).modal('show');

        }).fail((error) => {
            toastr.error("Une erreur est survenue.");
        });
    }

    openProductModal(title, href, size) {
        $.get(href).done((response) => {
            if (title) {
                $(this.modalProductTarget).find('.modal-title').html(title);
            }
            if (size == true) {
                $(this.modalProductTarget).find('.modal-dialog').addClass('modal-lg');
            }
            $(this.modalProductTarget).find('.wrapper').html(response);
            this.handleModalForm(this.modalProductTarget);
            $(this.modalProductTarget).modal('show');
        }).fail((error) => {
            toastr.error("Une erreur est survenue.");
        });
    }

    closeProductModal() {
       $(this.modalProductTarget).modal('hide');
    }

    hidePageLoader() {
        return $('[id=page-loader]').addClass('d-none');
    };

    /**
     * Traitement des formulaires en modale
     * @param target
     */
    handleModalForm(target) {
        $(target).find('form').on('submit', (event) => {
            event.preventDefault();

            const data = new FormData($(event.currentTarget)[0]);
            const action = $(event.currentTarget).attr('action');

            this.handleAjaxForm(target, data, action);
        });
    };

    /**
     * Permet de custom les input file bootstrap
     */

    imagesPreview(input, placeToInsertImagePreview) {
        if (input.files) {
            const filesAmount = input.files.length;
            const filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
            for (let i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = (event) => {
                    const image = new Image();

                    image.onload = function () {
                        const canvas = document.createElement("canvas");
                        const context = canvas.getContext("2d");
                        const max_size = 198;
                        let width = image.width;
                        let height = image.height;
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                        canvas.width = width;
                        canvas.height = height;
                        context.drawImage(image,
                            0,
                            0,
                            image.width,
                            image.height,
                            0,
                            0,
                            canvas.width,
                            canvas.height
                        );
                        $($.parseHTML('<div>')).attr('id', 'imgPrev' + i).attr('style', 'width:' + canvas.width + 'height:' + canvas.height)
                            .css({'margin-right': '4px'})
                            .appendTo(placeToInsertImagePreview);
                        $($.parseHTML('<img>')).attr('src', canvas.toDataURL())
                            .addClass('img-fluid img-thumbnail float-end')
                            .appendTo('div#imgPrev' + i);
                    }
                    image.src = event.target.result;
                }
                if (!filterType.test(input.files[i].type)) {
                    alert("Please select a valid image.");
                    return;
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    previewProfileFile() {
        var input = this.profileFileTarget;
        $('div#previewFile').fadeOut('slow');
        $('#previewFile').remove();
        var el = $('<div id="previewFile" class="previewFile"></div>');
        $('#file').append(el);
        this.imagesPreview(input, 'div#previewFile');
        $('div#previewFile').fadeIn('slow');
    }

    previewChatFile() {
        var input = this.chatFileTarget;
        $('div#previewFile').fadeOut();
        $('#previewFile').remove();
        var el = $('<div id="previewFile" class="previewFile"></div>');
        $('#receiver').append(el);
        this.imagesPreview(input, 'div#previewFile');
        $('div#previewFile').fadeIn('slow');
    }

    /**
     * Callback button target alertSuccess
     */
    alertSuccessTargetConnected() {
        setTimeout(() => {
            if ($(this.alertSuccessTarget).css('display') == "block") {
                $(this.alertSuccessTarget).hide('slideUp');
            }
        }, 5000);
    }

    handleBsCustomFileInput(container) {
        if ($(container)) {
            bsCustomFileInput.init();
            $(container).change(function () {
                var fieldVal = $(this).val();
                if (fieldVal != undefined || fieldVal != "") {
                    $(this).next(".custom-file-label").text(fieldVal);
                }
            });
        }
    }

    initPlugins() {
        ////////////////////////////////////////////////////
        // 01. PreLoader Js
        $("#loading").fadeOut(2000);

        ////////////////////////////////////////////////////
        // 02. Search Js
        $(".search-toggle").on("click", function () {
            $(".header__search").addClass("search-opened");
            $(".body-overlay").addClass("opened");
        });
        $(".header__search-btn-close").on("click", function () {
            $(".header__search").removeClass("search-opened");
            $(".body-overlay").removeClass("opened");
        });
        $(".body-overlay").on("click", function () {
            $(".header__search").removeClass("search-opened");
            $(".body-overlay").removeClass("opened");
        });

        ////////////////////////////////////////////////////
        // 03. Info Bar Js
        $(".mobile-menu-toggle").on("click", function () {
            $(".extra__info").addClass("info-opened");
            $(".body-overlay").addClass("opened");
        });
        $(".extra__info-close-btn").on("click", function () {
            $(".extra__info").removeClass("info-opened");
            $(".body-overlay").removeClass("opened");
        });
        $(".body-overlay").on("click", function () {
            $(".extra__info").removeClass("info-opened");
            $(".body-overlay").removeClass("opened");
        });


        ////////////////////////////////////////////////////
        // 04. Sticky Header Js
        $(window).on('scroll', function () {
            var scroll = $(window).scrollTop();
            if (scroll < 100) {
                $("#header-sticky").removeClass("sticky");
                $("#header__transparent").removeClass("transparent-sticky");
            } else {
                $("#header-sticky").addClass("sticky");
                $("#header__transparent").addClass("transparent-sticky");
            }
        });

        ////////////////////////////////////////////////////
        // 05. Data-Background Js
        $("[data-background]").each(function () {
            $(this).css("background-image", "url( " + $(this).attr("data-background") + "  )");
        });


        ////////////////////////////////////////////////////
        // 06. Mobile Menu Js
        $('#mobile-menu-active').metisMenu();

        $('#mobile-menu-active .has-dropdown > a').on('click', function (e) {
            e.preventDefault();
        });

        ////////////////////////////////////////////////////
        // 07. Scroll To Top Js
        function smoothSctollTop() {
            $('.smooth-scroll a').on('click', function (event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 0
                    }, 1500);
                }
            });
        }

        smoothSctollTop();

        // Show or hide the sticky footer button
        $(window).on('scroll', function (event) {
            if ($(this).scrollTop() > 600) {
                $('#scroll').fadeIn(200)
            } else {
                $('#scroll').fadeOut(200)
            }
        });

        //Animate the scroll to yop
        $('#scroll').on('click', function (event) {
            event.preventDefault();

            $('html, body').animate({
                scrollTop: 0,
            }, 1500);
        });

        ////////////////////////////////////////////////////
        // 08. Hero Slider Js
        function mainSlider() {
            var BasicSlider = $('.slider-active');
            BasicSlider.on('init', function (e, slick) {
                var $firstAnimatingElements = $('.single-slider:first-child').find('[data-animation]');
                doAnimations($firstAnimatingElements);
            });
            BasicSlider.on('beforeChange', function (e, slick, currentSlide, nextSlide) {
                var $animatingElements = $('.single-slider[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
                doAnimations($animatingElements);
            });
            BasicSlider.slick({
                autoplay: true,
                autoplaySpeed: 8000,
                dots: true,
                fade: true,
                arrows: false,
                prevArrow: '<button type="button" class="slick-prev"><i class="fal fa-angle-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fal fa-angle-right"></i></button>',
                responsive: [{
                    breakpoint: 767,
                    settings: {
                        dots: false,
                        arrows: false
                    }
                }]
            });

            function doAnimations(elements) {
                var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                elements.each(function () {
                    var $this = $(this);
                    var $animationDelay = $this.data('delay');
                    var $animationType = 'animated ' + $this.data('animation');
                    $this.css({
                        'animation-delay': $animationDelay,
                        '-webkit-animation-delay': $animationDelay
                    });
                    $this.addClass($animationType).one(animationEndEvents, function () {
                        $this.removeClass($animationType);
                    });
                });
            }
        }

        mainSlider();

        ////////////////////////////////////////////////////
        // 08. Hero Slider Js
        function mainSlider2() {
            var BasicSlider = $('.slider-active-3');
            BasicSlider.on('init', function (e, slick) {
                var $firstAnimatingElements = $('.single-slider:first-child').find('[data-animation]');
                doAnimations($firstAnimatingElements);
            });
            BasicSlider.on('beforeChange', function (e, slick, currentSlide, nextSlide) {
                var $animatingElements = $('.single-slider[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
                doAnimations($animatingElements);
            });
            BasicSlider.slick({
                autoplay: true,
                autoplaySpeed: 8000,
                dots: true,
                fade: true,
                arrows: true,
                prevArrow: '<button type="button" class="slick-prev"><i class="fal fa-angle-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fal fa-angle-right"></i></button>',
                responsive: [{
                    breakpoint: 767,
                    settings: {
                        dots: false,
                        arrows: false
                    }
                }]
            });

            function doAnimations(elements) {
                var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                elements.each(function () {
                    var $this = $(this);
                    var $animationDelay = $this.data('delay');
                    var $animationType = 'animated ' + $this.data('animation');
                    $this.css({
                        'animation-delay': $animationDelay,
                        '-webkit-animation-delay': $animationDelay
                    });
                    $this.addClass($animationType).one(animationEndEvents, function () {
                        $this.removeClass($animationType);
                    });
                });
            }
        }

        mainSlider2();


        ////////////////////////////////////////////////////
        // 09. Testimonial Js
        $('.testimonial__wrapper').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            dots: true,
            asNavFor: '.testimonial__nav',

        });
        $('.testimonial__nav').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            asNavFor: '.testimonial__wrapper',
            dots: false,
            centerMode: true,
            centerPadding: 0,
            focusOnSelect: true,
            arrows: false,
            prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fas fa-angle-right"></i></button>',
            responsive: [
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        ////////////////////////////////////////////////////
        // 10. Product Slider Js
        $('.product__slider ').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 4
                },
                1600: {
                    items: 4
                }
            }
        });


        ////////////////////////////////////////////////////
        // 11. Product Slider 2 Js ( home page 2 )
        $('.product__slider-2 ').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 2
                },
                992: {
                    items: 2
                },
                1200: {
                    items: 2
                },
                1600: {
                    items: 3
                }
            }
        });


        ////////////////////////////////////////////////////
        // 12. Product Slider 3 Js ( home page 2 )
        $('.product__slider-3').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 2
                },
                992: {
                    items: 2
                },
                1200: {
                    items: 2
                },
                1600: {
                    items: 2
                }
            }
        });


        ////////////////////////////////////////////////////
        // 13. Product Slider 4 Js ( home page 4 )
        $('.product__slider-4').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 4
                },
                1600: {
                    items: 5
                }
            }
        });


        ////////////////////////////////////////////////////
        // 14. Sale Slider Js
        $('.sale__area-slider ').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 5
                },
                1600: {
                    items: 5
                }
            }
        });


        ////////////////////////////////////////////////////
        // 15. Sale Slider 2 Js  ( home page 2 )
        $('.sale__area-slider-2 ').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 5
                },
                1600: {
                    items: 6
                }
            }
        });


        ////////////////////////////////////////////////////
        // 16. Client Slider Js
        $('.client__slider').owlCarousel({
            loop: true,
            margin: 0,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>PREV</button>', '<button>NEXT<i class="fa fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                767: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 5
                },
                1600: {
                    items: 5
                }
            }
        });


        ////////////////////////////////////////////////////
        // 17. Blog Slider Js
        $('.blog__slider').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fal fa-angle-left"></i></button>', '<button><i class="fal fa-angle-right"></i></button>'],
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 3
                },
                1600: {
                    items: 3
                }
            }
        });

        ////////////////////////////////////////////////////
        // 18. Product Offer SLider Js ( home 2 )
        $('.product__offer-slider').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fal fa-angle-left"></i></button>', '<button><i class="fal fa-angle-right"></i></button>'],
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 1
                },
                992: {
                    items: 1
                },
                1200: {
                    items: 1
                },
                1600: {
                    items: 1
                }
            }
        });


        ////////////////////////////////////////////////////
        // 19. Masonary Js
        $('.grid').imagesLoaded(function () {
            // init Isotope
            var $grid = $('.grid').isotope({
                itemSelector: '.grid-item',
                percentPosition: true,
                masonry: {
                    // use outer width of grid-sizer for columnWidth
                    columnWidth: '.grid-item',
                }
            });


            // filter items on button click
            $('.masonary-menu').on('click', 'button', function () {
                var filterValue = $(this).attr('data-filter');
                $grid.isotope({filter: filterValue});
            });

            //for menu active class
            $('.masonary-menu button').on('click', function (event) {
                $(this).siblings('.active').removeClass('active');
                $(this).addClass('active');
                event.preventDefault();
            });

        });


        ////////////////////////////////////////////////////
        // 20. WoW Js
        new WOW().init();

        ////////////////////////////////////////////////////
        // 21. Cart Plus Minus Js
        $(".cart-plus-minus-cart").append('<div class="dec qtybutton update-product-cart-minus">-</div><div class="inc qtybutton update-product-cart-plus">+</div>');
        $(".cart-plus-minus").append('<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>');
        $(".qtybutton").on("click", function () {
            var $button = $(this);
            var oldValue = $button.parent().find("input").val();
            let max = $button.parent().find("input").data('max');
            let min = $button.parent().find("input").data('min');
            if ($button.text() == "+") {
                var newVal = parseFloat(oldValue) + 1;
            } else {
                // Don't allow decrementing below zero
                if (oldValue > 0) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 0;
                }
            }
            $button.parent().find("input").val(newVal);
        });


        ////////////////////////////////////////////////////
        // 22. Range Slider Js
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 500,
            values: [75, 300],
            slide: function (event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
            }
        });

        $("#amount").val("$" + $("#slider-range").slider("values", 0) +
            " - $" + $("#slider-range").slider("values", 1));


        ////////////////////////////////////////////////////
        // 23. Show Login Toggle Js
        $('#showlogin').on('click', function () {
            $('#checkout-login').slideToggle(900);
        });

        ////////////////////////////////////////////////////
        // 24. Show Coupon Toggle Js
        $('#showcoupon').on('click', function () {
            $('#checkout_coupon').slideToggle(900);
        });

        ////////////////////////////////////////////////////
        // 25. Create An Account Toggle Js
        $('#cbox').on('click', function () {
            $('#cbox_info').slideToggle(900);
        });

        ////////////////////////////////////////////////////
        // 26. Shipping Box Toggle Js
        $('#ship-box').on('click', function () {
            $('#ship-box-info').slideToggle(1000);
        });

        ////////////////////////////////////////////////////
        // 27. product__slider-active Js ( home 7 )
        $('.product__slider-active').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fal fa-angle-left"></i></button>', '<button><i class="fal fa-angle-right"></i></button>'],
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 4
                },
                1600: {
                    items: 4
                }
            }
        });

        ////////////////////////////////////////////////////
        // 28. testimonial__slider-active Js ( home 7 )
        $('.testimonial__slider-active').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fal fa-angle-left"></i></button>', '<button><i class="fal fa-angle-right"></i></button>'],
            nav: false,
            dots: true,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 1
                },
                992: {
                    items: 1
                },
                1200: {
                    items: 1
                },
                1600: {
                    items: 1
                }
            }
        });

        ////////////////////////////////////////////////////
        // 28. blog__slider-active Js ( home 7 )
        $('.blog__slider-active').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fal fa-angle-left"></i></button>', '<button><i class="fal fa-angle-right"></i></button>'],
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 1
                },
                992: {
                    items: 2
                },
                1200: {
                    items: 2
                },
                1600: {
                    items: 2
                }
            }
        });

        ////////////////////////////////////////////////////
        // 28. brand__slider-active Js ( home 7 )
        $('.brand__slider-active').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fal fa-angle-left"></i></button>', '<button><i class="fal fa-angle-right"></i></button>'],
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 5
                },
                1600: {
                    items: 5
                }
            }
        });
    }
}
