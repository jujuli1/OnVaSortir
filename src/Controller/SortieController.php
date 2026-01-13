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
        $user = $this->getUser();
        $sortie->setOrganisateur($user->getNom());

        $fakeSortie = (object) [
            'id' => 1,
            'nom' => 'Sortie test',
        ];

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $sortie->setUtilisateur($this->getUser());
            $em->persist($sortie);
            $em->flush();
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

        $sorties = $em->getRepository(Sortie::class)->findAll();


        return $this->render('sortie/vitrine.html.twig',[
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie/inscription/{id}', name: 'app_sortie_inscription')]
    public function inscription(int $id, Request $request,EntityManagerInterface $em,): Response
    {

        $inscription = new Inscription();
        $user = $this->getUser();
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $inscription->setSortie($sortie);
        $inscription->setUtilisateur($user);

        $em->persist($inscription);
        $em->flush();

        $this->addFlash('success', 'Vous etes inscrit ! !');
        return $this->render('profil/index.html.twig',[

        ]);
    }


}
