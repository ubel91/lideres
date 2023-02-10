<?php

namespace App\Controller;

use App\Entity\Canton;
use App\Entity\Role;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\FileUploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


/**
 * Class RegistrationController
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    const DASHBOARD = 'dashboard';

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface  */
    private $urlGenerator;

    /**
     * RegistrationController constructor.
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    /**
     * @Route("/register", name="app_register")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param FileUploader $fileUploader
     * @param MailerInterface $mailer
     *
     * @return Response
     *
     * @throws Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, FileUploader $fileUploader, MailerInterface $mailer): Response
    {
        $user = new User();
        $user->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $foto */
                $foto = $form['photo']->getData();
                if ($foto) {
                    $fotoFileName = $fileUploader->upload($foto, FileUploader::FOTO_PERFIL, '', $user->getUsername());
                    $user->setPhoto($fotoFileName);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $rolesChecker = $user->getRoles();
                if (count($rolesChecker) === 1) {
                    if ($rolesChecker[0] === Role::ROLE_ESTUDIANTE){
                        $estudiante = $user->getEstudiantesForm();
                        $user->setEstudiantes($estudiante);
                        $estudiante->setUser($user);
                        $entityManager->persist($estudiante);
                    } elseif ($rolesChecker[0] === Role::ROLE_PROFESOR) {
                        $profesor = $user->getProfesorForm();
                        $user->setProfesor($profesor);
                        $profesor->setUser($user);
                        $entityManager->persist($profesor);

                    }
                }

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->addFlash('success', User::REG_SUCCESS);

            $entityManager->persist($user);
            $entityManager->flush();

//            $token = $user->getConfirmationToken();
//            $subject = 'Correo de confirmación';
//            $email = $user->getEmail();

//            $linkToConfirmation = $this->urlGenerator->generate('confirm', [
//                'token' => $token,
//            ], UrlGeneratorInterface::ABSOLUTE_URL);

//            $message = (new Email())
//                ->from('egluzua@gmail.com')
//                ->to($email)
//                ->subject($subject)
//                ->html($linkToConfirmation)
//            ;

//            $mailer->send($message);

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user'=>$user
        ]);
    }

    /**
     * @Route("/canton_provincia", name="canton_by_provincia", condition="request.headers.get('X-Requested-With') == 'XMLHttpRequest'")
     * @param Request $request
     * @return JsonResponse
     */
    public function cantonByProvincia(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $canton = $em->getRepository(Canton::class)->findByProvincia($id);
        return new JsonResponse($canton);
    }

    /**
     * @Route("/profile/{id}", name="profile")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param FileUploader $fileUploader
     * @param int|null $id
     * @return Response
     * @throws Exception
     */
    public function editProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, FileUploader $fileUploader, int $id = null): Response
    {
        $route = '';
        if (!$id){
            $user = $this->getUser();
            $route = self::DASHBOARD;
        }
        else
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);


        $user->setEstudiantesForm($user->getEstudiantes());
        $user->setProfesorForm($user->getProfesor());

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $foto */
            $foto = $form['photo']->getData();
            if ($foto) {
                $fotoFileName = $fileUploader->upload($foto, FileUploader::FOTO_PERFIL, $user->getPhoto(), $user->getUsername());
                $user->setPhoto($fotoFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $rolesChecker = $user->getRoles();
            if (count($rolesChecker) === 1) {
                if ($rolesChecker[0] === Role::ROLE_ESTUDIANTE){
                    $estudiante = $user->getEstudiantesForm();
                    $user->setEstudiantes($estudiante);
                    $estudiante->setUser($user);
                } elseif ($rolesChecker[0] === Role::ROLE_PROFESOR) {
                    $profesor = $user->getProfesorForm();
                    $user->setProfesor($profesor);
                    $profesor->setUser($user);
                }
            }

            // encode the plain password
            if ($form->get('plainPassword')->getData())
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

            $this->addFlash('success', User::UPD_SUCCESS);

            $entityManager->flush();

            if ($route !== self::DASHBOARD){
                $roles = $user->getRoles();
                if ($roles[0] === Role::ROLE_ESTUDIANTE){
                    $route = 'superListadoEstudiantes';
                } elseif ($roles[0] === Role::ROLE_PROFESOR) {
                    $route = 'superListadoProfesores';
                }

                $user = $this->getUser();
            }

            return $this->redirectToRoute($route);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user
        ]);
    }



    /**
     * @Route("/register/confirm/{token}", name="confirm")
     * @param $token
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function confirm($token, UserRepository $userRepository)
    {

        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('El usuario con token de confirmación "%s" no existe', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('dashboard');

    }
}
