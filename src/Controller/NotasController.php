<?php

namespace App\Controller;

use App\Entity\Notas;
use App\Form\NotasType;
use App\Repository\NotasRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/notas")
 */
class NotasController extends AbstractController
{
    /**
     * @Route("/", name="notas_index", methods={"GET"})
     * @param NotasRepository $notasRepository
     * @return Response
     */
    public function index(NotasRepository $notasRepository): Response
    {
        return $this->render('notas/index.html.twig', [
            'notas' => $notasRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="notas_new", options={"expose"=true}, methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $nota = new Notas();
        $form = $this->createForm(NotasType::class, $nota);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $nota->setUser($this->getUser());
            $entityManager->persist($nota);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()){
                return new JsonResponse(['success'=> [
                    'data' => [
                        'message' => 'Nota salvada correctamente',
                        'id'=> $nota->getId()
                    ]
                ]]);
            }

            return $this->redirectToRoute('notas_index');
        } elseif ($request->isXmlHttpRequest()) {
            $json = $form->getErrors();
            return new JsonResponse(['error' => json_encode($json)]);
        }

        return $this->render('notas/new.html.twig', [
            'nota' => $nota,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notas_show", methods={"GET"})
     */
    public function show(Notas $nota): Response
    {
        return $this->render('notas/show.html.twig', [
            'nota' => $nota,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="notas_edit", options={"expose"=true}, methods={"GET","POST"})
     */
    public function edit(Request $request, Notas $nota): Response
    {
        $form = $this->createForm(NotasType::class, $nota);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($request->isXmlHttpRequest()){
                return new JsonResponse(['success'=> [
                    'data' => [
                        'message' => 'Nota editada correctamente',
                    ]
                ]]);
            }
            return $this->redirectToRoute('notas_index');
        } elseif ($request->isXmlHttpRequest()) {
            $json = $form->getErrors();
            return new JsonResponse(['error' => json_encode($json)]);
        }

        return $this->render('notas/edit.html.twig', [
            'nota' => $nota,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete", options={"expose"=true}, name="notas_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $nota = $entityManager->getRepository(Notas::class)->find($id);
        if ($nota){
            $entityManager->remove($nota);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Nota eliminada']);
        } else {
            return new JsonResponse(['error'=> 'La nota no existe']);
        }

    }
}
