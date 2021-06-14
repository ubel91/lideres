<?php

namespace App\Controller;

use App\Entity\Canton;
use App\Form\CantonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LocationController extends AbstractController
{
    /**
     * @Route("/location", name="location")
     * @return Response
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $canton = $em->getRepository(Canton::class)->findWithProvincia();

        return $this->render('location/index.html.twig', [
            'controller_name' => 'LocationController',
            'canton' => $canton
        ]);
    }

    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/insert_canton", name="insert_canton")
     * @param Request $request
     * @return Response
     */

    public function insertCanton(Request $request)
    {
        $canton = new Canton();

        $form = $this->createForm(CantonType::class, $canton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($canton);
            $entityManager->flush();

            $this->redirectToRoute('insert_canton');
        }

        return $this->render('location/add_canton.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
