<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Prestations::class)]
    private Collection $Prestations;

    public function __construct()
    {
        $this->Prestations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Prestations>
     */
    public function getPrestations(): Collection
    {
        return $this->Prestations;
    }

    public function addPrestation(Prestations $prestation): self
    {
        if (!$this->Prestations->contains($prestation)) {
            $this->Prestations[] = $prestation;
            $prestation->setCategorie($this);
        }

        return $this;
    }

    public function removePrestation(Prestations $prestation): self
    {
        if ($this->Prestations->removeElement($prestation)) {
            // set the owning side to null (unless already changed)
            if ($prestation->getCategorie() === $this) {
                $prestation->setCategorie(null);
            }
        }

        return $this;
    }
}
