<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SortieType;
use App\Services\SortieInterface;
use App\Services\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function publicSortie(EntityManagerInterface $em,Request $request, SluggerInterface $slugger ): Response
    {
        return $this->sortie($em,$request, $slugger);
    }


    protected function sortie(EntityManagerInterface $em,Request $request, SluggerInterface $slugger): Response
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

            $photoFile = $form->get('photo')->getData();




            if ($photoFile) {
                //retire extention fichier
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                //genere id unique
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                $photoFile->move(
                // a trouver dans service.yml
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                $sortie->setPhoto($newFilename);


            }
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
    public function publicVitrine(EntityManagerInterface $em, SortieService $service): Response
    {
        return $this->vitrine($em, $service);
    }




    protected function vitrine(EntityManagerInterface $em, SortieService $service): Response
    {

        $user = $this->getUser();

        $data = $service->vitrine($em, $user);

        return $this->render('sortie/vitrine.html.twig', $data);

    }

    #[Route('/sortie/inscription/{id}', name: 'app_sortie_inscription')]
    public function inscription(int $id,EntityManagerInterface $em, SortieInterface $sortieService): Response
    {

        // s'inscrire a une sortie

        $user = $this->getUser();
        $sortie = $em->getRepository(Sortie::class)->find($id);

        if ($sortieService->inscription($user, $sortie)) {
            $this->addFlash('success', 'Inscription confirmée');
        } else {
            $this->addFlash('warning', 'Déjà inscrit');
        }

        return $this->redirectToRoute('app_sortie_vitrine');



        $this->addFlash('success', 'Vous etes inscrit ! !');
        return $this->render('sortie/vitrine.html.twig',[
            'sorties' => $sortie,


        ]);
    }




}
