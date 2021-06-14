<?php

namespace App\Controller;

use App\Entity\GradoEscolar;
use App\Form\GradoEscolarType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GradoEscolarController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/grado", name="grado_escolar")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $grado = $entityManager->getRepository(GradoEscolar::class)->findAll();

        return $this->render('grado_escolar/index.html.twig', [
            'grados' => $grado,
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/grado/add", name="add_grado")
     */
    public function add(Request $request){

        $grado = new GradoEscolar();
        $form = $this->createForm(GradoEscolarType::class, $grado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grado);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('grado_escolar'));
        }

        return $this->render('grado_escolar/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/grado/{id}/edit", name="grado_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GradoEscolar $gradoEscolar): Response
    {
        $form = $this->createForm(GradoEscolarType::class, $gradoEscolar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('grado_escolar');
        }

        return $this->render('grado_escolar/edit.html.twig', [
            'gradoEscolar' => $gradoEscolar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/grado/delete", options={"expose"=true}, name="grado_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $gradoEscolar = $entityManager->getRepository(GradoEscolar::class)->find($id);
        if($gradoEscolar){
            $entityManager->remove($gradoEscolar);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }
    }




}
