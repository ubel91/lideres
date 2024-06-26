<?php

namespace App\Controller;

use App\Entity\Codigo;
use App\Entity\Estudiantes;
use App\Entity\Libro;
use App\Entity\LibroActivado;
use App\Entity\Profesor;
use App\Entity\Role;
use App\Entity\User;
use App\Form\LibroActivadoType;
use App\Repository\LibroActivadoRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/libro/activado")
 */
class LibroActivadoController extends AbstractController
{
    /**
     * @Route("/", name="libro_activado_index", methods={"GET"})
     * @param LibroActivadoRepository $libroActivadoRepository
     * @return Response
     */
    public function index(LibroActivadoRepository $libroActivadoRepository): Response
    {
        return $this->render('libro_activado/index.html.twig', [
            'libro_activados' => $libroActivadoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="libro_activado_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        if(null != $id = $request->query->get('id')){
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        }else{
            $user = $this->getUser();
        }

        $form = $this->createForm(LibroActivadoType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $codeStr = $form->get('codigo_activacion')->getData();
            $code = $em->getRepository(Codigo::class)->findOneBy(['codebook'=>$codeStr]);
            if (null != $code && !$code->getActivo()){
                $code->setActivo(true);
                $code->setUser($user);
                $em->flush();
            }else{
                $form->get('codigo_activacion')->addError(new FormError('El código de activación proporcionado es inválido o ya ha sido utilizado.'));
                return $this->render('libro_activado/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            return $this->redirectToRoute('textos');
        }

        return $this->render('libro_activado/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/activate", name="libro_activado_book_new", methods={"GET","POST"})
     */
    public function newFromBook(Request $request, Libro $libro): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(LibroActivadoType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $codeStr = $form->get('codigo_activacion')->getData();
            $code = $em->getRepository(Codigo::class)->findOneBy(['codebook'=>$codeStr]);
            if (null != $code && !$code->getActivo()){
                $code->setActivo(true);
                $code->setUser($user);
                $em->flush();
            }else{
                $form->get('codigo_activacion')->addError(new FormError('El código de activación proporcionado es inválido o ya ha sido utilizado.'));
                return $this->render('libro_activado/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            return $this->redirectToRoute('textos');
        }

        return $this->render('libro_activado/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}", name="libro_activado_show", methods={"GET"})
     * @param LibroActivado $libroActivado
     * @return Response
     */
    public function show(LibroActivado $libroActivado): Response
    {
        return $this->render('libro_activado/show.html.twig', [
            'libro_activado' => $libroActivado,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="libro_activado_edit", methods={"GET","POST"})
     * @param Request $request
     * @param LibroActivado $libroActivado
     * @return Response
     */
    public function edit(Request $request, LibroActivado $libroActivado): Response
    {
        $form = $this->createForm(LibroActivadoType::class, $libroActivado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('textos');
        }

        return $this->render('libro_activado/edit.html.twig', [
            'libro_activado' => $libroActivado,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="libro_activado_delete", options={"expose" = true}, methods={"POST"}) 
     * @param Request $request
     * @param LibroActivado $libroActivado
     * @return Response
     */
    public function delete(Request $request, LibroActivado $libroActivado): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($libroActivado);
            $entityManager->flush();
        return new JsonResponse([
            'data' => 'success',
        ]);
    }


    /**
     * @Route("/desactivate/{id}", name="desactivate_code", options={"expose" = true}, methods={"POST"}) 
     * @param Request $request
     * @param LibroActivado $libroActivado
     * @return Response
     */
    public function desactivate(Request $request, Codigo $code): Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var LibroActivado $libroActivado */
        $libroActivado = $em->getRepository(LibroActivado::class)->findOneBy([
            'codigoActivacion' => $code->getCodebook()
        ]);

        if($libroActivado){
            $em->remove($libroActivado);
            $code->setLibro(null);
            $em->flush();
            return new JsonResponse([
                'data' => [
                    'success' => 'Eliminado'
                ],
            ]);
        }

        return new JsonResponse([
            'data' => [
                'error' => 'Ha ocurrido un error'
            ],
        ]);
    }

    private function cleanSearchBooks($array, $user, $role): ?array
    {
        $deletedItems = [];
        $now = new DateTime('NOW');
        foreach ($array as $i=>$l){
            foreach ($l->getLibroActivados() as $la){
                $outDateCode = false;
                foreach ($l->getCodigos() as $codigos){
                    if ($la->getCodigoActivacion() === $codigos->getCodebook()){
                        if ($now < $codigos->getFechaInicio() || $codigos->getFechaFin() < $now)
                            $outDateCode = true;
                    }
                }
                if($role === Role::ROLE_ESTUDIANTE){
                    $estudiante = $la->getEstudiante();
                    if ($estudiante && ($la->getEstudiante()->getId() === $user->getEstudiantes()->getId())){
                        if (!$outDateCode)
                            $deletedItems[] = $i;
                    }
                } elseif ($role === Role::ROLE_PROFESOR){
                    $profesor = $la->getProfesor();
                    if ($profesor && ($la->getProfesor()->getId() === $user->getProfesor()->getId())){
                        if (!$outDateCode)
                            $deletedItems[] = $i;
                    }
                }
            }
        }
        foreach ($deletedItems as $d){
            unset($array[$d]);
        }
        array_values($array);

        return $array ? $array : [];
    }
}
