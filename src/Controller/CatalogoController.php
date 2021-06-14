<?php

namespace App\Controller;

use App\Entity\Catalogo;
use App\Form\CatalogoType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogoController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/catalogo", name="catalogo")
     */
    public function index()
    {
        return $this->render('catalogo/index.html.twig', [
            'controller_name' => 'CatalogoController',
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/catalogo/add", name="add_catalogo")
     */
    public function add(Request $request)
    {
        $catalogo = new Catalogo();
        $form = $this->createForm(CatalogoType::class, $catalogo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($catalogo);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('catalogo_listado'));
        }

        return $this->render('catalogo/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/catalogo/listado", name="catalogo_listado")
     */

    public function listado(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $catalogos = $entityManager->getRepository(Catalogo::class)->findAll();

        return $this->render('catalogo/listado.html.twig', [
            'catalogos' => $catalogos
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/catalogo/delete", options={"expose"=true}, name="catalogo_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
            $id = $request->get('id');
            $entityManager = $this->getDoctrine()->getManager();
            $catalogo = $entityManager->getRepository(Catalogo::class)->find($id);
            if($catalogo){
                $entityManager->remove($catalogo);
                $entityManager->flush();
                return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
            } else {
                return new JsonResponse(['error'=> 'El elemento no existe']);
            }

    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/{id}/edit", name="catalogo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Catalogo $catalogo): Response
    {
        $form = $this->createForm(CatalogoType::class, $catalogo);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('catalogo_listado');
        }

        return $this->render('catalogo/edit.html.twig', [
            'catalogo' => $catalogo,
            'form' => $form->createView(),
        ]);
    }

}
