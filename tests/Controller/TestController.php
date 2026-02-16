<?php
namespace App\Tests\Controller;

use App\Entity\Inscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
#[Route('/test/count-inscriptions', name: 'test_count_inscriptions')]
public function countInscriptions(EntityManagerInterface $em): JsonResponse
{
$count = $em->getRepository(Inscription::class)->count([]);
return $this->json(['count' => $count]);
}
}
