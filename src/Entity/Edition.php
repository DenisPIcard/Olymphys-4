<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EditionRepository")
 */
class Edition
{
    /**
     * @var \datetime
     * @ORM\Column(name="datelimite_cia", type="datetime", nullable=true)
     */
    protected datetime $datelimcia;
    /**
     * @var \datetime
     * @ORM\Column(name="datelimite_nat", type="datetime",nullable=true)
     */
    protected datetime $datelimnat;
    /**
     * @var \datetime
     * @ORM\Column(name="date_ouverture_site", type="datetime",nullable=true)
     */
    protected $dateouverturesite;
    /**
     * @var \datetime
     * @ORM\Column(name="concours_cia", type="datetime",nullable=true)
     */
    protected datetime $concourscia;
    /**
     * @var \datetime
     * @ORM\Column(name="concours_cn", type="datetime",nullable=true)
     */
    protected $concourscn;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ed;
    /**
     * @ORM\Column(type="datetime",  nullable=true)
     */
    private ?DateTime $date;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ville;
    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private ?string $lieu;
    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $dateclotureinscription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $annee;

    public function __toString()
    {
        return $this->ed;


    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEd(): ?string
    {
        return $this->ed;
    }

    public function setEd(string $ed): self
    {
        $this->ed = $ed;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }


    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDatelimcia()
    {
        return $this->datelimcia;
    }

    public function setDatelimcia($Date)
    {
        $this->datelimcia = $Date;
    }

    public function getDatelimnat()
    {
        return $this->datelimnat;
    }

    public function setDatelimnat($Date)
    {
        $this->datelimnat = $Date;
    }

    public function getDateouverturesite()
    {
        return $this->dateouverturesite;
    }

    public function setDateouverturesite($Date)
    {
        $this->dateouverturesite = $Date;
    }

    public function getConcourscia()
    {
        return $this->concourscia;
    }

    public function setConcourscia($Date)
    {
        $this->concourscia = $Date;
    }

    public function getConcourscn()
    {
        return $this->concourscn;
    }

    public function setConcourscn($Date)
    {
        $this->concourscn = $Date;
    }

    public function getEncours(): ?bool
    {
        return $this->encours;
    }

    public function setEncours(?bool $encours): self
    {
        $this->encours = $encours;

        return $this;
    }

    public function getDateclotureinscription(): ?\DateTime
    {
        return $this->dateclotureinscription;
    }

    public function setDateclotureinscription(DateTime $dateclotureinscription): self
    {
        $this->dateclotureinscription = $dateclotureinscription;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }


}
