<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Chat
{
    private $container;
    private $botName = 'ChatBot';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sendMessageToChannel(ConnectionInterface $conn, $users, $channel, $user, $message)
    {
        if (!isset($users[$conn->resourceId]['channels'][$channel])) {
            return false;
        }
       foreach ($users as $connectionId => $userConnection) {
            if (array_key_exists($channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => 'message',
                    'channel' => $channel,
                    'user' => $user,
                    'message' => $message
                ]));
            }
        }
        return true;
    }

    public function sendGeneralInfoToChannel(ConnectionInterface $conn, $users, $channel, $message)
    {
        return $this->sendMessageToChannel($conn, $users, $channel, $this->botName, $message);
    }
}