<?php

namespace App\Entity;

use Cassandra\Decimal;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cadeaux
 *
 * @ORM\Table(name="cadeaux")
 * @ORM\Entity(repositoryClass="App\Repository\CadeauxRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cadeaux
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=255, nullable=true)
     */
    private ?string $contenu = null;

    /**
     * @var string
     *
     * @ORM\Column(name="fournisseur", type="string", length=255, nullable=true)
     */
    private ?string $fournisseur = null;
    /**
     * @var decimal
     *
     * @ORM\Column(name="montant", type="decimal", precision=6, scale=2, nullable=true)
     */
    private ?Decimal $montant = null;

    /**
     * @var boolean
     *
     * @ORM\Column(name="attribue", type="boolean")
     */
    private bool $attribue;

    /**
     * @var text
     *
     * @ORM\Column(name="raccourci", type="string", length=255, nullable=true)
     */
    private ?string $raccourci = null;


    /*    public function attribuercadeau()
        {
            if ($this->attribue==0)
            {
              $this->attribue = 1;
              return $this->attribue;
            }
            else
            {
              $this->attribue = 0;
              return $this->attribue;
            }
        }*/

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
     * Get raccourci
     *
     * @return string
     */
    public function getRaccourci()
    {
        return $this->raccourci;
    }

    public function setRaccourci($raccourci)
    {
        $this->raccourci = $raccourci;

        return $this;
    }

    public function displayCadeau()
    {
        $var1 = $this->getContenu();
        $var2 = $this->getFournisseur();
        $var3 = $this->getMontant();
        $var = $var1 . " offet par " . strtoupper($var2) . " d'une valeur de " . $var3 . " EUR.";

        return $var;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Cadeaux
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return string
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param string $fournisseur
     *
     * @return Cadeaux
     */
    public function setFournisseur($fournisseur)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Cadeaux
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get attribue
     *
     * @return boolean
     */
    public function getAttribue()
    {
        return $this->attribue;
    }

    /**
     * Set attribue
     *
     * @param boolean $attribue
     *
     * @return Cadeaux
     */
    public function setAttribue($attribue)
    {
        $this->attribue = $attribue;

        return $this;
    }
}
