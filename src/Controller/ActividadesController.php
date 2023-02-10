<?php

namespace App\Controller;

use App\Entity\Actividades;
use App\Form\ActividadesType;
use App\Repository\ActividadesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actividades")
 */
class ActividadesController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/", name="actividades_index", methods={"GET"})
     */
    public function index(ActividadesRepository $actividadesRepository): Response
    {
        return $this->render('actividades/index.html.twig', [
            'actividades' => $actividadesRepository->findAll(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/new", name="actividades_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $actividade = new Actividades();
        $form = $this->createForm(ActividadesType::class, $actividade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actividade);
            $entityManager->flush();

            return $this->redirectToRoute('actividades_index');
        }

        return $this->render('actividades/new.html.twig', [
            'actividade' => $actividade,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", options={"expose"=true}, name="actividades_show", methods={"GET"})
     * @param Actividades $actividades
     * @return Response
     */
    public function show(Actividades $actividades): Response
    {

        return $this->render('actividades/show.html.twig', [
            'actividades' => $actividades,
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/{id}/edit", name="actividades_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Actividades $actividade): Response
    {
        $form = $this->createForm(ActividadesType::class, $actividade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('unidad_index');
        }

        return $this->render('actividades/edit.html.twig', [
            'actividade' => $actividade,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/delete", options={"expose"=true}, name="actividades_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $actividad = $entityManager->getRepository(Actividades::class)->find($id);
        if ($actividad){
            $entityManager->remove($actividad);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }
    }
}
