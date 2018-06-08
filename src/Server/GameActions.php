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
            case 'game-releaseCard':
                $this->releaseCard($data);
                return true;
            case 'game-dragCard':
                $this->dragCard($data);
                return true;
            case 'game-dropCard':
                $this->dropCard($data);
                return true;
            default:
                echo sprintf('Action "%s" is not supported yet!', $data->action);
                break;
        }
        
        return true;
    }

    private function releaseCard($data)
    {
        $this->sendDataToChannel($data);
    }

    private function dragCard($data)
    {
        $this->sendDataToChannel($data);
    }

    private function dropCard($data)
    {
        $card = $this->em->getRepository('App:Card')->find($data['cardId']);
        $cardBefore = (array_key_exists('idBefore', $data) ? $this->em->getRepository('App:Card')->find($data['idBefore']) : null);
        $cardAfter = (array_key_exists('idAfter', $data) ? $this->em->getRepository('App:Card')->find($data['idAfter']) : null);
        $game = $card->getHand()->getGame();

        $card->getHand()->removeCard($card);
        $game->getBoard()->addCard($card);
        $positionBefore = ($cardBefore ? $cardBefore->getPosition() : -1000);
        $positionAfter = ($cardAfter ? $cardAfter->getPosition() : 1000);
        $card->setPosition(($positionAfter+$positionBefore)/2);
        $this->em->flush();

        $this->sendDataToChannel($data);
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