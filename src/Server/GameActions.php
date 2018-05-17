<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GameActions
{
    private $container;
    private $connection;
    private $users;
    private $channel;
    private $user;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ConnectionInterface $conn, $users, $channel, $user, $data)
    {
        $this->connection = $conn;
        $this->users = $users;
        $this->channel = $channel;
        $this->user = $user;

        // Vérifier que la connexion en cours a bien accès au channel
        if (!isset($users[$conn->resourceId]['channels'][$channel])) {
            return false;
        }

        switch ($data->action) {
            case 'dragCard':
                $this->dragCard();
                return true;
            case 'dropCard':
                $this->dropCard($data);
                return true;
            default:
                echo sprintf('Action "%s" is not supported yet!', $data->action);
                break;
        }
        
        return true;
    }

    private function dragCard()
    {
        $this->sendMessageToChannel('Card dragged by '.$this->user);
    }

    private function dropCard($data)
    {
        $this->sendDataToChannel($data);
    }

    private function sendDataToChannel($data)
    {
        foreach ($this->users as $connectionId => $userConnection) {
            if (array_key_exists($this->channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => 'gameAction',
                    'channel' => $this->channel,
                    'user' => $this->user,
                    'data' => $data
                ]));
            }
        }
    }

    private function sendMessageToChannel($message)
    {
        foreach ($this->users as $connectionId => $userConnection) {
            if (array_key_exists($this->channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => 'gameAction',
                    'channel' => $this->channel,
                    'user' => 'Auto-message',
                    'message' => $message
                ]));
            }
        }
    }
}