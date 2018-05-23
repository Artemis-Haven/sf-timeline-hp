<?php

namespace App\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use App\Server\Chat;

class WebSocketServer implements MessageComponentInterface
{
    private $container;
    private $chat;
    private $gameActions;

    private $clients;
    private $users = [];
    private $defaultChannel = 'general';

    public function __construct(Chat $chat, GameActions $gameActions)
    {
        $this->chat = $chat;
        $this->gameActions = $gameActions;
        $this->clients = new \SplObjectStorage();
        echo "Listening...\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = [
            'connection' => $conn,
            'user' => '',
            'channels' => []
        ];
    }

    public function onClose(ConnectionInterface $closedConnection)
    {
        $username = $this->users[$closedConnection->resourceId]['user'];
        $this->clients->detach($closedConnection);
        echo sprintf("Connection #%d has disconnected\n", $closedConnection->resourceId);
        foreach ($this->users[$closedConnection->resourceId]['channels'] as $channel) {
            $this->unsubscribeFromChannel($closedConnection, $channel, $username);
        }
        unset($this->users[$closedConnection->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('An error has occurred: '.$e->getMessage());
        $conn->close();
    }

    public function onMessage(ConnectionInterface $conn, $message)
    {
        $messageData = json_decode($message, true);
        if ($messageData === null) {
            return false;
        }
        echo sprintf("- %s \n", $message);
        $action = $messageData['action'] ?? 'unknown';
        $channel = $messageData['channel'] ?? $this->defaultChannel;
        $user = $messageData['user'] ?? $this->botName;
        $data = $messageData['data'] ?? [];

        if ($action == 'subscribe') {
            $this->subscribeToChannel($conn, $channel, $user);
            return true;
        } else if ($action == 'unsubscribe') {
            $this->unsubscribeFromChannel($conn, $channel, $user);
            return true;
        } else if ($action == 'message') {
            return $this->chat->sendMessageToChannel($conn, $this->users, $channel, $user, $data);
        } else if (substr($action, 0, 5) == 'game-') {
            return $this->gameActions->process($conn, $this->users, $action, $channel, $user, $data);
        } else {
            echo sprintf('Action "%s" is not supported yet!', $action);
        }
        return false;
    }

    private function subscribeToChannel(ConnectionInterface $conn, $channel, $user)
    {
        $this->users[$conn->resourceId]['channels'][$channel] = $channel;
        $this->users[$conn->resourceId]['user'] = $user;
        $this->chat->sendGeneralInfoToChannel(
            $conn,
            $this->users,
            $channel,
            ['message' => $user.' joined #'.$channel]
        );
    }

    private function unsubscribeFromChannel(ConnectionInterface $conn, $channel, $user)
    {
        $this->chat->sendGeneralInfoToChannel(
            $conn,
            $this->users,
            $channel,
            ['message' => $user.' left #'.$channel]
        );
        if (array_key_exists($channel, $this->users[$conn->resourceId]['channels'])) {
            unset($this->users[$conn->resourceId]['channels']);
        }
    }

}