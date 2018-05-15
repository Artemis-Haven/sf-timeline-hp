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

	        return $this->redirectToRoute('game');
	    }

        return [
        	'games' => $em->getRepository('App:Game')->findAll(),
        	'newGameForm' => $newGameForm->createView()
        ];
    }
    /**
     * @Route("/game/{id}", name="game")
	 * @ParamConverter("game", class="App:Game")
     * @Template
     */
    public function game(Game $game)
    {
        return ['game' => $game];
    }
}
