<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Message;

class Chat
{
    private $em;
    private $botName = 'ChatBot';

    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function sendMessageToChannel(ConnectionInterface $conn, $users, $channel, $username, $message)
    {
        if (!isset($users[$conn->resourceId]['channels'][$channel])) {
            return false;
        }

        $game = $this->em->getRepository('App:Game')->find($channel);
        $msg = new Message();
        $msg->setContent($message['message'])->setSender($username)->setGame($game)->setCreatedAt(new \DateTime('now'));

       foreach ($users as $connectionId => $userConnection) {
            if (array_key_exists($channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => 'message',
                    'channel' => $channel,
                    'user' => $username,
                    'data' => $message
                ]));
                
                $this->em->persist($msg);
                $this->em->flush();
            }
        }
        return true;
    }

    public function sendGeneralInfoToChannel(ConnectionInterface $conn, $users, $channel, $message)
    {
        return $this->sendMessageToChannel($conn, $users, $channel, $this->botName, $message);
    }
}