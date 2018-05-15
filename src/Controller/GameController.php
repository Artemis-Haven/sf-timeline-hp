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
use App\Entity\Board;
use App\Entity\Hand;
use App\Entity\Deck;

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
	 * @ParamConverter("game", class="App:Game")
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
    		$game->setStarted(true);
    		$board = new Board();
    		$game->setBoard($board);
    		$em->persist($board);
    		$deck = new Deck();
    		$game->setDeck($deck);
    		$em->persist($deck);
    		foreach ($game->getMembers() as $user) {
    			$hand = new Hand();
    			$user->addHand($hand);
    			$game->addHand($hand);
    			$em->persist($hand);
    		}
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
    	if (!$game->getStarted() && !$game->getEnded()) {
    		$game->removeMember($this->getUser());
    		if ($game->getMembers()->isEmpty()) {
    			$em->remove($game);
    		}
    		$em->flush();
    	}

        return $this->redirectToRoute('index');
    }
}
