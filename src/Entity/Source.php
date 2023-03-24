<?php

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pattern = null;

    #[ORM\Column(length: 255)]
    private ?string $importType = null;

    #[ORM\Column(length: 255)]
    private ?string $templateCode = null;

    #[ORM\Column(length: 255)]
    private ?string $montage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $credential = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setPattern(?string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getImportType(): ?string
    {
        return $this->importType;
    }

    public function setImportType(string $importType): self
    {
        $this->importType = $importType;

        return $this;
    }

    public function getTemplateCode(): ?string
    {
        return $this->templateCode;
    }

    public function setTemplateCode(string $templateCode): self
    {
        $this->templateCode = $templateCode;

        return $this;
    }

    public function getMontage(): ?string
    {
        return $this->montage;
    }

    public function setMontage(string $montage): self
    {
        $this->montage = $montage;

        return $this;
    }

    public function getExterne(): ?string
    {
        return $this->externe;
    }

    public function setExterne(?string $externe): self
    {
        $this->externe = $externe;

        return $this;
    }

    public function getCredential(): ?string
    {
        return $this->credential;
    }

    public function setCredential(?string $credential): self
    {
        $this->credential = $credential;

        return $this;
    }
}
