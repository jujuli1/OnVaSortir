<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;

final class GestionProfilController extends AbstractController
{
    #[Route('/profil/gestion', name: 'app_gestion_profil')]
    public function index(Request $request,
                          SluggerInterface $slugger,
                          EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);  // //!\\

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


        return $this->render('gestion_profil/index.html.twig', [

            'form' => $form->createView(),
        ]);
    }
}
