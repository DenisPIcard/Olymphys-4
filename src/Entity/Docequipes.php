<?php

namespace App\Entity;

use App\Repository\DocequipesRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=DocequipesRepository::class)
 */
class Docequipes
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $fichier;


    /**
     *
     * @var File
     * @Vich\UploadableField(mapping="docequipes", fileNameProperty="fichier")
     *
     *
     */
    private File $fichierFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFichierFile()
    {

        return $this->fichierFile;
    }

    public function setFichierFile(File $fichierFile = null)

    {

        //$nom=$this->getFichier();


        if ($fichierFile) {
            $this->updatedAt = new DateTime('now');
        }
        $this->fichierFile = $fichierFile;
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        //$this->fichier=$nom;

    }
}
