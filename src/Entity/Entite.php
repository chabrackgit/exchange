<?php

namespace App\Entity;

use App\Repository\EntiteRepository;
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
}
