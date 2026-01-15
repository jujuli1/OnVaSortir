<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GestionProfilController extends AbstractController
{
    #[Route('/profil/gestion', name: 'app_gestion_profil')]
    public function index(): Response
    {


        return $this->render('gestion_profil/index.html.twig', [
            'controller_name' => 'GestionProfilController',
        ]);
    }
}
