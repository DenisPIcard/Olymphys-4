<?php

namespace App\Entity\Odpf;

use App\Repository\Odpf\OdpfEquipesPasseesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OdpfEquipesPasseesRepository::class)
 */
class OdpfEquipesPassees
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id=0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $numero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lettre=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lycee=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ville=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $academie=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titreProjet=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $profs=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $eleves=null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $selectionnee=null;

    /**
     * @ORM\ManyToOne(targetEntity=OdpfEditionsPassees::class, inversedBy="odpfEquipesPassees")
     */
    private ?OdpfEditionsPassees $edition=null;
    public function __toString(){
        $this->getLettre()!=null?$num=$this->getLettre():$num=$this->getNumero();
        return $this->edition->getEdition().'-'.$num.'-'.$this->getTitreProjet();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getLettre(): ?string
    {
        return $this->lettre;
    }

    public function setLettre(?string $lettre): self
    {
        $this->lettre = $lettre;

        return $this;
    }

    public function getLycee(): ?string
    {
        return $this->lycee;
    }

    public function setLycee(?string $lycee): self
    {
        $this->lycee = $lycee;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAcademie(): ?string
    {
        return $this->academie;
    }

    public function setAcademie(?string $academie): self
    {
        $this->academie = $academie;

        return $this;
    }

    public function getTitreProjet(): ?string
    {
        return $this->titreProjet;
    }

    public function setTitreProjet(?string $titreProjet): self
    {
        $this->titreProjet = $titreProjet;

        return $this;
    }

    public function getProfs(): ?string
    {
        return $this->profs;
    }

    public function setProfs(string $profs): self
    {
        $this->profs = $profs;

        return $this;
    }

    public function getEleves(): ?string
    {
        return $this->eleves;
    }

    public function setEleves(string $eleves): self
    {
        $this->eleves = $eleves;

        return $this;
    }

    public function getSelectionnee(): ?bool
    {
        return $this->selectionnee;
    }

    public function setSelectionnee(bool $selectionnee): self
    {
        $this->selectionnee = $selectionnee;

        return $this;
    }

    public function getEdition(): ?OdpfEditionsPassees
    {
        return $this->edition;
    }

    public function setEdition(?OdpfEditionsPassees $edition): self
    {
        $this->edition = $edition;

        return $this;
    }
}