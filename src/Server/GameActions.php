<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Game;

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
            case 'game-electCards':
                $this->electCards($data);
                return true;
            default:
                echo sprintf('Action "%s" is not supported yet!', $data->action);
                break;
        }
        
        return true;
    }

    private function submitCards($data)
    {
        $game = null;
        // On sélectionne chaque carte et on définit sa position
        foreach ($data as $cardData) {
            $card = $this->em->getRepository('App:WhiteCard')->find($cardData['cardId']);
            $card->setSelected(true);
            $card->setPosition($cardData['position']);
            // S'il existe déjà une carte dans la même position, on la remet dans la main
            foreach ($card->getHand()->getCards() as $otherCard) {
                if ($otherCard != $card && $otherCard->getPosition() == $card->getPosition()) {
                    $otherCard->setSelected(false);
                    $otherCard->setPosition(null);
                }
            }
            $game = $card->getHand()->getGame();
        }
        $this->sendMessageToChannel($this->user.' a joué.');

        // On vérifie si tout les joueurs (sauf le meneur de ce tour) ont joué
        $allMembersHavePlayed = true;
        foreach ($game->getHands() as $hand) {
            if (!$hand->areCardsSubmitted() && $hand->getOwner() != $game->getTurn()) {
                $allMembersHavePlayed = false;
                break;
            }
        }
        // Si tous les joueurs (sauf le meneur de ce tour) ont joué, envoyer un message
        // et passer à la phase suivante (choix de la meilleure carte)
        if ($allMembersHavePlayed) {
            $this->sendMessageToChannel("Tout le monde a joué. C'est à ".$game->getTurn()." de décider de la meilleure carte.");
            $selectedCards = [];
            foreach ($game->getHands() as $hand) {
                $handSelectedCards = [];
                foreach ($hand->getSelectedCards() as $card) {
                    $handSelectedCards[] = ['id' => $card->getId(), 'content' => $card->getContent()];
                }
                if (!empty($handSelectedCards)) {
                    $selectedCards[] = $handSelectedCards;
                }
            }
            $game->setState(Game::STATE_ELECT_CARD);
            $this->sendDataToChannel('game-electCards', ['elector' => $game->getTurn()->getUsername(), 'selectedCards' => $selectedCards]);
            // TODO : passage à la phase de choix des cartes
        }

        $this->em->flush();        
    }

    private function electCards($data)
    {
        $card = $this->em->getRepository('App:WhiteCard')->find($data['cardId']);
        $winner = $card->getHand()->getOwner();
        $game = $card->getHand()->getGame();
        $card->getHand()->addonePoint();
        $previousWinner = $game->getTurn();
        $game->setTurn($winner);
        $game->setState(Game::STATE_SELECT_CARD);
        $this->sendMessageToChannel($previousWinner." a choisi la carte de ".$winner.' : '.$card->getContent());
        // TODO distribuer une ou des nouvelles cartes blanches
        // TODO piocher une nouvelle carte noire
        // TODO renvoyer des infos concernant le décompte des points, les nouvelles cartes piochées, la nouvelle carte noire
        //$this->em->flush();
    }

    private function sendDataToChannel($action, $data)
    {
        foreach ($this->users as $connectionId => $userConnection) {
            if (array_key_exists($this->channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => $action,
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