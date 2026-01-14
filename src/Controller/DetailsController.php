<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DetailsController extends AbstractController
{
    #[Route('/details/{id}', name: 'app_details')]
    public function index(EntityManagerInterface $em, int $id): Response
    {
        $user = $this->getUser();
        $inscriptions= $em->getRepository(Inscription::class)->findBy(['utilisateur' => $user]);
        $sorties = $em->getRepository(Sortie::class)->find($id);
        return $this->render('details/index.html.twig', [
            'sorties' => $sorties,
            'inscriptions' => $inscriptions,
        ]);
    }
}
