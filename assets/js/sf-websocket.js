/* globals wsUrl: true */
(function () {
  'use strict';

  var _receiver = document.getElementById('ws-content-receiver');
  global.ws = new WebSocket('ws://' + wsUrl);
  var botName = 'ChatBot';

  var addMessageToChannel = function(message) {
    _receiver.innerHTML += '<div class="message">' + message + '</div>';
  };

  ws.onopen = function () {
    ws.send(JSON.stringify({
      action: 'subscribe',
      channel: channel,
      user: userName
    }));
  };

  ws.onmessage = function (event) {
    var data = JSON.parse(event.data);
    if (data.action == 'game-dropCard' && data.user != userName) {
      var card = $(".card[data-id="+data.data.cardId+"]").detach();
      if (data.data.idBefore) {
        card.insertAfter(".card[data-id="+data.data.idBefore+"]");
        $('<article class="card-placeholder"></article>').insertBefore(card).droppable(placeholderDropSettings);
      } else {
        card.insertBefore(".card[data-id="+data.data.idAfter+"]");
        $('<article class="card-placeholder"></article>').insertAfter(card).droppable(placeholderDropSettings);
      }
    } else {
      addMessageToChannel(event.data);
    }
  };

  ws.onclose = function () {
    addMessageToChannel('Connection closed');
  };

  ws.onerror = function () {
    addMessageToChannel('An error occured!');
  };



  var _textInput = document.getElementById('ws-content-to-send');
  var _textSender = document.getElementById('ws-send-content');
  var enterKeyCode = 13;

  var sendTextInputContent = function () {
    // Get text input content
    var content = _textInput.value;

    // Send it to WS
    ws.send(JSON.stringify({
      action: 'message',
      user: userName,
      channel: channel,
      data: {message: content}
    }));

    // Reset input
    _textInput.value = '';
  };

  _textSender.onclick = sendTextInputContent;
  _textInput.onkeyup = function(e) {
    // Check for Enter key
    if (e.keyCode === enterKeyCode) {
      sendTextInputContent();
    }
  };
}) ();
