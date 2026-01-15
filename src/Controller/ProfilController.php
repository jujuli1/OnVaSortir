<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(EntityManagerInterface $em,): Response
    {

        $user = $this->getUser();
        $inscriptions= $em->getRepository(Inscription::class)->findBy(['utilisateur' => $user]);



        return $this->render('profil/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    #[Route('/profil/desistement/{id}', name: 'app_sortie_desistement')]
    public function desistement(int $id, Request $request,EntityManagerInterface $em,): Response
    {

        // se desisté d'une sortie

        $user = $this->getUser();
        $sortie = $em->getRepository(Sortie::class)->find($id);

        // Recup linscription existante
        $inscription = $em->getRepository(Inscription::class)
            ->findOneBy([
                'utilisateur' => $user,
                'sortie' => $sortie
            ]);




        if($inscription){
            $em->remove($inscription);
            $em->flush();
            return $this->redirectToRoute('app_profil');
        }



        // Recup toutes les inscriptions de l utilisateur pour le profil
        $inscriptions = $em->getRepository(Inscription::class)
            ->findBy(['utilisateur' => $user]);



        return $this->render('profil/index.html.twig',[
            'inscriptions' => $inscriptions,


        ]);
    }


}
