<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    /**
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     * @Route("/role", name="role")
     */
    public function index(Request $request)
    {
        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $role->setRolename(Role::ROLE_PREFIX.strtoupper($role->getRolename()));

            $entityManager->persist($role);
            $entityManager->flush();

            $this->redirectToRoute('role');
        }

        return $this->render('role/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
