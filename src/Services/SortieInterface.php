<?php

namespace App\Services;

use App\Entity\Utilisateur;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

interface SortieInterface
{
    public function inscription(Utilisateur $utilisateur, Sortie $sortie): bool;

    public function vitrine(EntityManagerInterface $em, Utilisateur $user): array;
}
