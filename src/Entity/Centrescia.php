<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;



/**
 * Centrescia
 * 
 * @ORM\Table(name="centrescia")
 * @ORM\Entity(repositoryClass="App\Repository\CentresciaRepository")
 * 
 */

class Centrescia
{
    /**
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
      private $id;
      
      /**
        * @ORM\Column(type="string", length=255, nullable = true)
        * @var string
        */
      private $centre;
    


     
      public function __toString(){
          return $this->centre;

      }
      
      
      
      
      public function getId()
        {
            return $this->id;
        }
    
     public function getCentre()
    {
        return $this->centre;
    }
    
    public function setCentre($centre)
    {
        $this->centre=$centre;
    }

    public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition)
    {
        $this->edition = $edition;

        return $this;
    }


      
}