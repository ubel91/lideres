<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 29/07/20
 * Time: 23:18
 */

namespace App\Controller;


use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="app_home_page")
     */
    public function index():Response
    {
        $user = $this->getUser();

        if ($user->getRoles()[0] === Role::ROLE_ESTUDIANTE || $user->getRoles()[0] === Role::ROLE_PROFESOR)
            return new RedirectResponse($this->generateUrl('textos'));
        return new RedirectResponse($this->generateUrl('dashboard'));
//         return $this->render('home_page.html.twig');
//        return $this->redirect('http://inicio.corplideres.com/?page_id=9');
    }
}