<?php

namespace App\Controller;

use App\Entity\Libro;
use App\Entity\Materia;
use App\Entity\Role;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Service\FileUploader;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);
        $libros = [];
        $librosJson = [];
        $emLibros = $this->getDoctrine()->getManager();
        $route = 'dashboard/index.html.twig';

        if(count($user->getRoles()) === 1 ){
            $roles = $user->getRoles()[0];
            if ($roles === Role::ROLE_ESTUDIANTE){
                $libros = $emLibros->getRepository(Libro::class)->findByRoleEstAndNotActivated();
                $libros = $this->cleanSearchBooks($libros, $user, $roles);
            }
            elseif ($roles === Role::ROLE_PROFESOR){
                $libros = $emLibros->getRepository(Libro::class)->findByRoleDocAndNotActivated();
                $libros = $this->cleanSearchBooks($libros, $user, $roles);
            }
            else {
                $route = 'super/index.html.twig';
            }
            $serializer = $this->get('serializer');
            $librosJson = $serializer->serialize($libros, 'json', ['groups' => ['libro']]);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            $currentPath = $request->headers->get('referer');

            if ($request->isXmlHttpRequest()){
                return new JsonResponse(['success' => 'Clave actualizada correctamente']);
            }

            return $this->redirect($currentPath);
        }
        elseif ($form->isSubmitted() && !$form->isValid() && $request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'Las claves no coinciden o no son válidas por favor rectifique...']);
        }


        return $this->render($route, [
            'books' => $libros,
            'booksJson' => $librosJson
        ]);
    }
    /**
     * @Route("/changePass", name="changePass")
     */
    public function changePasswordForm()
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        return $form->createView();
    }

    /**
     * @Route("/user/photo/{id}", options={"expose"=true}, name="photo_change", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param int|null $id
     * @return Response
     * @throws Exception
     */
    public function photoChange(Request $request, FileUploader $fileUploader, int $id = null): Response
    {
//        $id = $request->get('id');
        /** @var UploadedFile $foto */
        $foto = $request->files->get('photo');

        if (!$id)
            $user = $this->getUser();
        else
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $photoName = $user->getPhoto();
        if ($foto) {
            $fotoFileName = $fileUploader->upload($foto, FileUploader::FOTO_PERFIL, $photoName, $user->getUsername() );
            $user->setPhoto($fotoFileName);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return new JsonResponse(['success'=> 'true']);
    }

    /**
     * @Route("/dashboard/photo/{id}", name="photoProfile", methods={"GET"})
     */
    public function showPhotoProfile(FileUploader $fileUploader, int $id = null)
    {
        //@Todo Security files
        if (!$id)
            $user = $this->getUser();
        else
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        return new StreamedResponse(function () use ($user, $fileUploader){
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $fileUploader->readStream($user->getPhotoDir());

            stream_copy_to_stream($fileStream, $outputStream);
        });
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

        $materias = $this->getDoctrine()->getRepository(Materia::class)->findAll();
        $ouput = [];

        foreach ($materias as $materia){
            $ouput[$materia->getNombre()] = $this->getByMateria($materia,$array);
        }

        return $ouput ? $ouput : [];
    }

    /**
     * @param \App\Entity\Materia $materia
     * @param array $books
     * @return array
     */
    public function getByMateria(Materia $materia, $books = array()){
        $ouput = [];
        /** @var Libro $book */
        foreach ($books as $book) {
            if ($book->getCatalogo()->getMaterias() === $materia)
                $ouput[]=$book;
        }
        return $ouput;
    }


}
