<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\DeleteSortieType;
use App\Form\SortieType;
use App\Form\UpdateSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

final class DetailsController extends AbstractController
{
    //Route de détails d'une aire
    #[Route('/details/{id}', name: 'app_details')]
    public function publicDetails(EntityManagerInterface $em, int $id, Request $request,SluggerInterface $slugger,): Response
    {
        return $this->details($em, $id, $request,$slugger);
    }


    protected function details(EntityManagerInterface $em, int $id, Request $request,SluggerInterface $slugger,): Response
    {


        $user = $this->getUser();
        $inscriptions= $em->getRepository(Inscription::class)->findOneBy([
            'utilisateur' => $user,
            'sortie' => $id,]);

        $sorties = $em->getRepository(Sortie::class)->find($id);

        //formulaire d'update d'une sortie
        $form = $this->createForm(UpdateSortieType::class, $sorties);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

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

                $sorties->setPhoto($newFilename);



                //securité
                if ($this->getUser() !== $sorties->getUtilisateur()) {
                    throw $this->createAccessDeniedException(
                        'Vous n etes pas l organisateur '
                    );
                }

                $em->flush();

                return $this->redirectToRoute('app_sortie_vitrine');


            }

            //id utilisateur connecté remplit automatiquement le champ utilisateur_id de la table sortie
            $sorties->setUtilisateur($this->getUser());
            $em->persist($sorties);
            $em->flush();
        }

        //formulaire de suppression
        $formSupp = $this->createForm(DeleteSortieType::class, $sorties);
        $formSupp->handleRequest($request);


        if($formSupp->isSubmitted() && $formSupp->isValid()){


            //securité
            if ($this->getUser() !== $sorties->getUtilisateur()) {
                throw $this->createAccessDeniedException(
                    'Vous n etes pas l organisateur '
                );
            }

            $em->remove($sorties);
            $em->flush();
            return $this->redirectToRoute('app_sortie_vitrine');

        }





        //formulaire de gestion organisateur d'une sortie
        $showForm = false;
        //id utilisateur connecté est le même que l'id utilisateur qui as créer la sortie
        if($user->getId() === $sorties->getUtilisateur()->getId()){

            $showForm=true;
        }

        return $this->render('details/index.html.twig', [
            'sorties' => $sorties,
            'inscriptions' => $inscriptions,
            'showForm' => $showForm,
            'form' => $form->createView(),
            'formSupp' => $formSupp->createView(),



        ]);
    }



}
