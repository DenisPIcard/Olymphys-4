<?php

namespace App\Entity;

use App\Repository\CoefficientsRepository;
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
    private int $id=0;


    /**
     * @ORM\Column(type="integer")
     */
    private ?int $demarche;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $oral;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $origin;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $wgroupe;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $ecrit;

    /**
     * @ORM\Column(type="integer",)
     */
    private ?int $exper;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemarche(): ?int
    {
        return $this->demarche;
    }

    public function setDemarche(string $demarche): self
    {
        $this->demarche = $demarche;

        return $this;
    }

    public function getOral(): ?int
    {
        return $this->oral;
    }

    public function setOral(string $oral): self
    {
        $this->oral = $oral;

        return $this;
    }

    public function getOrigin(): ?int
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getWgroupe(): ?int
    {
        return $this->wgroupe;
    }

    public function setWgroupe(string $wgroupe): self
    {
        $this->wgroupe = $wgroupe;

        return $this;
    }

    public function getEcrit(): ?int
    {
        return $this->ecrit;
    }

    public function setEcrit(string $ecrit): self
    {
        $this->ecrit = $ecrit;

        return $this;
    }

    public function getExper(): ?int
    {
        return $this->exper;
    }

    public function setExper(string $exper): self
    {
        $this->exper = $exper;

        return $this;
    }


}
