var limit = 5;
var step = 0;
var receive = 0;
var load = false;
var loadFriend = true;
var loadGroup = true;
var selectedId = 0;
//
$(function () {
	var socket = io.connect('http://product-dev.fr:1337');
	/**
	 * on se connecte
	 */
	if (isLogged) {
		// introJS()
		// 	.onbeforeexit(function () {
		// 		return confirm("Vous me quittez déjà ?");
		// 	})
		// 	.setOptions({
		// 		prevLabel: 'Précédent',
		// 		nextLabel: 'Suivant',
		// 		doneLabel: 'Terminer',
		// 		disableInteraction: true,
		// 		showProgress: true,
		// 		steps: [
		// 			{
		// 				title: 'Welcome',
		// 				intro: 'Hello World! 👋 Nous sommes sur le chat',
		// 				//element: document.querySelector('.card-demo'),
		// 			},
		// 			{
		// 				element: document.querySelector('.friendLists '),
		// 				intro: `Salut! 👋 Tu trouves tes informations personnelles ici.<br>
		// 				Elles te\n seront utile afin de trouver des correspondances.`
		// 			},
		// 			{
		// 				element: document.querySelector('#receiver'),
		// 				intro: 'Visualise tes photos et choisi celle que tu veux voir en principale'
		// 			},
		// 			{
		// 				element: document.querySelector('#FormMessenger'),
		// 				intro: 'Visualise tes photos et choisi celle que tu veux voir en principale'
		// 			},
		// 			// {
		// 			// 	element: document.querySelector('#pills-contact-tab'),
		// 			// 	intro: 'Tu peux choisir tes passions ici, c\'est le <b>plus</b> important.'
		// 			// },
		// 			// {
		// 			// 	element: document.querySelector('#match'),
		// 			// 	intro: 'C\'est <b>le coeur</b> de ne notre application.' +
		// 			// 		'<br> Tu y passera la majeur parti de ton temps. Afin de trouver l\'ame soeur.'
		// 			// },
		// 			// {
		// 			// 	element: document.querySelector('#chat'),
		// 			// 	intro: 'Lorsque tu aura des matchs alors tu pourras déployer tout ton charme.'
		// 			// },
		// 			// {
		// 			// 	element: document.querySelector('#compte'),
		// 			// 	intro: 'Ici tu as accès à ton compte et tout les paramètres afin de trouver la bonne personne.'
		// 			// }
		// 		]
		// 	}).start()

		socket.emit('login', {
			userId: userId,
			firstname: firstname,
			lastname: lastname,
			avatar: 'avatar',
		});
		socket.on('login', function (data) {
			$('#connected_' + data.token).removeClass('offline');
		});
		socket.on('disconnect', function (data) {
			$('#connected_' + data.token).addClass('offline');
		});
		/**
		 * message reçu
		 */
		socket.on('message', function (data) {
			if (data.groupId == $('#groupeId').val()) {
				addTemplate(data, 1);
				$('#receiver').animate({scrollTop: $('#receiver').prop('scrollHeight')}, 500);
			} else {
				alert('Message reçu abs')
				$("#badgeNotif").html(parseInt($("#badgeNotif").html()) + 1);
			}
		});
		/**
		 * Envoie Message
		 */
		$('.send_btn').on('click', function () {
			$('#FormMessenger').submit();
		});
		$('#FormMessenger').submit(function (event) {
			event.preventDefault();
			var message = $('#message').val();
			var media = $('#chat-file').prop("files");
			if (message == "") {
				$('#message').css('border', '1px red solid');
				return true;
			}
			var to = $('#receiverId').val();
			var groupe = $('#groupeId').val();
			var formData = new FormData();
			for (i = 0; i < media.length; i++) {
				formData.append("media[]", media[i]);
			}
			formData.append("message", message);
			$.ajax({
				url: Routing.generate('chat_save_message',
					{
						'discussion': groupe,
						'tokenId': userId
					}
				),
				contentType: false,
				processData: false,
				type: 'post',
				data: formData,
				beforeSend: function () {
					$('#message').val('');
					$("#previewFile").remove();
				},
				success: function (data) {
					socket.emit('message', data.resultat);
				}
			});
		});
		// $('.send_btn').click(function (event) {
		// 	event.preventDefault();
		// 	var messageSanitaze = $('#message').val();
		// 	var message = $('#message').val();
		// 	var media = $('#chat-file').prop("files");
		// 	var to = $('#receiverId').val();
		// 	var groupe = $('#groupeId').val();
		// 	if (message == "" && media.length == 0) {
		// 		$('#message').css('border', '1px red solid');
		// 		return false;
		// 	}
		// 	$('#message').css('border', 'none');
		// 	var formData = new FormData();
		// 	for (i = 0; i < media.length; i++) {
		// 		formData.append("media[]", media[i]);
		// 	}
		// 	formData.append("message", message);
		// 	formData.append("messageSanitaze", messageSanitaze);
		// 	$.ajax({
		// 		url: Routing.generate('chat_save_message',
		// 			{
		// 				'discussion': groupe,
		// 				'tokenId': userId
		// 			}
		// 		),
		// 		contentType: false,
		// 		processData: false,
		// 		type: 'post',
		// 		data: formData,
		// 		beforeSend: function () {
		// 			$('#message').val('');
		// 		},
		// 		success: function (data) {
		// 			socket.emit('message', data.resultat);
		// 		}
		// 	});
		// });
		/**
		 * Liste d'amis
		 */
		$('.friendLists').click(function () {
			step = 0;
			$('#receiver').html('');
			selectedId = $(this).prop('id');
			$('.friendLists a').removeClass('active');
			$(this).find('a').addClass('active');
			if (selectedId != $('#groupeId').val() && loadFriend) {
				$('#groupeId').val(selectedId);
				$.ajax({
					url: Routing.generate('chat_load_message'),
					data: {
						groupe: selectedId,
					},
					method: "POST",
					beforeSend: function () {
						$('#receiver').html('');
						loadFriend = false;
						loader();
					},
					success: function (data) {
						for (var k in data.resultat) {
							addTemplate(data.resultat[k]);
						}
						$('#receiver').animate({scrollTop: $('#receiver').prop('scrollHeight')}, 500);
						loadFriend = true;
						headerChat(data.interlocuteur);
						// il faut dire au scoket de mettre à jour tous les messages non en lu
						removeLoader();
						enabled();
					}
				})
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
						groupe: selectedId,
					},
					method: "POST",
					beforeSend: function () {
						$('#receiver').html('');
						loadGroup = false;
					},
					success: function (data) {
						for (var k in data.resultat) {
							addTemplate(data.resultat[k]);
						}
						$('#receiver').animate({scrollTop: $('#receiver').prop('scrollHeight')}, 500);
						loadGroup = true;
						removeLoader();
						enabled();
					}
				})
			}
		});

		$('#receiver').scroll(function () {
			if ($(this).scrollTop() == 0 && receive == false) {
				if (!receive) {
					var selectedId = $('#groupeId').val();
					step = step + limit;
					$.ajax({
						url: Routing.generate('chat_load_message'),
						data: {
							groupe: selectedId,
							step: step
						},
						method: "POST",
						beforeSend: function () {
							receive = true;
							loader();
						},
						success: function (data) {
							for (var k in data.resultat) {
								addTemplate(data.resultat[k]);
							}
							receive = false;
							enabled();
						},
						complete: function () {
							receive = false;
							removeLoader();
						}
					});
				}
			}
		});

		/**
		 *
		 * @param data
		 */
		function addTemplate(data, append) {
			if (append) {
				if (data.token != userId) {
					// pas mon message
					$('#receiver').append(
						'<div class="d-flex justify-content-start mb-4" id="' + data.messageId + '">'
						+ '    <div class="img_cont_msg">'
						+ '        <img src="/uploads/profile_picture/' + data.avatar + '" class="rounded-circle user_img_msg img-fluid" alt="' + data.alt + '" style="max-width: 50px"/>'
						+ '    </div>'
						+ '    <div class="msg_cotainer text-break text-wrap">' + data.message.replace(/\n/g, '<br>\n')
						+ '        <span class="msg_time">' + data.createdAt + '</span>'
						+ '    </div>'
						+ '</div>'
					);
				} else {
					// mon message
					$('#receiver').append(
						'<div class="d-flex justify-content-end mb-4" id="' + data.messageId + '">' +
						'<div class="msg_cotainer_send text-break text-wrap">' + data.message.replace(/\n/g, '<br>\n') +
						'       <span class="msg_time_send">' + data.createdAt + '</span>' +
						'   </div>' +
						'   <div class="img_cont_msg">' +
						'       <img src="/uploads/profile_picture/' + data.avatar + '" alt="' + data.alt + '" class="rounded-circle user_img_msg img-fluid" style="max-width: 50px">' +
						'   </div>' +
						'</div>'
					);
				}
			} else {
				if (data.token != userId) {
					// pas mon message
					$('#receiver').prepend(
						'<div class="d-flex justify-content-start mb-4" id="' + data.messageId + '">'
						+ '    <div class="img_cont_msg">'
						+ '        <img src="/uploads/profile_picture/' + data.avatar + '" class="rounded-circle user_img_msg img-fluid" style="max-width: 50px" alt="' + data.alt + '"/>'
						+ '    </div>'
						+ '    <div class="msg_cotainer text-break text-wrap">' + data.message.replace(/\n/g, '<br>\n')
						+ '        <span class="msg_time">' + data.createdAt + '</span>'
						+ '    </div>'
						+ '</div>'
					);
				} else {
					// mon message
					$('#receiver').prepend(
						'<div class="d-flex justify-content-end mb-4" id="' + data.messageId + '">' +
						'<div class="msg_cotainer_send text-break text-wrap">' + data.message.replace(/\n/g, '<br>\n') +
						'       <span class="msg_time_send">' + data.createdAt + '</span>' +
						'   </div>' +
						'   <div class="img_cont_msg">' +
						'       <img src="/uploads/profile_picture/' + data.avatar + '" alt="' + data.alt + '"class="rounded-circle user_img_msg img-fluid" style="max-width: 50px">' +
						'   </div>' +
						'</div>'
					);
				}
			}
		}


		/**
		 * retourne le loader
		 */
		function loader() {
			$("#receiver").prepend(
				'<div class="d-flex justify-content-center loader">\n' +
				'  <div class="spinner-border" role="status">\n' +
				'    <span class="visually-hidden">Loading...</span>\n' +
				'  </div>\n' +
				'</div>');
		}

		/**
		 * destroy loader
		 */
		function removeLoader() {
			$('.loader').remove();
		}

		function enabled() {
			$('#message').removeAttr('disabled');
			$('#chat-file').removeAttr('disabled');
			$('.send_btn').removeAttr('disabled');
		}

		/**
		 *
		 * @param interlocuteur
		 */
		function headerChat(interlocuteur) {
			$('#interlocteur').removeClass("d-none").fadeIn('slow');
			$('#action_menu_btn').removeClass("d-none").fadeIn('slow');
			$('#interlProfile').attr('href', interlocuteur.interlocuteurProfile);
			$('#interlProfile').attr('title', 'Photo de profile de '+ interlocuteur.interlocuteurName);
			$('#signal-user').attr('href', Routing.generate('app_signal_chat', {'token': interlocuteur.interlocuteurToken}));
			$('#link-delete-user').attr('href', Routing.generate('user_remove_connexion', {'token': interlocuteur.interlocuteurToken}));
			$('#interlAvatar').attr('src', interlocuteur.interlocuteurAvatar ? interlocuteur.interlocuteurAvatar : 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg');
			$('#interlAvatar').attr('alt', '');
			$('#interlAvatar').attr('alt', interlocuteur.interlocuteurName);
		}
	}

	$('#action_menu_btn').click(function () {
		$('.action_menu').toggle();
	});
});
