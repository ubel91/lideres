<?php

namespace App\Controller;

use App\Entity\Etapa;
use App\Form\EtapaType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtapaController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/etapa", name="etapa")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $etapas = $entityManager->getRepository(Etapa::class)->findAll();

        return $this->render('etapa/index.html.twig', [
            'etapas' => $etapas
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/etapa/add", name="add_etapa")
     */
    public function add(Request $request)
    {
        $etapa = new Etapa();
        $form = $this->createForm(EtapaType::class, $etapa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($etapa);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('etapa'));
        }

        return $this->render('etapa/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/etapa/{id}/edit", name="etapa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Etapa $etapa): Response
    {
        $form = $this->createForm(EtapaType::class, $etapa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('etapa');
        }

        return $this->render('etapa/edit.html.twig', [
            'etapa' => $etapa,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/etapa/delete", options={"expose"=true}, name="etapa_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $etapa = $entityManager->getRepository(Etapa::class)->find($id);
        if($etapa){
            $entityManager->remove($etapa);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }
    }
}
