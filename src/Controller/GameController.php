<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Game;
use App\Entity\Hand;
use App\Entity\WhiteDeck;
use App\Entity\BlackDeck;
use App\Entity\WhiteCard;
use App\Entity\BlackCard;

class GameController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template
     */
    public function index(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$newGame = new Game();

    	$newGameForm = $this->createFormBuilder($newGame)
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, array('label' => 'Create'))
            ->getForm();

    	$newGameForm->handleRequest($request);

        if ($newGameForm->isSubmitted() && $newGameForm->isValid()) {
	        $newGame->setCreatedAt(new \DateTime('now'));
	        $newGame->addMember($this->getUser());
	        $em->persist($newGame);
	        $em->flush();

	        return $this->redirectToRoute('index');
	    }

        return [
        	'games' => $em->getRepository('App:Game')->findAll(),
        	'newGameForm' => $newGameForm->createView()
        ];
    }
    
    /**
     * @Route("/game-{id}", name="game")
     * @Template
     */
    public function game(Game $game)
    {
        return ['game' => $game];
    }
    
    /**
     * @Route("/start-game-{id}", name="start_game")
	 * @ParamConverter("game", class="App:Game")
     * @Template
     */
    public function start(Game $game)
    {
    	$em = $this->getDoctrine()->getManager();

    	if (!$game->getStarted() && !$game->getEnded()) {
    		$game->start();
            $whiteDeck = new WhiteDeck();
            $game->setWhiteDeck($whiteDeck);
            $em->persist($whiteDeck);
            $blackDeck = new BlackDeck();
            $game->setBlackDeck($blackDeck);
            $em->persist($blackDeck);
    		foreach ($game->getMembers() as $user) {
    			$hand = new Hand();
    			$user->addHand($hand);
    			$game->addHand($hand);
    			$em->persist($hand);
    		}

            $whiteCardsRefs = $em->getRepository('App:WhiteCardReference')->findAll();
            shuffle($whiteCardsRefs);
            // Creation of the White Deck
            foreach ($whiteCardsRefs as $key => $ref) {
                $card = new WhiteCard($ref->getContent());
                $whiteDeck->addCard($card);
                $em->persist($card);
            }

            $blackCardsRefs = $em->getRepository('App:BlackCardReference')->findAll();
            shuffle($blackCardsRefs);
            // Creation of the Black Deck
            foreach ($blackCardsRefs as $key => $ref) {
                $card = new BlackCard($ref->getContent(), $ref->getNbrOfBlanks());
                $blackDeck->addCard($card);
                $em->persist($card);
            }
            // Each player picks 11 cards from the Deck
            for ($i=0; $i < 11; $i++) { 
                foreach ($game->getMembers() as $user) {
                    $pickedCard = $game->getWhiteDeck()->popCard();
                    $game->getHand($user)->addWhiteCard($pickedCard);
                }
            }

            // One black card is placed on the Board
            $pickedCard = $game->getBlackDeck()->popCard();
            $game->setBlackCard($pickedCard);
            
            // A random player starts
            $game->setTurn($game->getMembers()->get(array_rand($game->getMembers()->toArray())));

    		$em->flush();
    	}

        return $this->redirectToRoute('game', ['id' => $game->getId()]);
    }
    
    /**
     * @Route("/join-game-{id}", name="join_game")
	 * @ParamConverter("game", class="App:Game")
     * @Template
     */
    public function join(Game $game)
    {
    	$em = $this->getDoctrine()->getManager();
    	if (!$game->getStarted() && !$game->getEnded()) {
    		$game->addMember($this->getUser());
    		$em->flush();
    	}

        return $this->redirectToRoute('game', ['id' => $game->getId()]);
    }
    
    /**
     * @Route("/quit-game-{id}", name="quit_game")
     * @ParamConverter("game", class="App:Game")
     * @Template
     */
    public function quit(Game $game)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$game->getEnded()) {
            if (!$game->getStarted()) { // Before the game begins
                $game->removeMember($this->getUser());
            } else { // During the game (= abandon)
                $game->removeMember($this->getUser());
                $hand = $game->getHand($this->getUser());
                $maxDeckPosition = $game->getDeck()->getMaxPosition();
                foreach ($hand->getCards() as $key => $card) {
                    $hand->removeCard($card);
                    $game->getDeck()->addCard($card);
                    $card->setPosition($maxDeckPosition+$key);
                }
            }
            if ($game->getMembers()->isEmpty()) {
                $em->remove($game);
            }
            $em->flush();
        }

        return $this->redirectToRoute('index');
    }
    
}
