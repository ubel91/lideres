<?php

namespace App\Controller;

use App\Entity\Recurso;
use App\Entity\Role;
use App\Entity\TipoRecurso;
use App\Form\RecursoType;
use App\Repository\RecursoRepository;
use App\Service\FileUploader;
use Exception;
use League\Flysystem\FilesystemInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\Settings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Context\RequestStackContext;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/recurso")
 */
class RecursoController extends AbstractController
{
    private $uploadsDirectory;
    private $base_path;
    private $fileUploader;

    /**
     * RecursoController constructor.
     * @param $projectDir
     * @param FilesystemInterface $uploadFilesystem
     */
    public function __construct($projectDir, FilesystemInterface $uploadFilesystem, FileUploader $fileUploader)
    {
        $this->base_path = $projectDir;
        $this->fileUploader = $fileUploader;
        $this->uploadsDirectory = $uploadFilesystem;
    }


    /**
     * @Route("/", name="recurso_index", methods={"GET"})
     * @param RecursoRepository $recursoRepository
     * @return Response
     */
    public function index(RecursoRepository $recursoRepository): Response
    {
        return $this->render('recurso/index.html.twig', [
            'recursos' => $recursoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/plataforma", name="plataforma_index", methods={"GET"}, options={"expose" = true})
     *
     * @param RecursoRepository $recursoRepository
     * @param Request $request
     *
     * @return Response
     */
    public function platform(RecursoRepository $recursoRepository, Request $request)
    {
        $user = $this->getUser();
        $recursos = [];

        $book = $request->get('book');
        if ($this->getUser()->getEstudiantes()) {
            $recursos = $recursoRepository->findRecursosById($user->getEstudiantes()->getId(), Role::ROLE_ESTUDIANTE, $book);
        } else if ($this->getUser()->getProfesor()) {
            $recursos = $recursoRepository->findRecursosById($user->getProfesor()->getId(), Role::ROLE_PROFESOR, $book);
        } else if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER_ADMIN'))
            $recursos = $recursoRepository->findRecursos($book);

        $serializer = $this->get('serializer');
        $jrecursos = $serializer->serialize($recursos, 'json', ['groups' => ['recurso', 'tipo', 'libro']]);
        $type = $request->get('type');
        if ('personal' === $type)
            return $this->render('recurso/personal.html.twig', [
                'recursos' => $jrecursos,
                'recursosT' => $recursos
            ]);

        return $this->render('recurso/plataforma.twig', [
            'recursos' => $jrecursos,
            'recursosT' => $recursos
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/new", name="recurso_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $recurso = new Recurso();
        $form = $this->createForm(RecursoType::class, $recurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var UploadedFile $archivo */
            $archivo = $form['referenciaFile']->getData();//$request->files->get('referenciaFile');
            if ($archivo) {
//                if ($archivo->getClientOriginalExtension() === 'xls' || $archivo->getClientOriginalExtension() === 'xlsx' || $archivo->getClientOriginalExtension() === 'doc' || $archivo->getClientOriginalExtension() === 'docx' || $archivo->getClientOriginalExtension() === 'ppt' || $archivo->getClientOriginalExtension() === 'pptx'){
//                    $path = $archivo->getRealPath();
//                    $destinationFolder = $recurso->getReferenciaFileDir();
//                    $archivoFileName = $this->convertToPDF($archivo, $path, $destinationFolder);
//                    $recurso->setMimeType('application/pdf');
//                    $recurso->setReferencia($archivo->getClientOriginalName());
//                    $recurso->setReferenciaFile($archivoFileName);
//                } else {
                $archivoFileName = $fileUploader->upload($archivo, FileUploader::RECURSOS, '', $recurso->getLibro()->getNombre());
                $recurso->setReferencia($archivo->getClientOriginalName());
                $recurso->setReferenciaFile($archivoFileName);
                $recurso->setMimeType($archivo->getMimeType());
//                }
            }

            $entityManager->persist($recurso);
            $entityManager->flush();

            return $this->redirectToRoute('recurso_index');
        }

        return $this->render('recurso/new.html.twig', [
            'recurso' => $recurso,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recurso_show", methods={"GET"})
     * @param Recurso $recurso
     * @return Response
     */
    public function show(Recurso $recurso): Response
    {
        return $this->render('recurso/show.html.twig', [
            'recurso' => $recurso,
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/{id}/edit", name="recurso_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Recurso $recurso
     * @param FileUploader $fileUploader
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Recurso $recurso, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(RecursoType::class, $recurso);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var UploadedFile $archivo */
            $archivo = $form['referenciaFile']->getData();//$request->files->get('referenciaFile');
            if ($archivo) {
//                if ($archivo->getClientOriginalExtension() === 'xls' || $archivo->getClientOriginalExtension() === 'xlsx' || $archivo->getClientOriginalExtension() === 'doc' || $archivo->getClientOriginalExtension() === 'docx' || $archivo->getClientOriginalExtension() === 'ppt' || $archivo->getClientOriginalExtension() === 'pptx'){
//                    if ($recurso->getReferenciaFileDir())
//                        $path = $fileUploader->getFullUploadPath($recurso->getReferenciaFileDir());
//                    else{
//                        $path = $archivo->getRealPath();
//                    }
//                    $this->convertToPDF($archivo, $path);
//                }
                $archivoFileName = $fileUploader->upload($archivo, FileUploader::RECURSOS, $recurso->getReferenciaFile(), $recurso->getLibro()->getNombre());
                $recurso->setReferencia($archivo->getClientOriginalName());
                $recurso->setReferenciaFile($archivoFileName);
                $recurso->setMimeType($archivo->getMimeType());
            } else {
                if ($recurso->getTipo()->getNombre() == TipoRecurso::REFERENCE_URL && $recurso->getReferenciaFile()) {
                    $fileUploader->deleteFile(FileUploader::TEXTOS, $recurso->getReferenciaFile(), $recurso->getLibro()->getNombre() . '/' . FileUploader::RECURSOS, false);
                    $recurso->setReferenciaFile('');
                    $recurso->setMimeType('');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('recurso_index');
        }

        return $this->render('recurso/edit.html.twig', [
            'recurso' => $recurso,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/load", name="resourceLoader", options={"expose"=true}, methods={"GET", "POST"})
     * @param Recurso $recurso
     * @param FileUploader $fileUploader
     * @return BinaryFileResponse|StreamedResponse
     */
    public function resourceLoader(Recurso $recurso, FileUploader $fileUploader)
    {
        //@Todo Security files
        if ($recurso->getMimeType() === 'audio/mpeg' || $recurso->getMimeType() === 'video/mp4') {
            $path = $fileUploader->getFullUploadPath($recurso->getReferenciaFileDir());
            $response = new BinaryFileResponse($path);
        } else {
            $response = new StreamedResponse(function () use ($recurso, $fileUploader) {
                $outputStream = fopen('php://output', 'wb');
                $fileStream = $fileUploader->readStream($recurso->getReferenciaFileDir());
                stream_copy_to_stream($fileStream, $outputStream);
            });
        }

        $response->headers->set('Content-Type', $recurso->getMimeType());
        if ($recurso->getMimeType() === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $recurso->getMimeType() === 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ||
            $recurso->getMimeType() === 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' || $recurso->getMimeType() === 'application/msword' ||
            $recurso->getMimeType() === 'application/vnd.ms-powerpoint' || $recurso->getMimeType() === 'application/vnd.ms-excel' || $recurso->getMimeType() === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {

                if (preg_match('/^[\x20-\x7e]*$/', $recurso->getReferencia())) {
                    $disposition = HeaderUtils::makeDisposition(
                        HeaderUtils::DISPOSITION_ATTACHMENT,
                    
                        $recurso->getNombreRecurso()
                    );
                    $response->headers->set('Content-Disposition', $disposition);
                }
        }

        return $response;
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/delete", options={"expose"=true}, name="recurso_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $recurso = $entityManager->getRepository(Recurso::class)->find($id);
        if ($recurso) {
            $entityManager->remove($recurso);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error' => 'El elemento no existe']);
        }
    }

    public function convertToPDF(UploadedFile $file, string $path, string $destinationPath): string
    {
        $saveFilename = '';
        $extension = $file->getClientOriginalExtension();
        $pdfWriter = null;

        if ($extension === 'xls' || $extension === 'xlsx') {
            $phpWordXls = IOFactory::load($path);
            $pdfWriter = IOFactory::createWriter($phpWordXls, 'Dompdf');

        } else if ($extension === 'doc') {
            Settings::setPdfRendererPath($this->base_path . '/vendor/dompdf/dompdf');
            Settings::setPdfRendererName('DomPDF');

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path, 'MsDoc');
            $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');

        } else if ($extension === 'docx') {

            Settings::setPdfRendererPath($this->base_path . '/vendor/dompdf/dompdf');
            Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
            $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');

//            $path = explode('.',$safeFilename);
//            array_pop($path);
//            $path = implode($path);

//            $this->fileUploader->deleteFile(FileUploader::TEXTOS, $filename, $recurso->getLibro()->getNombre().'/'.FileUploader::RECURSOS, false);
        }

        if ($pdfWriter) {
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $saveFilename = $this->generatePathName($originalFileName);
            $this->uploadsDirectory->createDir($destinationPath);
            $destinationPath = $this->fileUploader->getFullUploadPath($destinationPath);
            $destinationPath .= $saveFilename;
            $pdfWriter->save($destinationPath);
        }

        return $saveFilename;
    }

    private function generatePathName(string $originalName): string
    {
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalName);
        $fileName = $safeFilename . '-' . uniqid() . '.pdf';
        return $fileName;
    }

}
