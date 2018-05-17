require('./sf-websocket.js');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('jquery-ui/ui/widgets/draggable');
require('jquery-ui/ui/widgets/droppable');
require('bootstrap-sass');

$('#hand-container .card').draggable({
	revert: 'invalid'
});

$('#board-container .card-placeholder').droppable({
	accept: '#hand-container .card',
	drop: function(event, ui) {
		var card = ui.draggable.css({position: 'static'}).detach();
		var idBefore = card.prev('.card').data('id');
		var idAfter = card.next('.card').data('id');
		$(this).after(card);
		card.after('<article class="card-placeholder"></article>');

		ws.send(JSON.stringify({
			action: 'gameAction',
			user: userName,
			gameActionData: {action: 'dropCard', cardId: card.data('id'), idBefore: idBefore, idAfter: idAfter},
			channel: channel
		}));
	}
});