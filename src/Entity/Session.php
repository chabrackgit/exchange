<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: TemplateCode::class)]
    private Collection $templateCodes;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: TypeTransfert::class)]
    private Collection $typeTransferts;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Fichier::class)]
    private Collection $fichiers;

    public function __construct()
    {
        $this->templateCodes = new ArrayCollection();
        $this->typeTransferts = new ArrayCollection();
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * @return Collection<int, TemplateCode>
     */
    public function getTemplateCodes(): Collection
    {
        return $this->templateCodes;
    }

    public function addTemplateCode(TemplateCode $templateCode): self
    {
        if (!$this->templateCodes->contains($templateCode)) {
            $this->templateCodes->add($templateCode);
            $templateCode->setSession($this);
        }

        return $this;
    }

    public function removeTemplateCode(TemplateCode $templateCode): self
    {
        if ($this->templateCodes->removeElement($templateCode)) {
            // set the owning side to null (unless already changed)
            if ($templateCode->getSession() === $this) {
                $templateCode->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeTransfert>
     */
    public function getTypeTransferts(): Collection
    {
        return $this->typeTransferts;
    }

    public function addTypeTransfert(TypeTransfert $typeTransfert): self
    {
        if (!$this->typeTransferts->contains($typeTransfert)) {
            $this->typeTransferts->add($typeTransfert);
            $typeTransfert->setSession($this);
        }

        return $this;
    }

    public function removeTypeTransfert(TypeTransfert $typeTransfert): self
    {
        if ($this->typeTransferts->removeElement($typeTransfert)) {
            // set the owning side to null (unless already changed)
            if ($typeTransfert->getSession() === $this) {
                $typeTransfert->setSession(null);
            }
        }

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
            $fichier->setSession($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): self
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getSession() === $this) {
                $fichier->setSession(null);
            }
        }

        return $this;
    }
}
