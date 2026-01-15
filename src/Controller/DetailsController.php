<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class DetailsController extends AbstractController
{
    #[Route('/details/{id}', name: 'app_details')]
    public function index(EntityManagerInterface $em, int $id, Request $request): Response
    {


        $user = $this->getUser();
        $inscriptions= $em->getRepository(Inscription::class)->findOneBy([
            'utilisateur' => $user,
            'sortie' => $id,]);

        $sorties = $em->getRepository(Sortie::class)->find($id);

        $showForm = false;
        if($user->getId() === $sorties->getUtilisateur()->getId()){
            $showForm=true;
        }

        return $this->render('details/index.html.twig', [
            'sorties' => $sorties,
            'inscriptions' => $inscriptions,
            'showForm' => $showForm,

        ]);
    }
}
