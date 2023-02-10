<?php

namespace App\Controller;

use App\Entity\ImagenGuardada;
use App\Entity\Unidad;
use App\Form\ImagenGuardadaType;
use App\Repository\ImagenGuardadaRepository;
use App\Service\ErrorService;
use App\Service\FileUploader;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/imagen/guardada")
 */
class ImagenGuardadaController extends AbstractController
{
    /**
     * @Route("/", name="imagen_guardada_index", methods={"GET"})
     * @param ImagenGuardadaRepository $imagenGuardadaRepository
     * @return Response
     */
    public function index(ImagenGuardadaRepository $imagenGuardadaRepository): Response
    {
        return $this->render('imagen_guardada/index.html.twig', [
            'imagen_guardadas' => $imagenGuardadaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", options={"expose"=true}, name="imagen_guardada_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param ErrorService $errorService
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploader $fileUploader, ErrorService $errorService): Response
    {
        $imagenGuardada = new ImagenGuardada();
        if (count($request->request)) {
            $unidad_id = $request->request->get('imagen_guardada')['unidad'];
            $pagina = $request->request->get('imagen_guardada')['pagina'];
            $unidad = $this->getDoctrine()->getManager()->getRepository(Unidad::class)->find($unidad_id);
            $imagenGuardada->setUnidad($unidad);
            $imagenGuardada->setPagina($pagina);
        }
        $form = $this->createForm(ImagenGuardadaType::class, $imagenGuardada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $foto */
            $foto = $form['archivo']->getData();
            if ($foto) {
                $username = $this->getUser()->getUsername();
                $fotoFileName = $fileUploader->upload($foto, FileUploader::IMAGEN_GUARDADA, '', $username);
                $imagenGuardada->setArchivo($fotoFileName);
                $imagenGuardada->setMimeType($foto->getMimeType());
                $imagenGuardada->setNombre($foto->getClientOriginalName());
            }
            $imagenGuardada->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($imagenGuardada);
            $entityManager->flush();

            $serializer = $this->get('serializer');
            $imagenGuardada = $serializer->serialize($imagenGuardada, 'json', ['groups' => ['imagenes_guardadas']]);

            if($request->isXmlHttpRequest()){
                return new JsonResponse([
                    'result' => 'success',
                    'message' => '¡Cambios guardados con éxito!',
                    'data' => $imagenGuardada,
                ]);
            }

            return $this->redirectToRoute('imagen_guardada_index');

        }elseif ($request->isXmlHttpRequest()){
            if ($form->isSubmitted() && $form->isValid()){
                return new JsonResponse([
                    'result' => 'success',
                    'message' => '¡Cambios guardados con éxito!',
                    'data' => $errorService->getErrorMessages($form),
                ]);
            }
        }

        return $this->render('imagen_guardada/new.html.twig', [
            'imagen_guardada' => $imagenGuardada,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="imagen_guardada_show", methods={"GET"})
     */
    public function show(ImagenGuardada $imagenGuardada): Response
    {
        return $this->render('imagen_guardada/show.html.twig', [
            'imagen_guardada' => $imagenGuardada,
        ]);
    }

    /**
     * @Route("/{id}/edit", options={"expose"=true}, name="imagen_guardada_edit", methods={"GET","POST"})
     * @param Request $request
     * @param ImagenGuardada $imagenGuardada
     * @param FileUploader $fileUploader
     * @param ErrorService $errorService
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, ImagenGuardada $imagenGuardada, FileUploader $fileUploader, ErrorService $errorService): Response
    {
        $form = $this->createForm(ImagenGuardadaType::class, $imagenGuardada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $foto */
            $foto = $form['archivo']->getData();
            if ($foto) {
                $username = $this->getUser()->getUsername();
                $fotoFileName = $fileUploader->upload($foto, FileUploader::IMAGEN_GUARDADA, $imagenGuardada->getArchivo(), $username);
                $imagenGuardada->setArchivo($fotoFileName);
                $imagenGuardada->setMimeType($foto->getMimeType());
                $imagenGuardada->setNombre($foto->getClientOriginalName());
            }

            $this->getDoctrine()->getManager()->flush();

            $serializer = $this->get('serializer');
            $imagenGuardada = $serializer->serialize($imagenGuardada, 'json', ['groups' => ['imagenes_guardadas']]);

            if($request->isXmlHttpRequest()){
                return new JsonResponse([
                    'result' => 'success',
                    'message' => '¡Cambios guardados con éxito!',
                    'data' => $imagenGuardada,
                ]);
            }

            return $this->redirectToRoute('imagen_guardada_index');
        } elseif ($request->isXmlHttpRequest()){
            if ($form->isSubmitted() && $form->isValid()){
                return new JsonResponse([
                    'result' => 'error',
                    'message' => '¡No se pudieron guardar los cambios',
                    'data' => $errorService->getErrorMessages($form),
                ]);
            }
        }

        return $this->render('imagen_guardada/edit.html.twig', [
            'imagen_guardada' => $imagenGuardada,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", options={"expose"=true}, name="imagen_guardada_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $imagenGuardada = $entityManager->getRepository(ImagenGuardada::class)->find($id);
        if($imagenGuardada){
            $entityManager->remove($imagenGuardada);
            $entityManager->flush();
            return new JsonResponse(['success'=> 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error'=> 'El elemento no existe']);
        }
    }

    /**
     * @Route("/{id}/saved", options={"expose"=true}, name="imagen_guardada", methods={"GET"})
     * @param ImagenGuardada $imagenGuardada
     * @param FileUploader $fileUploader
     * @return BinaryFileResponse
     */
    public function showImgenGuardada(ImagenGuardada $imagenGuardada, FileUploader $fileUploader)
    {
        //@Todo Security files
        $username = $this->getUser()->getUsername();

        $path = $fileUploader->getFullUploadPath($imagenGuardada->getArchivoDir($username));
        $response = new BinaryFileResponse($path);

//        $response = new StreamedResponse(function () use ($imagenGuardada, $fileUploader, $username){
//            $outputStream = fopen('php://output', 'wb');
//            $fileStream = $fileUploader->readStream($imagenGuardada->getArchivoDir($username));
//
//            stream_copy_to_stream($fileStream, $outputStream);
//        });

        $response->headers->set('Content-Type', $imagenGuardada->getMimeType());
        return $response;
    }



}
