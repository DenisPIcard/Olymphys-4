<?php

namespace App\Entity;

use App\Repository\CoefficientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoefficientsRepository::class)
 */
class Coefficients
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;




    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $demarche;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $oral;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $origin;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $wgroupe;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $ecrit;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $exper;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemarche(): ?string
    {
        return $this->demarche;
    }

    public function setDemarche(string $demarche): self
    {
        $this->demarche = $demarche;

        return $this;
    }

    public function getOral(): ?string
    {
        return $this->oral;
    }

    public function setOral(string $oral): self
    {
        $this->oral = $oral;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getWgroupe(): ?string
    {
        return $this->wgroupe;
    }

    public function setWgroupe(string $wgroupe): self
    {
        $this->wgroupe = $wgroupe;

        return $this;
    }

    public function getEcrit(): ?string
    {
        return $this->ecrit;
    }

    public function setEcrit(string $ecrit): self
    {
        $this->ecrit = $ecrit;

        return $this;
    }

    public function getExper(): ?string
    {
        return $this->exper;
    }

    public function setExper(string $exper): self
    {
        $this->exper = $exper;

        return $this;
    }




}