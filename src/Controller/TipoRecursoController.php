<?php

namespace App\Controller;

use App\Entity\TipoRecurso;
use App\Form\TipoRecursoType;
use App\Repository\TipoRecursoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
 * @Route("/tipo/recurso")
 */
class TipoRecursoController extends AbstractController
{
    /**
     * @Route("/", name="tipo_recurso_index", methods={"GET"})
     */
    public function index(TipoRecursoRepository $tipoRecursoRepository): Response
    {
        return $this->render('tipo_recurso/index.html.twig', [
            'tipo_recursos' => $tipoRecursoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tipo_recurso_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tipoRecurso = new TipoRecurso();
        $form = $this->createForm(TipoRecursoType::class, $tipoRecurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipoRecurso);
            $entityManager->flush();

            return $this->redirectToRoute('tipo_recurso_index');
        }

        return $this->render('tipo_recurso/new.html.twig', [
            'tipo_recurso' => $tipoRecurso,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_recurso_show", methods={"GET"})
     */
    public function show(TipoRecurso $tipoRecurso): Response
    {
        return $this->render('tipo_recurso/show.html.twig', [
            'tipo_recurso' => $tipoRecurso,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tipo_recurso_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TipoRecurso $tipoRecurso): Response
    {
        $form = $this->createForm(TipoRecursoType::class, $tipoRecurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tipo_recurso_index');
        }

        return $this->render('tipo_recurso/edit.html.twig', [
            'tipo_recurso' => $tipoRecurso,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete", options={"expose"=true}, name="tipo_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $tipo = $entityManager->getRepository(TipoRecurso::class)->find($id);
        if ($tipo){
            $entityManager->remove($tipo);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }
    }
}
