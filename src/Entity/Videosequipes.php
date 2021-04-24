<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Centrescia
 * 
 * @ORM\Table(name="videosequipes")
 * @ORM\Entity(repositoryClass="App\Repository\VideosequipesRepository")
 * 
 */

class Videosequipes
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
     * @Assert\Url(
     *    message = "L\'url '{{ value }}' n'est pas valide",
     *  )
      * @ORM\Column(name="lien", type="string")
       */
      private $lien;
      /**
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Edition")
       * @ORM\JoinColumn(name="edition_id",  referencedColumnName="id",onDelete="CASCADE" )
       */
      private $edition;
      
      /**
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
       * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id",onDelete="CASCADE" )
       */
      private $equipe;
      
      /**
     * @var string
     *  
      * @ORM\Column(name="nom", type="string", nullable=true)
       */
      private $nom;
      
      /**
       * 
       * 
       * @ORM\Column(type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
     public function getId()
    {
        return $this->id;
    }
     public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {    
        $this->edition = $edition;
         return $this;
    }
    public function getEquipe()
    {
        return $this->equipe;
    }

    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;
        return $this;
    }
    
    public function getLien()
    {
        return $this->lien;
    }

    public function setLien($lien)
    {    
        $this->lien = $lien;
         $this->updatedAt = new \DateTime('now');
        return $this;
    }
     public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {    
        $this->nom = $nom;
         return $this;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
      
      
}
