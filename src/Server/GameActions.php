<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GameActions
{
    private $em;
    private $connection;
    private $users;
    private $channel;
    private $user;

    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function process(ConnectionInterface $conn, $users, $action, $channel, $user, $data)
    {
        $this->connection = $conn;
        $this->users = $users;
        $this->action = $action;
        $this->channel = $channel;
        $this->user = $user;

        // Vérifier que la connexion en cours a bien accès au channel
        if (!isset($users[$conn->resourceId]['channels'][$channel])) {
            return false;
        }

        switch ($action) {
            case 'game-submitCards':
                $this->submitCards($data);
                return true;
            default:
                echo sprintf('Action "%s" is not supported yet!', $data->action);
                break;
        }
        
        return true;
    }

    private function submitCards($data)
    {
        foreach ($data as $cardData) {
            $card = $this->em->getRepository('App:WhiteCard')->find($cardData['cardId']);
            foreach ($card->getHand()->getCards() as $otherCard) {
                if ($otherCard != $card) {
                    $otherCard->setSelected(false);
                    $otherCard->setPosition(null);
                }
            }
            $card->setSelected(true);
            $card->setPosition($cardData['position']);
        }

        $this->em->flush();
        $this->sendMessageToChannel($this->user.' a joué.');
    }

    private function sendDataToChannel($data)
    {
        foreach ($this->users as $connectionId => $userConnection) {
            if (array_key_exists($this->channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => $this->action,
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
                    'action' => 'message',
                    'channel' => $this->channel,
                    'user' => 'Auto-message',
                    'data' => ['message' => $message]
                ]));
            }
        }
    }
}