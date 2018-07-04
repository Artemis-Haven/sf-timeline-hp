<?php

namespace App\Controller;

use App\Entity\WhiteCardReference;
use App\Form\WhiteCardReferenceType;
use App\Repository\WhiteCardReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reference")
 */
class ReferenceController extends Controller
{
    /**
     * @Route("/", name="reference_index", methods="GET")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('App:WhiteCardReference');
        return $this->render('reference/index.html.twig', ['references' => $repo->findAll()]);
    }

    /**
     * @Route("/new", name="reference_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $reference = new WhiteCardReference();
        $form = $this->createForm(WhiteCardReferenceType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reference);
            $em->flush();

            return $this->redirectToRoute('reference_index');
        }

        return $this->render('reference/new.html.twig', [
            'reference' => $reference,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reference_show", methods="GET")
     */
    public function show(WhiteCardReference $reference): Response
    {
        return $this->render('reference/show.html.twig', ['reference' => $reference]);
    }

    /**
     * @Route("/{id}/edit", name="reference_edit", methods="GET|POST")
     */
    public function edit(Request $request, WhiteCardReference $reference): Response
    {
        $form = $this->createForm(WhiteCardReferenceType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reference_edit', ['id' => $reference->getId()]);
        }

        return $this->render('reference/edit.html.twig', [
            'reference' => $reference,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reference_delete", methods="DELETE")
     */
    public function delete(Request $request, WhiteCardReference $reference): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reference->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reference);
            $em->flush();
        }

        return $this->redirectToRoute('reference_index');
    }
}
