<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(Request $request,EntityManagerInterface $em,): Response
    {
        $sortie = new Sortie();
        //recup user connecté
        $user = $this->getUser();
        $sortie->setOrganisateur($user->getNom());

        //fausse sortie de test
        $fakeSortie = (object) [
            'id' => 1,
            'nom' => 'Sortie test',
        ];

        //formulaire de création d'une sortie
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //id utilisateur connecté remplit automatiquement le champ utilisateur_id de la table sortie
            $sortie->setUtilisateur($this->getUser());
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie_vitrine');
        }


        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'fakeSortie' => $fakeSortie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sortie/vitrine', name: 'app_sortie_vitrine')]
    public function vitrine(Request $request,EntityManagerInterface $em,): Response
    {

        //affiche les sortie en bdd sur la page /vitrine
        $sorties = $em->getRepository(Sortie::class)->findAll();

        $user = $this->getUser();
        $inscriptions = [];

        //boucle d'affichage
        foreach ($sorties as $sortie) {
            $inscription = $em->getRepository(Inscription::class)->findOneBy(
                ['utilisateur' => $user,
                    'sortie' => $sortie
            ]);
            $inscriptions[$sortie->getId()] = $inscription;
        }


        return $this->render('sortie/vitrine.html.twig',[
            'sorties' => $sorties,
            'inscriptions' => $inscriptions,
        ]);
    }

    #[Route('/sortie/inscription/{id}', name: 'app_sortie_inscription')]
    public function inscription(int $id, Request $request,EntityManagerInterface $em,): Response
    {

        // s'inscrire a une sortie
        $inscription = new Inscription();
        $user = $this->getUser();
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $inscription->setSortie($sortie);
        $inscription->setUtilisateur($user);

        $inscription->setDateInscription(new \DateTime());
        $inscription->setStatusInscription(True);

        $em->persist($inscription);
        $em->flush();

        $this->addFlash('success', 'Vous etes inscrit ! !');
        return $this->render('sortie/vitrine.html.twig',[
            'sorties' => $sortie,


        ]);
    }




}
