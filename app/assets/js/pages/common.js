// const container = $(document).find('#page-top');
// const modal = $(document).find('#front-modal');
//
// /**
//  * Permet de simuler un POST sur une URL
//  */
// global.postUrl = function (url) {
// 	$('<form></form>')
// 		.attr('action', url)
// 		.attr('id', 'form-confirm')
// 		.attr('method', 'POST')
// 		.appendTo('body');
//
// 	$('#form-confirm').submit();
// }
//
// /**
//  * Gestion des fomulaires ajax
//  */
// function handleAjaxForm(target, data, action) {
// 	$.ajax({
// 		type: "POST",
// 		url: action,
// 		enctype: 'multipart/form-data',
// 		data: data,
// 		processData: false,
// 		contentType: false,
// 		cache: false,
// 		success: function (response) {
// 			if (response.template) {
// 				$(target).html($(response.template));
// 			}
//
// 			if (response.error) {
// 				toastr.error(response.error);
//
// 				return false;
// 			}
//
// 			if (!response.success) {
// 				if ($(target).hasClass('modal')) {
// 					$(target).find('.wrapper').html($(response));
// 					handleModalForm(target);
// 				} else if (!response.template) {
// 					$(target).html($(response));
// 				}
//
// 				return false;
// 			}
//
// 			if (response.success && response.redirectUrl) {
// 				document.location = response.redirectUrl;
// 				document.location.reload();
// 				return false;
// 			}
//
// 			if (response.success && response.callback) {
// 				if (response.callbackData) {
// 					window[response.callback](response.callbackData)
// 				} else {
// 					window[response.callback]();
// 				}
// 				$(modal).modal('hide');
// 			}
//
// 			if (response.message) {
// 				toastr.success(response.message);
// 			}
// 		},
// 		error: function (response) {
// 			console.error(response);
// 			toastr.error("Une erreur est survenue.");
// 		}
// 	});
// }
//
// window.openModal = function (title, href, size) {
// 	$.get(href).done((response) => {
// 		if (title) {
// 			$(modal).find('.modal-title').html(title);
// 		}
// 		if (size == true) {
// 			$(modal).find('.modal-dialog').addClass('modal-lg');
// 		}
// 		$(modal).find('.wrapper').html(response);
// 		handleModalForm(modal);
// 		$(modal).modal('show');
//
// 	}).fail((error) => {
// 		console.log({error});
// 		toastr.error("Une erreur est survenue.");
// 	});
// }
// var hidePageLoader = function hidePageLoader() {
// 	return $('[id=page-loader]').addClass('d-none');
// };
// /**
//  * Traitement des formulaires en modale
//  * @param target
//  */
// const handleModalForm = (target) => {
// 	$(target).find('form').on('submit', function (event) {
// 		event.preventDefault();
//
// 		const data = new FormData($(this)[0]);
// 		const action = $(this).attr('action');
//
// 		handleAjaxForm(target, data, action);
// 	});
// };
//
// const imagesPreview = (input, placeToInsertImagePreview) => {
// 	if (input.files) {
// 		var filesAmount = input.files.length;
// 		for (i = 0; i < filesAmount; i++) {
// 			var reader = new FileReader();
// 			reader.onload = function (event) {
// 				$($.parseHTML('<img>')).attr('src', event.target.result)
// 					.addClass('img-fluid img-thumbnail float-end')
// 					.appendTo(placeToInsertImagePreview)
// 					.css(
// 						{
// 							'max-height': '230px',
// 							'max-width': '230px',
// 							'min-height': '230px',
// 							'min-width': '230px'
// 						}
// 					);
// 			}
// 			reader.readAsDataURL(input.files[i]);
// 		}
// 	}
// };
//
// $(function () {
// 	// container
// 	// 	// CollectionType Ajout et suppression
// 	// 	.on('click', '.add-collection-element', function (e) {
// 	// 		e.preventDefault();
// 	// 		var container = $('#' + $(this).data('target'));
// 	// 		var counter = container.data('widget-counter');
// 	// 		var newWidget = container.data('prototype');
// 	// 		newWidget = newWidget.replace(/__name__/g, counter);
// 	// 		counter++;
// 	// 		container.data('widget-counter', counter);
// 	// 		var newElem = jQuery(container.attr('data-widget-tags')).html(newWidget);
// 	// 		newElem.appendTo(container);
// 	//
// 	// 		handleBsCustomInputFile(newElem.find('[type=file]'));
// 	// 	})
// 	// 	.on('click', '.delete-collection-element', function (e) {
// 	// 		e.preventDefault();
// 	// 		$(this).closest('.collection-element').remove();
// 	// 	})
// 	// 	// Liens d'actions sans confirmation
// 	// 	.on('click', 'a.post-without-confirm', function (e) {
// 	// 		e.preventDefault();
// 	//
// 	// 		postUrl($(this).attr('href'));
// 	// 	})
// 	//
// 	// 	// Gestion du max en mode maxlength
// 	// 	.on('keydown', 'input[type=number]', function (e) {
// 	// 		if ($(this).attr('max')
// 	// 			&& Number($(this).attr('max')) < Number($(this).val() + e.key.toString())
// 	// 			&& e.key != 'Backspace') {
// 	// 			e.preventDefault();
// 	// 		}
// 	// 	})
// 	//
//
// 	// Ouverture d'un formulaire en modale
// 	container
// 		.on('click', 'a.open-front-modal', function (e) {
// 			e.preventDefault();
// 			const item = $(this);
// 			const href = item.attr('href');
// 			const title = item.data('modal-title');
// 			const size = item.data('lg-size');
//
// 			openModal(title, href, size);
// 		})
// 		.on('click', 'a.post-confirm', function (e) {
// 			// Liens d'actions avec confirmation
// 			e.preventDefault();
//
// 			const item = $(this);
//
// 			$.confirm({
// 				title: item.data('title'),
// 				content: item.data('confirm-message'),
// 				type: item.data('type') || 'red',
// 				typeAnimated: true,
// 				buttons: {
// 					confirm: {
// 						text: item.data('button-text'),
// 						btnClass: item.data('btn-class') || 'btn-red',
// 						action: function () {
// 							postUrl(item.attr('href'))
// 						}
// 					},
// 					close: {
// 						text: "Annuler"
// 					}
// 				}
// 			});
// 		})
// 		.on('change', '#chat-file', function () {
// 			$('div#previewFile').fadeOut();
// 			$('#previewFile').remove();
// 			var el = $('<div id="previewFile" class="previewFile"></div>');
// 			$('#receiver').append(el);
// 			imagesPreview(this, 'div#previewFile');
// 			$('div#previewFile').fadeIn('slow');
// 		}).on('change', '#user_pictures', function () {
// 			$('div#previewFile').fadeOut('slow');
// 			$('#previewFile').remove();
// 			var el = $('<div id="previewFile" class="previewFile"></div>');
// 			$('#file').append(el);
// 			imagesPreview(this, 'div#previewFile');
// 			$('div#previewFile').fadeIn('slow');
// 		});
// });
