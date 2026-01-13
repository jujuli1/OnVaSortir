<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Campus>
     */
    #[ORM\OneToMany(targetEntity: Campus::class, mappedBy: 'ville')]
    private Collection $campuses;

    public function __construct()
    {
        $this->campuses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Campus>
     */
    public function getCampuses(): Collection
    {
        return $this->campuses;
    }

    public function addCampus(Campus $campus): static
    {
        if (!$this->campuses->contains($campus)) {
            $this->campuses->add($campus);
            $campus->setVille($this);
        }

        return $this;
    }

    public function removeCampus(Campus $campus): static
    {
        if ($this->campuses->removeElement($campus)) {
            // set the owning side to null (unless already changed)
            if ($campus->getVille() === $this) {
                $campus->setVille(null);
            }
        }

        return $this;
    }
}
