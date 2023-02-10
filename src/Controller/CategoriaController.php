<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\SubCategoria;
use App\Form\CategoriaType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class CategoriaController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/categoria", name="categoria")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categorias = $entityManager->getRepository(Categoria::class)->findAll();

        return $this->render('categoria/listado.html.twig', [
            'categorias' => $categorias
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/categoria/add", name="addCategoria")
     */

    public function addCategoria(Request $request)
    {
        $categoria = new Categoria();

        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categoria);
            $entityManager->flush();

            return $this->redirectToRoute('categoria');
        }
        return $this->render('categoria/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/categoria/{id}/edit", name="categoria_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categoria $categoria): Response
    {
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categoria');
        }

        return $this->render('categoria/edit.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/categoria/delete", options={"expose"=true}, name="categoria_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $categoria = $entityManager->getRepository(Categoria::class)->find($id);
        if($categoria){
            $entityManager->remove($categoria);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }

    }

    /**
     * @Route("/subcategoria_categoria", name="subcategorias_by_categorias", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @param Request $request
     * @return JsonResponse
     */
    public function subCategoriasByCategorias(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $subCategorias = $em->getRepository(SubCategoria::class)->findByCategoria($id);
        return new JsonResponse($subCategorias);
    }

}
