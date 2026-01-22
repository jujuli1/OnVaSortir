<?php
// tests/Entity/SortieTest.php

namespace App\Tests\Entity;

use App\Entity\Sortie;
use PHPUnit\Framework\TestCase;

class SortieTest extends TestCase
{
    public function testSortie(): void
    {
        $sortie = new Sortie();
        $sortie->setNom('Apéro Symfony');

        $this->assertSame('Apéro Symfony', $sortie->getNom());
    }

    public function testDateHeureSortie(): void
    {
        $date = new \DateTime('2026-01-22 18:00');
        $sortie = new Sortie();
        $sortie->setDateHeure($date);

        $this->assertEquals($date, $sortie->getDateHeure());
    }
}
