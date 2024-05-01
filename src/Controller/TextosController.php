<?php

namespace App\Controller;

use App\Entity\Actividades;
use App\Entity\Codigo;
use App\Entity\Estudiantes;
use App\Entity\Libro;
use App\Entity\LibroActivado;
use App\Entity\Materia;
use App\Entity\Role;
use App\Entity\Unidad;
use App\Entity\User;
use App\Form\LibroActivadoType;
use App\Form\LibroType;
use App\Service\FileUploader;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class TextosController
 * @package App\Controller
 */
class TextosController extends AbstractController
{
    /**
     * @Route("/textos", name="textos")
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $super = false;
        $id = $request->query->get('id');
        if ($id !== null && ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER_ADMIN'))) {
            $user = $em->getRepository(User::class)->find($id);
            $super = true;
        } else {
            $user = $this->getUser();
        }

        if (null == $user)
            $user = $this->getUser();

            $result1 = [];
        if ($user->getRoles()[0] === Role::ROLE_ESTUDIANTE) {
            $id = $user->getEstudiantes() ? $user->getEstudiantes()->getId() : null;
            $result1 = $em->getRepository(LibroActivado::class)->findLibrosActivadosByEst($id);

        } elseif ($user->getRoles()[0] === Role::ROLE_PROFESOR) {
            $id = $user->getProfesor() ? $user->getProfesor()->getId() : null;
            $result1 = $em->getRepository(LibroActivado::class)->findLibrosActivadosByDoc($id);

        }
        $result1 = array_filter($result1, function ($k){
            return $k->getDeletedAt() == null ;
        });
        $result = $em->getRepository(Libro::class)->findByUser($user);

        $result = array_filter($result, function ($k) use ($user) {
            $c = $k->getCodigos();
            /** @var Codigo $code */
            foreach ($c as $code){
                if ($code->getUser() == $user){
                   return $code->getDeletedAt() === null;
                }
            }
        });
        $materias = $this->getDoctrine()->getRepository(Materia::class)->findAll();
        $ouput = [];
        $ouput2 = [];

        foreach ($materias as $materia) {
            $ouput[$materia->getNombre()] = $this->getByMateria1($materia, $result1);
            $ouput2[$materia->getNombre()] = $this->getByMateria($materia, $result);
        }

        $formActivacion = $this->createForm(LibroActivadoType::class, null);
        $formActivacion->handleRequest($request);
        if ($formActivacion->isSubmitted() && $formActivacion->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $codeStr = $formActivacion->get('codigo_activacion')->getData();
            $code = $em->getRepository(Codigo::class)->findOneBy(['codebook' => $codeStr]);
            if (null != $code && !$code->getActivo()) {
                $code->setActivo(true);
                $code->setUser($user);
                $em->flush();
            } else {
                $formActivacion->get('codigo_activacion')->addError(new FormError('El código de activación proporcionado es inválido o ya ha sido utilizado.'));

                return $this->render('textos/index.html.twig', [
                    'result' => $ouput,
                    'result2' => $ouput2,
                    'is_super' => $super,
                    'user' => $user,
                    'formActivacion' => $formActivacion->createView()
                ]);
            }
            return $super ? $this->redirectToRoute('textos', ['id' => $user->getId()]) :
                $this->redirectToRoute('textos');
        }

        return $this->render('textos/index.html.twig', [
            'result' => $ouput,
            'result2' => $ouput2,
            'is_super' => $super,
            'user' => $user,
            'formActivacion' => $formActivacion->createView()
        ]);
    }

    function mergue($array1, $array2)
    {
        $tempIds = array();
        $resultado = array();


        foreach ($array1 as $objeto) {
            if (!in_array($objeto->getId(), $tempIds)) {
                $resultado[] = $objeto;
                $tempIds[] = $objeto->getId();
            }
        }

        foreach ($array2 as $objeto) {
            if (!in_array($objeto->getId(), $tempIds)) {
                $resultado[] = $objeto;
                $tempIds[] = $objeto->getId();
            }
        }

        return $resultado;
    }

    public function getByMateria1(Materia $materia, $books = array())
    {
        $ouput = [];
        /** @var LibroActivado $book */
        foreach ($books as $book) {
            if ($book->getLibro()->getCatalogo()->getMaterias() === $materia)
                $ouput[]=$book;
        }
        return $ouput;
    }

    /**
     * @param Materia $materia
     * @param array $books
     * @return array
     */
    public function getByMateria(Materia $materia, $books = array())
    {
        $ouput = [];
        /** @var Libro $book */
        foreach ($books as $book) {
            if ($book->getCatalogo()->getMaterias() === $materia)
                $ouput[] = $book;
        }
        return $ouput;
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     *
     * @Route("/textos/add", name="addTextos")
     *
     * @param Request $request
     * @param FileUploader $fileUploader
     *
     * @return RedirectResponse|Response
     *
     * @throws Exception
     */
    public function addTextos(Request $request, FileUploader $fileUploader)
    {
        $libro = new Libro();
        $form = $this->createForm(LibroType::class, $libro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $portada */
            $portada = $form['portada']->getData();
            if ($portada) {
//                $fileUploader->setTargetDirectory('/portadas');
                $portadaFileName = $fileUploader->upload($portada, FileUploader::PORTADA_IMAGEN, '', $libro->getNombre());
                $libro->setPortada($portadaFileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($libro);
            $em->flush();

            return $this->redirect($this->generateUrl('listado_admin'));

        }
        return $this->render('textos/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/textos/{id}", name="libro_show", methods={"GET"})
     *
     * @param Libro $libro
     *
     * @return Response
     */
    public function show(Libro $libro): Response
    {
        $actividades = [];
        /** @var Unidad $unidad */
        foreach ($libro->getUnidades() as $unidad)
            /** @var Actividades $actividad */
            foreach ($unidad->getActividades() as $actividad)
                if (!in_array($actividad, $actividades))
                    $actividades[] = $actividad;

        return $this->render('textos/show.html.twig', [
            'libro' => $libro,
            'actividades' => $actividades
        ]);
    }

    /**
     * @Route("/textos/{id}/embed", name="texto_embed")
     *
     * @param Libro $book
     *
     * @return Response
     */
    public function embed(Libro $book)
    {
        return $this->render('textos/embed.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/textos/listado/admin", name="listado_admin")
     */
    public function listadoAdmin()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $id = $user->getId();

        if (count($user->getRoles()) === 1)
            if ($user->getRoles()[0] === Role::ROLE_ADMIN || $user->getRoles()[0] === Role::ROLE_SUPER_ADMIN)
                $books = $em->getRepository(Libro::class)->findAll();
            else
                $books = [];

        return $this->render('textos/listado_admin.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     *
     * @Route("/libro/delete", options={"expose"=true}, name="libro_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     *
     * @param Request $request
     * @param FileUploader $fileUploader
     *
     * @return Response
     */
    public function delete(Request $request, FileUploader $fileUploader): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $libro = $entityManager->getRepository(Libro::class)->find($id);
        if ($libro) {
            $entityManager->remove($libro);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error' => 'El elemento no existe']);
        }

    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/textos/{id}/edit", name="textosEdit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Libro $libro
     * @param FileUploader $fileUploader
     *
     * @return Response
     *
     * @throws Exception
     */
    public function edit(Request $request, Libro $libro, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(LibroType::class, $libro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $portada */
            $portada = $form['portada']->getData();
            if ($portada) {
                $portadaFileName = $fileUploader->upload($portada, FileUploader::PORTADA_IMAGEN, $libro->getPortada(), $libro->getNombre());
                $libro->setPortada($portadaFileName);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('listado_admin');
        }

        return $this->render('textos/edit.html.twig', [
            'libro' => $libro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/textos/{id}/portada", name="portada", methods={"GET"})
     *
     * @param Libro $libro
     * @param FileUploader $fileUploader
     *
     * @return StreamedResponse
     */
    public function showPortada(Libro $libro, FileUploader $fileUploader)
    {
        //@Todo Security files

        $response = new StreamedResponse(function () use ($libro, $fileUploader) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $fileUploader->readStream($libro->getPortadaDir());

            stream_copy_to_stream($fileStream, $outputStream);
        });

        return $response;
    }
}
