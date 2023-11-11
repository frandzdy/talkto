
const modal = $('#back-modal');
const container = $('#page-container');

$('body').on('click', '.password-show-and-hide a', function(e) {
    e.preventDefault();

    let $btn = $(this);
    let $input = $(this).closest('.password-show-and-hide').find('input');
    let $img = $btn.find('i');

    if ($btn.hasClass('show')) {
        $btn.removeClass('show');
        $input.attr('type', 'password');
    } else {
        $btn.addClass('show');
        $input.attr('type', 'text');
    }

    $img.toggleClass( "fa-eye-slash" );
    $img.toggleClass( "fa-eye" );
});
