<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FichierImportRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: FichierImportRepository::class)]
#[Vich\Uploadable()]
class FichierImport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customer = null;

    #[ORM\Column(length: 255)]
    private ?string $ncVersion = null;

    #[ORM\Column(length: 255)]
    private ?string $session = null;

    #[ORM\Column(length: 255)]
    private ?string $uid = null;

    #[ORM\Column(length: 255)]
    private ?string $mimeType = null;

    #[ORM\Column(length: 255)]
    private ?string $templateType = null;

    #[ORM\Column(length: 255)]
    private ?string $TemplateCode = null;

    #[ORM\Column(length: 255)]
    private ?string $Company = null;

    #[ORM\Column(length: 255)]
    private ?string $Repository = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[Vich\UploadableField(mapping:"fichier_import", fileNameProperty:"file")]
    private File $imageFile;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;

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


    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getTemplateType(): ?string
    {
        return $this->templateType;
    }

    public function setTemplateType(string $templateType): self
    {
        $this->templateType = $templateType;

        return $this;
    }

    public function getTemplateCode(): ?string
    {
        return $this->TemplateCode;
    }

    public function setTemplateCode(string $TemplateCode): self
    {
        $this->TemplateCode = $TemplateCode;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->Company;
    }

    public function setCompany(string $Company): self
    {
        $this->Company = $Company;

        return $this;
    }

    public function getRepository(): ?string
    {
        return $this->Repository;
    }

    public function setRepository(string $Repository): self
    {
        $this->Repository = $Repository;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->createdAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
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
}
