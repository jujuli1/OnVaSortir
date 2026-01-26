<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {

        $sorties = $em->getRepository(Sortie::class)->findAll();




        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sorties' => $sorties,
        ]);
    }
}
