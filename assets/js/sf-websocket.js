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
    console.log(data);
    if (data.action == 'game-electCards') {
      $('#board-select-cards').empty();
      var $boardElectCards = $('#board-elect-cards');
      for (var i = 0; i < data.data.selectedCards.length; i++) {
        var $cardsGroup = $('<div/>');
        for (var j = 0; j < data.data.selectedCards[i].length; j++) {
          var card = data.data.selectedCards[i][j];
          $cardsGroup.append('<article class="card" data-id="'+card.id+'">'+card.content+'</article>');
        }
        $boardElectCards.append($cardsGroup);
      }
      if (data.data.elector == userName) {
        // TODO election
      }
    } else if (data.action == 'message') {
      addMessageToChannel(data.user+' : '+data.data.message);
    } else {
      //addMessageToChannel(event.data);
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
