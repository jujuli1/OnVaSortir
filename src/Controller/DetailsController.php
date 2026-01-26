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

final class DetailsController extends AbstractController
{
    //Route de détails d'une aire
    #[Route('/details/{id}', name: 'app_details')]
    public function index(EntityManagerInterface $em, int $id, Request $request): Response
    {


        $user = $this->getUser();
        $inscriptions= $em->getRepository(Inscription::class)->findOneBy([
            'utilisateur' => $user,
            'sortie' => $id,]);

        $sorties = $em->getRepository(Sortie::class)->find($id);

        //formulaire d'update d'une sortie
        $form = $this->createForm(SortieType::class, $sorties);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            //id utilisateur connecté remplit automatiquement le champ utilisateur_id de la table sortie
            $sorties->setUtilisateur($this->getUser());
            $em->persist($sorties);
            $em->flush();
        }

        //formulaire de suppression
        $formSupp = $this->createForm(DeleteSortieType::class, $sorties);
        $formSupp->handleRequest($request);

        //formulaire de changement de statuts
        $formUpdate = $this->createForm(UpdateSortieType::class, $sorties);
        $formUpdate->handleRequest($request);

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

        if($formUpdate->isSubmitted() && $formUpdate->isValid()){

            //securité
            if ($this->getUser() !== $sorties->getUtilisateur()) {
                throw $this->createAccessDeniedException(
                    'Vous n etes pas l organisateur '
                );
            }


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
            'formUpdate' => $formUpdate->createView(),


        ]);
    }



}
