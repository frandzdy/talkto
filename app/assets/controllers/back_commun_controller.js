import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container', 'modal'];
    connect() {
        $.when($('#page-loader').addClass('d-none')).done(() => {
            $('#page-container').addClass('show');
        });

        this.handleBsCustomInputFile($('[type=file]'));
        $('[data-toggle="tooltip"]').tooltip();
        $(this.containerTarget)
            .on('click', 'a.open-back-modal', (event) => {
                event.preventDefault();
                const item = $(event.currentTarget);
                const href = item.attr('href');
                const title = item.data('modal-title');
                const size = item.data('lg-size');
                $(this.modalTarget).modal('hide');
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
            })
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
                            this.handleBsCustomInputFile($(target).find('[type="file"]'));
                        } else if (!response.template) {
                            $(target).html($(response));
                        }

                        return false;
                    }

                    if (response.success && response.redirectUrl) {
                        toastr.success('Enregistrement effectué');
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
                    }
                },
                error:  (response) => {
                    if (response.status === 422) {
                        if ($(target).hasClass('modal')) {
                            $(target).find('.wrapper').html(response.responseText);
                            this.handleModalForm(target);
                            this.handleBsCustomInputFile($(target).find('[type="file"]'));
                        } else if (!response.template) {
                            $(target).html($(response));
                        }
                    } else {
                        toastr.error("Une erreur est survenue.");
                    }
                }
            });
        } catch (e) {
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
                $(this.modalTarget).find('.modal-dialog').addClass('modal-lg');
            } else {
                $(this.modalTarget).find('.modal-dialog').removeClass('modal-lg');
            }
            $(this.modalTarget).find('.wrapper').html(response);
            this.handleModalForm(this.modalTarget);
            this.handleBsCustomInputFile($(this.modalTarget).find('[type="file"]'));
            $(this.modalTarget).find('.chat-history').animate({scrollTop: $(this.modalTarget).find('.chat-history').prop('scrollHeight')}, 500);
            $(this.modalTarget).modal('show');

        }).fail((error) => {
            toastr.error("Une erreur est survenue.");
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
        if (container) {
            bsCustomFile.init();
        }
    };
}
