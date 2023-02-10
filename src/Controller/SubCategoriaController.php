<?php

namespace App\Controller;

use App\Entity\SubCategoria;
use App\Form\SubCategoriaType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubCategoriaController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/sub/categoria", name="sub_categoria")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $subCategorias = $entityManager->getRepository(SubCategoria::class)->findAll();

        return $this->render('sub_categoria/index.html.twig', [
            'subCategorias' => $subCategorias
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/sub/categoria/add", name="add_sub_categoria")
     */
    public function add(Request $request)
    {
        $subCategoria = new SubCategoria();
        $form = $this->createForm(SubCategoriaType::class, $subCategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($subCategoria);
            $entityManager->flush();

            return $this->redirectToRoute('sub_categoria');

        }

        return $this->render('sub_categoria/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/sub/categoria/{id}/edit", name="sub_categoria_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SubCategoria $subCategoria): Response
    {
        $form = $this->createForm(SubCategoriaType::class, $subCategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sub_categoria');
        }

        return $this->render('sub_categoria/edit.html.twig', [
            'subCategoria' => $subCategoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/sub/categoria/delete", options={"expose"=true}, name="subCategoria_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $subCategoria = $entityManager->getRepository(SubCategoria::class)->find($id);
        if($subCategoria){
            $entityManager->remove($subCategoria);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }

    }
}
