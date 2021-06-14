<?php

namespace App\Controller;

use App\Entity\Actividades;
use App\Entity\ImagenGuardada;
use App\Entity\Notas;
use App\Entity\Unidad;
use App\Form\UnidadType;
use App\Repository\UnidadRepository;
use App\Service\ErrorService;
use App\Service\FileUploader;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/unidad")
 */
class UnidadController extends AbstractController
{
    /**
     * @Route("/", name="unidad_index", methods={"GET"})
     */
    public function index(UnidadRepository $unidadRepository): Response
    {
        return $this->render('unidad/index.html.twig', [
            'unidades' => $unidadRepository->findAll(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/new", name="unidad_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param ErrorService $errorService
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploader $fileUploader, ErrorService $errorService): Response
    {
        $unidad = new Unidad();
        $form = $this->createForm(UnidadType::class, $unidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

                /** @var UploadedFile $archivo */
                $archivoT = $request->files->get('archivo');
                $archivo = $form['archivo']->getData();
                if ($archivo) {
//                $fileUploader->setTargetDirectory('/portadas');
                    $archivoFileName = $fileUploader->upload($archivo, FileUploader::UNIDAD_ARCHIVO, '',$unidad->getLibro()->getNombre());
                    $unidad->setArchivo($archivoFileName);
                    $unidad->setNombreArchivo($archivo->getClientOriginalName());
                    $unidad->setMimeType($archivo->getMimeType());
                }

            $actividad = $unidad->getActividadForm();

//            if ($actividad){
//                $actividad->setUnidad($unidad);
//                $entityManager->persist($actividad);
//            }

            if ($actividad){
                $id = $request->request->get('unidad')['actividadForm']['id'];
                if(!$id){
                    $actividad->setUnidad($unidad);
                    $entityManager->persist($actividad);
                }else {
                    foreach ($unidad->getActividades() as $a){
                        if ($a->getId() == $id){
                            $a->setNombre($actividad->getNombre());
                            $a->setPagina($actividad->getPagina());
                            $a->setUrl($actividad->getUrl());
                            $actividad = $a;
                        }
                    }
                    $actividad->setUnidad($unidad);
                }
                $serializer = $this->get('serializer');
                $actividad = $serializer->serialize($actividad, 'json', ['groups' => ['actividades']]);
                $edited = !!$id;
            }

            $entityManager->persist($unidad);
            $entityManager->flush();

            $this->addFlash('success', Unidad::CREATED_SUCCESS);

            if ($request->isXmlHttpRequest()){
                return new JsonResponse([
                    'result' => 'success',
                    'message' => 'Actividad asignada con éxito',
                    'data' => $unidad->getId(),
                    'newUnit' => true
                ]);
            }
            return $this->redirectToRoute('unidad_index');

        } else if ( $request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && !$form->isValid()) {
                return new JsonResponse([
                    'result' => 'error',
                    'message' => 'Formulario no válido',
                    'data' => $errorService->getErrorMessages($form)
                ]);
            }
        }

        return $this->render('unidad/new.html.twig', [
            'unidad' => $unidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="unidad_show", methods={"GET"})
     * @param Unidad $unidad
     * @return Response
     */
    public function show(Unidad $unidad): Response
    {
        $libro =  $unidad->getLibro();
        $id = $this->getUser();
        $notas = $this->getDoctrine()->getManager()->getRepository(Notas::class)->findNotesByUser($id, $unidad->getId());
        $actividades = $unidad->getActividades();
        $imagenesGuardadas = $this->getDoctrine()->getManager()->getRepository(ImagenGuardada::class)->findImagenesByUser($id, $unidad->getId());

        $serializer = $this->get('serializer');
        $actividades = $serializer->serialize($actividades, 'json', ['groups' => ['actividades']]);
        $imagenesGuardadas = $serializer->serialize($imagenesGuardadas, 'json', ['groups' => ['imagenes_guardadas']]);

        return $this->render('unidad/show.html.twig',[
            'unidad' => $unidad,
            'libro' => $libro,
            'notas' => $notas,
            'actividades' => $actividades,
            'imagenesGuardadas' => $imagenesGuardadas
        ]);
    }

    /**
     * @Route("/{id}/edit", options={"expose"=true}, name="unidad_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Unidad $unidad
     * @param FileUploader $fileUploader
     * @param ErrorService $errorService
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Unidad $unidad, FileUploader $fileUploader, ErrorService $errorService): Response
    {

        $actividad = null;
        $id = null;

        if (count($request->request)){
            $actividadForm = $request->request->get('unidad')['actividadForm'];
            if ($actividadForm && $actividadForm['actividad_nombre']) {
                if($actividadForm['id']){
                    $id = $actividadForm['id'];
                    $actividad = $this->getDoctrine()->getRepository(Actividades::class)->find($id);
                    $edited = true;
                } else {
                    $actividad = new Actividades();
                    $edited = false;
                }
                $actividad->setNombre($actividadForm['actividad_nombre']);
                $actividad->setPagina($actividadForm['pagina']);
                $actividad->setUrl($actividadForm['actividad_url']);
                $actividad->setUnidad($unidad);
                $unidad->setActividadForm($actividad);
            }
        }

        $form = $this->createForm(UnidadType::class, $unidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $archivo */
            $archivo = $form['archivo']->getData();
            if ($archivo) {
//                $fileUploader->setTargetDirectory('/portadas');
                $archivoFileName = $fileUploader->upload($archivo, FileUploader::UNIDAD_ARCHIVO, $unidad->getArchivo(), $unidad->getLibro()->getNombre());
                $unidad->setArchivo($archivoFileName);
                $unidad->setNombreArchivo($archivo->getClientOriginalName());
                $unidad->setMimeType($archivo->getMimeType());
            }

            if ($actividad){
                if (!$id)
                    $this->getDoctrine()->getManager()->persist($actividad);
                $this->getDoctrine()->getManager()->flush();
                $serializer = $this->get('serializer');
                $actividad = $serializer->serialize($actividad, 'json', ['groups' => ['actividades']]);
            } else {
                $this->getDoctrine()->getManager()->flush();
            }

            if ($request->isXmlHttpRequest()){
                return new JsonResponse([
                    'result' => 'success',
                    'message' => 'Actividad asignada con éxito',
                    'data' => $actividad,
                    'edited' => $edited
                ]);
            }

            return $this->redirectToRoute('unidad_index');

        } else if ( $request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && !$form->isValid()) {
                return new JsonResponse([
                    'result' => 'error',
                    'message' => 'Formulario no válido',
                    'data' => $errorService->getErrorMessages($form)
                ]);
            }
         }

        $serializer = $this->get('serializer');
        $actividades = $serializer->serialize($unidad->getActividades(), 'json', ['groups' => ['actividades']]);

        return $this->render('unidad/edit.html.twig', [
            'unidad' => $unidad,
            'actividades' => $actividades,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/delete", options={"expose"=true}, name="unidad_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function delete(Request $request, FileUploader $fileUploader): Response
    {
            $id = $request->get('id');
            $entityManager = $this->getDoctrine()->getManager();
            $unidad = $entityManager->getRepository(Unidad::class)->find($id);
            if ($unidad){
                $entityManager->remove($unidad);
                $entityManager->flush();
                return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
            } else {
                return new JsonResponse(['error'=> 'El elemento no existe']);
            }

    }

    /**
     * @Route("/{id}/load", name="unitLoader", methods={"GET"})
     */
    public function unitLoader(Unidad $unidad, FileUploader $fileUploader)
    {
        //@Todo Security files

        $response = new StreamedResponse(function () use ($unidad, $fileUploader){
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $fileUploader->readStream($unidad->getArchivoDir());
            stream_copy_to_stream($fileStream, $outputStream);
        });
        $response->headers->set('Content-Type', $unidad->getMimeType());
        return $response;
    }

//    /**
//     * @Route("/cache", options={"expose"=true}, name="unidad_cache", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
//     * @param Request $request
//     * @param Unidad $unidad
//     * @param CacheInterface $cache
//     * @return void
//     */
//    public function cacheStore(Request $request, Unidad $unidad, CacheInterface $cache)
//    {
//        $folderName = $request->request->get('folderName');
//        $image = utf8_decode($request->request->get('img'));
//        $image = str_replace('data:image/jpeg;base64,', '', $image);
//        $data = base64_decode($image);
//        $file = $request->request->get('num');
//
//    }
}
