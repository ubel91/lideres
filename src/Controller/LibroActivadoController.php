<?php

namespace App\Controller;

use App\Entity\Codigo;
use App\Entity\Estudiantes;
use App\Entity\Libro;
use App\Entity\LibroActivado;
use App\Entity\Profesor;
use App\Entity\Role;
use App\Form\LibroActivadoType;
use App\Repository\LibroActivadoRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $libroActivado = new LibroActivado();
        $user = $this->getUser();
        $roles = $user->getRoles()[0];
        $emLibros = $this->getDoctrine()->getManager();

        if (count($user->getRoles()) === 1){
            if ($roles === Role::ROLE_ESTUDIANTE){
                $estudiante = $user->getEstudiantes();
                $libroActivado->setEstudiante($estudiante);
                $libros = $emLibros->getRepository(Libro::class)->findByRoleEstAndNotActivated();
            }
            elseif ($roles === Role::ROLE_PROFESOR){
                $profesor = $user->getProfesor();
                $libroActivado->setProfesor($profesor);
                $libros = $emLibros->getRepository(Libro::class)->findByRoleDocAndNotActivated();
            }
        }

        //-----
            $libros = $this->cleanSearchBooks($libros, $user, $roles);
            $choiceBooks = [];
            foreach ($libros as $l){
                $choiceBooks[$l->getId()] = $l;
            }
        //-----
        $form = $this->createForm(LibroActivadoType::class, $libroActivado, ['choiceBooks'=>$choiceBooks]);
//        $form = $this->createForm(LibroActivadoType::class, $libroActivado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->persist($libroActivado);
            $entityManager->flush();

            return $this->redirectToRoute('textos');
        }

        return $this->render('libro_activado/new.html.twig', [
            'libro_activado' => $libroActivado,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/activate", name="libro_activado_book_new", methods={"GET","POST"})
     */
    public function newFromBook(Request $request, Libro $libro): Response
    {
        $libroActivado = new LibroActivado();
        $user = $this->getUser();
        $libroActivado->setLibro($libro);

        if (count($user->getRoles()) === 1){
            if ($user->getRoles()[0] === Role::ROLE_ESTUDIANTE){
                $estudiante = $user->getEstudiantes();
                $libroActivado->setEstudiante($estudiante);
            }
            elseif ($user->getRoles()[0] === Role::ROLE_PROFESOR){
                $profesor = $user->getProfesor();
                $libroActivado->setProfesor($profesor);
            }
        }

        $emLibros = $this->getDoctrine()->getManager();
        $roles = $user->getRoles()[0];

        if (count($user->getRoles()) === 1){
            if ($roles === Role::ROLE_ESTUDIANTE){
                $estudiante = $user->getEstudiantes();
                $libroActivado->setEstudiante($estudiante);
                $libros = $emLibros->getRepository(Libro::class)->findByRoleEstAndNotActivated();
            }
            elseif ($roles === Role::ROLE_PROFESOR){
                $profesor = $user->getProfesor();
                $libroActivado->setProfesor($profesor);
                $libros = $emLibros->getRepository(Libro::class)->findByRoleDocAndNotActivated();
            }
        }
        //----------------------

        $libros = $this->cleanSearchBooks($libros, $user, $roles);
        $choiceBooks = [];
        foreach ($libros as $l){
            $choiceBooks[$l->getId()] = $l;
        }

        //----------------------

        $form = $this->createForm(LibroActivadoType::class, $libroActivado, ['choiceBooks'=>$choiceBooks]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->persist($libroActivado);
            $entityManager->flush();

            return $this->redirectToRoute('textos');
        }

        return $this->render('libro_activado/new.html.twig', [
            'libro_activado' => $libroActivado,
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
     * @Route("/{id}", name="libro_activado_delete", methods={"DELETE"})
     * @param Request $request
     * @param LibroActivado $libroActivado
     * @return Response
     */
    public function delete(Request $request, LibroActivado $libroActivado): Response
    {
        if ($this->isCsrfTokenValid('delete'.$libroActivado->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($libroActivado);
            $entityManager->flush();
        }

        return $this->redirectToRoute('libro_activado_index');
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
