<?php

namespace App\Entity;

use App\Repository\OdpfDocumentsRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Odpf_documents
 * @ORM\Table(name="odpf_documents")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=OdpfDocumentsRepository::class)
 */
class OdpfDocuments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fichier;

    /**
     * @var File
     * @Vich\UploadableField(mapping="odpfDocuments", fileNameProperty="fichier")
     *
     */
    private File $fichierFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private DateTime $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    public function getFichierFile()
    {
        return $this->fichierFile;
    }

    public function setFichierFile(File $fichierFile = null)
    {

        if ($fichierFile) {
            $this->updatedAt = new \DateTime('now');
        }
        $this->fichierFile = $fichierFile;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($dateTime)
    {
        $this->updatedAt = $dateTime;

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

    public function getUpdatedAtString()
    {
        return $this->updatedAt->format('d-m-Y H:i:s');

    }
}
