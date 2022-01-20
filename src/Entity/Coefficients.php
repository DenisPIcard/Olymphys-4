<?php

namespace App\Entity;

use App\Repository\CoefficientsRepository;
use Decimal\Decimal;
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
    private int $id;


    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private ?decimal $demarche;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private ?decimal $oral;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private ?decimal $origin;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private ?decimal $wgroupe;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private ?decimal $ecrit;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private ?decimal $exper;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemarche(): ?decimal
    {
        return $this->demarche;
    }

    public function setDemarche(decimal $demarche): self
    {
        $this->demarche = $demarche;

        return $this;
    }

    public function getOral(): ?decimal
    {
        return $this->oral;
    }

    public function setOral(decimal $oral): self
    {
        $this->oral = $oral;

        return $this;
    }

    public function getOrigin(): ?decimal
    {
        return $this->origin;
    }

    public function setOrigin(decimal $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getWgroupe(): ?decimal
    {
        return $this->wgroupe;
    }

    public function setWgroupe(decimal $wgroupe): self
    {
        $this->wgroupe = $wgroupe;

        return $this;
    }

    public function getEcrit(): ?decimal
    {
        return $this->ecrit;
    }

    public function setEcrit(decimal $ecrit): self
    {
        $this->ecrit = $ecrit;

        return $this;
    }

    public function getExper(): ?decimal
    {
        return $this->exper;
    }

    public function setExper(decimal $exper): self
    {
        $this->exper = $exper;

        return $this;
    }


}