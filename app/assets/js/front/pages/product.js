/**
 * Callback pour mettre à jour la liste des produits
 * @param data
 */
window.onProductChange = () => {
    toastr.success('Enregistrement effectué !');
    $.get(Routing.generate('front_product_update_list'), null, function (data) {
        $('#product-list').html(data);
    });
}