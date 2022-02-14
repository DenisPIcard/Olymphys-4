<?php

namespace App\Entity;

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
    private int $id = 0;

    /**

     * @ORM\Column(name="niveau", type="string", length=255, nullable=true)
     */
    private ?string $niveau = null;

    /**
     * @var int
     *
     * @ORM\Column(name="nbreprix", type="smallint", nullable=false)
     */
    private int $nbreprix = 0;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    /**
     * Set niveau
     *
     * @param string $niveau
     *
     * @return Classement
     */
    public function setNiveau(string $niveau): Classement
    {
        $this->niveau = $niveau;

        return $this;
    }


    /**
     * Get nbreprix
     *
     * @return integer
     */
    public function getNbreprix(): ?int
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
    public function setNbreprix(int $nbreprix): Classement
    {
        $this->nbreprix = $nbreprix;

        return $this;
    }
}
