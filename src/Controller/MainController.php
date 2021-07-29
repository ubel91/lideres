<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 29/07/20
 * Time: 23:18
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="app_home_page")
     */
    public function index():Response
    {
        // return $this->render('home_page.html.twig');
        return $this->redirect('http://inicio.corplideres.com/?page_id=9');
    }
}