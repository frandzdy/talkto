import {Controller} from '@hotwired/stimulus';
import Cookies from "js-cookie";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container', 'modal', 'modalProduct', 'alertSuccess']
    cart = []
    connect() {
        if(Cookies.get('geolocalisation') == undefined) {
            $("#geolocalisation").removeClass('d-none')
        }
        this.handleToolTips()
        this.handleBsCustomInputFile($('[type=file]'))
        this.initPlugins()
        this.updateWidgetCart()

        $(this.containerTarget)
            .on('click', 'a.open-front-modal', (event) => {
                event.preventDefault()
                const item = $(event.currentTarget)
                const href = item.attr('href')
                const title = item.data('modal-title')
                const size = item.data('lg-size')
                $(this.modalTarget).modal('hide')
                $(this.modalProductTarget).modal('hide')
                this.openModal(title, href, size)
            })
            .on('click', 'a.open-product-modal', (event) => {
                event.preventDefault()
                const item = $(event.currentTarget)
                const href = item.attr('href')
                const title = item.data('modal-title')
                const size = item.data('lg-size')

                this.openProductModal(title, href, size)
            })
            .on('click', 'a.post-confirm', (event) => {
                // Liens d'actions avec confirmation
                event.preventDefault()
                const item = $(event.currentTarget)
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
                })
            })
            .on('click', 'a.post-confirm-product', (event) => {
                // Liens d'actions avec confirmation
                event.preventDefault()
                const item = $(event.currentTarget)
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
                                this.postAjaxUrlProduct(item.attr('href'))
                            }
                        },
                        close: {
                            text: "Annuler"
                        }
                    }
                });
            })
            .on('click', 'a.add-product-cart', (event) => {
                event.preventDefault()
                event.stopPropagation()
                $("#productForm").submit()
            })
            .on('click', 'div.update-product-cart-minus', (event) => {
                event.preventDefault()
                event.stopPropagation()
                this.updateCart()
            })
            .on('click', 'div.update-product-cart-plus', (event) => {
                event.preventDefault()
                event.stopPropagation()
                this.updateCart()
            })
            .on('blur', 'input.reservation-date', (event) => {
                event.preventDefault()
                event.stopPropagation()
                this.updateCart()
            })
            .on('click', 'a.update-cart', (event) => {
                event.preventDefault()
                event.stopPropagation()
                this.submitUpdateCart(event)
                this.updateWidgetCart()
            })
    }

    onStartLocalisation() {
        navigator.geolocation.getCurrentPosition(this.successAccuracy, this.error, this.optionsAccuracy);
        $("#geolocalisation").remove()
    }
    /**
     * GPS COORD SAVE
     */
    optionsAccuracy = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };

    successAccuracy = (pos) => {
        const cards = pos.coords
        $.post(Routing.generate('front_user_save_coord', {
            'lat': cards.latitude,
            'lon': cards.longitude
        })).done(_ => {
            Cookies.set('geolocalisation', true, {expires: 7})
        })
        clearTimeout(this.timeOut)
        toastr.success('activé', 'Géolocalisation')
    }

    error = (err) => {
        clearTimeout(this.timeOut)
        Cookies.set('geolocalisation', false, {expires: 7})
    }
    updateCart() {
        this.cart = [];
        let listProducts = $('#cart-products').find('tr');
        for (let i = 0; i < listProducts.length; i++) {
            let quantity = $(listProducts[i]).find('#quantity').val()
            let token = $(listProducts[i]).find('#token').val()
            let startDate = $(listProducts[i]).find('#startDate').val()
            this.cart.push({token: token, quantity: quantity, startDate: startDate})
        }
    }

    submitUpdateCart() {
        $.post({
            url: Routing.generate('front_cart_update'),
            data: {'carts': JSON.stringify(this.cart)}
        }).done(function (data) {
            Turbo.visit(Routing.generate('front_cart_index'), {action: "replace"})
        });
    }

    updateWidgetCart() {
        setTimeout(function () {
            $.get(Routing.generate('front_cart_widget'))
                .done(function (data) {
                    $('.mini-cart').html(data.response);
                    $('#cart-length').html(data.totalQuantity);
                });
        }, 2000)
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

    postAjaxUrlProduct(url) {
        $.post({
            url: url,
            type: 'post',
        }).done(function (data) {
            $('#nav-' + data.token + '-tab').remove();
            $('#nav-' + data.token).remove();
        });
    }

    /**
     * Gestion des fomulaires ajax
     */
    handleAjaxForm(target, data, action) {
        try {
            let $btn = $('#submit-btn');
            $btn
                .html(
                    '<img style="width: 50px;" src="' + $btn.data('loading-img') + '" alt="Envoi en cours"> Envoi en cours'
                )
                .attr('disabled', 'disabled');
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
                        $btn.html($btn.data('title')).removeAttr('disabled');
                        return false;
                    }

                    if (!response.success) {
                        if ($(target).hasClass('modal')) {
                            $(target).find('.wrapper').html($(response));
                            this.handleModalForm(target);
                            this.handleBsCustomInputFile($(target).find('[type="file"]'));
                            this.handleToolTips()
                        } else if (!response.template) {
                            $(target).html($(response));
                        }
                        $btn.html($btn.data('title')).removeAttr('disabled');
                        return false;
                    }

                    if (response.success && response.redirectUrl) {
                        toastr.success(response.message || 'Enregistrement effectué');
                        if (response.reload) {
                            window.location.reload(true)
                        }
                        Turbo.visit(response.redirectUrl, {action: "replace"});
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
                        $btn.html($btn.data('title')).removeAttr('disabled');
                    }
                },
                error: (response) => {
                    if (response.status === 422) {
                        if ($(target).hasClass('modal')) {
                            $(target).find('.wrapper').html(response.responseText);
                            this.handleModalForm(target);
                            this.handleBsCustomInputFile($(target).find('[type="file"]'));
                            this.handleToolTips()
                        } else if (!response.template) {
                            $(target).html($(response));
                        }
                        $btn.html($btn.data('title')).removeAttr('disabled');
                    } else {
                        $btn.html($btn.data('title')).removeAttr('disabled');
                        toastr.error("Une erreur est survenue.");
                    }
                }
            });
        } catch (e) {
            $btn.html($btn.data('title')).removeAttr('disabled');
            toastr.error("Une erreur est survenue.");
        }
    }

    /**
     *
     * @param title
     * @param href
     * @param size
     */
    openModal(title, href, size) {
        $.get(href).done((response) => {
            if (title) {
                $(this.modalTarget).find('.modal-title').html(title);
            }
            if (size === true) {
                $(this.modalTarget).find('.modal-dialog').addClass('modal-lg')
            } else {
                $(this.modalTarget).find('.modal-dialog').removeClass('modal-lg')
            }
            $(this.modalTarget).find('.wrapper').html(response);
            this.handleModalForm(this.modalTarget);
            this.handleBsCustomInputFile($(this.modalTarget).find('[type="file"]'))
            this.handleToolTips()
            $(this.modalTarget).find('.chat-history').animate({scrollTop: $(this.modalTarget).find('.chat-history').prop('scrollHeight')}, 500);
            $(this.modalTarget).modal('show')

        }).fail((error) => {
            toastr.error("Une erreur est survenue.")
        });
    }

    /**
     *
     * @param title
     * @param href
     * @param size
     */
    openProductModal(title, href, size) {
        $.get(href).done((response) => {
            if (title) {
                $(this.modalProductTarget).find('.modal-title').html(title)
            }
            if (size === true) {
                $(this.modalProductTarget).find('.modal-dialog').addClass('modal-lg')
            }
            $(this.modalProductTarget).find('.wrapper').html(response)
            this.handleModalForm(this.modalProductTarget)
            this.handleBsCustomInputFile($(this.modalProductTarget).find('[type="file"]'))
            this.handleToolTips()
            $(this.modalProductTarget).modal({
                focus: false
            })
            $(this.modalProductTarget).modal('show')
        }).fail((error) => {
            toastr.error("Une erreur est survenue.")
        });
    }

    /**
     *
     */
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

    handleToolTips() {
        $('[data-toggle="tooltip"]').tooltip();
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

    handleBsCustomInputFile(container) {
        if ($(container)) {
            bsCustomFile.init();
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
                infinite: true,
                autoplay: true,
                swipe: true,
                lazyLoad: 'ondemand',
                autoplaySpeed: 8000,
                dots: false,
                fade: true,
                arrows: false,
                mobileFirst: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
                prevArrow: '<button type="button" class="slick-prev"><i class="fal fa-angle-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fal fa-angle-right"></i></button>',
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
                swipe: true,
                autoplaySpeed: 8000,
                dots: false,
                lazyLoad: 'ondemand',
                fade: true,
                arrows: true,
                prevArrow: '<button type="button" class="slick-prev"><i class="fal fa-angle-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fal fa-angle-right"></i></button>',
                responsive: [{
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: false
                    }
                }, {
                    breakpoint: 480,
                    settings: {
                        arrows: false,
                        centerMode: true,
                        slidesToShow: 1,
                        infinite: true,
                        dots: false
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
            dots: false,
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
        $('.product__slider').owlCarousel({
            loop: false,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
            margin: 30,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
            margin: 0,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 500,
            items: 6,
            navText: ['<button><i class="fa fa-angle-left"></i>Précedent</button>', '<button>Suivant<i class="fa fa-angle-right"></i></button>'],
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
            loop: false,
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
            loop: false,
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
        const minAmount = $("#amount-value").val() ? $("#amount-value").val().split('-')[0] : 0;
        const maxAmount = $("#amount-value").val() ? $("#amount-value").val().split('-')[1] : 500;
        // 22. Range Slider Js
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 10000,
            values: [minAmount, maxAmount],
            slide: function (event, ui) {
                $("#amount").val(ui.values[0] + " € - " + ui.values[1] + " €");
                $("#amount-value").val(ui.values[0] + "-" + ui.values[1])
            }
        });

        $("#amount").val($("#slider-range").slider("values", 0) +
            " € - " + $("#slider-range").slider("values", 1) + " €");
        $("#amount-value").val($("#slider-range").slider("values", 0) +
            "-" + $("#slider-range").slider("values", 1))

        // 22.bis Range Slider Js distance
        const minDistance = $("#distance-value").val() ? $("#distance-value").val().split('-')[0] : 0;
        const maxdistance = $("#distance-value").val() ? $("#distance-value").val().split('-')[1] : 5;
        $("#slider-range-distance").slider({
            range: true,
            min: 0,
            max: 100,
            values: [minDistance, maxdistance],
            slide: function (event, ui) {
                $("#distance").val(ui.values[0] + " Km - " + ui.values[1] + " Km");
                $("#distance-value").val(ui.values[0] + "-" + ui.values[1]);
            }
        });

        $("#distance").val($("#slider-range-distance").slider("values", 0) +
            " Km - " + $("#slider-range-distance").slider("values", 1) + " Km");
        $("#distance-value").val($("#slider-range-distance").slider("values", 0) +
            "-" + $("#slider-range-distance").slider("values", 1))
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
        $('#user_payment_isGuess').on('click', function () {
            $('#cbox_info').slideToggle(900);
        });

        if ($('#user_payment_isGuess').attr('checked') === 'checked') {
            $('#cbox_info').slideToggle(900);
        }
        ////////////////////////////////////////////////////
        // 26. Shipping Box Toggle Js
        $('#ship-box').on('click', function () {
            $('#ship-box-info').slideToggle(1000);
        });

        ////////////////////////////////////////////////////
        // 27. product__slider-active Js ( home 7 )
        $('.product__slider-active').owlCarousel({
            loop: false,
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
            loop: false,
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
            loop: false,
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
            loop: false,
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

    onSortedBy(e) {
        $('#categoryForm').submit();
    }

    onSearchSortedBy(e) {
        $('#searchProductForm').submit();
    }
}
