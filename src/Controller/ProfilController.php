<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProfilController extends AbstractController
{
    //Route profil
    #[Route('/profil', name: 'app_profil')]
    public function index(EntityManagerInterface $em,Request $request,
                          SluggerInterface $slugger,): Response
    {

        $user = $this->getUser();
        $inscriptions= $em->getRepository(Inscription::class)->findBy(['utilisateur' => $user]);



        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);  // //!\\


        //formulaire photo de profil
        if($form->isSubmitted() ) {

            $photoFile = $form['photo']->getData();


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

                $user->setPhoto($newFilename);


            }
            $em->flush();

            return $this->redirectToRoute('app_profil');
        }
        $sortie = new Sortie();
        $sortie->setOrganisateur($user->getNom());




        $formCreateSortie = $this->createForm(SortieType::class, $sortie);
        $formCreateSortie->handleRequest($request);
        //formulaire de création d'une sortie
        if($formCreateSortie->isSubmitted() && $formCreateSortie->isValid()){

            $photoFile = $formCreateSortie->get('photo')->getData();




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

            return $this->redirectToRoute('app_profil');


        }






        return $this->render('profil/index.html.twig', [
            'inscriptions' => $inscriptions,
            'user' => $user,
            'form' => $form->createView(),
            'formCreateSortie' => $formCreateSortie->createView(),
        ]);
    }

    #[Route('/profil/desistement/{id}', name: 'app_sortie_desistement')]
    public function desistement(int $id,EntityManagerInterface $em,): Response
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



        return $this->render('profil/indexAdminRegister.html.twig',[
            'inscriptions' => $inscriptions,


        ]);
    }




}
