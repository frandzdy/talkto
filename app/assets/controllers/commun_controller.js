import {Controller} from '@hotwired/stimulus';
import 'toastr'
import $ from "jquery";
import simpleParallax from 'simple-parallax-js';
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
    static targets = ['container', 'modal', 'profileFile', 'chatFile'];

    connect() {
        $(this.containerTarget)
            .on('click', 'a.open-front-modal', (event) => {
                event.preventDefault();
                const item = $(event.currentTarget);
                const href = item.attr('href');
                const title = item.data('modal-title');
                const size = item.data('lg-size');

                this.openModal(title, href, size);
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
            });
    }

    containerTargetConnected() {
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(this.successNoAccuracy, this.error, this.optionsNoAccuracy);
            navigator.geolocation.getCurrentPosition(this.successAccuracy, this.error, this.optionsAccuracy);
        }, 30000);
        // Scroll to top
        $(window).on('scroll', function () {
            if ($(window).scrollTop() > 200) {
                $('.scroll-to-top').removeClass('d-none');
            } else {
                $('.scroll-to-top').addClass('d-none');
            }
        });
    }

    /**
     * GPS COORD SAVE
     */
    optionsNoAccuracy = {
        enableHighAccuracy: false,
        timeout: 5000,
        maximumAge: 0
    };
    optionsAccuracy = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };
    successNoAccuracy = (pos) => {
        var cards = pos.coords;
        $.post(Routing.generate('user_save_coord', {
            'lat': cards.latitude,
            'lon': cards.longitude
        }));
    }
    successAccuracy = (pos) => {
        var cards = pos.coords;
        $.post(Routing.generate('user_save_coord', {
            'lat': cards.latitude,
            'lon': cards.longitude
        }));
    }
    saveMatch = () => {
        $.post(Routing.generate('app_generate_matches'));
    }
    checkCoordonate = () => {
        $.post(Routing.generate('user_check_coord'));
    }
    error = (err) => {
        console.warn(`Erreur (${err.code} : ${err.message})`);
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
     * Gestion des formulaires ajax
     */
    handleAjaxForm(target, data, action) {
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
                }

                if (response.message) {
                    toastr.success(response.message);
                }
            },
            error: function (response) {
                console.error(response);
                toastr.error("Une erreur est survenue.");
            }
        });
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
            $(this.modalTarget).modal('show');

        }).fail((error) => {
            toastr.error("Une erreur est survenue.");
        });
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
                        $($.parseHTML('<div>')).attr('id', 'imgPrev'+i).attr('style', 'width:' + canvas.width + 'height:' + canvas.height)
                            .css({'margin-right':'4px'})
                            .appendTo(placeToInsertImagePreview);
                        $($.parseHTML('<img>')).attr('src', canvas.toDataURL())
                            .addClass('img-fluid img-thumbnail float-end')
                            .appendTo('div#imgPrev'+i);
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
}
