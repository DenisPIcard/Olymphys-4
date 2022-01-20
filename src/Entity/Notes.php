<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notes
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="App\Repository\NotesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Notes
{
const NE_PAS_NOTER = 0;
    const INSUFFISANT = 1;
    const MOYEN = 2;
    const BIEN = 3;
    const EXCELLENT = 4;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;
    /**
     * @var int
     *
     * @ORM\Column(name="exper", type="smallint")
     */
    private int $exper;
    /**
     * @var int
     *
     * @ORM\Column(name="demarche", type="smallint")
     */
    private int $demarche;
    /**
     * @var int
     *
     * @ORM\Column(name="oral", type="smallint")
     */
    private int $oral;
    /**
     * @var int
     *
     * @ORM\Column(name="origin", type="smallint")
     */
    private int $origin;
    /**
     * @var int
     *
     * @ORM\Column(name="Wgroupe", type="smallint")
     */
    private int $wgroupe;
    /**
     * @var int
     *
     * @ORM\Column(name="ecrit", type="smallint", nullable=true)
     */
    private int $ecrit;

    // les constantes de classe 
    //const PAS_NOTE = 0; // état initial de toutes les notes 
        /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipes", inversedBy="notess")
     * @ORM\JoinColumn(name="equipe_id",nullable=false)
     */
    private Equipes $equipe; // pour les écrits....
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Jures", inversedBy="notesj")
     * @ORM\JoinColumn(nullable=false)
     */
    private Jures $jure;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $total;
    /**
     * @ORM\ManyToOne(targetEntity=Coefficients::class)
     * @ORM\JoinColumn(name="coefficients_id",  referencedColumnName="id",onDelete="CASCADE" )
     */
    private ?coefficients $coefficients;

    /**
     *
     * @ORM\PrePersist
     */
    public function increase()
    {
        $this->getEquipe()->increaseNbNotes();
    }

    public function getEquipe(): Equipes
    {
        return $this->equipe;
    }

    public function setEquipe(Equipes $equipe): Notes
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPoints()
    {
        ;
        $points = $this->getExper() * $this->coefficients->getExper()//10
            + $this->getDemarche() * $this->coefficients->getDemarche()//10
            + $this->getOral() * $this->coefficients->getOral()//12.5
            + $this->getOrigin() * $this->coefficients->getOrigin()//12.5
            + $this->getWgroupe() * $this->coefficients->getWgroupe();//5;
        return $points;
    }

    public function getExper(): int
    {
        return $this->exper;
    }

    /**
     * Set exper
     *
     * @param integer $exper
     *
     * @return Notes
     */
    public function setExper($exper): Notes
    {
        $this->exper = $exper;

        return $this;
    }

    public function getDemarche(): int
    {
        return $this->demarche;
    }

    public function setDemarche($demarche): Notes
    {
        $this->demarche = $demarche;

        return $this;
    }

    public function getOral(): int
    {
        return $this->oral;
    }

    public function setOral($oral): Notes
    {
        $this->oral = $oral;

        return $this;
    }

    public function getOrigin(): int
    {
        return $this->origin;
    }

    public function setOrigin($origin): Notes
    {
        $this->origin = $origin;

        return $this;
    }

    public function getWgroupe(): int
    {
        return $this->wgroupe;
    }

// Les attributs calculés

    public function setWgroupe($wgroupe): Notes
    {
        $this->wgroupe = $wgroupe;

        return $this;
    }

    public function getSousTotal()
    {
        $points = $this->getExper() * $this->coefficients->getExper()//10
            + $this->getDemarche() * $this->coefficients->getDemarche()//10
            + $this->getOral() * $this->coefficients->getOral()//12.5
            + $this->getOrigin() * $this->coefficients->getOrigin()//12.5
            + $this->getWgroupe() * $this->coefficients->getWgroupe();//5
        +$this->getEcrit() * $this->coefficients->getEcrit();//5;;
        return $points;
    }

    public function getEcrit(): int
    {
        return $this->ecrit;
    }

    public function setEcrit($ecrit): Notes
    {
        $this->ecrit = $ecrit;

        return $this;
    }

    public function getJure(): Jures
    {
        return $this->jure;
    }

    public function setJure(Jures $jure): Notes
    {
        $this->jure = $jure;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCoefficients(): ?coefficients
    {
        return $this->coefficients;
    }

    public function setCoefficients(?coefficients $coefficients): self
    {
        $this->coefficients = $coefficients;

        return $this;
    }


}

