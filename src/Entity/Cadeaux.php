<?php

namespace App\Entity;

use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\String_;
use PhpOffice\Common\Text;
use Symfony\Component\Validator\Constraints as Assert;

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
    private int $id=0;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=255, nullable=true)
     */
    private string $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="fournisseur", type="string", length=255, nullable=true)
     */
    private string $fournisseur;

    /**
     * @var boolean
     *
     * @ORM\Column(name="attribue", type="boolean")
     */
    private bool $attribue;
    
      /**
     * @var string
     *
     * @ORM\Column(name="raccourci", type="string", length=255, nullable=true)
     */
    private string $raccourci;
    
    

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
    public function __toString(){

        return $this->contenu.'-'.$this->fournisseur;

    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }



    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Cadeaux
     */
    public function setContenu(string $contenu): Cadeaux
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu(): string
    {
        return $this->contenu;
    }

    /**
     * Set fournisseur
     *
     * @param string $fournisseur
     *
     * @return Cadeaux
     */
    public function setFournisseur(string $fournisseur): Cadeaux
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return string
     */
    public function getFournisseur(): string
    {
        return $this->fournisseur;
    }


    /**
     * Get raccourci
     *
     * @return string
     */
    public function getRaccourci(): string
    {
        return $this->raccourci;
    }
    
    
     public function setRaccourci($raccourci): Cadeaux
     {
        $this->raccourci = $raccourci;

        return $this;
    }


    public function displayCadeau(): string
    {
        $var1 = $this->getContenu(); 
        $var2 = $this->getFournisseur();
        return $var1." offert par ".strtoupper($var2);
    }


    /**
     * Set attribue
     *
     * @param boolean $attribue
     *
     * @return Cadeaux
     */
    public function setAttribue(bool $attribue): Cadeaux
    {
        $this->attribue = $attribue;

        return $this;
    }

    /**
     * Get attribue
     *
     * @return boolean
     */
    public function getAttribue(): bool
    {
        return $this->attribue;
    }
}
