<?php

namespace App\Entity;



use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Edition
 *
 * @ORM\Table(name="edition")
 * @ORM\Entity(repositoryClass="App\Repository\EditionRepository")
 */
class Edition
{
    /**
     * @ORM\Column(name="datelimite_cia", type="datetime", nullable=true)
     */
    protected ?DateTime $datelimcia;
    /**
     * @ORM\Column(name="datelimite_nat", type="datetime",nullable=true)
     */
    protected ?DateTime $datelimnat;
    /**
     * @ORM\Column(name="date_ouverture_site", type="datetime",nullable=true)
     */
    protected ?DateTime $dateouverturesite;
    /**
      * @ORM\Column(name="concours_cia", type="datetime",nullable=true)
     */
    protected ?DateTime $concourscia;
    /**
     * @ORM\Column(name="concours_cn", type="datetime",nullable=true)
     */
    protected ?DateTime $concourscn;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ed = null;
    /**
     * @ORM\Column(type="datetime",  nullable=true)
     */
    private ?DateTime $date;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ville = null;
    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private ?string $lieu = null;
    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $dateclotureinscription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $annee = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lienYoutube = null;

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

    public function getDate(): ?DateTime
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

    public function getDatelimcia(): ?DateTime
    {
        return $this->datelimcia;
    }

    public function setDatelimcia(DateTime $Date)
    {
        $this->datelimcia = $Date;
    }

    public function getDatelimnat(): ?DateTime
    {
        return $this->datelimnat;
    }

    public function setDatelimnat(DateTime $Date)
    {
        $this->datelimnat = $Date;
    }

    public function getDateouverturesite(): ?DateTime
    {
        return $this->dateouverturesite;
    }

    public function setDateouverturesite(DateTime $Date)
    {
        $this->dateouverturesite = $Date;
    }

    public function getConcourscia(): ?DateTime
    {
        return $this->concourscia;
    }

    public function setConcourscia(DateTime $Date)
    {
        $this->concourscia = $Date;
    }

    public function getConcourscn(): ?DateTime
    {
        return $this->concourscn;
    }

    public function setConcourscn(DateTime $Date)
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

    public function getDateclotureinscription(): ?DateTime
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

    public function getLienYoutube(): ?string
    {
        return $this->lienYoutube;
    }

    public function setLienYoutube(?string $lienYoutube): self
    {
        $this->lienYoutube = $lienYoutube;

        return $this;
    }


}
