<?php

namespace App\Entity;

use App\Repository\OdpfLogosRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Odpf_logos
 * @ORM\Table(name="odpf_logos")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=OdpfLogosRepository::class)
 */
class OdpfLogos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nom = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $url;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $alt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $image;

    /**
     *  @Vich\UploadableField(mapping="odpfImagescarousels", fileNameProperty="nom")
     *  @var File
     */
    private File $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $type;

    public function __construct(){
        $this->createdAt=new DateTime('now');

    }

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

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     */
    public function setImageFile(?File $imageFile = null) : void

    {
        $this->imageFile=$imageFile;

        if($this->imageFile instanceof UploadedFile){
            $this->updatedAt = new DateTime('now');
        }

    }

    public function setUrl( ?string $url): OdpfLogos
    {
        $this->url=$url();
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

}


