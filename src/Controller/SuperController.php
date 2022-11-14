<?php

namespace App\Controller;

use App\Entity\Estudiantes;
use App\Entity\LibroActivado;
use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Form\EstudianteType;
use App\Form\UserType;
use App\Service\FileUploader;
use Exception;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function PHPUnit\Framework\isNull;


/**
 * Class SuperController
 * @package App\Controller
 */
class SuperController extends AbstractController
{

    /** @var Pdf */
    private $pdf;


    private $kernel;


    /**
     * SuperController constructor.
     * @param Pdf $pdf
     * @param KernelInterface $kernel
     */
    public function __construct(Pdf $pdf, KernelInterface $kernel)
    {
        $this->pdf = $pdf;
        $this->kernel = $kernel;
    }


    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super", name="super")
     */

    public function index()
    {

        return $this->render('super/index.html.twig', [
            'index' => 'On prepare!!'
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/add", name="superAdd")
     */

    public function addUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash('success', User::INS_SUCCESS);

            return $this->redirectToRoute('superListado');

        }

        return $this->render('super/user/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/listado", name="superListado")
     */

    public function listado(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findUsers();

        return $this->render('super/user/listado.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/print/{id}", name="superPrint")
     * @param User $user
     * @return Response
     */
    public function printUserData(User $user)
    {
        $web_uploads_Path = $this->kernel->getProjectDir() . '/public/uploads/';
        $path = 'pdf/';
        $documento_nombre = 'reporte.pdf';

        $this->pdf->generateFromHtml(
            $this->render(
                'super/user/print.html.twig', [
                    'user' => $user
                ]
            )->getContent(),
            $web_uploads_Path . $path . $documento_nombre,
            ['encoding' => 'utf-8'],
            true);

        return $this->render('pdf_templates/iframe.html.twig', [
            'pdf' => '/uploads/' . $path . $documento_nombre,
        ]);
    }


    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/print_all/", name="superPrintAll")
     * @param Request $request
     * @return Response
     */
    public function printAll(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($request->query->get('type') === 'estudiantes') {
            $users = $entityManager->getRepository(User::class)->findAllEstudiantes();
        } else {
            $users = $entityManager->getRepository(User::class)->findAllProfesores();
        }

        $web_uploads_Path = $this->kernel->getProjectDir() . '/public/uploads/';
        $path = 'pdf/';
        $documento_nombre = 'reporte.pdf';

        $this->pdf->generateFromHtml(
            $this->render(
                'super/user/printAll.html.twig', [
                    'users' => $users,
                    'type' => $request->query->get('type')
                ]
            )->getContent(),
            $web_uploads_Path . $path . $documento_nombre,
            ['encoding' => 'utf-8'],
            true);

        return $this->render('pdf_templates/iframe.html.twig', [
            'pdf' => '/uploads/' . $path . $documento_nombre,
        ]);

    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/listado/estudiantes", name="superListadoEstudiantes")
     */

    public function listadoEstudiantes()
    {

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findUsersEstudiantes();

        return $this->render('super/user/listado_estudiantes.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/listado/profesores", name="superListadoProfesores")
     */

    public function listadoProfesores()
    {

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findUsersProfesores();

        return $this->render('super/user/listado_profesores.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/edit/{id}", name="super_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $role = $user->getRoleObj();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();


            $this->addFlash('success', User::UPD_SUCCESS);

            return $this->redirectToRoute('superListado');
        }

        return $this->render('super/user/edit.html.twig', [
            'role' => $role,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/delete", options={"expose"=true}, name="usuario_delete", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @throws Exception
     */
    public function delete(Request $request, FileUploader $fileUploader): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if ($user) {
            $libros = $entityManager->getRepository(LibroActivado::class)
                ->findByUserProfesor($id);
            foreach ($libros as $libro) {
                $entityManager->remove($libro);
                $entityManager->flush();
            }
            $resets = $entityManager->getRepository(ResetPasswordRequest::class)->findAll();
            foreach ($resets as $reset)
                if (!isNull($reset))
                    $entityManager->remove($reset);
            $entityManager->flush();
            $entityManager->remove($user);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Elemento eliminado correctamente']);
        } else {
            return new JsonResponse(['error' => 'El elemento no existe']);
        }
    }

    /**
     * @return Response
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/texts/list", name="listado_de_textos")
     */
    public function textsList(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository(LibroActivado::class)->findAll();
        return $this->render('super/user/listado_textos.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/eliminar-activado", options={"expose"=true}, name="eliminar_activado", methods={"POST"})
     * @throws Exception
     */
    public function removeLibroActivado(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $entityManager = $this->getDoctrine()->getManager();
            $codigo = $entityManager->getRepository(LibroActivado::class)->find($id);
            $entityManager->remove($codigo);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Elemento eliminado correctamente']);
        } else {
            throw new Exception('¡Operación no permitida!');
        }
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/super/print_activados/", name="print_activados")
     * @param Request $request
     * @return Response
     */
    public function printActivados(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(LibroActivado::class)->findAll();

        $web_uploads_Path = $this->kernel->getProjectDir() . '/public/uploads/';
        $path = 'pdf/';
        $documento_nombre = 'activados.pdf';

        $this->pdf->generateFromHtml(
            $this->render(
                'super/user/printActivados.html.twig', [
                    'books' => $books,
                ]
            )->getContent(),
            $web_uploads_Path . $path . $documento_nombre,
            ['encoding' => 'utf-8'],
            true);

        return $this->render('pdf_templates/iframe.html.twig', [
            'pdf' => '/uploads/' . $path . $documento_nombre,
        ]);

    }
}
