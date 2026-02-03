<?php

namespace App\Services;



use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

interface ProfilInterface
{
    public function index(EntityManagerInterface $em,Request $request,
                          SluggerInterface $slugger): bool;
}
