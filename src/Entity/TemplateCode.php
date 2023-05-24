<?php

namespace App\Entity;

use App\Repository\TemplateCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateCodeRepository::class)]
class TemplateCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'templateCodes')]
    private ?Session $session = null;

    #[ORM\OneToMany(mappedBy: 'templateCode', targetEntity: Fichier::class)]
    private Collection $fichiers;

    #[ORM\Column(length: 255)]
    private ?string $dossier = null;

    #[ORM\Column(length: 255)]
    private ?string $cheminBackup = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cheminImport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cheminBackupImport = null;

    public function __construct()
    {
        $this->fichiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function __toString()
    {
        return $this->libelle;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
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
            $fichier->setTemplateCode($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): self
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getTemplateCode() === $this) {
                $fichier->setTemplateCode(null);
            }
        }

        return $this;
    }

    public function getDossier(): ?string
    {
        return $this->dossier;
    }

    public function setDossier(string $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getCheminBackup(): ?string
    {
        return $this->cheminBackup;
    }

    public function setCheminBackup(string $cheminBackup): self
    {
        $this->cheminBackup = $cheminBackup;

        return $this;
    }

    public function getCheminImport(): ?string
    {
        return $this->cheminImport;
    }

    public function setCheminImport(?string $cheminImport): self
    {
        $this->cheminImport = $cheminImport;

        return $this;
    }

    public function getCheminBackupImport(): ?string
    {
        return $this->cheminBackupImport;
    }

    public function setCheminBackupImport(?string $cheminBackupImport): self
    {
        $this->cheminBackupImport = $cheminBackupImport;

        return $this;
    }
}
