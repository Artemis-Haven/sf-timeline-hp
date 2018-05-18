require('./sf-websocket.js');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('jquery-ui/ui/widgets/draggable');
require('jquery-ui/ui/widgets/droppable');
require('bootstrap-sass');

$('#hand-container .card').draggable({
	revert: 'invalid',
	start: function (event, ui) {
		ws.send(JSON.stringify({
			action: 'game-dragCard',
			user: userName,
			channel: channel
		}));
	}
});

global.placeholderDropSettings = {
	accept: '#hand-container .card',
	drop: function(event, ui) {
		var card = ui.draggable.css({position: 'static'}).detach();
		var idBefore = $(this).prev('.card').data('id');
		var idAfter = $(this).next('.card').data('id');
	    if (idBefore) {
			$(this).after(card);
	        $('<article class="card-placeholder"></article>').insertAfter(card).droppable(placeholderDropSettings);
	    } else {
			$(this).before(card);
	        $('<article class="card-placeholder"></article>').insertBefore(card).droppable(placeholderDropSettings);
	    }

		ws.send(JSON.stringify({
			action: 'game-dropCard',
			user: userName,
			channel: channel,
			data: {cardId: card.data('id'), idBefore: idBefore, idAfter: idAfter}
		}));
	}
};

$('#board-container .card-placeholder').droppable(placeholderDropSettings);