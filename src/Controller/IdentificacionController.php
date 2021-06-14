<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IdentificacionController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/identificacion", name="identificacion")
     */
    public function index()
    {
        return $this->render('identificacion/index.html.twig', [
            'controller_name' => 'IdentificacionController',
        ]);
    }
}
