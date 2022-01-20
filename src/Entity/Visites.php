<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visites
 *
 * @ORM\Table(name="visites")
 * @ORM\Entity(repositoryClass="App\Repository\VisitesRepository")
 */
class Visites
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="attribue", type="boolean")
     */
    public $attribue;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=255, nullable=true)
     */
    private $intitule;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Visites
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function setAttribue(bool $attribue): self
    {
        $this->attribue = $attribue;

        return $this;
    }


}


