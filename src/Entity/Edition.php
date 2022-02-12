<?php

namespace App\Entity;

use datetime;
use DateTimeImmutable;
use DateTimeInterface;
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
    protected DateTimeInterface $datelimcia;
    /**
     * @var datetime
     * @ORM\Column(name="datelimite_nat", type="datetime",nullable=true)
     */
    protected DateTimeInterface $datelimnat;
    /**
     * @var datetime
     * @ORM\Column(name="date_ouverture_site", type="datetime",nullable=true)
     */
    protected DateTimeInterface $dateouverturesite;
    /**
     * @var datetime
     * @ORM\Column(name="concours_cia", type="datetime",nullable=true)
     */
    protected DateTimeInterface $concourscia;
    /**
     * @var datetime
     * @ORM\Column(name="concours_cn", type="datetime",nullable=true)
     */
    protected DateTimeInterface $concourscn;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id = 0;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ed = null;
    /**
     * @ORM\Column(type="datetime",  nullable=true)
     */
    private ?DateTimeInterface $date;
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
    private ?DateTimeInterface $dateclotureinscription;

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

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->date = new DateTimeImmutable();
        $this->dateclotureinscription = new DateTimeImmutable();
        $this->datelimcia = new DateTimeImmutable();
        $this->datelimnat = new DateTimeImmutable();
        $this->dateouverturesite = new DateTimeImmutable();
        $this->concourscia = new DateTimeImmutable();
        $this->concourscn = new DateTimeImmutable();

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

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
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

    public function getDatelimcia(): DateTimeInterface
    {
        return $this->datelimcia;
    }

    public function setDatelimcia(DateTimeInterface $Date)
    {
        $this->datelimcia = $Date;
    }

    public function getDatelimnat(): DateTimeInterface
    {
        return $this->datelimnat;
    }

    public function setDatelimnat(DateTimeInterface $Date)
    {
        $this->datelimnat = $Date;
    }

    public function getDateouverturesite(): DateTimeInterface
    {
        return $this->dateouverturesite;
    }

    public function setDateouverturesite(DateTimeInterface $Date)
    {
        $this->dateouverturesite = $Date;
    }

    public function getConcourscia(): DateTimeInterface
    {
        return $this->concourscia;
    }

    public function setConcourscia(DateTimeInterface $Date)
    {
        $this->concourscia = $Date;
    }

    public function getConcourscn(): DateTimeInterface
    {
        return $this->concourscn;
    }

    public function setConcourscn(DateTimeInterface $Date)
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

    public function getDateclotureinscription(): ?DateTimeInterface
    {
        return $this->dateclotureinscription;
    }

    public function setDateclotureinscription(DateTimeInterface $dateclotureinscription): self
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
