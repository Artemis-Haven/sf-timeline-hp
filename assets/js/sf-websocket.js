(function () {
  'use strict';

  var _receiver = document.getElementById('ws-content-receiver');
  var ws = new WebSocket('ws://' + wsUrl);

  ws.onopen = function () {
    ws.send('Hello');
    _receiver.innerHTML = 'Connected !';
  };

  ws.onmessage = function (event) {
    _receiver.innerHTML = event.data;
  };

  ws.onclose = function () {
    _receiver.innerHTML = 'Connection closed';
  };

  ws.onerror = function () {
    _receiver.innerHTML = 'An error occured!';
  };
})();