<?php

namespace App\Entity;

use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classement
 *
 * @ORM\Table(name="classement")
 * @ORM\Entity(repositoryClass="App\Repository\ClassementRepository")
 */
class Classement
{
    const PREMIER = 1;
    const DEUXIEME = 2;
    const TROISIEME = 3;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    // les constantes de classe  
    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255, nullable=true)
     */
    private ?string $niveau = null;
    /**
     * @var decimal
     *
     * @ORM\Column(name="montant", type="decimal", precision=3, scale=0, nullable=true)
     */
    private ?decimal $montant = null;
    /**
     * @var int
     *
     * @ORM\Column(name="nbreprix", type="smallint", nullable=false)
     */
    private int $nbreprix;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get niveau
     *
     * @return integer
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set niveau
     *
     * @param integer $niveau
     *
     * @return Classement
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get montant
     *
     * @return decimal
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set montant
     *
     * @param integer $montant
     *
     * @return Classement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get nbreprix
     *
     * @return integer
     */
    public function getNbreprix()
    {
        return $this->nbreprix;
    }

    /**
     * Set nbreprix
     *
     * @param integer $nbreprix
     *
     * @return Classement
     */
    public function setNbreprix($nbreprix)
    {
        $this->nbreprix = $nbreprix;

        return $this;
    }
}
