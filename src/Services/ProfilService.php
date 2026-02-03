<?php

namespace App\Services;



use App\Entity\Inscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilService implements ProfilInterface
{

    public function index(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): bool
    {

        return true;
    }
}
