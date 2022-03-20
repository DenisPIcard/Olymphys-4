<?php

namespace App\Entity\Odpf;

use App\Entity\Odpf\OdpfEditionsPassees;
use App\Entity\Odpf\OdpfEquipesPassees;
use App\Repository\OdpfFichierspassesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OdpfFichierspassesRepository::class)
 */
class OdpfFichierspasses
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=OdpfEditionsPassees::class)
     */
    private ?\App\Entity\Odpf\OdpfEditionsPassees $editionpassee;

    /**
     * @ORM\ManyToOne(targetEntity=OdpfEquipesPassees::class)
     */
    private ?\App\Entity\Odpf\OdpfEquipesPassees $equipepassee;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $typefichier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nomfichier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fichierFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;
    public function __construct(){

        $this->updatedAt=new \datetime('now');

    }



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getEditionpassee(): ?OdpfEditionsPassees
    {
        return $this->editionpassee;
    }

    public function setEditionpassee(?OdpfEditionsPassees $Editionpassee): self
    {
        $this->editionpassee = $Editionpassee;

        return $this;
    }

    public function getEquipepassee(): ?OdpfEquipesPassees
    {
        return $this->equipepassee;
    }

    public function setEquipepassee(?OdpfEquipesPassees $equipepassee): self
    {
        $this->equipepassee = $equipepassee;

        return $this;
    }

    public function getTypefichier(): ?int
    {
        return $this->typefichier;
    }

    public function setTypefichier(?int $typefichier): self
    {
        $this->typefichier = $typefichier;

        return $this;
    }

    public function getNomfichier(): ?string
    {
        return $this->nomfichier;
    }

    public function setNomfichier(?string $Nomfichier): self
    {
        $this->nomfichier = $Nomfichier;

        return $this;
    }

    public function getFichierFile(): ?string
    {
        return $this->fichierFile;
    }

    public function setFichierFile(?string $fichierFile): self
    {
        $this->fichierFile = $fichierFile;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
