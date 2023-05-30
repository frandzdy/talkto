import { Controller } from '@hotwired/stimulus';
import $ from "jquery";
import bsCustomFileInput from "bs-custom-file-input";
/**
 * Gestion des justificatifs
 */
export default class extends Controller {
    /**
     * Initialise la liste des photos avec un element si elle est vide
     */
    connect() {
        if ($('.manager-collection').children().length == 0) {
            this.onFileAdd();
        }
    }

    /**
     * Ajout d'un champ photo
     */
    onFileAdd(event) {
        const collectionHolder = $('.manager-collection');
        const listIndex = collectionHolder.find('input').length + 1;
        if (listIndex <= 5) {
            collectionHolder.append(collectionHolder.data('prototype').replace(/__name__/g, listIndex));
        }
        this.handleBsCustomFileInput(collectionHolder.find('input[type=file]'));
    }

    /**
     * A la suppression d'un widget fichier
     */
    onFileDelete(e) {
        const elt = e.target;
        $.confirm({
            title: 'Suppression d\'une photo',
            content: 'Souhaitez-vous supprimer ce photo ?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Supprimer',
                    btnClass: 'btn-red',
                    action: () => {
                         elt.parentElement.parentElement.remove();
                    }
                },
                close: {
                    text: "Annuler"
                }
            }
        });
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

    /**
     * Recharge la modale de panier en fonction des données en session
     */
    reloadCartModal(openOnReload = false) {
        $.get(
            Routing.generate('front_cart_modal'),
            null,
            function(data) {
                $('#cart-dialog').html(data);
                $('#cart-size').text($('#cart-dialog .item').length);
            }
        )
    }

    /**
     * Envoie la demande de suppression d'un article du panier
     * Récupère les nouveaux prix
     */
    deleteItem(qualificationSageCode, callback) {
        $.post(
            Routing.generate('front_cart_item_remove', {"sageCode": qualificationSageCode}),
            null,
            callback
        );
    }

    /**
     * Retourne les prix du panier
     */
    getPrices(callback) {
        $.get(
            Routing.generate('front_cart_prices'),
            null,
            callback
        );
    }

    /**
     * Enregistre le cookie de panier
     */
    setCartCookie(value) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
        document.cookie = 'cart=' + value + ';path=/;expires=' + expires.toUTCString();
    }

    deleteCartCookie() {
        var expires = new Date();
        expires.setTime(expires.getTime() + 1);
        document.cookie = 'cart=;path=/;expires=1' + expires.toUTCString();
    }
}
