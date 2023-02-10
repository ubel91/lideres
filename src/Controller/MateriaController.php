<?php

namespace App\Controller;

use App\Entity\Materia;
use App\Form\MateriaType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MateriaController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/materia", name="materia")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $materias = $entityManager->getRepository(Materia::class)->findAll();

        return $this->render('materia/index.html.twig', [
            'materias' => $materias
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/materia/add", name="add_materia")
     */
    public function add(Request $request){

        $materia = new Materia();
        $form = $this->createForm(MateriaType::class, $materia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($materia);
            $entityManager->flush();

            return $this->redirectToRoute('materia');
        }

        return $this->render('materia/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/materia/{id}/edit", name="materia_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Materia $materia): Response
    {
        $form = $this->createForm(MateriaType::class, $materia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('materia');
        }

        return $this->render('materia/edit.html.twig', [
            'materia' => $materia,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/materia/delete", options={"expose"=true}, name="materia_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $materia = $entityManager->getRepository(Materia::class)->find($id);
        if($materia){
            $entityManager->remove($materia);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }
    }

}
