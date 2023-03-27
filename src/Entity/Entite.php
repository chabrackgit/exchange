<?php

namespace App\Entity;

use App\Repository\EntiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntiteRepository::class)]
class Entite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codeKyriba = null;

    #[ORM\Column(length: 255)]
    private ?string $codePeopleSoft = null;

    #[ORM\Column(length: 255)]
    private ?string $codeUbw = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'entite', targetEntity: Fichier::class)]
    private Collection $fichiers;

    public function __construct()
    {
        $this->fichiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeKyriba(): ?string
    {
        return $this->codeKyriba;
    }

    public function setCodeKyriba(string $codeKyriba): self
    {
        $this->codeKyriba = $codeKyriba;

        return $this;
    }

    public function getCodePeopleSoft(): ?string
    {
        return $this->codePeopleSoft;
    }

    public function setCodePeopleSoft(string $codePeopleSoft): self
    {
        $this->codePeopleSoft = $codePeopleSoft;

        return $this;
    }

    public function getCodeUbw(): ?string
    {
        return $this->codeUbw;
    }

    public function setCodeUbw(string $codeUbw): self
    {
        $this->codeUbw = $codeUbw;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichier $fichier): self
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers->add($fichier);
            $fichier->setEntite($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): self
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getEntite() === $this) {
                $fichier->setEntite(null);
            }
        }

        return $this;
    }
}
