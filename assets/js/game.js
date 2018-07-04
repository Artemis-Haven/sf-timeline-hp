require('./sf-websocket.js');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('jquery-ui/ui/widgets/draggable');
require('jquery-ui/ui/widgets/droppable');
require('bootstrap-sass');

$('#hand-container .card').draggable({
	revert: 'invalid',
	containment: '#board-container',
});

placeholderDropSettings = {
	accept: '#hand-container .card',
	drop: function(event, ui) {
		//ui.helper.data('dropped', true);
		var currentCard = $(this).find('article.card').detach();
		var card = ui.draggable.css({position: 'static'}).detach();
		$(this).append(card);
		$('#hand-container').append(currentCard);
		if ($('#board-container .card-placeholder').length == $('#board-container .card-placeholder article.card').length) {
			ws.send(JSON.stringify({
				action: 'game-submitCards',
				user: userName,
				channel: channel,
				data: [{position: 1, cardId: card.data('id')}]
			}));
		}
		/*ws.send(JSON.stringify({
			action: 'game-dropCard',
			user: userName,
			channel: channel,
			data: {cardId: card.data('id'), idBefore: idBefore, idAfter: idAfter}
		}));*/
	}
};

$('#board-container .card-placeholder').droppable(placeholderDropSettings);