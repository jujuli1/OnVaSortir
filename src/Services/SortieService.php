<?php

namespace App\Services;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class SortieService implements SortieInterface
{

    public function __construct(
        private EntityManagerInterface $em
    ) {}


    public function inscription(Utilisateur $utilisateur, Sortie $sortie): bool
    {

        //recup id user connecté + id sortie
        $exist = $this->em->getRepository(Inscription::class)->findOneBy([
            'utilisateur' => $utilisateur,
            'sortie' => $sortie
        ]);

        if (!$exist) {
            $inscription = new Inscription();
            $inscription->setUtilisateur($utilisateur);
            $inscription->setSortie($sortie);
            $inscription->setDateInscription(new \DateTime());
            $inscription->setStatusInscription(true);

            $this->em->persist($inscription);
            $this->em->flush();


        }

        return true;
    }

    public function vitrine(EntityManagerInterface $em, Utilisateur $user): array
    {
        //affiche les sortie en bdd sur la page /vitrine
        $sorties = $em->getRepository(Sortie::class)->findAll();



        $inscriptions = [];

        //boucle d'affichage
        foreach ($sorties as $sortie) {
            $inscription = $em->getRepository(Inscription::class)->findOneBy(
                ['utilisateur' => $user,
                    'sortie' => $sortie
                ]);
            $inscriptions[$sortie->getId()] = $inscription;
        }

        $inscrit = $em->getRepository(Inscription::class)->findOneBy(
            ['utilisateur' => $user,
                'sortie' => $sorties
            ]);
        return [
            'sorties' => $sorties,
            'inscriptions' => $inscriptions,
            'inscrit' => $inscrit,
        ];
    }




}
