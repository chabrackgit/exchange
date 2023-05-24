<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierRepository::class)]
class Fichier
{

    // constantes EXPORT KYRIBA

    CONST SESSION_EXPORT = 'EXPORT';
    CONST SESSION_REPORT = 'REPORT';
    CONST SESSION_IMPORT = 'IMPORT';
    CONST SESSION_BANKFW = 'BANKFW';
    CONST SESSION_PAYMFW = 'PAYMFW';

    // constantes de templates Codes 
    CONST TEMPLATE_CODE_EXPORT_PS  ='PS';
    CONST TEMPLATE_CODE_REPORT  ='BANK04';
    CONST TEMPLATE_CODE_EXPORT_UBW  ='RDCV';

    CONST TYPE_TRANSFERT_BANK = 'BANK';

    CONST KYRIBA_CUSTOMER = 'INSEEC';
    CONST KYRIBA_NCVERSION= 'NC4';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $nomKyriba = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $customer = null;

    #[ORM\Column(length: 255)]
    private ?string $ncVersion = null;

    #[ORM\Column(length: 255)]
    private ?string $uid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $other = null;

    #[ORM\Column(length: 255)]
    private ?string $mimeType = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?Entite $entite = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?TemplateCode $templateCode = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?Etablissement $etablissement = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?TypeTransfert $typeTransfert = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?User $user = null;


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

    public function getNomKyriba(): ?string
    {
        return $this->nomKyriba;
    }

    public function setNomKyriba(string $nomKyriba): self
    {
        $this->nomKyriba = $nomKyriba;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getNcVersion(): ?string
    {
        return $this->ncVersion;
    }

    public function setNcVersion(string $ncVersion): self
    {
        $this->ncVersion = $ncVersion;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }


    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

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

    public function getEntite(): ?Entite
    {
        return $this->entite;
    }

    public function setEntite(?Entite $entite): self
    {
        $this->entite = $entite;

        return $this;
    }

    public function getTemplateCode(): ?TemplateCode
    {
        return $this->templateCode;
    }

    public function setTemplateCode(?TemplateCode $templateCode): self
    {
        $this->templateCode = $templateCode;

        return $this;
    }

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): self
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getTypeTransfert(): ?TypeTransfert
    {
        return $this->typeTransfert;
    }

    public function setTypeTransfert(?TypeTransfert $typeTransfert): self
    {
        $this->typeTransfert = $typeTransfert;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
